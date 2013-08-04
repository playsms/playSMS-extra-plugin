<?php
if(!(defined('_SECURE_'))){die('Intruder alert');};


/*
 * Implementations of hook checkavailablekeyword()
 *
 * @param $keyword
 *   checkavailablekeyword() will insert keyword for checking to the hook here
 * @return
 *   TRUE if keyword is available
 */
function sms_collect_mc_hook_checkavailablekeyword($keyword) {
	$ok = true;
	$db_query = "SELECT collect_id FROM " . _DB_PREF_ . "_featureCollect_mc WHERE collect_keyword='$keyword'";
	if ($db_result = dba_num_rows($db_query)) {
		$ok = false;
	}
	return $ok;
}

/*
 * Implementations of hook setsmsincomingaction()
 *
 * @param $sms_datetime
 *   date and time when incoming sms inserted to playsms
 * @param $sms_sender
 *   sender on incoming sms
 * @param $collect_keyword
 *   check if keyword is for sms_collect_mc
 * @param $collect_param
 *   get parameters from incoming sms
 * @param $sms_receiver
 *   receiver number that is receiving incoming sms
 * @return $ret
 *   array of keyword owner uid and status, TRUE if incoming sms handled
 */
function sms_collect_mc_hook_setsmsincomingaction($sms_datetime, $sms_sender, $collect_keyword, $collect_param = '', $sms_receiver = '') {
	$ok = false;
	$db_query = "SELECT uid FROM " . _DB_PREF_ . "_featureCollect_mc WHERE collect_keyword='$collect_keyword'";
	$db_result = dba_query($db_query);
	if ($db_row = dba_fetch_array($db_result)) {
		$c_uid = $db_row['uid'];
		if (sms_collect_mc_handle($c_uid, $sms_datetime, $sms_sender, $collect_keyword, $collect_param, $sms_receiver)) {
			$ok = true;
		}
	}
	$ret['uid'] = $c_uid;
	$ret['status'] = $ok;
	return $ret;
}

function sms_collect_mc_handle($c_uid, $sms_datetime, $sms_sender, $collect_keyword, $collect_param = '', $sms_receiver) {
	global $core_config;
	global $web_title,$email_service,$email_footer,$gateway_module;
	$ok = false;
	$collect_keyword = strtoupper($collect_keyword);
	$username = uid2username($c_uid);
	$sms_to = $sms_sender; // we are replying to this sender
	$db_query = "SELECT * FROM " . _DB_PREF_ . "_featureCollect_mc WHERE collect_keyword='$collect_keyword'";
	$db_result = dba_query($db_query);
	if ($db_row = dba_fetch_array($db_result)) {
		if (! $db_row['collect_enable']) {
			$message = _('Collect service inactive');
			//list($ok,$to,$smslog_id) = sendsms_pv($username, $sms_to, $message);
			//$ok = $ok[0];
			$unicode = 0;
			if (function_exists('mb_detect_encoding')) {
				$encoding = mb_detect_encoding($message, 'auto');
				if ($encoding != 'ASCII') {
					$unicode = 1;
				}
			}
			$ret = sendsms($core_config['main']['cfg_gateway_number'],'',$sms_to,$message,$c_uid,0,'text',$unicode);
			$ok = $ret['status'];
			return $ok;
		}
	}
	$c_uid = $db_row['uid'];
	$collect_id = $db_row['collect_id'];
	$num_rows = dba_num_rows($db_query);
	if ($num_rows) {
		$msg1 = $db_row['collect_msg'];

		$db_query = "INSERT INTO " . _DB_PREF_ . "_featureCollect_mc_member (collect_id,collect_msg,member_number,member_since) VALUES ('$collect_id','$collect_param','$sms_to',now())";
		$message = $msg1;
		$logged = dba_query($db_query);
		$ok = true;

		$unicode = core_detect_unicode($message);
		logger_print('to:'.$sms_to.' m:'.$message, 3, "sms_collect_mc");
		list($ok, $to, $smslog_id, $queue) = sendsms($username, $sms_to, $message, 'text', $unicode);

		$ok = $ok[0];

		// Forward to email as well if enable
		$db_query = "SELECT collect_fwd_email FROM "._DB_PREF_."_featureCollect_mc WHERE collect_keyword='$collect_keyword'";
                $db_result = dba_query($db_query);
                $db_row = dba_fetch_array($db_result);
                $email = $db_row['collect_fwd_email'];
                if ($email)
                {
                   // get name from c_uid's phonebook
                   $c_username = uid2username($c_uid);
                   $c_name = phonebook_number2name($sms_sender, $c_username);
                   $sms_sender = $c_name ? $c_name.' <'.$sms_sender.'>' : $sms_sender;

                   $subject = "[SMSGW-".$collect_keyword."] "._('from')." $sms_sender";
                   $body = _('Forward WebSMS')." ($web_title)\n\n";
                   $body .= _('Date and time').": $sms_datetime\n";
                   $body .= _('Sender').": $sms_sender\n";
                   $body .= _('Receiver').": $sms_receiver\n";
                   $body .= _('Keyword').": $collect_keyword\n\n";
                   $body .= _('Message').":\n$collect_param\n\n";
                   $body .= $email_footer."\n\n";
                   sendmail($email_service,$email,$subject,$body);
		   logger_print($body, "3", "DEBUG SMS Collect");
                }
                //$ok = true;

	} else {
		$ok = false;
	}
	return $ok;
}

?>

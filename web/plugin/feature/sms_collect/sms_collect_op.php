<?php
defined('_SECURE_') or die('Forbidden');
if(!valid()){forcenoaccess();};

if ($collect_id = $_REQUEST['collect_id']) {
	if (! ($collect_id = dba_valid(_DB_PREF_.'_featureCollect', 'collect_id', $collect_id))) {
		forcenoaccess();
	}
}

switch ($op) {
	case "export":
		if ($collect_id) {
			$db_query = "SELECT * FROM "._DB_PREF_."_featureCollect_member WHERE collect_id='$collect_id'";
			$filename = "collect-".sms_collect_id2keyword($collect_id)."-".date(Ymd,time()).".csv";
		} else {
			$db_query = "SELECT * FROM "._DB_PREF_."_toolsSimplephonebook WHERE uid='$uid'";
			$filename = "phonebook-".date(Ymd,time()).".csv";
		}
		$db_result = dba_query($db_query);
		while ($db_row = dba_fetch_array($db_result)) {
			$content .= "\"".$db_row['member_id']."\",\"".$db_row['collect_msg']."\",\"".$db_row['member_number']."\",\"".$db_row['member_since']."\"\r\n";
		}
		ob_end_clean();
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment;filename=\"$filename\"");
		echo $content;
		exit();
		break;
}

?>
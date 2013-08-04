<?php
if(!(defined('_SECURE_'))){die('Intruder alert');};

$collect_id = $_REQUEST['collect_id'];

switch ($op)
{
	case "export":
		if ($collect_id)
		{
			$db_query = "SELECT * FROM "._DB_PREF_."_featureCollect_mc_member WHERE collect_id='$collect_id'";
			$filename = "collect-".collectid2keyword($collect_id)."-".date(Ymd,time()).".csv";
		}
		else
		{
			$db_query = "SELECT * FROM "._DB_PREF_."_toolsSimplephonebook WHERE uid='$uid'";
			$filename = "phonebook-".date(Ymd,time()).".csv";
		}
		$db_result = dba_query($db_query);
		while ($db_row = dba_fetch_array($db_result))
		{
			$content .= "\"".$db_row['member_id']."\",\"".$db_row['collect_msg']."\",\"".$db_row['member_number']."\",\"".$db_row['member_since']."\"\r\n";
		}
		ob_end_clean();
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment;filename=\"$filename\"");
		echo $content;
		die();
		break;
}

function collectid2keyword($collect_id) {
        if ($collect_id) {
                $db_query = "SELECT collect_keyword FROM "._DB_PREF_."_featureCollect_mc WHERE collect_id='$collect_id'";
                $db_result = dba_query($db_query);
                $db_row = dba_fetch_array($db_result);
                $collect_keyword = $db_row['collect_keyword'];
        }
        return $collect_keyword;
}

?>

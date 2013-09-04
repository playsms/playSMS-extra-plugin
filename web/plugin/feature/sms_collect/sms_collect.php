<?php
defined('_SECURE_') or die('Forbidden');
if(!valid()){forcenoaccess();};

if ($collect_id = $_REQUEST['collect_id']) {
	if (! ($collect_id = dba_valid(_DB_PREF_.'_featureCollect', 'collect_id', $collect_id))) {
		forcenoaccess();
	}
}

if ($route = $_REQUEST['route']) {
	$fn = $apps_path['plug'].'/feature/sms_collect/'.$route.'.php';
	if (file_exists($fn)) {
		include $fn;
		exit();
	}
}

switch ($op) {
	case "sms_collect_list" :
		if ($err = $_SESSION['error_string']) {
			$content = "<div class=error_string>$err</div>";
		}
		$content .= "
			<h2>"._('Manage collect')."</h2>
			<p>"._button('index.php?app=menu&inc=feature_sms_collect&op=sms_collect_add', _('Add SMS collect'))."
			<table width=100% class=sortable>
			<thead><tr>
				<th width=20%>"._('Keyword')."</th>
				<th width=20%>"._('Total Requests')."</th>
				<th width=40%>"._('User')."</th>
				<th width=10%>"._('Status')."</th>
				<th width=10%>"._('Action')."</th>
			</tr></thead>";
		if (!isadmin()) {
			$query_user_only = "WHERE uid='$uid'";
		}
		$db_query = "SELECT * FROM " . _DB_PREF_ . "_featureCollect $query_user_only ORDER BY collect_id";
		$db_result = dba_query($db_query);
		$i = 0;
		while ($db_row = dba_fetch_array($db_result)) {
			$db_query = "SELECT * FROM " . _DB_PREF_ . "_featureCollect_member WHERE collect_id = '".$db_row['collect_id']."'";
			$num_rows = dba_num_rows($db_query);
			if (!$num_rows) {
				$num_rows = "0";
			}
			$owner = uid2username($db_row['uid']);
			$collect_status = "<a href=\"index.php?app=menu&inc=feature_sms_collect&op=sms_collect_status&collect_id=".$db_row['collect_id']."&ps=1\"><span class=status_disabled /></a>";
			if ($db_row['collect_enable']) {
				$collect_status = "<a href=\"index.php?app=menu&inc=feature_sms_collect&op=sms_collect_status&collect_id=".$db_row['collect_id']."&ps=0\"><span class=status_enabled /></a>";
			}
			$action = "<a href=index.php?app=menu&inc=feature_sms_collect&op=mbr_list&collect_id=".$db_row['collect_id'].">".$core_config['icon']['view']."</a>&nbsp;";
			$action .= "<a href=index.php?app=menu&inc=feature_sms_collect&op=sms_collect_edit&collect_id=".$db_row['collect_id'].">".$core_config['icon']['edit']."</a>&nbsp;";
			$action .= "<a href=\"javascript: ConfirmURL('"._('Are you sure you want to delete SMS collect ?')." ("._('keyword').": ".$db_row['collect_keyword'].")','index.php?app=menu&inc=feature_sms_collect&op=sms_collect_del&collect_id=".$db_row['collect_id']."')\">".$core_config['icon']['delete']."</a>";
			$i++;
			$tr_class = ($i % 2) ? "row_odd" : "row_even";
			$content .= "
				<tr class=$tr_class>
					<td align=center>".$db_row['collect_keyword']."</td>
					<td align=center>$num_rows</td>
					<td align=center>$owner</td>
					<td align=center>$collect_status</td>
					<td align=center align=center>$action</td>
				</tr>";
		}
		$content .= "
			</table>
			"._button('index.php?app=menu&inc=feature_sms_collect&op=sms_collect_add', _('Add SMS collect'));
		echo $content;
		break;

	case "mbr_list" :
		$collect_id = $_REQUEST['collect_id'];
		$db_query = "SELECT collect_keyword FROM " . _DB_PREF_ . "_featureCollect WHERE collect_id = '$collect_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$collect_name = $db_row['collect_keyword'];

		$db_query = "SELECT * FROM " . _DB_PREF_ . "_featureCollect_member WHERE collect_id = '$collect_id' ORDER BY member_since DESC";
		$db_result = dba_query($db_query);

		$button_export = _button("index.php?app=menu&inc=feature_sms_collect&route=sms_collect_op&op=export&collect_id=$collect_id", _('Export'));
		if ($err = $_SESSION['error_string']) {
			$content = "<div class=error_string>$err</div>";
		}
		$content .= "
			<h2>"._('Manage collect')."</h2>
			<h3>"._('Member list for keyword')." $collect_name</h3>
			".$button_export;

		$content .= "
			<table width=100% class=sortable>
			<tr>
				<th width=20%>"._('Phone number')."</th>
				<th width=30%>"._('Datetime')."</th>
				<th width=40%>"._('Message')."</th>
				<th width=10%>"._('Action')."</th>
			</tr>";
		$i = 0;
		while ($db_row = dba_fetch_array($db_result)) {
			$action = "<a href=\"javascript: ConfirmURL('"._('Are you sure you want to delete this entry ?')."','index.php?app=menu&inc=feature_sms_collect&op=mbr_del&collect_id=$collect_id&mbr_id=".$db_row['member_id']."')\">".$core_config['icon']['delete']."</a>";
			$i++;
			$tr_class = ($i % 2) ? "row_odd" : "row_even";
			$content .= "
				<tr class=$tr_class>
					<td align=center>".$db_row['member_number']."</td>
					<td align=center>".$db_row['member_since']."</td>
					<td align=center>".$db_row['collect_msg']."</td>
					<td align=center>$action</td>
				</tr>";
		}
		$content .= "
			</table>
			"._b('index.php?app=menu&inc=feature_sms_collect&op=sms_collect_list');
		echo $content;
		break;

	case "mbr_del" :
		$collect_id = $_REQUEST['collect_id'];
		$mbr_id = $_REQUEST['mbr_id'];
		if ($mbr_id) {
			$db_query = "DELETE FROM " . _DB_PREF_ . "_featureCollect_member WHERE member_id='$mbr_id'";
			if (@ dba_affected_rows($db_query)) {
				$_SESSION['error_string'] = _('Member has been deleted');
			}
		}
		header("Location: index.php?app=menu&inc=feature_sms_collect&op=mbr_list&collect_id=$collect_id");
		exit();
		break;

	case "sms_collect_edit" :
		$collect_id = $_REQUEST['collect_id'];
		$db_query = "SELECT * FROM " . _DB_PREF_ . "_featureCollect WHERE collect_id='$collect_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$edit_collect_keyword = $db_row['collect_keyword'];
		$edit_collect_msg = $db_row['collect_msg'];
		$edit_collect_fwd_email = $db_row['collect_fwd_email'];
		if ($err = $_SESSION['error_string']) {
			$content = "<div class=error_string>$err</div>";
		}
		$content .= "
			<h2>"._('Manage collect')."</h2>
			<h3>"._('Edit SMS collect')."</h3>
			<form action=index.php?app=menu&inc=feature_sms_collect&op=sms_collect_edit_yes method=post>
			<input type=hidden name=edit_collect_id value=\"$collect_id\">
			<input type=hidden name=edit_collect_keyword value=\"$edit_collect_keyword\">
			<table width=100% cellpadding=1 cellspacing=2 border=0>
			<tr>
				<td width=270>"._('SMS collect keyword')."</td><td><b>$edit_collect_keyword</b></td>
			</tr>
			<tr>
				<td>"._('SMS collect reply')."</td><td><input type=text size=30 maxlength=200 name=edit_collect_msg value=\"$edit_collect_msg\"></td>
			</tr>
			<tr>
				<td>"._('Forward to email')."</td><td><input type=text size=30 maxlength=250 name=edit_fwd_email value=\"$edit_collect_fwd_email\"></td>
			</tr>
			</table>
			<p><input type=submit class=button value=\""._('Save')."\">
			</form>
			"._b('index.php?app=menu&inc=feature_sms_collect&op=sms_collect_list');
		echo $content;
		break;

	case "sms_collect_edit_yes" :
		$edit_collect_id = $_REQUEST['edit_collect_id'];
		$edit_collect_keyword = $_REQUEST['edit_collect_keyword'];
		$edit_collect_msg = $_REQUEST['edit_collect_msg'];
		$edit_fwd_email = $_REQUEST['edit_fwd_email'];
		if ($edit_collect_id && $edit_collect_keyword && $edit_collect_msg) {
			$db_query = "
			UPDATE " . _DB_PREF_ . "_featureCollect
			SET c_timestamp='" . mktime() . "',collect_keyword='$edit_collect_keyword',collect_msg='$edit_collect_msg',collect_fwd_email='$edit_fwd_email'
					WHERE collect_id='$edit_collect_id' AND uid='$uid'
				";
			if (@ dba_affected_rows($db_query)) {
				$_SESSION['error_string'] = _('SMS collect has been saved')." ("._('keyword').": ".$edit_collect_keyword.")";
			}
		} else {
			$_SESSION['error_string'] = _('You must fill all fields');
		}
		header("Location: index.php?app=menu&inc=feature_sms_collect&op=sms_collect_edit&collect_id=".$edit_collect_id);
		exit();
		break;

	case "sms_collect_status" :
		$collect_id = $_REQUEST['collect_id'];
		$ps = $_REQUEST['ps'];
		$db_query = "UPDATE " . _DB_PREF_ . "_featureCollect SET c_timestamp='" . mktime() . "',collect_enable='$ps' WHERE collect_id='$collect_id'";
		$db_result = @ dba_affected_rows($db_query);
		if ($db_result > 0) {
			$_SESSION['error_string'] = _('SMS collect status has been changed');
		}
		header("Location: index.php?app=menu&inc=feature_sms_collect&op=sms_collect_list");
		exit();
		break;

	case "sms_collect_del" :
		$collect_id = $_REQUEST['collect_id'];
		$db_query = "SELECT collect_keyword FROM " . _DB_PREF_ . "_featureCollect WHERE collect_id='$collect_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$collect_keyword = $db_row['collect_keyword'];
		if ($collect_keyword) {
			$db_query = "DELETE FROM " . _DB_PREF_ . "_featureCollect WHERE collect_keyword='$collect_keyword'";
			if (@ dba_affected_rows($db_query)) {
				$db_query = "DELETE FROM " . _DB_PREF_ . "_featureCollect_msg WHERE collect_id='$collect_id'";
				$del_msg = dba_affected_rows($db_query);
				$db_query = "DELETE FROM " . _DB_PREF_ . "_featureCollect_member WHERE collect_id='$collect_id'";
				$del_member = dba_affected_rows($db_query);
				$_SESSION['error_string'] = _('SMS collect with all its messages and members has been deleted')." ("._('keyword').": ".$collect_keyword.")";
			}
		}
		header("Location: index.php?app=menu&inc=feature_sms_collect&op=sms_collect_list");
		exit();
		break;

	case "sms_collect_add" :
		if ($err = $_SESSION['error_string']) {
			$content = "<div class=error_string>$err</div>";
		}
		$content .= "
			<h2>"._('Manage collect')."</h2>
			<h3>"._('Add SMS collect')."</h3>
			<form action=index.php?app=menu&inc=feature_sms_collect&op=sms_collect_add_yes method=post>
			<table width=100%>
			<tr>
				<td width=270>"._('SMS collect keyword')."</td><td><input type=text size=8 maxlength=10 name=add_collect_keyword value=\"$add_collect_keyword\"></td>
			</tr>
			<tr>
				<td>"._('SMS collect reply')."</td><td><input type=text size=30 maxlength=200 name=add_collect_msg value=\"$add_collect_msg\"></td>
			</tr>
			<tr>
				<td>"._('Forward to email')."</td><td><input type=text size=30 maxlength=250 name=add_collect_fwd_email value=\"$add_collect_fwd_email\"></td>
			</tr>
			</table>
			<p><input type=submit class=button value=\""._('Save')."\">
			</form>
			"._b('index.php?app=menu&inc=feature_sms_collect&op=sms_collect_list');
		echo $content;
		break;

	case "sms_collect_add_yes" :
		$add_collect_keyword = strtoupper($_REQUEST['add_collect_keyword']);
		$add_collect_msg = $_REQUEST['add_collect_msg'];
		$add_collect_fwd_email = $_REQUEST['add_collect_fwd_email'];
		if ($add_collect_keyword && $add_collect_msg) {
			if (checkavailablekeyword($add_collect_keyword)) {
				$db_query = "
					INSERT INTO " . _DB_PREF_ . "_featureCollect (uid,collect_keyword,collect_msg, collect_fwd_email)
					VALUES ('$uid','$add_collect_keyword','$add_collect_msg', '$add_collect_fwd_email')";
				if ($new_uid = @ dba_insert_id($db_query)) {
					$_SESSION['error_string'] = _('SMS collect has been added')." ("._('keyword').": ".$add_collect_keyword.")";
				}
			} else {
				$_SESSION['error_string'] = _('SMS collect already exists, reserved or use by other feature')." ("._('keyword').": ".$add_collect_keyword.")";
			}
		} else {
			$_SESSION['error_string'] = _('You must fill all fields');
		}
		header("Location: index.php?app=menu&inc=feature_sms_collect&op=sms_collect_add");
		exit();
		break;
}

?>
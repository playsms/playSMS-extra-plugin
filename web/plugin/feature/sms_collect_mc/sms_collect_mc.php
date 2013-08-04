<?php
if(!(defined('_SECURE_'))){die('Intruder alert');};
if (!valid()) {
	forcenoaccess();
};

if ($route = $_REQUEST['route']) {
        $fn = $apps_path['plug'].'/feature/sms_collect_mc/'.$route.'.php';
        if (file_exists($fn)) {
                include $fn;
                exit();
        }
}

switch ($op) {
	case "sms_collect_mc_list" :
		if ($err) {
			$content = "<div class=error_string>$err</div>";
		}
		$content .= "
				<h2>"._('Manage collect')."</h2>
				<p>
				<input type=button value=\""._('Add SMS collect')."\" onClick=\"javascript:linkto('index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_add')\" class=\"button\" />
				<p>
			";
		if (!isadmin()) {
			$query_user_only = "WHERE uid='$uid'";
		}
		$db_query = "SELECT * FROM " . _DB_PREF_ . "_featureCollect_mc $query_user_only ORDER BY collect_id";
		$db_result = dba_query($db_query);
		$content .= "
					<table cellpadding=1 cellspacing=2 border=0 width=100%>
			<tr>
			    <td class=box_title width=5>*</td>
			    <td class=box_title width=20%>"._('Keyword')."</td>
				<td class=box_title width=30%>"._('Total Requests')."</td>
			   	<td class=box_title width=20%>"._('User')."</td>	
			    <td class=box_title width=20%>"._('Status')."</td>
			    <td class=box_title width=20%>"._('Action')."</td>
			</tr>		
			";
		$i = 0;
		while ($db_row = dba_fetch_array($db_result)) {
			$db_query = "SELECT * FROM " . _DB_PREF_ . "_featureCollect_mc_member WHERE collect_id = '".$db_row['collect_id']."'";
			$num_rows = dba_num_rows($db_query);
			if (!$num_rows) {
				$num_rows = "0";
			}
			$i++;
			$td_class = ($i % 2) ? "box_text_odd" : "box_text_even";
			$owner = uid2username($db_row['uid']);
			$collect_status = "<font color=red>"._('Disabled')."</font>";
			if ($db_row['collect_enable']) {
				$collect_status = "<font color=green>"._('Enabled')."</font>";
			}
			$action = "<a href=index.php?app=menu&inc=feature_sms_collect_mc&op=mbr_list&collect_id=".$db_row['collect_id'].">".$collect_icon_view_members."</a>&nbsp;";
			$action .= "<a href=index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_edit&collect_id=".$db_row['collect_id'].">$icon_edit</a>&nbsp;";
			$action .= "<a href=\"javascript: ConfirmURL('"._('Are you sure you want to delete SMS collect ?')." ("._('keyword').": `".$db_row['collect_keyword']."`)','index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_del&collect_id=".$db_row['collect_id']."')\">$icon_delete</a>";
			$content .= "
					<tr>
						<td class=$td_class>&nbsp;$i.</td>
						<td class=$td_class>".$db_row['collect_keyword']."</td>
						<td class=$td_class>$num_rows</td>
						<td class=$td_class>$owner</td>
						<td class=$td_class>$collect_status</td>		
						<td class=$td_class align=center>$action</td>
					</tr>";
		}
		$content .= "</table>";
		echo $content;
		echo "
				<p>
				<input type=button value=\""._('Add SMS collect')."\" onClick=\"javascript:linkto('index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_add')\" class=\"button\" />
				</p>
				";
		break;

	case "mbr_list" :
		$collect_id = $_REQUEST['collect_id'];
		$db_query = "SELECT collect_keyword FROM " . _DB_PREF_ . "_featureCollect_mc WHERE collect_id = '$collect_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$collect_name = $db_row['collect_keyword'];

		$db_query = "SELECT * FROM " . _DB_PREF_ . "_featureCollect_mc_member WHERE collect_id = '$collect_id' ORDER BY member_since DESC";
		$db_result = dba_query($db_query);

		if ($err) {
			$content = "<div class=error_string>$err</div>";
		}
	        $option_group_export = "<a href=\"index.php?app=menu&inc=feature_sms_collect_mc&route=sms_collect_op&op=export&collect_id=$collect_id\">$simplephonebook_icon_export</a>";
	        $option_group_import = "<a href=\"index.php?app=menu&inc=tools_simplephonebook&route=sms_collect_op&op=import&gpid=$gpid\">$simplephonebook_icon_import</a>";

		$content .= "
		    <h2>"._('Member list for keyword')." $collect_name</h2>
			 Operation:&nbsp;&nbsp;$option_group_export
			";

		$content .= "
	    	<table cellpadding=1 cellspacing=2 border=0 width=100%>
	    	<tr>
	        	<td class=box_title width=4>*</td>
				<td class=box_title width=30%>"._('Phone number')."</td>
				<td class=box_title width=30%>"._('Datetime')."</td>
				<td class=box_title width=30%>"._('Message')."</td>
				<td class=box_title>"._('Action')."</td>
	    	</tr>
			";
		$i = 0;
		while ($db_row = dba_fetch_array($db_result)) {
			$i++;
			$td_class = ($i % 2) ? "box_text_odd" : "box_text_even";

			$action = "<a href=\"javascript: ConfirmURL('"._('Are you sure you want to delete this member ?')."','index.php?app=menu&inc=feature_sms_collect_mc&op=mbr_del&collect_id=$collect_id&mbr_id=".$db_row['member_id']."')\">$icon_delete</a>";

			$content .= "
		    		<tr>
					<td class=$td_class>&nbsp;$i.</td>
					<td class=$td_class>".$db_row['member_number']."</td>
					<td class=$td_class>".$db_row['member_since']."</td>
					<td class=$td_class>".$db_row['collect_msg']."</td>
					<td class=$td_class>$action</td>	
					</tr>";
		}
		$content .= "</table>";
		echo $content;
		break;

	case "mbr_del" :
		$collect_id = $_REQUEST['collect_id'];
		$mbr_id = $_REQUEST['mbr_id'];
		if ($mbr_id) {
			$db_query = "DELETE FROM " . _DB_PREF_ . "_featureCollect_mc_member WHERE member_id='$mbr_id'";
			if (@ dba_affected_rows($db_query)) {
				$error_string =_('"Member has been deleted');
			}
		}
		header("Location: index.php?app=menu&inc=feature_sms_collect_mc&op=mbr_list&collect_id=$collect_id&err=" . urlencode($error_string));
		break;

	case "sms_collect_mc_edit" :
		$collect_id = $_REQUEST['collect_id'];
		$db_query = "SELECT * FROM " . _DB_PREF_ . "_featureCollect_mc WHERE collect_id='$collect_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$edit_collect_keyword = $db_row['collect_keyword'];
		$edit_collect_msg = $db_row['collect_msg'];
		$edit_collect_fwd_email = $db_row['collect_fwd_email'];
		if ($err) {
			$content = "<div class=error_string>$err</div>";
		}
		$content .= "
		    <h2>"._('Edit SMS collect')."</h2>
		    <p>
		    <form action=index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_edit_yes method=post>
		    <input type=hidden name=edit_collect_id value=\"$collect_id\">
		    <input type=hidden name=edit_collect_keyword value=\"$edit_collect_keyword\">
			<table width=100% cellpadding=1 cellspacing=2 border=0>
		    	<tr>
				<td width=150>"._('SMS collect keyword')."</td><td width=5>:</td><td><b>$edit_collect_keyword</b></td>
		    	</tr>
			<tr>
				<td>"._('SMS collect reply')."</td><td>:</td><td><input type=text size=50 maxlength=200 name=edit_collect_msg value=\"$edit_collect_msg\"></td>
		   	</tr>
                        <tr>
                                <td>"._('Forward to email')."</td><td>:</td><td><input type=text size=50 maxlength=200 name=edit_fwd_email value=\"$edit_collect_fwd_email\"></td>
                        </tr>
			</table>	    
		    <p><input type=submit class=button value=\""._('Save')."\">
		    </form>
		    <br>
			";
		echo $content;

		$db_query = "SELECT collect_enable FROM " . _DB_PREF_ . "_featureCollect_mc WHERE collect_id='$collect_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$collect_status = "<b><font color=red>"._('Disabled')."</font></b>";
		if ($db_row['collect_enable']) {
			$collect_status = "<b><font color=green>"._('Enabled')."</font></b>";
		}
		$content = "
				<h2>"._('Enable or disable this subscribe')."</h2>
				<p>
				<p>"._('Current status').": $collect_status
				<p>"._('What do you want to do ?')."
				<p>- <a href=\"index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_status&collect_id=$collect_id&ps=1\">"._('I want to enable this subscribe')."</a>
				<p>- <a href=\"index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_status&collect_id=$collect_id&ps=0\">"._('I want to disable this subscribe')."</a>
				<br>
				";
		echo $content;
		break;

	case "sms_collect_mc_edit_yes" :
		$edit_collect_id = $_POST['edit_collect_id'];
		$edit_collect_keyword = $_POST['edit_collect_keyword'];
		$edit_collect_msg = $_POST['edit_collect_msg'];
		$edit_fwd_email = $_POST['edit_fwd_email'];
		if ($edit_collect_id && $edit_collect_keyword && $edit_collect_msg) {
			$db_query = "
			        UPDATE " . _DB_PREF_ . "_featureCollect_mc
			        SET c_timestamp='" . mktime() . "',collect_keyword='$edit_collect_keyword',collect_msg='$edit_collect_msg',collect_fwd_email='$edit_fwd_email'
					WHERE collect_id='$edit_collect_id' AND uid='$uid'
			    	";
			if (@ dba_affected_rows($db_query)) {
				$error_string = _('SMS collect has been saved')." ("._('keyword').": `$edit_collect_keyword`)";
			}
		} else {
			$error_string = _('You must fill all fields');
		}
		header("Location: index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_list&err=" . urlencode($error_string));
		break;

	case "sms_collect_mc_status" :
		$collect_id = $_REQUEST['collect_id'];
		$ps = $_REQUEST['ps'];
		$db_query = "UPDATE " . _DB_PREF_ . "_featureCollect_mc SET c_timestamp='" . mktime() . "',collect_enable='$ps' WHERE collect_id='$collect_id'";
		$db_result = @ dba_affected_rows($db_query);
		if ($db_result > 0) {
			$error_string = _('SMS collect status has been changed');
		}
		header("Location: index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_edit&collect_id=$collect_id&err=" . urlencode($error_string));
		break;

	case "sms_collect_mc_del" :
		$collect_id = $_REQUEST['collect_id'];
		$db_query = "SELECT collect_keyword FROM " . _DB_PREF_ . "_featureCollect_mc WHERE collect_id='$collect_id'";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$collect_keyword = $db_row['collect_keyword'];
		if ($collect_keyword) {
			$db_query = "DELETE FROM " . _DB_PREF_ . "_featureCollect_mc WHERE collect_keyword='$collect_keyword'";
			if (@ dba_affected_rows($db_query)) {
				$db_query = "DELETE FROM " . _DB_PREF_ . "_featureCollect_mc_msg WHERE collect_id='$collect_id'";
				$del_msg = dba_affected_rows($db_query);
				$db_query = "DELETE FROM " . _DB_PREF_ . "_featureCollect_mc_member WHERE collect_id='$collect_id'";
				$del_member = dba_affected_rows($db_query);
				$error_string = _('SMS collect with all its messages and members has been deleted')." ("._('keyword').": `$collect_keyword`)";
			}
		}
		header("Location: index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_list&err=" . urlencode($error_string));
		break;

	case "sms_collect_mc_add" :
		if ($err) {
			$content = "<div class=error_string>$err</div>";
		}
		$content .= "
				<h2>"._('Add SMS collect')."</h2>
		    <p>
		    <form action=index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_add_yes method=post>
			<table width=100% cellpadding=1 cellspacing=2 border=0>
			  <tr>
				<td width=150>"._('SMS collect keyword')."</td><td width=5>:</td><td><input type=text size=8 maxlength=10 name=add_collect_keyword value=\"$add_collect_keyword\"></td>
			  </tr>
			  <tr>
				<td>"._('SMS collect reply')."</td><td>:</td><td><input type=text size=50 maxlength=200 name=add_collect_msg value=\"$add_collect_msg\"></td>
			  </tr>
                          <tr>
                                <td>"._('Forward to email')."</td><td>:</td><td><input type=text size=50 maxlength=200 name=add_collect_fwd_email value=\"$add_collect_fwd_email\"></td>
                          </tr>
			</table>
				<p><input type=submit class=button value=\""._('Add')."\">
				</form>
			";
		echo $content;
		break;

	case "sms_collect_mc_add_yes" :
		$add_collect_keyword = strtoupper($_POST['add_collect_keyword']);
		$add_collect_msg = $_POST['add_collect_msg'];
		$add_collect_fwd_email = $_POST['add_collect_fwd_email'];
		if ($add_collect_keyword && $add_collect_msg) {
			if (checkavailablekeyword($add_collect_keyword)) {
				$db_query = "
							INSERT INTO " . _DB_PREF_ . "_featureCollect_mc (uid,collect_keyword,collect_msg, collect_fwd_email)
							VALUES ('$uid','$add_collect_keyword','$add_collect_msg', '$add_collect_fwd_email')
							";
				if ($new_uid = @ dba_insert_id($db_query)) {
					$error_string = _('SMS collect has been added')." ("._('keyword').": `$add_collect_keyword`)";
				}
			} else {
				$error_string = _('SMS collect already exists, reserved or use by other feature')." ("._('keyword').": `$add_collect_keyword`)";
			}
		} else {
			$error_string = _('You must fill all fields');
		}
		header("Location: index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_list&err=" . urlencode($error_string));
		break;

}
?>

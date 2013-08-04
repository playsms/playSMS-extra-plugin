<?php
if(!(defined('_SECURE_'))){die('Intruder alert');};

// insert to left menu array
$menutab_feature = $core_config['menutab']['feature'];
$menu_config[$menutab_feature][] = array("index.php?app=menu&inc=feature_sms_collect_mc&op=sms_collect_mc_list", _('Manage collect'));

$collect_icon_add_message = "<img src=\"".$http_path['themes']."/".$themes_module."/images/edit_action.gif\" alt=\""._('Add message')."\" title=\""._('Add message')."\" border=0>";
$collect_icon_view_members = "<img src=\"".$http_path['themes']."/".$themes_module."/images/view_action.gif\" alt=\""._('View members')."\" title=\""._('View members')."\" border=0>";
$collect_icon_view_messages = "<img src=\"".$http_path['themes']."/".$themes_module."/images/view_action.gif\" alt=\""._('View messages')."\" title=\""._('View messages')."\" border=0>";

?>
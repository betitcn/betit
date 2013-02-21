<?php

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$newnum = 0;
foreach (array('notenum','pokenum','addfriendnum','mtaginvitenum','eventinvitenum','myinvitenum') as $value) {
		$newnum = $newnum + $space[$value];
}
	
$_SGLOBAL['member']['allnotenum'] = $newnum;

$data = array();
$data['allnotenum'] = $_SGLOBAL['member']['allnotenum'];
$data['newpm'] = $space['newpm'];
$data['addfriendnum'] = $space['addfriendnum'];

capi_showmessage_by_data('rest_success',  0, $data);
?>

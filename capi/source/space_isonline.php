<?php

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$uid = $_REQUEST['uid'];
$tmpspace = getspace($uid);
$isonline = capi_isonline($uid, $tmpspace);

capi_showmessage_by_data('rest_success',  0, array('isonline'=>$isonline));
?>

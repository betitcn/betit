<?php

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$token = empty($_REQUEST['token'])?'':$_REQUEST['token'];

if (!empty($token)){
	updatetable('space', array('iostoken'=>$token), array('uid'=>$_SGLOBAL['supe_uid']));

	capi_showmessage_by_data('rest_success', 0);
}else{
	capi_showmessage_by_data('rest_faild', 1);
}


?>
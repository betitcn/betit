<?php

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$bind = empty($_REQUEST['bind'])?1:$_REQUEST['bind'];

if ($bind){
	updatetable('space', array('isandriod'=>1), array('uid'=>$_SGLOBAL['supe_uid']));

	capi_showmessage_by_data('rest_success', 0);
}else{
	updatetable('space', array('isandriod'=>0), array('uid'=>$_SGLOBAL['supe_uid']));

	capi_showmessage_by_data('rest_success', 0);
}


?>
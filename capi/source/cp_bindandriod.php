<?php

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$bind = empty($_REQUEST['bind'])?0:$_REQUEST['bind'];


updatetable('space', array('isandriod'=>$bind), array('uid'=>$_SGLOBAL['supe_uid']));

capi_showmessage_by_data('rest_success', 0);



?>
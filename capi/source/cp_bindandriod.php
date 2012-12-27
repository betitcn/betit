<?php

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

updatetable('space', array('isandriod'=>1), array('uid'=>$_SGLOBAL['supe_uid']));

capi_showmessage_by_data('rest_success', 0);


?>
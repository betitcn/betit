<?php

include_once('./common.php');

$token = empty($_REQUEST['token'])?'':$_REQUEST['token'];

if (!empty($token)){
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('iostoken')." WHERE iostoken='$token' ");
	if (!$value = $_SGLOBAL['db']->fetch_array($query))
	{
		inserttable('iostoken', array('iostoken'=>$token));
	}
	capi_showmessage_by_data('rest_success', 0);
}else{
	capi_showmessage_by_data('rest_faild', 1);
}

?>
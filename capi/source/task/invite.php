<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: invite.php 12304 2009-06-03 07:29:34Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//用户任务完成标识变量 		$task['done']
//任务完成结果html存储变量 	$task['result']
//用户任务向导html存储变量 	$task['guide']

$query = $_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('invite')." WHERE uid='$space[uid]' AND fuid>'0'");
$count = $_SGLOBAL['db']->result($query, 0);

if($count >= 10) {
	
	capi_showmessage_by_data('do_success', 0, array("done"=>1));

} else {

	capi_showmessage_by_data('do_success', 0, array("done"=>0));

}

?>

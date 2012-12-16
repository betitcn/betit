<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp.php 13003 2009-08-05 06:46:06Z liguode $
*/

//通用文件
include_once('./common.php');
include_once(S_ROOT.'./source/function_cp.php');
include_once(S_ROOT.'./source/function_magic.php');

//允许的方法
$acs = array('space', 'doing', 'upload', 'comment', 'blog', 'album', 'relatekw', 'common', 'class',
	'swfupload', 'thread', 'mtag', 'poke', 'friend',
	'avatar', 'profile', 'theme', 'import', 'feed', 'privacy', 'pm', 'share', 'advance', 'invite','sendmail',
	'userapp', 'task', 'credit', 'password', 'domain', 'event', 'poll', 'topic',
	'click','magic', 'top', 'videophoto', 'quiz', 'devtoken');
$ac = (empty($_REQUEST['ac']) || !in_array($_REQUEST['ac'], $acs))?'profile':$_REQUEST['ac'];
$op = empty($_REQUEST['op'])?'':$_REQUEST['op'];

if ($ac=='upload' && $_REQUEST['uid']){ 
   $_SGLOBAL['supe_uid'] = $_REQUEST['uid'];
} 

//权限判断
if(empty($_SGLOBAL['supe_uid'])) {
	if($_SERVER['REQUEST_METHOD'] == 'GET') {
		ssetcookie('_refer', rawurlencode($_SERVER['REQUEST_URI']));
	} else {
		ssetcookie('_refer', rawurlencode('cp.php?ac='.$ac));
	}
	capi_showmessage_by_data('to_login');
}

//获取空间信息
$space = getspace($_SGLOBAL['supe_uid']);
if(empty($space)) {
	capi_showmessage_by_data('space_does_not_exist');
}

//是否关闭站点
if(!in_array($ac, array('common', 'pm'))) {
	checkclose();
	//空间被锁定
	if($space['flag'] == -1) {
		capi_showmessage_by_data('space_has_been_locked');
	}
	//禁止访问
	if(checkperm('banvisit')) {
		ckspacelog();
		capi_showmessage_by_data('you_do_not_have_permission_to_visit');
	}
	//验证是否有权限玩应用
	if($ac =='userapp' && !checkperm('allowmyop')) {
		capi_showmessage_by_data('no_privilege');
	}
}

include_once(S_ROOT.'./source/function_cron.php');
runcron(6);

//菜单
$actives = array($ac => ' class="active"');

include_once(S_ROOT.'./capi/source/cp_'.$ac.'.php');

?>

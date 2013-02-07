<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space.php 13003 2009-08-05 06:46:06Z liguode $
*/

include_once('./common.php');
include_once(S_ROOT.'./data/data_magic.php');

//是否关闭站点
//checkclose();

//处理rewrite
if($_SCONFIG['allowrewrite'] && isset($_REQUEST['rewrite'])) {
	$rws = explode('-', $_REQUEST['rewrite']);
	if($rw_uid = intval($rws[0])) {
		$_REQUEST['uid'] = $rw_uid;
	} else {
		$_REQUEST['do'] = $rws[0];
	}
	if(isset($rws[1])) {
		$rw_count = count($rws);
		for ($rw_i=1; $rw_i<$rw_count; $rw_i=$rw_i+2) {
			$_REQUEST[$rws[$rw_i]] = empty($rws[$rw_i+1])?'':$rws[$rw_i+1];
		}
	}
	unset($_REQUEST['rewrite']);
}

//允许动作
$dos = array('feed', 'doing', 'mood', 'blog', 'album', 'thread', 'mtag', 'friend', 'wall', 'tag', 'notice', 'share', 'topic', 'home', 'pm', 'event', 'poll', 'top', 'info', 'videophoto', /* chenler add */ 'quiz','stat', 'isonline', 'task','ajax');

//获取变量
$isinvite = 0;
$uid = empty($_REQUEST['uid'])?0:intval($_REQUEST['uid']);
$username = empty($_REQUEST['username'])?'':$_REQUEST['username'];
$domain = empty($_REQUEST['domain'])?'':$_REQUEST['domain'];
$do = (!empty($_REQUEST['do']) && in_array($_REQUEST['do'], $dos))?$_REQUEST['do']:'index';

if($do == 'home') {
	$do = 'feed';
} elseif ($do == 'index') {
	//邀请好友
	$invite = empty($_REQUEST['invite'])?'':$_REQUEST['invite'];
	$code = empty($_REQUEST['code'])?'':$_REQUEST['code'];
	$reward = getreward('invitecode', 0);
	if($code && !$reward['credit']) {
		$isinvite = -1;
	} elseif($invite) {
		$isinvite = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT id FROM ".tname('invite')." WHERE uid='$uid' AND code='$invite' AND fuid='0'"), 0);
	}
}

//是否公开
//if(empty($isinvite) && empty($_SCONFIG['networkpublic'])) {
//	checklogin();//需要登录
//}

//获取空间
if($uid) {
	$space = getspace($uid, 'uid');
} elseif ($username) {
	$space = getspace($username, 'username');
} elseif ($domain) {
	$space = getspace($domain, 'domain');
} elseif ($_SGLOBAL['supe_uid']) {
	$space = getspace($_SGLOBAL['supe_uid'], 'uid');
}

if($space) {
	
	//验证空间是否被锁定
	if($space['flag'] == -1) {
		capi_showmessage_by_data('space_has_been_locked');
	}
	
	//隐私检查
	/*if(empty($isinvite) || ($isinvite<0 && $code != space_key($space, $_REQUEST['app']))) {
		//游客
		if(empty($_SCONFIG['networkpublic'])) {
			checklogin();//需要登录
		}
		if(!ckprivacy($do)) {
			include template('space_privacy');
			exit();
		}
	}*/
	
	//别人只查看自己
	/*if(!$space['self']) {
		$_REQUEST['view'] = 'me';
	} else if(empty($space['feedfriend']) && empty($_REQUEST['view'])) {
		$_REQUEST['view'] = 'all';
	}
	if ($_REQUEST['view'] == 'me') {
		$space['feedfriend'] = '';
	}*/
	
} elseif($uid) {

	//判断当前用户是否删除
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spacelog')." WHERE uid='$uid' AND flag='-1'");
	if($value = $_SGLOBAL['db']->fetch_array($query)) {
		capi_showmessage_by_data('the_space_has_been_closed');
	}
	
	//未开通空间
	include_once(S_ROOT.'./uc_client/client.php');
	if($user = uc_get_user($uid, 1)) {
		$space = array('uid' => $user[0], 'username' => $user[1], 'dateline'=>$_SGLOBAL['timestamp'], 'friends'=>array());
		$_SN[$space['uid']] = $space['username'];
	}
}

//游客
if(empty($space)) {
	$space = array('uid'=>0, 'username'=>'guest', 'self'=>1);
	if($do == 'index') $do = 'feed';
}

//更新活动session
if($_SGLOBAL['supe_uid']) {
	
	getmember(); //获取当前用户信息
	
	if($_SGLOBAL['member']['flag'] == -1) {
		capi_showmessage_by_data('space_has_been_locked');
	}
	
	//禁止访问
	if(checkperm('banvisit')) {
		ckspacelog();
		capi_showmessage_by_data('you_do_not_have_permission_to_visit');
	}
	
	updatetable('session', array('lastactivity' => $_SGLOBAL['timestamp']), array('uid'=>$_SGLOBAL['supe_uid']));
}

//计划任务
if(!empty($_SCONFIG['cronnextrun']) && $_SCONFIG['cronnextrun'] <= $_SGLOBAL['timestamp']) {
	include_once S_ROOT.'./source/function_cron.php';
	runcron();
}

//处理
include_once(S_ROOT."./capi/source/space_{$do}.php");

?>

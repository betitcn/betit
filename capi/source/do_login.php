<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: do_login.php 13210 2009-08-20 07:09:06Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

include_once(S_ROOT.'./source/function_cp.php');

if($_SGLOBAL['supe_uid']) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." WHERE username='$_REQUEST[username]'");
	if($member = $_SGLOBAL['db']->fetch_array($query)) {
		$auth = authcode("$member[password]\t$member[uid]", 'ENCODE');
	}
	$space = getspace($_SGLOBAL['supe_uid']);
	capi_showmessage_by_data('do_success', 0, array("space"=>$space, "m_auth"=>rawurlencode($auth)));
}

$refer = empty($_REQUEST['refer'])?rawurldecode($_SCOOKIE['_refer']):$_REQUEST['refer'];
preg_match("/(admincp|do|cp)\.php\?ac\=([a-z]+)/i", $refer, $ms);
if($ms) {
	if($ms[1] != 'cp' || $ms[2] != 'sendmail') $refer = '';
}
if(empty($refer)) {
	$refer = 'space.php?do=home';
}

//��������
$uid = empty($_REQUEST['uid'])?0:intval($_REQUEST['uid']);
$code = empty($_REQUEST['code'])?'':$_REQUEST['code'];
$app = empty($_REQUEST['app'])?'':intval($_REQUEST['app']);
$invite = empty($_REQUEST['invite'])?'':$_REQUEST['invite'];
$invitearr = array();
$reward = getreward('invitecode', 0);
if($uid && $code && !$reward['credit']) {
	$m_space = getspace($uid);
	if($code == space_key($m_space, $app)) {//��֤ͨ��
		$invitearr['uid'] = $uid;
		$invitearr['username'] = $m_space['username'];
	}
	$url_plus = "uid=$uid&app=$app&code=$code";
} elseif($uid && $invite) {
	include_once(S_ROOT.'./source/function_cp.php');
	$invitearr = invite_get($uid, $invite);
	$url_plus = "uid=$uid&invite=$invite";
}

//û�е�¼��
$_SGLOBAL['nologinform'] = 1;

if(capi_submitcheck('loginsubmit')) {

	$password = $_REQUEST['password'];
	$username = trim($_REQUEST['username']);
	$cookietime = intval($_REQUEST['cookietime']);
	
	$cookiecheck = $cookietime?' checked':'';
	$membername = $username;
	
	if(empty($_REQUEST['username'])) {
		capi_showmessage_by_data('users_were_not_empty_please_re_login');
	}
	
	if($_SCONFIG['seccode_login']) {
		include_once(S_ROOT.'./source/function_cp.php');
		if(!ckseccode($_REQUEST['seccode'])) {
			$_SGLOBAL['input_seccode'] = 1;
			include template('do_login');
			exit;
		}
	}

	//ͬ����ȡ�û�Դ
	if(!$passport = getpassport($username, $password)) {
		capi_showmessage_by_data('login_failure_please_re_login', 'do.php?ac='.$_SCONFIG['login_action']);
	}
	
	$setarr = array(
		'uid' => $passport['uid'],
		'username' => addslashes($passport['username']),
		'password' => md5("$passport[uid]|$_SGLOBAL[timestamp]")//���������������
	);
	
	include_once(S_ROOT.'./source/function_space.php');
	//��ͨ�ռ�
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('space')." WHERE uid='$setarr[uid]'");
	if(!$space = $_SGLOBAL['db']->fetch_array($query)) {
		$space = space_open($setarr['uid'], $setarr['username'], 0, $passport['email']);
	}
	
	$_SGLOBAL['member'] = $space;
	
	//ʵ��
	realname_set($space['uid'], $space['username'], $space['name'], $space['namestatus']);
	
	//������ǰ�û�
	$query = $_SGLOBAL['db']->query("SELECT password FROM ".tname('member')." WHERE uid='$setarr[uid]'");
	if($value = $_SGLOBAL['db']->fetch_array($query)) {
		$setarr['password'] = addslashes($value['password']);
	} else {
		//���±����û���
		inserttable('member', $setarr, 0, true);
	}

	//��������session
	insertsession($setarr, 1); //�ͻ��˵�½
	$auth = authcode("$setarr[password]\t$setarr[uid]", 'ENCODE');
	//����cookie
	ssetcookie('auth', $auth, $cookietime);
	ssetcookie('loginuser', $passport['username'], 31536000);
	ssetcookie('_refer', '');
	
	//ͬ����¼
	if($_SCONFIG['uc_status']) {
		include_once S_ROOT.'./uc_client/client.php';
		$ucsynlogin = uc_user_synlogin($setarr['uid']);
	} else {
		$ucsynlogin = '';
	}
	
	//��������
	if($invitearr) {
		//��Ϊ����
		invite_update($invitearr['id'], $setarr['uid'], $setarr['username'], $invitearr['uid'], $invitearr['username'], $app);
	}
	$_SGLOBAL['supe_uid'] = $space['uid'];
	//�ж��û��Ƿ�������ͷ��
	$reward = $setarr = array();
	$experience = $credit = 0;
	$avatar_exists = ckavatar($space['uid']);
	if($avatar_exists) {
		if(!$space['avatar']) {
			//��������
			$reward = getreward('setavatar', 0);
			$credit = $reward['credit'];
			$experience = $reward['experience'];
			if($credit) {
				$setarr['credit'] = "credit=credit+$credit";
			}
			if($experience) {
				$setarr['experience'] = "experience=experience+$experience";
			}
			$setarr['avatar'] = 'avatar=1';
			$setarr['updatetime'] = "updatetime=$_SGLOBAL[timestamp]";
			$space["reward"] = $reward;
		}
	} else {
		if($space['avatar']) {
			$setarr['avatar'] = 'avatar=0';
		}
		$space["reward"] = getreward('null', 0);
	}
	
	if($setarr) {
		$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET ".implode(',', $setarr)." WHERE uid='$space[uid]'");
	}

	if(empty($_REQUEST['refer'])) {
		$_REQUEST['refer'] = 'space.php?do=home';
	}
	
	realname_get();
	
	capi_showmessage_by_data('login_success',  0, array("space"=>$space, "m_auth"=>rawurlencode($auth)));
}

$membername = empty($_SCOOKIE['loginuser'])?'':sstripslashes($_SCOOKIE['loginuser']);
$cookiecheck = ' checked';

//include template('do_login');

?>

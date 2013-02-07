<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space.php 13003 2009-08-05 06:46:06Z liguode $
*/

include_once('./common.php');
include_once(S_ROOT.'./data/data_magic.php');

//�Ƿ�ر�վ��
//checkclose();

//����rewrite
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

//������
$dos = array('feed', 'doing', 'mood', 'blog', 'album', 'thread', 'mtag', 'friend', 'wall', 'tag', 'notice', 'share', 'topic', 'home', 'pm', 'event', 'poll', 'top', 'info', 'videophoto', /* chenler add */ 'quiz','stat', 'isonline', 'task','ajax');

//��ȡ����
$isinvite = 0;
$uid = empty($_REQUEST['uid'])?0:intval($_REQUEST['uid']);
$username = empty($_REQUEST['username'])?'':$_REQUEST['username'];
$domain = empty($_REQUEST['domain'])?'':$_REQUEST['domain'];
$do = (!empty($_REQUEST['do']) && in_array($_REQUEST['do'], $dos))?$_REQUEST['do']:'index';

if($do == 'home') {
	$do = 'feed';
} elseif ($do == 'index') {
	//�������
	$invite = empty($_REQUEST['invite'])?'':$_REQUEST['invite'];
	$code = empty($_REQUEST['code'])?'':$_REQUEST['code'];
	$reward = getreward('invitecode', 0);
	if($code && !$reward['credit']) {
		$isinvite = -1;
	} elseif($invite) {
		$isinvite = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT id FROM ".tname('invite')." WHERE uid='$uid' AND code='$invite' AND fuid='0'"), 0);
	}
}

//�Ƿ񹫿�
//if(empty($isinvite) && empty($_SCONFIG['networkpublic'])) {
//	checklogin();//��Ҫ��¼
//}

//��ȡ�ռ�
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
	
	//��֤�ռ��Ƿ�����
	if($space['flag'] == -1) {
		capi_showmessage_by_data('space_has_been_locked');
	}
	
	//��˽���
	/*if(empty($isinvite) || ($isinvite<0 && $code != space_key($space, $_REQUEST['app']))) {
		//�ο�
		if(empty($_SCONFIG['networkpublic'])) {
			checklogin();//��Ҫ��¼
		}
		if(!ckprivacy($do)) {
			include template('space_privacy');
			exit();
		}
	}*/
	
	//����ֻ�鿴�Լ�
	/*if(!$space['self']) {
		$_REQUEST['view'] = 'me';
	} else if(empty($space['feedfriend']) && empty($_REQUEST['view'])) {
		$_REQUEST['view'] = 'all';
	}
	if ($_REQUEST['view'] == 'me') {
		$space['feedfriend'] = '';
	}*/
	
} elseif($uid) {

	//�жϵ�ǰ�û��Ƿ�ɾ��
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spacelog')." WHERE uid='$uid' AND flag='-1'");
	if($value = $_SGLOBAL['db']->fetch_array($query)) {
		capi_showmessage_by_data('the_space_has_been_closed');
	}
	
	//δ��ͨ�ռ�
	include_once(S_ROOT.'./uc_client/client.php');
	if($user = uc_get_user($uid, 1)) {
		$space = array('uid' => $user[0], 'username' => $user[1], 'dateline'=>$_SGLOBAL['timestamp'], 'friends'=>array());
		$_SN[$space['uid']] = $space['username'];
	}
}

//�ο�
if(empty($space)) {
	$space = array('uid'=>0, 'username'=>'guest', 'self'=>1);
	if($do == 'index') $do = 'feed';
}

//���»session
if($_SGLOBAL['supe_uid']) {
	
	getmember(); //��ȡ��ǰ�û���Ϣ
	
	if($_SGLOBAL['member']['flag'] == -1) {
		capi_showmessage_by_data('space_has_been_locked');
	}
	
	//��ֹ����
	if(checkperm('banvisit')) {
		ckspacelog();
		capi_showmessage_by_data('you_do_not_have_permission_to_visit');
	}
	
	updatetable('session', array('lastactivity' => $_SGLOBAL['timestamp']), array('uid'=>$_SGLOBAL['supe_uid']));
}

//�ƻ�����
if(!empty($_SCONFIG['cronnextrun']) && $_SCONFIG['cronnextrun'] <= $_SGLOBAL['timestamp']) {
	include_once S_ROOT.'./source/function_cron.php';
	runcron();
}

//����
include_once(S_ROOT."./capi/source/space_{$do}.php");

?>

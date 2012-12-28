<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_pm.php 13000 2009-08-05 05:58:30Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$pmid = empty($_REQUEST['pmid'])?0:floatval($_REQUEST['pmid']);
$uid = empty($_REQUEST['uid'])?0:intval($_REQUEST['uid']);
if($uid) {
	$touid = $uid;
} else {
	$touid = empty($_REQUEST['touid'])?0:intval($_REQUEST['touid']);
}
$daterange = empty($_REQUEST['daterange'])?1:intval($_REQUEST['daterange']);

include_once S_ROOT.'./uc_client/client.php';

if($_REQUEST['op'] == 'checknewpm') {

	//检查当前用户
	if($_SGLOBAL['supe_uid']) {
		$ucnewpm = uc_pm_checknew($_SGLOBAL['supe_uid']);
		if($_SGLOBAL['member']['newpm'] != $ucnewpm) {
			updatetable('space', array('newpm'=>$ucnewpm), array('uid'=>$_SGLOBAL['supe_uid']));
		}
	}
	ssetcookie('checkpm', 1, 30);
	exit();

} elseif($_REQUEST['op'] == 'delete') {

	$pmid = empty($_REQUEST['pmid'])?0:floatval($_REQUEST['pmid']);
	$folder = $_REQUEST['folder']=='inbox'?'inbox':'outbox';
	
	if(capi_submitcheck('deletesubmit')) {
		$retrun = uc_pm_delete($_SGLOBAL['supe_uid'], $folder, array($pmid));
		if($retrun>0) {
			capi_showmessage_by_data('do_success', 0);
		} else {
			capi_showmessage_by_data('this_message_could_not_be_deleted');
		}
	}
	
} elseif($_REQUEST['op'] == 'send') {

	//判断是否发布太快
	//$waittime = interval_check('post');
	//if($waittime > 0) {
	//	capi_showmessage_by_data('operating_too_fast','',1,array($waittime));
	//}
	
	//新用户见习
	cknewuser();
	
	//黑名单
	if($touid) {
		if(isblacklist($touid)) {
			capi_showmessage_by_data('is_blacklist');
		}
	}

	if(capi_submitcheck('pmsubmit')) {

		//发送消息
		$username = empty($_REQUEST['username'])?'':$_REQUEST['username'];

		$message = trim($_REQUEST['message']);
		if(empty($message)) {
			capi_showmessage_by_data('unable_to_send_air_news');
		}
		$subject = '';

		$return = 0;
		if($touid) {
			//直接给一个用户发PM
			$return = uc_pm_send($_SGLOBAL['supe_uid'], $touid, $subject, $message, 1, $pmid, 0);

			//发送邮件通知
			if($return > 0) {
				smail($touid, '', cplang('friend_pm',array(capi_realname($space['uid'],$space), getsiteurl().'space.php?do=pm')), '', 'friend_pm');
			}

			$tospace = getspace($touid);
			
			if ($tospace["iostoken"]){
				$realname = capi_realname($space['uid'], $space);
				apple_push($tospace["iostoken"],$realname.": ".$message);
			}

			if ($tospace["isandriod"]){
				$realname = capi_realname($space['uid'], $space);
				andriod_push($tospace["uid"],$realname.": ".$message);
			}

		} elseif($username) {
			$newusers = array();
			$users = explode(',', $username);
			foreach ($users as $value) {
				$value = trim($value);
				if($value) {
					$newusers[] = $value;
				}
			}
			if($newusers) {
				$return = uc_pm_send($_SGLOBAL['supe_uid'], implode(',', $newusers), $subject, $message, 1, $pmid, 1);
				$realname = capi_realname($space['uid'], $space);
				$query = $_SGLOBAL['db']->query('SELECT * FROM '.tname('space').' WHERE username IN ('.simplode($users).')');
				while($value = $_SGLOBAL['db']->fetch_array($query)) {
					if ($value["iostoken"]){
						
						apple_push($value["iostoken"],$realname.": ".$message );
					}
					if ($value["isandriod"]){
						
						andriod_push($value["uid"], $realname.": ".$message );
					}
				}
			}

			//发送邮件通知
			$touid = 0;
			if($return > 0) {
				$query = $_SGLOBAL['db']->query('SELECT uid FROM '.tname('space').' WHERE username IN ('.simplode($users).')');
				while($value = $_SGLOBAL['db']->fetch_array($query)) {
					if(empty($touid)) $touid = $value['uid'];
					smail($value['uid'], '', cplang('friend_pm',array(capi_realname($space['uid'],$space), getsiteurl().'space.php?do=pm')), '', 'friend_pm');
				}
			}
		}

		if($return > 0) {
			//更新最后发布时间
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET lastpost='$_SGLOBAL[timestamp]' WHERE uid='$_SGLOBAL[supe_uid]'");
			capi_showmessage_by_data('do_success',0);
		} else {
			if(in_array($return, array(-1,-2,-3,-4))) {
				capi_showmessage_by_data('message_can_not_send'.abs($return));
			} else {
				capi_showmessage_by_data('message_can_not_send');
			}
		}
	}

} elseif($_REQUEST['op'] == 'ignore') {
	
	if(capi_submitcheck('ignoresubmit')) {
		uc_pm_blackls_set($_SGLOBAL['supe_uid'], $_REQUEST['ignorelist']);
		capi_showmessage_by_data('do_success', 'space.php?do=pm&view=ignore');
	}
	
} else {
	
	//新用户见习
	cknewuser();

	if(!checkperm('allowpm')) {
		ckspacelog();
		capi_showmessage_by_data('no_privilege');
	}
	//发送
	$friends = array();
	if($space['friendnum']) {
		$query = $_SGLOBAL['db']->query("SELECT fuid AS uid, fusername AS username FROM ".tname('friend')." WHERE uid=$_SGLOBAL[supe_uid] AND status='1' ORDER BY num DESC, dateline DESC LIMIT 0,100");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$value['username'] = saddslashes($value['username']);
			$friends[] = $value;
		}
	}
}

include_once template("cp_pm");

?>

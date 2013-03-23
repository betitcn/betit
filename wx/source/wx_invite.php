<?php

/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: do_register.php 13111 2009-08-12 02:39:58Z liguode $
*/
include_once(S_ROOT.'./source/function_cp.php');
include_once(S_ROOT.'./source/function_magic.php');

$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('space')." WHERE wxkey='$_GET[wxkey]'");
$space=$_SGLOBAL['db']->fetch_array($query);

$siteurl = getsiteurl();

$friendnum = getcount('friend', array('fuid'=>$space['uid'], 'status'=>0));

$maxcount = 50;//最多好友邀请
$reward = getreward('invitecode', 0);
$appid = empty($_GET['app']) ? 0 : intval($_GET['app']);

$inviteapp = $invite_code = '';
if(empty($reward['credit']) || $appid) {
	$reward['credit'] = 0;
	$invite_code = space_key($space, $appid);
}

$siteurl = getsiteurl();
$spaceurl = $siteurl.'space.php?uid='.$_SGLOBAL['supe_uid'];
$mailvar = array(
	"<a href=\"$spaceurl\">".avatar($space['uid'], 'middle')."</a><br>".$_SN[$space['uid']],
	$_SN[$space['uid']],
	$_SCONFIG['sitename'],
	'',
	'',
	$spaceurl,
	''
);

//取出相应的应用
$appinfo = array();
if($appid) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('myapp')." WHERE appid='$appid'");
	$appinfo = $_SGLOBAL['db']->fetch_array($query);
	if($appinfo) {
		$inviteapp = "&amp;app=$appid";
		$mailvar[6] = $appinfo['appname'];
	} else {
		$appid = 0;
	}
}

	

//处理短信邀请
if($_GET[op]=="smsinvite") {
	$username = trim($_POST['username']);
	$name = trim($_POST['name']);
	if(empty($username)) {
		showmessage('user_name_is_not_legitimate');
	}elseif(!preg_match("/^0?(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/",$username)){
	    showmessage('user_name_is_not_legitimate');
	}
	
  
   //file_get_contents($url);
	if (!empty($space)){
	   if($reward['credit']) {
				//计算积分扣减积分
			$credit = intval($reward['credit'])*($invitenum+1);
			
			$setarr = array(
				'uid' => $space['uid'],
				'code' => $_POST['password'],
				'email' => $username.'@familyday.com.cn',
				'type' => 1
			);
			$id = inserttable('invite', $setarr, 1);
			realname_set($setarr['uid'],$space['username']);
			if ($id){
				
				
				$space2 = addmember($username, $_POST['password'], $username.'@familyday.com.cn');
			
				invite_update($id, $space2['uid'], $space2['username'], $space['uid'], $space['username'], 0);
				//$message = $name.",我是".(($space[namestatus]>0)?$space['name']:$space['username'])."，我在爱发现(www.atfaxian.com)分享了很多本地时尚购物的东东哦，请你来看看。你的账号是".$username."，初始密码是".$_POST['password'];
				//SendMessage($username,$message);
				
				realname_get();
				 SendMessage($username,smlang('invite_friend',array($name,$_POST['password'],$_SN[$setarr['uid']])));
				//creatsms($message, $username);
				if($reward['credit']) {
					$credit = intval($reward['credit']);
					$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$credit WHERE uid='$setarr[uid]'");
				}
				wxshowmessage('邀请成功','wx.php?do=feed&wxkey='.$_GET['wxkey']);
			}else{
				wxshowmessage('邀请失败','cp.php?ac=invite');
			}
			/*if($id) {
				$mailvar[4] = "{$siteurl}invite.php?{$id}{$code}{$inviteapp}";
				createmail($value, $mailvar);
				$invitenum++;
			} else {
				$failingmail[] = $value;
			}*/
		} else {
			/*$mailvar[4] = "{$siteurl}invite.php?u=$space[uid]&amp;c=$invite_code{$inviteapp}";
			if($appid) {
				$mailvar[6] = $appinfo['appname'];
			}
			createmail($value, $mailvar);*/

			$setarr = array(
				'uid' => $space['uid'],
				'code' => $_POST['password'],
				'email' => $username.'@familyday.com.cn',
				'type' => 1
			);
			realname_set($setarr['uid'],$space['username']);
			$id = inserttable('invite', $setarr, 1);

			$space2 = addmember($username, $_POST['password'], $username.'@aifaxian.com');
			
			invite_update($id, $space2['uid'], $space2['username'], $space['uid'], $space['username'], 0);
			
			realname_get();
			 SendMessage($username,smlang('invite_friend',array($name,$_POST['password'],$_SN[$setarr['uid']])));
			if($reward['credit']) {
					$credit = intval($reward['credit']);
					$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$credit WHERE uid='$space[uid]'");
			}
			wxshowmessage('邀请成功','wx.php?do=bind&wxkey='.$_GET['wxkey']);
		}
	}
   
}

if ($_GET[op]=="mobileinvite"){
	


	

	if (!empty($space)){

		$username = trim($_POST['username']);
		$name = trim($_POST['name']);
		if(empty($username)) {
			wxshowmessage('user_name_is_not_legitimate');
		}elseif(!preg_match("/^0?(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/",$username)){
			wxshowmessage('user_name_is_not_legitimate');
		}

	   if($reward['credit']) {
				//计算积分扣减积分
			$credit = intval($reward['credit'])*($invitenum+1);
			
			$setarr = array(
				'uid' => $space['uid'],
				'code' => $_POST['password'],
				'email' => saddslashes($value),
				'type' => 1
			);
			$id = inserttable('invite', $setarr, 1);
			realname_set($setarr['uid'],$space['username']);
			if ($id){
				
				
				$space2 = addmember($username, $_POST['password'], $username.'@familyday.com.cn');
			
				invite_update($id, $space2['uid'], $space2['username'], $space['uid'], $space['username'], 0);
				
				
				realname_get();
				SendMessage($username,smlang('invite_friend',array($name,$_POST['password'],$_SN[$setarr['uid']])));
				
				if($reward['credit']) {
					$credit = intval($reward['credit']);
					$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$credit WHERE uid='$space[uid]'");
				}
				
			}else{
			   
			}
			
		} else {
			

			$setarr = array(
				'uid' => $space['uid'],
				'code' => $_POST['password'],
				'email' => saddslashes($value),
				'type' => 1
			);
			$id = inserttable('invite', $setarr, 1);
			 realname_set($setarr['uid'],$space['username']);
			$space2 = addmember($username, $_POST['password'], $username.'@familyday.com.cn');
			
			invite_update($id, $space2['uid'], $space2['username'], $space['uid'], $space['username'], 0);
			
			realname_get();
			 SendMessage($username,smlang('invite_friend',array($name,$_POST['password'],$_SN[$setarr['uid']])));
			if($reward['credit']) {
					$credit = intval($reward['credit']);
					$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit-$credit WHERE uid='$space[uid]'");
			}
			
		}

	   wxshowmessage('邀请成功', 'wx.php?do=feed&wxkey='.$_GET['wxkey']);
	}else{
		wxshowmessage('login_failure_please_re_login',  'wx.php?do=bind&wxkey='.$_GET['wxkey']);
	}
}


realname_set($space['uid'],$space['username']);

realname_get();
$result=0;

$code = strtolower(random(6));

include_once template("./wx/template/invite");


function createmail($mail, $mailvar) {
	global $_SGLOBAL, $_SCONFIG, $space, $_SN, $appinfo;
	
	$mailvar[3] = empty($_POST['saymsg'])?'':getstr($_POST['saymsg'], 500);
	smail(0, $mail, cplang($appinfo ? 'app_invite_subject' : 'invite_subject', array($_SN[$space['uid']], $_SCONFIG['sitename'], $appinfo['appname'])), cplang($appinfo ? 'app_invite_massage' : 'invite_massage', $mailvar));
}

function creatsms($msg, $username){
   //$msg = shtmlspecialchars(trim($msg));
   $message = urlencode(iconv("UTF-8","GB2312//IGNORE",$msg));
   $url='http://www.020smsvip.com/api/mtsms.php?corpid=hongmen&password=20091020&mobilelist=1,'.$username.'&content='.$message;
   $content = file_get_contents($url);
}

function addmember($username, $password, $email){
   global $_SGLOBAL;
	include_once(S_ROOT.'./uc_client/client.php');
	$newuid = uc_user_register($username, $password, $email);
	if($newuid <= 0) {
		if($newuid == -1) {
			wxshowmessage('user_name_is_not_legitimate');
		} elseif($newuid == -2) {
			wxshowmessage('include_not_registered_words');
		} elseif($newuid == -3) {
			wxshowmessage('user_name_already_exists');
		} elseif($newuid == -4) {
			wxshowmessage('email_format_is_wrong');
		} elseif($newuid == -5) {
			wxshowmessage('email_not_registered');
		} elseif($newuid == -6) {
			wxshowmessage('email_has_been_registered');
		} else {
			wxshowmessage('register_error');
		}
	} else {
		$setarr = array(
		'uid' => $newuid,
		'username' => $username,
		'password' => md5("$newuid|$_SGLOBAL[timestamp]")//本地密码随机生成
		);
		//更新本地用户库
		inserttable('member', $setarr, 0, true);

		//开通空间
		include_once(S_ROOT.'./source/function_space.php');
		$space = space_open($newuid, $username, 0, $email);
		//介绍人
        updatetable('space', array('invite_uid'=>$_SGLOBAL['supe_uid'], 'invite_username'=>$_SGLOBAL['supe_username']), array('uid'=>$newuid));
		//结束
		$flog = $inserts = $fuids = $pokes = array();
		if(!empty($_SCONFIG['defaultfusername'])) {
			$query = $_SGLOBAL['db']->query("SELECT uid,username FROM ".tname('space')." WHERE username IN (".simplode(explode(',', $_SCONFIG['defaultfusername'])).")");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				$value = saddslashes($value);
				$fuids[] = $value['uid'];
				$inserts[] = "('$newuid','$value[uid]','$value[username]','1','$_SGLOBAL[timestamp]')";
				$inserts[] = "('$value[uid]','$newuid','$username','1','$_SGLOBAL[timestamp]')";
				$pokes[] = "('$newuid','$value[uid]','$value[username]','".addslashes($_SCONFIG['defaultpoke'])."','$_SGLOBAL[timestamp]')";
				//添加好友变更记录
				$flog[] = "('$value[uid]','$newuid','add','$_SGLOBAL[timestamp]')";
			}
			if($inserts) {
				$_SGLOBAL['db']->query("REPLACE INTO ".tname('friend')." (uid,fuid,fusername,status,dateline) VALUES ".implode(',', $inserts));
				$_SGLOBAL['db']->query("REPLACE INTO ".tname('poke')." (uid,fromuid,fromusername,note,dateline) VALUES ".implode(',', $pokes));
				$_SGLOBAL['db']->query("REPLACE INTO ".tname('friendlog')." (uid,fuid,action,dateline) VALUES ".implode(',', $flog));

				//添加到附加表
				$friendstr = empty($fuids)?'':implode(',', $fuids);
				updatetable('space', array('friendnum'=>count($fuids), 'pokenum'=>count($pokes)), array('uid'=>$newuid));
				updatetable('spacefield', array('friend'=>$friendstr, 'feedfriend'=>$friendstr), array('uid'=>$newuid));

				//更新默认用户好友缓存
				include_once(S_ROOT.'./source/function_cp.php');
				foreach ($fuids as $fuid) {
					friend_cache($fuid);
				}
			}
		}
	}

	return $space;
}

?>
<?php

/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: do_register.php 13111 2009-08-12 02:39:58Z liguode $
*/


if ($_GET[op]=="register"){


	$username = empty($_REQUEST['username']) ? '' : trim($_REQUEST['username']);
	$name = empty($_REQUEST['name']) ? '' : trim($_REQUEST['name']);
	$password = empty($_REQUEST['password1']) ? '' : trim($_REQUEST['password1']);
	$password2 = empty($_REQUEST['password2']) ? '' : trim($_REQUEST['password2']);

	$email = isemail($_REQUEST['email']) ? $_REQUEST['email'] : $username."@familyday.com.cn";

	$data = array();

	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('space')." WHERE wxkey='$_GET[wxkey]'");
	$os = mobile_user_agent_switch();

	if ($value=$_SGLOBAL['db']->fetch_array($query)){
		$result = 2;
		include_once template("./wx/template/reg");
		exit;
	}

	if(empty($name)) {
		wxshowmessage('昵称不能为空');
	}

	// 验证手机号码
	if(empty($username)) {
		wxshowmessage('用户名不能为空');
	} elseif (!preg_match("/^0?(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/",$username)) {
		wxshowmessage('user_name_is_not_legitimate');
	}

	// 验证密码
	if(empty($password)) {
		wxshowmessage('passwrod_can_not_be_empty');
	}

	if($password!=$password2) {
		wxshowmessage('两次输入密码不一致');
	}

	

	//检查邮箱
	if(empty($email)) {
		wxshowmessage('email_format_is_wrong');
	}
	if($_SCONFIG['checkemail']) {
		if($count = getcount('spacefield', array('email'=>$email))) {
			wxshowmessage('email_has_been_registered');
		}
	}

	//检查IP
	$onlineip = getonlineip();
	if($_SCONFIG['regipdate']) {
		$query = $_SGLOBAL['db']->query("SELECT dateline FROM ".tname('space')." WHERE regip='$onlineip' ORDER BY dateline DESC LIMIT 1");
		if($value = $_SGLOBAL['db']->fetch_array($query)) {
			if($_SGLOBAL['timestamp'] - $value['dateline'] < $_SCONFIG['regipdate']*3600) {
				wxshowmessage('regip_has_been_registered');
			}
		}
	}

	// 开始注册
	include_once S_ROOT.'./uc_client/client.php';
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
		$space = space_open($newuid, $username, 0, $email, $cityid);
		
		//默认好友
		$flog = $inserts = $fuids = $pokes = array();
		if(!empty($_SCONFIG['defaultfusername'])) {
			$query = $_SGLOBAL['db']->query("SELECT uid,username FROM ".tname('space')." WHERE username IN (".simplode(explode(',', $_SCONFIG['defaultfusername'])).")");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				$value = saddslashes($value);
				$fuids[] = $value['uid'];
				$inserts[] = "('$newuid','$value[uid]','$value[username]','1','$_SGLOBAL[timestamp]')";
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
				updatetable('spacefield', array('friend'=>$friendstr), array('uid'=>$newuid));

				//更新默认用户好友缓存
				include_once(S_ROOT.'./source/function_cp.php');
				friend_cache($_SGLOBAL['supe_uid']);
				foreach ($fuids as $fuid) {
					feed_friend_cache($fuid);
				}
			}
		}
		
		//绑定新浪微博
		updatetable('space', array('sina_uid'=>$_SESSION['sian_token']['uid'], 'sina_token'=>$_SESSION['sian_token']['access_token'],'name'=>$userinfo['name']), array('uid'=>$newuid));

		// unbind
		updatetable('space', array('wxkey'=>''), array('wxkey'=>$_GET['wxkey']));
		
		

		// 同步登陆
		$jsonurl = "http://www.familyday.com.cn/dapi/do.php?ac=login&username=".$username."&password=".$password;
		$json = file_get_contents($jsonurl,0,null,null);
		$json_output = json_decode($json);
		
		$device = json_encode(array("os"=>$os, "auth"=>$json_output->data->m_auth));

		// 绑定微信key
		updatetable('space', array('wxkey'=>$_GET['wxkey'], 'name'=>$name, 'namestatus'=>1,'device'=>$device), array('uid'=>$setarr[uid]));
		
		echo "<script>localStorage.removeItem('auth');localStorage.setItem('auth','".$json_output->data->m_auth."');</script>";

		//在线session
		insertsession($setarr);

		//设置cookie
		ssetcookie('auth', authcode("$setarr[password]\t$setarr[uid]", 'ENCODE'), 2592000);
		ssetcookie('loginuser', $username, 31536000);
		ssetcookie('_refer', '');
		
		//好友邀请
		if($invitearr) {
			include_once(S_ROOT.'./source/function_cp.php');
			$inviteed = array('fuid'=>$setarr['uid'],'fusername'=>$setarr['username'],'type'=>1);
			updatetable('invite',$inviteed , array('id'=>$invitearr['id']));
			
			//统计更新
			include_once(S_ROOT.'./source/function_cp.php');
			if($app) {
				updatestat('appinvite');
			} else {
				updatestat('invite');
			}
		}
		
		
		//变更记录
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$newuid, 'action'=>'add', 'dateline'=>$_SGLOBAL['timestamp']), 0, true);
		}
		
		$result=1;
		include_once template("./wx/template/reg");
		exit;
	}


}


$result=0;


include_once template("./wx/template/reg");
?>
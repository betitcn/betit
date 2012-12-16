<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: do_register.php 13111 2009-08-12 02:39:58Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$op = $_REQUEST['op'] ? trim($_REQUEST['op']) : '';

if($_SGLOBAL['supe_uid']) {
	capi_showmessage_by_data('do_success', 0);
}

//没有登录表单
$_SGLOBAL['nologinform'] = 1;

//好友邀请
$uid = empty($_REQUEST['uid'])?0:intval($_REQUEST['uid']);
$code = empty($_REQUEST['code'])?'':$_REQUEST['code'];
$app = empty($_REQUEST['app'])?'':intval($_REQUEST['app']);
$invite = empty($_REQUEST['invite'])?'':$_REQUEST['invite'];
$invitearr = array();

$invitepay = getreward('invitecode', 0);
$pay = $app ? 0 : $invitepay['credit'];

if($uid && $code && !$pay) {
	$m_space = getspace($uid);
	if($code == space_key($m_space, $app)) {//验证通过
		$invitearr['uid'] = $uid;
		$invitearr['username'] = $m_space['username'];
	}
	$url_plus = "uid=$uid&app=$app&code=$code";
} elseif($uid && $invite) {
	include_once(S_ROOT.'./source/function_cp.php');
	$invitearr = invite_get($uid, $invite);
	$url_plus = "uid=$uid&invite=$invite";
}

$jumpurl = $app?"userapp.php?id=$app&my_extra=invitedby_bi_{$uid}_{$code}&my_suffix=Lw%3D%3D":'space.php?do=home';

if(empty($op)) {

	if($_SCONFIG['closeregister']) {
		if($_SCONFIG['closeinvite']) {
			capi_showmessage_by_data('not_open_registration');
		} elseif(empty($invitearr)) {
			capi_showmessage_by_data('not_open_registration_invite');
		}
	}

	//是否关闭站点
	checkclose();

	if(capi_submitcheck('registersubmit')) {

		//已经注册用户
		if($_SGLOBAL['supe_uid']) {
			capi_showmessage_by_data('registered');
		}

		if($_SCONFIG['seccode_register']) {
			include_once(S_ROOT.'./source/function_cp.php');
			if(!capi_ckseccode($_REQUEST['seccode'], $_REQUEST['m_auth'])) {
				capi_showmessage_by_data('incorrect_code');
			}
		}

		if(!@include_once S_ROOT.'./uc_client/client.php') {
			capi_showmessage_by_data('system_error');
		}

		if($_REQUEST['password'] != $_REQUEST['password2']) {
			capi_showmessage_by_data('password_inconsistency');
		}

		if(!$_REQUEST['password'] || $_REQUEST['password'] != addslashes($_REQUEST['password'])) {
			capi_showmessage_by_data('profile_passwd_illegal');
		}
		
		$username = trim($_REQUEST['username']);
		$password = $_REQUEST['password'];
		$_REQUEST['email'] = "$username@betit.cn";
		
		$email = isemail($_REQUEST['email'])?$_REQUEST['email']:'';
		if(empty($email)) {
			capi_showmessage_by_data('email_format_is_wrong');
		}
		//检查邮件
		if($_SCONFIG['checkemail']) {
			if($count = getcount('spacefield', array('email'=>$email))) {
				capi_showmessage_by_data('email_has_been_registered');
			}
		}
		//检查IP
		$onlineip = getonlineip();
		if($_SCONFIG['regipdate']) {
			$query = $_SGLOBAL['db']->query("SELECT dateline FROM ".tname('space')." WHERE regip='$onlineip' ORDER BY dateline DESC LIMIT 1");
			if($value = $_SGLOBAL['db']->fetch_array($query)) {
				if($_SGLOBAL['timestamp'] - $value['dateline'] < $_SCONFIG['regipdate']*3600) {
					capi_showmessage_by_data('regip_has_been_registered',  1, array($_SCONFIG['regipdate']));
				}
			}
		}

		$newuid = uc_user_register($username, $password, $email);
		if($newuid <= 0) {
			if($newuid == -1) {
				capi_showmessage_by_data('user_name_is_not_legitimate');
			} elseif($newuid == -2) {
				capi_showmessage_by_data('include_not_registered_words');
			} elseif($newuid == -3) {
				capi_showmessage_by_data('user_name_already_exists');
			} elseif($newuid == -4) {
				capi_showmessage_by_data('email_format_is_wrong');
			} elseif($newuid == -5) {
				capi_showmessage_by_data('email_not_registered');
			} elseif($newuid == -6) {
				capi_showmessage_by_data('email_has_been_registered');
			} else {
				capi_showmessage_by_data('register_error');
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

			//默认好友
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

			//在线session
			insertsession($setarr);
			
			$auth = authcode("$setarr[password]\t$setarr[uid]", 'ENCODE');
			//设置cookie
			ssetcookie('auth', $auth, 2592000);
			ssetcookie('loginuser', $username, 31536000);
			ssetcookie('_refer', '');

			//好友邀请
			if($invitearr) {
				include_once(S_ROOT.'./source/function_cp.php');
				invite_update($invitearr['id'], $setarr['uid'], $setarr['username'], $invitearr['uid'], $invitearr['username'], $app);
				//如果提交的邮箱地址与邀请相符的则直接通过邮箱验证
				if($invitearr['email'] == $email) {
					updatetable('spacefield', array('emailcheck'=>1), array('uid'=>$newuid));
				}
				
				//统计更新
				include_once(S_ROOT.'./source/function_cp.php');
				if($app) {
					updatestat('appinvite');
				} else {
					updatestat('invite');
				}
			}

			//变更记录
			if($_SCONFIG['my_status']) inserttable('userlog', array('uid'=>$newuid, 'action'=>'add', 'dateline'=>$_SGLOBAL['timestamp']), 0, true);

			capi_showmessage_by_data('registered',0,array("space"=>getspace($space["uid"]), "m_auth"=>rawurlencode($auth)));
		}

	}
	
	$register_rule = data_get('registerrule');

	//include template('do_register');

} elseif($op == "checkusername") {

	$username = trim($_REQUEST['username']);
	if(empty($username)) {
		capi_showmessage_by_data('user_name_is_not_legitimate');
	}
	@include_once (S_ROOT.'./uc_client/client.php');
	$ucresult = uc_user_checkname($username);

	if($ucresult == -1) {
		capi_showmessage_by_data('user_name_is_not_legitimate');
	} elseif($ucresult == -2) {
		capi_showmessage_by_data('include_not_registered_words');
	} elseif($ucresult == -3) {
		capi_showmessage_by_data('user_name_already_exists');
	} else {
		capi_showmessage_by_data('succeed');
	}
} elseif($op == "checkseccode") {
	
	include_once(S_ROOT.'./source/function_cp.php');
	if(ckseccode(trim($_REQUEST['seccode']))) {
		capi_showmessage_by_data('succeed');
	} else {
		capi_showmessage_by_data('incorrect_code');
	}
} elseif($op ==  "seccode"){
	//验证码
	$seccode = mkseccode();

	//设定cookie
	capi_showmessage_by_data("rest_success", 0, array("seccode_auth"=>rawurlencode(authcode($seccode, 'ENCODE')), "seccode"=>$seccode));
}

//生成随机
function mkseccode() {
	$seccode = random(6, 1);
	$s = sprintf('%04s', base_convert($seccode, 10, 24));
	$seccode = '';
	$seccodeunits = 'BCEFGHJKMPQRTVWXY2346789';
	for($i = 0; $i < 4; $i++) {
		$unit = ord($s{$i});
		$seccode .= ($unit >= 0x30 && $unit <= 0x39) ? $seccodeunits[$unit - 0x30] : $seccodeunits[$unit - 0x57];
	}
	return $seccode;
}
?>

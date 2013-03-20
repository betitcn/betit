<?php
session_start();

include_once( 'weibo/config.php' );
require_once '../common.php';
require_once 'wx_common.php';
include_once( CONNECT_ROOT.'/saetv2.ex.class.php' );
require_once CONNECT_ROOT."/common/jtee.inc.php";
require_once CONNECT_ROOT."/common/siteUserRegister.class.php";

$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	}
}

if ($token) {
	$_SESSION['token'] = $token;
	setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
	$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
	$uid_get = $c->get_uid();
	$sql = "SELECT uid FROM ".tname('sina_bind_info')." WHERE `sina_uid`='".$uid_get['uid']."'";
	showmessage($uid_get['uid']);  
	$rst = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
	if($rst){
		loaducenter();
		$user = uc_get_user($rst['uid'], 1); 
		uc_user_synlogin($rst['uid']);
		setSession($user[0],$user[1]);
		$sinauid=$uid_get['uid'];
		wxshowmessage('do_success',"wx.php?do=mine&sinauid=$sinauid");
	}else{
		 $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
		 $profile = $c->show_user_by_id($uid_get['uid']);
		 $idstr = ($profile['idstr'])?$profile['idstr']:$profile['id'];
		 $idstr = ($idstr)?$idstr:$_SGLOBAL["timestamp"];
		 $usernameS	= "sina_".$idstr;
		 $regEmailS	= "sina_".$idstr."@betit.cn";
		 $regPwdS	= "sina_".$idstr;
		 require_once CONNECT_ROOT."/common/siteUserRegister.class.php";
		 $regClass = new siteUserRegister();
		$uid = $regClass->reg($usernameS, $regEmailS, $regPwdS);
		if (empty($uid))wxshowmessage("授权失败");
		$msg = '';
		switch($uid){
			case -1:
				$msg = '用户名无效';
				wxshowmessage($msg);
				break;
			case -2:
				$msg = '用户名包含敏感词';
				wxshowmessage($msg);
				break;
			case -3:
				$msg = '用户名已经存在';
				wxshowmessage($msg);
				break;
			case -4:
				$msg = '邮箱格式不正确';
				wxshowmessage($msg);
				break;
			case -5:
				$msg = '此网站邮箱注册受限';
				wxshowmessage($msg);
				break;
			case -6:
				$msg = '邮箱已经存在';
				wxshowmessage($msg);
				break;
			case -7:
				$msg = '发生未知错误';	
				wxshowmessage($msg);
				break;
			default:
				$sql = "SELECT uid FROM ".tname('sina_bind_info')." WHERE `sina_uid`='".$uid_get['uid']."'";  
				$user = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
				if($user){
					$sinauid=$uid_get['uid'];
					wxshowmessage("已绑定", "wx.php?do=mine&sinauid=$sinauid");
				}
				
				$sql = "INSERT INTO " . tname('sina_bind_info') . " (uid,sina_uid,token,tsecret,profile) VALUES ('{$uid}','{$uid_get[uid]}','{$_SESSION[last_key][oauth_token]}','{$_SESSION[last_key][oauth_token_secret]}',' ');";
				$rst = $_SGLOBAL['db']->query($sql);
				if ($profile['name'])
				{
				//主表实名
					$setarr = array(
						'name' => getstr($name, 30, 1, 1, 1),
						'namestatus' => $_SCONFIG['namecheck']?0:1
					);
					updatetable('space', $setarr, array('uid'=>$uid ));
				}

				$setarr = array(
					'weibo' => 1
				);
				updatetable('spacefield', $setarr, array('uid'=>$uid ));

				$filetype = '.jpg';
				
	
				if(empty($uid)) {
					$return =  -1;
				}
				
				$data = file_get_contents($profile['avatar_large']);
				
				$avatarurl = S_ROOT.'/center/data/tmp/upload'.$uid.$filetype;
				$avatartype = $_GET["avatartype"] == 'real' ? 'real' : 'virtual';
				$bigavatarfile = S_ROOT.'/center/data/./avatar/'.get_avatar($uid, 'big', $avatartype);
				$middleavatarfile = S_ROOT.'/center/data/./avatar/'.get_avatar($uid, 'middle', $avatartype);
				$smallavatarfile = S_ROOT.'/center/data/./avatar/'.get_avatar($uid, 'small', $avatartype);
				
				$im = imagecreatefromstring($data);
					
				$bigavatar = resizeImage($im, 200,200);
				imagejpeg($bigavatar, $bigavatarfile);
				$middleavatar = resizeImage($im, 120,120);
				imagejpeg($middleavatar, $middleavatarfile);
				$smallavatar = imagecreatetruecolor(48,48);
				$pic_width = imagesx($im);
				$pic_height = imagesy($im);
				if ($pic_height>$pic_width)
				{
					imagecopyresampled($smallavatar,$im,0,0,0,intval(($pic_height-$pic_width)/2),48,48,$pic_width,$pic_width);
				}else{
					imagecopyresampled($smallavatar,$im,0,0,0,0,48,48,$pic_width,$pic_width);
				}
				imagejpeg($smallavatar, $smallavatarfile);

				$space = getspace($uid);
				$setarr = array();
				$avatar_exists = ckavatar($space["uid"]);
				if($avatar_exists) {
					if(!$space['avatar']) {
						//奖励成长
						$reward = getreward('setavatar', 0);
						if($reward['credit']) {
							$setarr['credit'] = "credit=credit+$reward[credit]";
						}
						if($reward['experience']) {
							$setarr['experience'] = "experience=experience+$reward[experience]";
						}
						
						$setarr['avatar'] = 'avatar=1';
						$setarr['updatetime'] = "updatetime=$_SGLOBAL[timestamp]";
					}
				} else {
					if($space['avatar']) {
						$setarr['avatar'] = 'avatar=0';
					}
				}
				if (empty($reward))
				{
					$reward['credit'] = 0;
					$reward['experience'] = 0;
				}
				if($setarr) {
					$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET ".implode(',', $setarr)." WHERE uid='$uid'");
					//变更记录
					if($_SCONFIG['my_status']) {
						inserttable('userlog', array('uid'=>$uid, 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp']), 0, true);
					}
				}

				
				$msg = "已为你创建了".$_SGLOBAL['sitename']."的帐号，并与你的Sina帐号进行绑定。下次你可以继续使用Sina帐号登录使用.用户名为".$usernameS;
				wxshowmessage($msg, "wx.php?do=index");
				break;
		}
	}
}else{
	showmessage("授权失败");
}

//include template("cp_avatar");
function get_avatar($uid, $size = 'middle', $type = '') {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$typeadd = $type == 'real' ? '_real' : '';
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
}

function resizeImage($im,$maxwidth,$maxheight)
{
	$pic_width = imagesx($im);
	$pic_height = imagesy($im);

	if(($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight))
	{
		if($maxwidth && $pic_width>$maxwidth)
		{
			$widthratio = $maxwidth/$pic_width;
			$resizewidth_tag = true;
		}

		if($maxheight && $pic_height>$maxheight)
		{
			$heightratio = $maxheight/$pic_height;
			$resizeheight_tag = true;
		}

		if($resizewidth_tag && $resizeheight_tag)
		{
			if($widthratio<$heightratio)
				$ratio = $widthratio;
			else
				$ratio = $heightratio;
		}

		if($resizewidth_tag && !$resizeheight_tag)
			$ratio = $widthratio;
		if($resizeheight_tag && !$resizewidth_tag)
			$ratio = $heightratio;

		$newwidth = $pic_width * $ratio;
		$newheight = $pic_height * $ratio;

		if(function_exists("imagecopyresampled"))
		{
			$newim = imagecreatetruecolor($newwidth,$newheight);
		   imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
		}
		else
		{
			$newim = imagecreate($newwidth,$newheight);
		   imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
		}

		return $newim;
	}
	else
	{
		return $newim;
	}           
}


?>
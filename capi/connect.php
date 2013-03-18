<?

include_once('./common.php');
require_once CONNECT_ROOT."/common/jtee.inc.php";
require_once CONNECT_ROOT."/common/siteUserRegister.class.php";

// sinauid 新浪用户id
// username 对应 profile[idstr]
// name 对应 profile[name]
// avatar 对应 $profile['avatar_large']

$site = empty( $_REQUEST['site'])?'weibo': $_REQUEST['site'];

$sql ='';

if ($site=='weibo'){
	$sql = "SELECT uid FROM ".tname('sina_bind_info')." WHERE `sina_uid`='".$_REQUEST['sinauid']."'";
}elseif ($site=='qq'){
	$sql = "SELECT uid FROM ".tname('qq_bind_info')." WHERE `qq_uid`='".$_REQUEST['qqopenid']."'";
}

$rst = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));

if($rst){
	loaducenter();
	$user = uc_get_user($rst['uid'], 1); 
	uc_user_synlogin($rst['uid']);
	$auth = setSession($user[0],$user[1]);
	$space  = getspace($rst['uid']);
	@include_once(S_ROOT.'./data/data_usergroup.php');
	$space["grouptitle"] = $_SGLOBAL["grouptitle"][$space["groupid"]]["grouptitle"];
	$space["avatar"] = capi_avatar($space["uid"]);
	capi_showmessage_by_data('login_success',  0, array("space"=>$space, "m_auth"=>rawurlencode($auth)));
	
}else{
	$usernameS = '';
	$regEmailS = '';
	$regPwdS = '';

	if ( $site=='weibo'){
	
		 $usernameS	= "sina_".$_REQUEST['username'];
		 $regEmailS	= "sina_".$_REQUEST['username']."@betit.cn";
		 $regPwdS	= "sina_".$_REQUEST['username'];
	}elseif ($site=='qq'){

		$usernameS	= randUsername();
	    $regEmailS	= randUsername()."@betit.cn";
		$regPwdS	= randUsername();
	}

	 require_once CONNECT_ROOT."/common/siteUserRegister.class.php";
	 $regClass = new siteUserRegister();

	$uid = $regClass->reg($usernameS, $regEmailS, $regPwdS);
	if (empty($uid))capi_showmessage_by_data("授权失败");
	$msg = '';
	
	switch($uid){
		case -1:
			$msg = '用户名无效';
			capi_showmessage_by_data($msg);
			break;
		case -2:
			$msg = '用户名包含敏感词';
			capi_showmessage_by_data($msg);
			break;
		case -3:
			
			$msg = '用户名已经存在';
			capi_showmessage_by_data($msg);
			break;
		case -4:
			$msg = '邮箱格式不正确';
			capi_showmessage_by_data($msg);
			break;
		case -5:
			$msg = '此网站邮箱注册受限';
			capi_showmessage_by_data($msg);
			break;
		case -6:
			$msg = '邮箱已经存在';
			capi_showmessage_by_data($msg);
			break;
		case -7:
			$msg = '发生未知错误';	
			capi_showmessage_by_data($msg);
			break;
		default:
			if ( $site=='weibo'){
				$sql = "SELECT uid FROM ".tname('sina_bind_info')." WHERE `sina_uid`='".$_REQUEST['sinauid']."'";  
			}else if($site=='qq'){
				$sql = "SELECT uid FROM ".tname('qq_bind_info')." WHERE `qq_uid`='".$_REQUEST['qqopenid']."'";
			}
			$user = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
			if($user){
				capi_showmessage_by_data('已绑定',  0, array("space"=>$space, "m_auth"=>rawurlencode($regClass->$auth)));
			}
			if ( $site=='weibo'){
				$sql = "INSERT INTO " . tname('sina_bind_info') . " (uid,sina_uid,token,tsecret,profile) VALUES ('{$uid}','{$_REQUEST[sinauid]}','{$_REQUEST[oauth_token]}','{$_REQUEST[oauth_token_secret]}',' ');";
			}elseif($site=='qq'){
				$sql = "INSERT INTO " . tname('qq_bind_info') . " (uid,qq_uid) VALUES ('{$uid}','{$_REQUEST[qqopenid]}');";
			}

			$rst = $_SGLOBAL['db']->query($sql);
			//主表实名
			$setarr = array(
				'name' => getstr($_REQUEST['name'], 30, 1, 1, 1),
				'namestatus' => $_SCONFIG['namecheck']?0:1
			);
			updatetable('space', $setarr, array('uid'=>$uid ));
			if ( $site=='weibo'){
				$setarr = array(
						'weibo' => 1
					);
			}elseif($site=='qq'){
				$setarr = array(
						'weibo' => 2
					);
			}
			updatetable('spacefield', $setarr, array('uid'=>$uid ));

			$filetype = '.jpg';
			

			if(empty($uid)) {
				$return =  -1;
			}
			
			$data = file_get_contents($_REQUEST['avatar']);
			
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

			$space["avatar"] = capi_avatar($space["uid"]);
			$msg = "已为你创建了".$_SGLOBAL['sitename']."的帐号，并与你的Sina帐号进行绑定。下次你可以继续使用Sina帐号登录使用.用户名为".$usernameS;
			
			capi_showmessage_by_data($msg,  0, array("space"=>$space, "m_auth"=>rawurlencode($regClass->getAuth())));
			break;
	
	}
}


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

function randStr($len=10) { 
	$chars='ABDEFGHJKLMNPQRSTVWXYabdefghijkmnpqrstvwxy23456789_'; // characters to build the password from 
	mt_srand((double)microtime()*1000000*getmypid()); // seed the random number generater (must be done) 
	$password=''; 
	while(strlen($password)<$len) 
	$password.=substr($chars,(mt_rand()%strlen($chars)),1); 

	return $password; 
} 

function randUsername($pre='qq_'){
	global $_SGLOBAL;

	$username  = $pre.randStr();

	$sql = "SELECT uid FROM ".tname('member')." WHERE `username`='".$username."'";  

    $rst = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
	while($rst){
		
		$username  = 'qq_'.randStr();

		$sql = "SELECT uid FROM ".tname('member')." WHERE `username`='".$username."'";  
	
	}
	return $username;

}

?>

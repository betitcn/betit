<?php
/**
 * 此为PHP-SDK 2.0 的一个使用Demo,用于流程和接口调用演示
 * 请根据自身需求和环境进相应的安全和兼容处理，勿直接用于生产环境
 */
error_reporting(0);


include_once( 'weibo/config.php' );
require_once '../common.php';
require_once 'wx_common.php';
include_once( CONNECT_ROOT.'/Tencent.php' );
require_once CONNECT_ROOT."/common/jtee.inc.php";
require_once CONNECT_ROOT."/common/siteUserRegister.class.php";


OAuth::init( QQ_AKEY , QQ_SKEY );
Tencent::$debug = QQ_DEBUG;

//打开session
session_start();
//header('Content-Type: text/html; charset=utf-8');


if ($_SESSION['t_access_token'] || ($_SESSION['t_openid'] && $_SESSION['t_openkey'])) {//用户已授权
   
    //获取用户信息
    $r = Tencent::api('user/info');
    $uid_get = json_decode($r, true);
	
	$sql = "SELECT uid FROM ".tname('qq_bind_info')." WHERE `qq_uid`='".$uid_get['data']['openid']."'";  

    $rst = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
	if($rst){
		loaducenter();
		$user = uc_get_user($rst['uid'], 1); 
		uc_user_synlogin($rst['uid']);
		setSession($user[0],$user[1]);
		$qquid=$_GET['openid'];
		$wxkey=$_GET['wxkey'];
		wxshowmessage('do_success',"wx.php?do=mine&qquid=$qquid&wxkey=$wxkey");
	}else{
		
		
		$usernameS	= randUsername();
	    $regEmailS	= randUsername()."@betit.cn";
		$regPwdS	= randUsername();

		

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
				$sql = "SELECT uid FROM ".tname('qq_bind_info')." WHERE `qq_uid`='".$uid_get['data']['openid']."'";  
				$user = $_SGLOBAL['db']->fetch_array($_SGLOBAL['db']->query($sql));
				$qquid=$uid_get['data']['openid'];
				if($user){
					wxshowmessage("已绑定", "wx.php?do=mine&qquid=$qquid");
				}
				
				$sql = "INSERT INTO " . tname('qq_bind_info') . " (uid,qq_uid) VALUES ('{$uid}','{$uid_get[data][openid]}');";
				$rst = $_SGLOBAL['db']->query($sql);
				//主表实名
				$setarr = array(
					'name' => getstr($uid_get['data']['nick'], 30, 1, 1, 1),
					'namestatus' => $_SCONFIG['namecheck']?0:1
				);
				updatetable('space', $setarr, array('uid'=>$uid ));

				$setarr = array(
					'weibo' => 2
				);
				updatetable('spacefield', $setarr, array('uid'=>$uid ));

				$filetype = '.jpg';
				
	
				if(empty($uid)) {
					$return =  -1;
				}
				
				$data = file_get_contents($uid_get['data']['head'].'/100');
				
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

				
				$msg = "已为你创建了".$_SGLOBAL['sitename']."的帐号，并与你的QQ帐号进行绑定。下次你可以继续使用QQ帐号登录使用.用户名为".$usernameS;
				wxshowmessage($msg, "space.php?do=index");
				break;
		}
	}
    // 部分接口的调用示例
    /**
     * 发表图片微博
     * pic参数后跟图片的路径,以表单形式上传的为 : $_FILES['pic']['tmp_name']
     * 服务器目录下的文件为: dirname(__FILE__).'/logo.png'
     * /
    $params = array(
        'content' => '测试发表一条图片微博'
    );
    $multi = array('pic' => dirname(__FILE__).'/logo.png');
    $r = Tencent::api('t/add_pic', $params, 'POST', $multi);
    echo $r;
    
    /**
     * 发表图片微博
     * 如果图片地址为网络上的一个可用链接
     * 则使用add_pic_url接口
     * /
    $params = array(
        'content' => '以链接形式发表一条图片微博',
        'pic_url' => 'http://mat1.gtimg.com/www/iskin960/qqcomlogo.png'
    );
    $r = Tencent::api('t/add_pic_url', $params, 'POST');
    echo $r;
    */
} else {//未授权
    // $callback = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];//回调url
    if ($_GET['code']) {//已获得code
        $code = $_GET['code'];
        $openid = $_GET['openid'];
        $openkey = $_GET['openkey'];
        //获取授权token
        $url = OAuth::getAccessToken($code, QQ_CALLBACK_URL);
        $r = Http::request($url);
        parse_str($r, $out);
        //存储授权数据
        if ($out['access_token']) {
            $_SESSION['t_access_token'] = $out['access_token'];
            $_SESSION['t_refresh_token'] = $out['refresh_token'];
            $_SESSION['t_expire_in'] = $out['expire_in'];
            $_SESSION['t_code'] = $code;
            $_SESSION['t_openid'] = $openid;
            $_SESSION['t_openkey'] = $openkey;
            
            //验证授权
            $r = OAuth::checkOAuthValid();
            if ($r) {
                header('Location: ' . QQ_CALLBACK_URL);//刷新页面
            } else {
                exit('<h3>授权失败,请重试</h3>');
            }
        } else {
            exit($r);
        }
    } else {//获取授权code
        if ($_GET['openid'] && $_GET['openkey']){//应用频道
            $_SESSION['t_openid'] = $_GET['openid'];
            $_SESSION['t_openkey'] = $_GET['openkey'];
            //验证授权
            $r = OAuth::checkOAuthValid();
            if ($r) {
                header('Location: ' . QQ_CALLBACK_URL);//刷新页面
            } else {
                exit('<h3>授权失败,请重试</h3>');
            }
        } else{
            $url = OAuth::getAuthorizeURL(QQ_CALLBACK_URL);
            header('Location: ' . $url);
        }
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


 

function base_encode($num, $alphabet) {
    $base_count = strlen($alphabet);
    $encoded = '';

    while ($num >= $base_count) {

        $div = $num/$base_count;
        $mod = ($num-($base_count*intval($div)));
        $encoded = $alphabet[$mod] . $encoded;
        $num = intval($div);
    }

    if ($num) $encoded = $alphabet[$num] . $encoded;
        return $encoded;
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
<?php
session_start();

include_once( 'weibo/config.php' );
require_once '../common.php';
include_once( CONNECT_ROOT.'/saetv2.ex.class.php' );
include_once( CONNECT_ROOT.'/Tencent.php' );

$wxkey=$_GET['wxkey'];

if ($site=='weibo'){

	$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

	$code_url = $o->getAuthorizeURL( WB_CALLBACK_URL.'&wxkey='.$wxkey );

	header("HTTP/1.1 301 Moved Permanently");
	header('Location:'.$code_url.'&wxkey='.$wxkey);

}elseif($site=='qq'){

	//$callback = 'http://www.betit.cn/connect.php?site=qq';//回调url

	OAuth::init(QQ_AKEY, QQ_SKEY);
	Tencent::$debug = QQ_DEBUG;

	header('Content-Type: text/html; charset=utf-8');

	if ($_GET['code']) {//已获得code
        $code = $_GET['code'];
        $openid = $_GET['openid'];
        $openkey = $_GET['openkey'];
        //»ñÈ¡ÊÚÈ¨token
        $code_url = OAuth::getAccessToken($code, QQ_CALLBACK_URL.'&wxkey='.$wxkey );
        $r = Http::request($code_url);
        parse_str($r, $out);
        //´æ´¢ÊÚÈ¨Êý¾Ý
        if ($out['access_token']) {
            $_SESSION['t_access_token'] = $out['access_token'];
            $_SESSION['t_refresh_token'] = $out['refresh_token'];
            $_SESSION['t_expire_in'] = $out['expire_in'];
            $_SESSION['t_code'] = $code;
            $_SESSION['t_openid'] = $openid;
            $_SESSION['t_openkey'] = $openkey;
            
            //ÑéÖ¤ÊÚÈ¨
            $r = OAuth::checkOAuthValid();
            if ($r) {
                header('Location: ' . $code_url);//刷新页面
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
            //ÑéÖ¤ÊÚÈ¨
            $r = OAuth::checkOAuthValid();
            if ($r) {
                header('Location: ' . QQ_CALLBACK_URL);//刷新页面
            } else {
                exit('<h3>授权失败,请重试</h3>');
            }
        } else{

           $code_url = OAuth::getAuthorizeURL( QQ_CALLBACK_URL.'&wxkey='.$wxkey);	//!!! 必须与 OAuth::getAccessToken($code, QQ_CALLBACK_URL); 的地址一致
            header('Location: ' . $code_url.'&wxkey='.$wxkey);
        }
    }

	
}




?>


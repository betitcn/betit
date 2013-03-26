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

	//$callback = 'http://www.betit.cn/connect.php?site=qq';//»Øµ÷url

	OAuth::init(QQ_AKEY, QQ_SKEY);
	Tencent::$debug = QQ_DEBUG;

	header('Content-Type: text/html; charset=utf-8');

	if ($_GET['code']) {//ÒÑ»ñµÃcode
        $code = $_GET['code'];
        $openid = $_GET['openid'];
        $openkey = $_GET['openkey'];
        //»ñÈ¡ÊÚÈ¨token
        $code_url = OAuth::getAccessToken($code, QQ_CALLBACK_URL);
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
                header('Location: ' . $code_url.'&wxkey='.$wxkey);//Ë¢ÐÂÒ³Ãæ
            } else {
                exit('<h3>ÊÚÈ¨Ê§°Ü,ÇëÖØÊÔ</h3>');
            }
        } else {
            exit($r);
        }
    } else {//»ñÈ¡ÊÚÈ¨code

		

        if ($_GET['openid'] && $_GET['openkey']){//Ó¦ÓÃÆµµÀ
            $_SESSION['t_openid'] = $_GET['openid'];
            $_SESSION['t_openkey'] = $_GET['openkey'];
            //ÑéÖ¤ÊÚÈ¨
            $r = OAuth::checkOAuthValid();
            if ($r) {
                header('Location: ' . QQ_CALLBACK_URL.'&wxkey='.$wxkey);//Ë¢ÐÂÒ³Ãæ
            } else {
                exit('<h3>ÊÚÈ¨Ê§°Ü,ÇëÖØÊÔ</h3>');
            }
        } else{

           $code_url = OAuth::getAuthorizeURL( QQ_CALLBACK_URL .'&wxkey='.$wxkey);	//!!! ±ØÐëÓë OAuth::getAccessToken($code, QQ_CALLBACK_URL); µÄµØÖ·Ò»ÖÂ
            header('Location: ' . $code_url.'&wxkey='.$wxkey);
        }
    }

	
}




?>


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

	//$callback = 'http://www.betit.cn/connect.php?site=qq';//?ص?url

	OAuth::init(QQ_AKEY, QQ_SKEY);
	Tencent::$debug = QQ_DEBUG;

	header('Content-Type: text/html; charset=utf-8');

	if ($_GET['code']) {//?ѻ???code
        $code = $_GET['code'];
        $openid = $_GET['openid'];
        $openkey = $_GET['openkey'];
        //??ȡ??Ȩtoken
        $code_url = OAuth::getAccessToken($code, QQ_CALLBACK_URL.'&wxkey='.$wxkey);
        $r = Http::request($code_url.'&wxkey='.$wxkey);
        parse_str($r, $out);
        //?洢??Ȩ????
        if ($out['access_token']) {
            $_SESSION['t_access_token'] = $out['access_token'];
            $_SESSION['t_refresh_token'] = $out['refresh_token'];
            $_SESSION['t_expire_in'] = $out['expire_in'];
            $_SESSION['t_code'] = $code;
            $_SESSION['t_openid'] = $openid;
            $_SESSION['t_openkey'] = $openkey;
            
            //??֤??Ȩ
            $r = OAuth::checkOAuthValid();
            if ($r) {
                header('Location: ' . $code_url.'&wxkey='.$wxkey);//ˢ??ҳ??
            } else {
                exit('<h3>??Ȩʧ??,??????</h3>');
            }
        } else {
            exit($r);
        }
    } else {//??ȡ??Ȩcode

		

        if ($_GET['openid'] && $_GET['openkey']){//Ӧ??Ƶ??
            $_SESSION['t_openid'] = $_GET['openid'];
            $_SESSION['t_openkey'] = $_GET['openkey'];
            //??֤??Ȩ
            $r = OAuth::checkOAuthValid();
            if ($r) {
                header('Location: ' . QQ_CALLBACK_URL.'&wxkey='.$wxkey);//ˢ??ҳ??
            } else {
                exit('<h3>??Ȩʧ??,??????</h3>');
            }
        } else{

           $code_url = OAuth::getAuthorizeURL( QQ_CALLBACK_URL .'&wxkey='.$wxkey);	//!!! ?????? OAuth::getAccessToken($code, QQ_CALLBACK_URL); ?ĵ?ַһ??
            header('Location: ' . $code_url.'&wxkey='.$wxkey);
        }
    }

	
}




?>


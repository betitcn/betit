<?php
//对话框
function wxshowmessage($msgkey, $url_forward='', $second=1, $values=array()) {
	global $_SGLOBAL, $_SC, $_SCONFIG, $_TPL, $space, $_SN;

	obclean();

	//去掉广告
	$_SGLOBAL['ad'] = array();
	
	//语言
	include_once(S_ROOT.'./language/lang_showmessage.php');
	if(isset($_SGLOBAL['msglang'][$msgkey])) {
		$message = lang_replace($_SGLOBAL['msglang'][$msgkey], $values);
	} else {
		$message = $msgkey;
	}
	//手机
	if($_SGLOBAL['mobile']) {
		include template('./wx/template/showmessage');
		exit();
	}
	//显示
	if(empty($_SGLOBAL['inajax']) && $url_forward && empty($second)) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: $url_forward");
	} else {
		if($_SGLOBAL['inajax']) {
			if($url_forward) {
				$message = "<a href=\"$url_forward\">$message</a><ajaxok>";
			}
			//$message = "<h1>".$_SGLOBAL['msglang']['box_title']."</h1><a href=\"javascript:;\" onclick=\"hideMenu();\" class=\"float_del\">X</a><div class=\"popupmenu_inner\">$message</div>";
			echo $message;
			ob_out();
		} else {
			if($url_forward) {
				$message = "<a href=\"$url_forward\">$message</a><script>setTimeout(\"window.location.href ='$url_forward';\", ".($second*1000).");</script>";
			}
			include template('./wx/template/showmessage');
		}
	}
	exit();
}
?>
<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: avatar.php 13217 2009-08-21 06:57:53Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//判断用户是否设置了头像
include_once(S_ROOT.'./source/function_cp.php');
$avatar_exists = trim(ckavatar($space['uid']));
if(strlen($avatar_exists) < 1) {
	capi_showmessage_by_data('这个功能要求您的UCenter的Server端的 avatar.php 程序需要进行升级。<br>如果您是本站管理员，请通过下面的地址下载 avatar.php 文件的压缩包，并覆盖您的UCenter根目录中的同名文件即可。<br><a href="http://u.discuz.net/download/avatar.zip">http://u.discuz.net/download/avatar.zip</a>');
}
	
if($avatar_exists) {

	capi_showmessage_by_data('do_success', 0, array("done"=>1));

} else {

	capi_showmessage_by_data('do_success', 0, array("done"=>0));

}

?>

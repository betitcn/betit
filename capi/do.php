<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: do.php 12354 2009-06-11 08:14:06Z liguode $
*/

include_once('./common.php');

//获取方法
$ac = empty( $_REQUEST['ac'])?'': $_REQUEST['ac'];

//自定义登录


//允许的方法
$acs = array('login', 'register', 'lostpasswd', 'swfupload', 'inputpwd',
	'ajax', 'seccode', 'sendmail', 'stat', 'emailcheck');
if(empty($ac) || !in_array($ac, $acs)) {
	capi_showmessage_by_data('enter_the_space',  0);
}

//链接
$theurl = 'do.php?ac='.$ac;

include_once(S_ROOT.'./capi/source/do_'.$ac.'.php');

?>

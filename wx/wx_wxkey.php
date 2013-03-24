<?php
function setSession($uid, $username) {
	$setarr = array(
		'uid' => $uid,
		'username' => $username,
		'password' => md5($uid."|".time())//本地密码随机生成
	);
	//在线session
	include_once(S_ROOT.'./source/function_space.php');
	$setarr = insertsession($setarr);
	$auth =  authcode("$setarr[password]\t$uid", 'ENCODE');
	//设置cookie
	ssetcookie('auth', $auth, 31536000);//原数据2592000
	ssetcookie('wxkey', $username, 31536000);
	ssetcookie('_refer', '');

	return $auth;
}
?>
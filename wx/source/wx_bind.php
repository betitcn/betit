<?php

$result = 0;

/*$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('space')." WHERE wxkey='$_GET[wxkey]'");

if ($_SGLOBAL['db']->fetch_array($query)){
	$result = 1;
}else{
	if (isset($_COOKIE['uchome_m_auth'])) 
	{
		$result = 1;
	}else{
		$result = 0;
	}
}*/

if ($_GET["op"]=="add"){

	include_once S_ROOT.'./uc_client/client.php';

	$username = empty($_POST['username']) ? '' : trim($_POST['username']);
	$password = empty($_POST['password']) ? '' : trim($_POST['password']);

	$os = mobile_user_agent_switch();

	if(empty($username) || empty($password)) {
		// showmessage('users_were_not_empty_please_re_login',  'wx.php?do=bind&wxkey='.$_POST['wxkey']);
		$result = -1;
		include_once template("./wx/template/bind");
		exit;
	}

	// 登陆验证
	if(!$passport = getpassport($username, $password)) {
		
		// showmessage('login_failure_please_re_login',  'wx.php?do=bind&wxkey='.$_POST['wxkey']);
		$result = -1;
		include_once template("./wx/template/bind");
		exit;
	}
	
	// unbind
	updatetable('space', array('wxkey'=>''), array('wxkey'=>$_POST['wxkey']));

	

	

	// 同步登陆
	$jsonurl = "http://www.familyday.com.cn/dapi/do.php?ac=login&username=".$username."&password=".$password;
	$json = file_get_contents($jsonurl,0,null,null);
	$json_output = json_decode($json);

	$device = json_encode(array("os"=>$os, "auth"=>$json_output->data->m_auth));
	// bind
	updatetable('space', array('wxkey'=>$_POST['wxkey'], 'device'=>$device), array('uid'=>$passport['uid']));
	
	echo "<script>localStorage.removeItem('auth');localStorage.setItem('auth','".$json_output->data->m_auth."');</script>";
	

	

	// showmessage('do_success', 'wx.php?do=feed&wxkey='.$_POST['wxkey'], 0);

	$result = 1;

}

include_once template("./wx/template/bind");

?>
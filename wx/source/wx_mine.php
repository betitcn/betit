﻿<?php


 $m_auth = getAuth();


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



	include_once S_ROOT.'./uc_client/client.php';
	

	

	// 同步登陆
	$sinauid=$_GET['sinauid'];
	$qquid=$_GET['qquid'];
	if($sinauid){
	$jsonurl = "http://www.betit.cn/capi/connect.php?site=weibo&sinauid=$sinauid";
	$json = file_get_contents($jsonurl,0,null,null);
	$os = mobile_user_agent_switch();
	$json_output = json_decode($json);
	$loginusername=$json_output->data->space->name;
	$credit=$json_output->data->space->credit;
	$experience=$json_output->data->space->experience;
	$avatar=$json_output->data->space->avatar;
	$note=$json_output->data->space->note;
	$friendnum=$json_output->data->space->friendnum;
	$quiznum=$json_output->data->space->quiznum;
	$grouptitle=$json_output->data->space->grouptitle;
	$uid=$json_output->data->space->uid;
	//$avatar = capi_avatar($value["uid"]);
	$device = json_encode(array("os"=>$os, "auth"=>$json_output->data->m_auth));
	// bind
	$wxkey=$_GET["wxkey"];
	updatetable('space', array('wxkey'=>'0'.$wxkey, 'device'=>$device), array('uid'=>$json_output->data->space->uid));
	
	echo "<script>localStorage.removeItem('auth');localStorage.setItem('auth','".$json_output->data->m_auth."');</script>";
	}
	if($qquid){
		$jsonurl = "http://www.betit.cn/capi/connect.php?site=qq&qqopenid=$qquid";
	$json = file_get_contents($jsonurl,0,null,null);
	$os = mobile_user_agent_switch();
	$json_output = json_decode($json);
	$loginusername=$json_output->data->space->name;
	$credit=$json_output->data->space->credit;
	$experience=$json_output->data->space->experience;
	$avatar=$json_output->data->space->avatar;
	$note=$json_output->data->space->note;
	$friendnum=$json_output->data->space->friendnum;
	$quiznum=$json_output->data->space->quiznum;
	$grouptitle=$json_output->data->space->grouptitle;
	$uid=$json_output->data->space->uid;
	//$avatar = capi_avatar($value["uid"]);
	$device = json_encode(array("os"=>$os, "auth"=>$json_output->data->m_auth));
	// bind
	$wxkey=$_GET["wxkey"];
	updatetable('space', array('wxkey'=>'1'.$wxkey, 'device'=>$device), array('uid'=>$json_output->data->space->uid));
	
	echo "<script>localStorage.removeItem('auth');localStorage.setItem('auth','".$json_output->data->m_auth."');</script>";

	}
	

	// showmessage('do_success', 'wx.php?do=feed&wxkey='.$_POST['wxkey'], 0);

	$result = 1;





	

	include_once template("./wx/template/mine");
?>
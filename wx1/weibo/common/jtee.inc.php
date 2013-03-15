<?php
/*
 * @version $Id: $
 */
 
/**
 * 加载UCenter
 * @return obj
 */
function loaducenter() {
	require_once S_ROOT.'/uc_client/client.php';
	require_once S_ROOT.'/uc_client/model/user.php';
} 
/**
 * 用户登录
 * @param int $uid
 * @param string $username
 */
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
	ssetcookie('loginuser', $username, 31536000);
	ssetcookie('_refer', '');

	return $auth;
}

function oScript($func,$ret){
	echo '<script>';
	echo "parent.".$func."(".json_encode($ret).");";
	echo '</script>';
}

/**
 * 获取Http通讯类
 * @param boolen $singleton 是否获取单例，默认为是
 * @return curlHttp|fsockopenHttp
 */
function getHttp(){
	if (! defined ('HTTP_ADAPTER')) {
		if(function_exists ('curl_init')) {
			define('HTTP_ADAPTER', 'curl');
		}else{
			define('HTTP_ADAPTER', 'fsockopen');
		}
	}
	
	require_once CONNECT_ROOT.'/common/http/'. HTTP_ADAPTER. 'Http.class.php';
	eval('$http = new '.HTTP_ADAPTER.'Http();');
	return $http;
}


/**
 * 记录log日志
 * @param string $msg 内容
 * @param string $logName 存放log的完整文件路径。若不指定，则存放于常量P_DATA的指定目录
 * @param bool $halt 无法记录时候是否终止整个脚本运行？默认为false
 * @return int
 */
function writeLog($msg, $logName='log', $halt = false){
	$logFile = strpos($logName,'/') === false ? P_DATA.'/'.$logName.'.php' : $logName;
	$msgPre = '';
	if (!file_exists($logFile)){
		$msgPre = "\r\n<?php  die('access deny!'); ?> \r\n\r\n";
	}
	
	$msg = $msgPre. sprintf("%s\t%s\r\n",date("Y-m-d H:i:s"),$msg);
	$mode = 'ab';
	
	$fp = @fopen($logFile, $mode);
	if( $fp ){
		@flock($fp, LOCK_EX);
		$len = @fwrite($fp, $msg);
		@flock($fp, LOCK_UN);
		@fclose($fp);
		return $len;
	}else{
		if( true == $halt ){
			exit("Can not open file $logFile !");
		}else{
			return 0;
		}
	}
}

/**
 * 字符集转换。mb_convert_encoding和iconv函数必须有一
 * @uses mb_convert_encoding|iconv
 * @since 2010-8-27
 * @param string $source 需要转换的字符集
 * @param string $in 转换前的编码
 * @param string $out 转换后的编码
 */
function convertEncoding($source, $in, $out){
	$in	= strtoupper($in);
	$out = strtoupper($out);
	if ($in == "UTF8"){
		$in = "UTF-8";
	}
	if ($out == "UTF8"){
		$out = "UTF-8";
	}
	if( $in==$out ){
		return $source;
	}

	if(function_exists('mb_convert_encoding')) {
		return mb_convert_encoding($source, $out, $in );
	}elseif (function_exists('iconv'))  {
		return iconv($in,$out."//IGNORE", $source);
	}
	return $source;
}
?>
<?php
require_once 'wx_common.php';	
require_once "./weibo/common/jtee.inc.php";
 $m_auth = getAuth();

$wxkey=$_GET['wxkey'];
setSession($wxkey,$wxkey);
wxshowmessage($_COOKIE["loginuser"]);	

	include_once template("./wx/template/login");
?>
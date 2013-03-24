<?php
require_once 'wx_common.php';	
 $m_auth = getAuth();

$wxkey=$_GET['wxkey'];
wxshowmessage($wxkey);
	

	include_once template("./wx/template/login");
?>
<?php
require_once 'wx_common.php';	
require_once CONNECT_ROOT."/common/jtee.inc.php";
 $m_auth = getAuth();

$wxkey=$_GET['wxkey'];
setSession($wxkey,$wxkey);
	

	include_once template("./wx/template/login");
?>
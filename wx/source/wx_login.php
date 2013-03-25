<?php
require_once 'wx_common.php';
	
require_once '../common.php';


 $m_auth = getAuth();

$wxkey=$_GET['wxkey'];
setcookie('wxkey', "$wxkey", 31536000);

	include_once template("./wx/template/login");
?>
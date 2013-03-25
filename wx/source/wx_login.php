<?php
require_once '../common.php';
require_once 'wx_common.php';



 $m_auth = getAuth();
session_start();  
$wxkey=$_GET['wxkey'];
   $_SESSION['wxkey'] = $wxkey;

	include_once template("./wx/template/login");
?>
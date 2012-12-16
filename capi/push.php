<?php

include_once('./common.php');

error_reporting(E_ALL | E_STRICT);

$_REQUEST['debug'] = (empty($_REQUEST['debug']))?0:intval($_REQUEST['debug']);

if ($_REQUEST['token'])
	apple_push($_REQUEST['token'], $_REQUEST['msg'], $_REQUEST['userinfo'], $_REQUEST['debug']);
else
	apple_push("064223e785496c69976de513c64a8a14d8668f089588237806111c5b5b238334", $_REQUEST['msg'], $_REQUEST['userinfo'], $_REQUEST['debug']);

?>
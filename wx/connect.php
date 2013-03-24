<?php

$site = empty($_GET['site'])?"weibo":$_GET['site'];
$ac = empty($_GET['ac'])?'':$_GET['ac'];

if ($site == "weibo")
{
	if ($ac=="callback"){
		$wxkey=$_GET['wxkey'];
		require 'weibo/callback.php';
	}else{
		require 'weibo/index.php';
	}

}elseif($site == "qq"){

	if ($ac=="callback"){
		require 'weibo/qqcallback.php';
	}else{
		require 'weibo/index.php';
	}
}



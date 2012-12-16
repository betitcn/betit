<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: blog.php 11056 2009-02-09 01:59:47Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$quizcount = getcount('quiz', array('uid'=>$space['uid']));
if($quizcount) {

	capi_showmessage_by_data('do_success', 0, array("done"=>1));

} else {

	capi_showmessage_by_data('do_success', 0, array("done"=>0));
}

?>

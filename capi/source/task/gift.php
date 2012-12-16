<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: gift.php 12841 2009-07-23 02:01:57Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

if($_SGLOBAL['supe_uid']) {
	
	capi_showmessage_by_data('do_success', 0, array("done"=>1));
	
} else {
	
	capi_showmessage_by_data('do_success', 0, array("done"=>0));
}

?>

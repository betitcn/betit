<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: email.php 12304 2009-06-03 07:29:34Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

if($space['emailcheck']) {

	capi_showmessage_by_data('do_success', 0, array("done"=>1));

} else {

	capi_showmessage_by_data('do_success', 0, array("done"=>0));

}

?>

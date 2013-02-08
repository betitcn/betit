<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space_pm.php 12880 2009-07-24 07:20:24Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}


include_once(S_ROOT.'./uc_client/client.php');

$list = array();

$pmid = empty( $_REQUEST['pmid'])?0:floatval( $_REQUEST['pmid']);
$touid = empty( $_REQUEST['touid'])?0:intval( $_REQUEST['touid']);
$daterange = empty( $_REQUEST['daterange'])?1:intval( $_REQUEST['daterange']);
$dateline = empty($_REQUEST['dateline'])?0:intval($_REQUEST['dateline']);
$queryop = $_REQUEST['queryop'];

if( $_REQUEST['subop'] == 'view') {

	$perpage = $_REQUEST['perpage']?$_REQUEST['perpage']:10;
	$perpage = mob_perpage($perpage);
	
	$page = empty( $_REQUEST['page'])?0:intval( $_REQUEST['page']);
	if($page<1) $page = 1;

	if($touid) {
		$list = uc_pm_view2($_SGLOBAL['supe_uid'], 0, $touid, $daterange, $page, $perpage);
		$pmid = empty($list)?0:$list[0]['pmid'];
	} elseif($pmid) {
		$list = uc_pm_view($_SGLOBAL['supe_uid'], $pmid);
	}

	$actives = array($daterange=>' class="active"');

} elseif( $_REQUEST['subop'] == 'ignore') {
	
	$ignorelist = uc_pm_blackls_get($_SGLOBAL['supe_uid']);
	$actives = array('ignore'=>' class="active"');
	
} else {
	
	$filter = in_array( $_REQUEST['filter'], array('newpm', 'privatepm', 'systempm', 'announcepm'))? $_REQUEST['filter']:($space['newpm']?'newpm':'privatepm');
	
	//分页
	$perpage = $_REQUEST['perpage']?$_REQUEST['perpage']:10;
	$perpage = mob_perpage($perpage);
	
	$page = empty( $_REQUEST['page'])?0:intval( $_REQUEST['page']);
	if($page<1) $page = 1;
	
	$result = uc_pm_list($_SGLOBAL['supe_uid'], $page, $perpage, 'inbox', $filter, 100, $dateline, $queryop);
	
	$count = $result['count'];
	$list = $result['data'];

	foreach ($list as $key => $value) {
		$list2 = uc_pm_view2($_SGLOBAL['supe_uid'], 0, $value["touid"], 1, 0, 1);
		if (count($list2)){
			$list[$key]["subject"] = $list2[0]["subject"];
			$list[$key]["message"] = $list2[0]["message"];
			$list[$key]["dateline"] =  $list2[0]["dateline"];
		}
	}

	$multi = multi($count, $perpage, $page, "space.php?do=pm&filter=$filter");
	
	if($_SGLOBAL['member']['newpm']) {
		//取消新短消息提示
		updatetable('space', array('newpm'=>0), array('uid'=>$_SGLOBAL['supe_uid']));
		//UCenter
		uc_pm_ignore($_SGLOBAL['supe_uid']);
	}



	$actives = array($filter=>' class="active"');
}

//实名
if($list) {
	$today = $_SGLOBAL['timestamp'] - ($_SGLOBAL['timestamp'] + $_SCONFIG['timeoffset'] * 3600) % 86400;
	foreach ($list as $key => $value) {
		
		realname_set($value['msgfromid'], $value['msgfrom']);
		$value['msgfrom'] = capi_realname($value['msgfromid']);
		$value['daterange'] = 5;
		if($value['dateline'] >= $today) {
			$value['daterange'] = 1;
		} elseif($value['dateline'] >= $today - 86400) {
			$value['daterange'] = 2;
		} elseif($value['dateline'] >= $today - 172800) {
			$value['daterange'] = 3;
		}
		$value["msgfromavatar"]  = capi_avatar($value["msgfromid"]);
		$value["msgtoavatar"]  = capi_avatar($value["msgtoid"]);
		$value["msgfromisonline"]  = capi_isonline($value["msgfromid"]);
		$value["msgtoisonline"]  = capi_isonline($value["msgtoid"]);
		
		$list[$key] = $value;
	}
	realname_get();
}

$plist = array();
foreach ($list as $key => $value) {
	$plist[] = $value;
}


capi_showmessage_by_data("rest_success", 0, array('pms'=>$plist, 'count'=>count($plist), 'newpm'=> count($plist)));


?>

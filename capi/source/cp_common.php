<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_common.php 12872 2009-07-24 01:55:54Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$op = empty($_REQUEST['op'])?'':trim($_REQUEST['op']);

if($op == 'logout') {
	
	//if($_REQUEST['uhash'] == $_SGLOBAL['uhash']) {
		//删除session
		if($_SGLOBAL['supe_uid']) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('session')." WHERE uid='$_SGLOBAL[supe_uid]'");
			$_SGLOBAL['db']->query("DELETE FROM ".tname('adminsession')." WHERE uid='$_SGLOBAL[supe_uid]'");//管理平台
		}
	
		if($_SCONFIG['uc_status']) {
			include_once S_ROOT.'./uc_client/client.php';
			$ucsynlogout = uc_user_synlogout();
		} else {
			$ucsynlogout = '';
		}
	
		clearcookie();
		ssetcookie('_refer', '');
	//}
	capi_showmessage_by_data('security_exit', 0);

} elseif($op == 'seccode') {

	if(ckseccode(trim($_REQUEST['code']))) {
		capi_showmessage_by_data('succeed');
	} else {
		capi_showmessage_by_data('incorrect_code');
	}

} elseif($op == 'report') {

	$_REQUEST['idtype'] = trim($_REQUEST['idtype']);
	$_REQUEST['id'] = intval($_REQUEST['id']);
	$uidarr = $report = array();
	
	if(!in_array($_REQUEST['idtype'], array('picid', 'blogid', 'albumid', 'tagid', 'tid', 'sid', 'uid', 'pid', 'eventid', 'comment', 'post', 'quizid')) || empty($_REQUEST['id'])) {
		capi_showmessage_by_data('report_error');
	}
	//获取举报记录
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('report')." WHERE id='$_REQUEST[id]' AND idtype='$_REQUEST[idtype]'");
	if($report = $_SGLOBAL['db']->fetch_array($query)) {
		$uidarr = unserialize($report['uids']);
		if($uidarr[$space['uid']]) {
			capi_showmessage_by_data('repeat_report');
		}
	}

	if(submitcheck('reportsubmit')) {
		$reason = getstr($_POST['reason'], 150, 1, 1);

		$reason = "<li><strong><a href=\"space.php?uid=$space[uid]\" target=\"_blank\">$_SGLOBAL[supe_username]</a>:</strong> ".$reason.' ('.sgmdate('m-d H:i').')</li>';

		if($report) {
			$uidarr[$space['uid']] = $space['username'];
			$uids = addslashes(serialize($uidarr));
			$reason = addslashes($report['reason']).$reason;
			$_SGLOBAL['db']->query("UPDATE ".tname('report')." SET num=num+1, reason='$reason', dateline='$_SGLOBAL[timestamp]', uids='$uids' WHERE rid='$report[rid]'");
		} else {
			$uidarr[$space['uid']] = $space['username'];

			$setarr = array(
				'id' => $_REQUEST['id'],
				'idtype' => $_REQUEST['idtype'],
				'num' => 1,
				'new' => 1,
				'reason' => $reason,
				'uids' => addslashes(serialize($uidarr)),
				'dateline' => $_SGLOBAL['timestamp']
			);
			inserttable('report', $setarr);
		}
		capi_showmessage_by_data('report_success');
	}

	//判断是否是被忽略的举报
	if(isset($report['num']) && $report['num'] < 1) {
		capi_showmessage_by_data('the_normal_information');
	}

	$reason = explode("\r\n", trim(preg_replace("/(\s*(\r\n|\n\r|\n|\r)\s*)/", "\r\n", data_get('reason'))));
	if(is_array($reason) && count($reason) == 1 && empty($reason[0])) {
		$reason = array();
	}

} elseif($op == 'ignore') {

	$type = empty($_REQUEST['type'])?'':preg_replace("/[^0-9a-zA-Z\_\-\.]/", '', $_REQUEST['type']);
	if(submitcheck('ignoresubmit')) {
		$authorid = empty($_POST['authorid']) ? 0 : intval($_POST['authorid']);
		if($type) {
			$type_uid = $type.'|'.$authorid;
			if(empty($space['privacy']['filter_note']) || !is_array($space['privacy']['filter_note'])) {
				$space['privacy']['filter_note'] = array();
			}
			$space['privacy']['filter_note'][$type_uid] = $type_uid;
			privacy_update();
		}
		capi_showmessage_by_data('do_success', $_POST['refer']);
	}
	$formid = random(8);

} elseif($op == 'getuserapp') {
	//处理
	if(empty($_REQUEST['subop'])) {
		//展开
		$my_userapp = array();
		foreach ($_SGLOBAL['my_userapp'] as $value) {
			if($value['allowsidenav'] && !isset($_SGLOBAL['userapp'][$value['appid']])) {
				$my_userapp[] = $value;
			}
		}
	} else {
		$my_userapp = $_SGLOBAL['my_menu'];
	}
} elseif($op == 'closefeedbox') {

	ssetcookie('closefeedbox', 1);

} elseif($op == 'changetpl') {

	$dir = empty($_REQUEST['name'])?'':str_replace('.','', trim($_REQUEST['name']));
	if($dir && file_exists(S_ROOT.'./template/'.$dir.'/style.css')) {
		ssetcookie('mytemplate', $dir, 3600*24*365);//长期有效
	}
	capi_showmessage_by_data('do_success', 'space.php?do=feed', 0);
}

include template('cp_common');

?>

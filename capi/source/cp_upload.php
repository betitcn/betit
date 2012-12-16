<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_upload.php 13245 2009-08-25 02:01:40Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$albumid = empty($_REQUEST['albumid'])?0:intval($_REQUEST['albumid']);
$eventid = empty($_REQUEST['eventid'])?0:intval($_REQUEST['eventid']);

if($eventid){
	$query = $_SGLOBAL['db']->query("SELECT e.*, ef.* FROM ".tname("event")." e LEFT JOIN ".tname("eventfield")." ef ON e.eventid=ef.eventid WHERE e.eventid='$_REQUEST[eventid]'");
	$event = $_SGLOBAL['db']->fetch_array($query);
	if(empty($event)){
		capi_showmessage_by_data('event_does_not_exist');
	}
	if($event['grade'] == -2) {
		capi_showmessage_by_data('event_is_closed');
	} elseif ($event['grade'] < 1) {
		capi_showmessage_by_data('event_under_verify');
	}
	$query = $_SGLOBAL['db']->query("SELECT * FROM " . tname("userevent") . " WHERE uid = '$_SGLOBAL[supe_uid]' AND eventid = '$eventid'");
	$userevent = $_SGLOBAL['db']->fetch_array($query);
	if($event['allowpic'] == 0 && $userevent['status'] < 3){
		capi_showmessage_by_data('event_only_allows_admins_to_upload');
	}
	if($event['allowpic'] && $userevent['status'] < 2) {
	    capi_showmessage_by_data("event_only_allows_members_to_upload");
    }
}

if(capi_submitcheck('albumsubmit')) {
	//创建相册
	if($_REQUEST['albumop'] == 'creatalbum') {
		$_REQUEST['albumname'] = empty($_REQUEST['albumname'])?'':getstr($_REQUEST['albumname'], 50, 1, 1);
		if(empty($_REQUEST['albumname'])) $_REQUEST['albumname'] = gmdate('Ymd');

		$_REQUEST['friend'] = intval($_REQUEST['friend']);

		//隐私
		$_REQUEST['target_ids'] = '';
		if($_REQUEST['friend'] == 2) {
			//特定好友
			$uids = array();
			$names = empty($_REQUEST['target_names'])?array():explode(' ', str_replace(array(cplang('tab_space'), "\r\n", "\n", "\r"), ' ', $_REQUEST['target_names']));
			if($names) {
				$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." WHERE username IN (".simplode($names).")");
				while ($value = $_SGLOBAL['db']->fetch_array($query)) {
					$uids[] = $value['uid'];
				}
			}
			if(empty($uids)) {
				$_REQUEST['friend'] = 3;//仅自己可见
			} else {
				$_REQUEST['target_ids'] = implode(',', $uids);
			}
		} elseif($_REQUEST['friend'] == 4) {
			//加密
			$_REQUEST['password'] = trim($_REQUEST['password']);
			if($_REQUEST['password'] == '') $_REQUEST['friend'] = 0;//公开
		}
		if($_REQUEST['friend'] !== 2) {
			$_REQUEST['target_ids'] = '';
		}
		if($_REQUEST['friend'] !== 4) {
			$_REQUEST['password'] = '';
		}

		//创建相册
		$setarr = array();
		$setarr['albumname'] = $_REQUEST['albumname'];
		$setarr['uid'] = $_SGLOBAL['supe_uid'];
		$setarr['username'] = $_SGLOBAL['supe_username'];
		$setarr['dateline'] = $setarr['updatetime'] = $_SGLOBAL['timestamp'];
		$setarr['friend'] = $_REQUEST['friend'];
		$setarr['password'] = $_REQUEST['password'];
		$setarr['target_ids'] = $_REQUEST['target_ids'];

		$albumid = inserttable('album', $setarr, 1);
		
		//更新用户统计
		if(empty($space['albumnum'])) {
			$space['albumnum'] = getcount('album', array('uid'=>$space['uid']));
			$albumnumsql = "albumnum=".$space['albumnum'];
		} else {
			$albumnumsql = 'albumnum=albumnum+1';
		}
		$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET {$albumnumsql}, updatetime='$_SGLOBAL[timestamp]' WHERE uid='$_SGLOBAL[supe_uid]'");
	} else {
		$albumid = intval($_REQUEST['albumid']);
	}
	
	$_REQUEST['topicid'] = topic_check($_REQUEST['topicid'], 'pic');
	
	if($_SGLOBAL['mobile']) {
		capi_showmessage_by_data('do_success', 'cp.php?ac=upload');
	} else {
		echo "<script>";
		echo "parent.no_insert = 1;";
		echo "parent.albumid = $albumid;";
		echo "parent.topicid = $_REQUEST[topicid];";
		echo "parent.start_upload();";
		echo "</script>";
	}
	exit();

} elseif(capi_submitcheck('uploadsubmit')) {

	//上传图片
	$albumid = $picid = 0;

	if(!checkperm('allowupload')) {
		if($_SGLOBAL['mobile']) {
			capi_showmessage_by_data(cplang('not_allow_upload'));
		} else {
			echo "<script>";
			echo "alert(\"".cplang('not_allow_upload')."\")";
			echo "</script>";
			exit();
		}
	}

	//上传
	$_REQUEST['topicid'] = topic_check($_REQUEST['topicid'], 'pic');
	
	$uploadfiles = pic_save($_FILES['attach'], $_REQUEST['albumid'], $_REQUEST['pic_title'], $_REQUEST['topicid']);
	if($uploadfiles && is_array($uploadfiles)) {
		$albumid = $uploadfiles['albumid'];
		$picid = $uploadfiles['picid'];
		$uploadStat = 1;
		if($eventid){
            $arr = array("eventid"=>$eventid, "picid" =>$picid, "uid"=>$_SGLOBAL['supe_uid'], "username"=>$_SGLOBAL['supe_username'], "dateline"=>$_SGLOBAL['timestamp']);
            inserttable("eventpic", $arr);
		}
	} else {
		$uploadStat = $uploadfiles;
	}

	if($_SGLOBAL['mobile']) {
		if($picid) {
			capi_showmessage_by_data('do_success', "space.php?do=album&picid=$picid");
		} else {
			capi_showmessage_by_data($uploadStat, 'cp.php?ac=upload');
		}
	} else {
		echo "<script>";
		echo "parent.albumid = $albumid;";
		echo "parent.topicid = $_REQUEST[topicid];";
		echo "parent.uploadStat = '$uploadStat';";
		echo "parent.picid = $picid;";
		echo "parent.upload();";
		echo "</script>";
	}
	exit();

} elseif($_REQUEST['uploadsubmit2']) {

	
	//上传图片
	$albumid = $picid = 0;

	if(!checkperm('allowupload')) {
		if($_SGLOBAL['mobile']) {
			capi_showmessage_by_data(cplang('not_allow_upload'));
		} else {
			echo "<script>";
			echo "alert(\"".cplang('not_allow_upload')."\")";
			echo "</script>";
			exit();
		}
	}

	//上传
	$_REQUEST['topicid'] = topic_check($_REQUEST['topicid'], 'pic');
	
	$uploadfiles = pic_save($_FILES['attach'], $_REQUEST['albumid'], $_REQUEST['pic_title'], $_REQUEST['topicid']);
	
	if($uploadfiles && is_array($uploadfiles)) {
		$albumid = $uploadfiles['albumid'];
		$picid = $uploadfiles['picid'];
		$uploadStat = 1;
		if($eventid){
            $arr = array("eventid"=>$eventid, "picid" =>$picid, "uid"=>$_SGLOBAL['supe_uid'], "username"=>$_SGLOBAL['supe_username'], "dateline"=>$_SGLOBAL['timestamp']);
            inserttable("eventpic", $arr);
		}
	} else {
		$uploadStat = $uploadfiles;
	}

	if($_SGLOBAL['mobile']) {
		if($picid) {
			$uploadfiles['pic'] = pic_get($uploadfiles['filepath'], $uploadfiles['thumb'], $uploadfiles['remote']);
			capi_showmessage_by_data('do_success',0, array("pic"=>$uploadfiles));
		} else {
			capi_showmessage_by_data('rest_error', 1, array("stat"=>$uploadStat));
		}
	} else {
		$uploadfiles['pic'] = pic_get($uploadfiles['filepath'], $uploadfiles['thumb'], $uploadfiles['remote']);
		capi_showmessage_by_data('do_success',0, array("pic"=>$uploadfiles));
	}
	exit();

}elseif(capi_submitcheck('viewAlbumid')) {
	
	//上传完成发送feed
	if($eventid){//跳到活动页面
	
		$imgs = array();
		$imglinks = array();
		$dateline = $_SGLOBAL['timestamp'] - 600;
		$query = $_SGLOBAL['db']->query("SELECT pic.* FROM ".tname("eventpic")." ep LEFT JOIN ".tname("pic")." pic ON ep.picid=pic.picid WHERE ep.uid='$_SGLOBAL[supe_uid]' AND ep.eventid='$eventid' AND ep.dateline > $dateline ORDER BY ep.dateline DESC LIMIT 4");
		while($value=$_SGLOBAL['db']->fetch_array($query)){
			$imgs[] = pic_get($value['filepath'], $value['thumb'], $value['remote']);
			$imglinks[] = "space.php?do=event&id=$eventid&view=pic&picid=".$value['picid'];
		}
		$picnum = 0;
		if($imgs){
			$picnum = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname("eventpic")." WHERE eventid='$eventid'"), 0);
			feed_add('event', cplang('event_feed_share_pic_title'), '', cplang('event_feed_share_pic_info'),array("eventid"=>$eventid,"title"=>$event['title'],"picnum"=>$picnum)
			,'',$imgs,$imglinks);
		}
		$_SGLOBAL['db']->query("UPDATE ".tname("event")." SET picnum='$picnum', updatetime='$_SGLOBAL[timestamp]' WHERE eventid='$eventid'");
	    capi_showmessage_by_data('do_success', 'space.php?do=event&view=pic&id='.$eventid, 0);
	    
	} else {	
		
		//相册feed
		if(ckprivacy('upload', 1)) {
			include_once(S_ROOT.'./source/function_feed.php');
			feed_publish($_REQUEST['opalbumid'], 'albumid');
		}
		
		//单个图片feed
		if($_REQUEST['topicid']) {
			topic_join($_REQUEST['topicid'], $_SGLOBAL['supe_uid'], $_SGLOBAL['supe_username']);
			$url = "space.php?do=topic&topicid=$_REQUEST[topicid]&view=pic";
		} else {
			$url = "space.php?uid=$_SGLOBAL[supe_uid]&do=album&id=".(empty($_REQUEST['opalbumid'])?-1:$_REQUEST['opalbumid']);
		}
		capi_showmessage_by_data('upload_images_completed', $url, 0);
	}
} else {
	
	if(!checkperm('allowupload')) {
		ckspacelog();
		capi_showmessage_by_data('no_privilege');
	}
	//实名认证
	ckrealname('album');
	
	//视频认证
	ckvideophoto('album');
	
	//新用户见习
	cknewuser();
	
	$siteurl = getsiteurl();
	
	//获取相册
	$albums = getalbums($_SGLOBAL['supe_uid']);
	
	//激活
	$actives = ($_REQUEST['op'] == 'flash' || $_REQUEST['op'] == 'cam')?array($_REQUEST['op']=>' class="active"'):array('js'=>' class="active"');
	
	//空间大小
	$maxattachsize = checkperm('maxattachsize');
	if(!empty($maxattachsize)) {
		$maxattachsize = $maxattachsize + $space['addsize'];//额外空间
		$haveattachsize = formatsize($maxattachsize - $space['attachsize']);
	} else {
		$haveattachsize = 0;
	}
	
	//好友组
	$groups = getfriendgroup();
	
	//热闹
	$topic = array();
	$topicid = $_REQUEST['topicid'] = intval($_REQUEST['topicid']);
	if($topicid) {
		$topic = topic_get($topicid);
	}
	if($topic) $actives = array('upload' => ' class="active"');

}

//模版
include_once template("cp_upload");

?>

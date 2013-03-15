<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: do_ajax.php 12535 2009-07-06 06:22:34Z zhengqingpeng $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$op = empty($_REQUEST['op'])?'':$_REQUEST['op'];

if($op == 'comment') {

	$cid = empty($_REQUEST['cid'])?0:intval($_REQUEST['cid']);
	
	if($cid) {
		$cidsql = "cid='$cid' AND";
		$ajax_edit = 1;
	} else {
		$cidsql = '';
		$ajax_edit = 0;
	}

	//评论
	$list = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE $cidsql authorid='$_SGLOBAL[supe_uid]' ORDER BY dateline DESC LIMIT 0,1");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['authorid'], $value['author']);
		$list[] = $value;
	}
	
	realname_get();
	
}elseif($op == 'getjoinuser'){
	$page = empty($_REQUEST['page'])?1:intval($_REQUEST['page']);
	$perpage = empty($_REQUEST['perpage'])?10:intval($_REQUEST['perpage']);
	$perpage = mob_perpage($perpage);
	
	$start = ($page-1)*$perpage;

	//检查开始数
	ckstart($start, $perpage);
	@include_once(S_ROOT.'./data/data_usergroup.php');
	$oid = empty($_REQUEST['oid'])?0:intval($_REQUEST['oid']);

	$query = $_SGLOBAL['db']->query("SELECT * , count(*) as joinnum FROM ".tname('quizuser')."  WHERE oid='$oid' group BY uid ORDER by joinnum DESC, dateline DESC LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$u = getspace($value["uid"]);
		$value["username"] = $u["username"];
		$uid1=$value['quizid'];
		$query1 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')."  WHERE quizid=$uid1");
		$value1= $_SGLOBAL['db']->fetch_array($query1);
		$value['joincost']=$value1['joincost'];
		$value["avatar"] = capi_avatar($value["uid"]);
		realname_set($value['uid'], $value['username']);//实名
		$value["username"] = capi_realname($value['uid']);
		$value["grouptitle"] = $_SGLOBAL["grouptitle"][$u["groupid"]]["grouptitle"] ;
		$list[] = $value;
	}
	
	$multi = smulti($start, $perpage, $count, "do.php?ac=ajax&op=getjoinuser&oid=$oid", $_REQUEST['ajaxdiv']);
	realname_get();

	capi_showmessage_by_data("rest_success", 0, array('joinusers'=>$list, 'count'=>count($list)));

}elseif($op == 'getcomment') {

	$page = empty($_REQUEST['page'])?1:intval($_REQUEST['page']);
	$idtypes = array('quizid', 'uid', 'sid');
	$idtype = (empty($_REQUEST['idtype']) || !in_array($_REQUEST['idtype'], $idtypes))?'quizid':$_REQUEST['idtype'];
	$id = empty($_REQUEST['id'])?0:intval($_REQUEST['id']);
	if($page<1) $page=1;
	//评论
	$perpage = empty($_REQUEST['perpage'])?10:intval($_REQUEST['perpage']);
	$perpage = mob_perpage($perpage);
	
	$start = ($page-1)*$perpage;

	$dateline = empty($_REQUEST['dateline'])?0:intval($_REQUEST['dateline']);
	$queryop = $_REQUEST['queryop'];

	if ($dateline){
		if ($queryop=="up"){
			$addsql = ' AND dateline > '.$dateline.'  ';
		}else{
			$addsql = ' AND dateline < '.$dateline.'  ';
		}
	}


	//检查开始数
	ckstart($start, $perpage);
	

	$list = array();
	
	$cid = empty($_REQUEST['cid'])?0:intval($_REQUEST['cid']);
	$csql = $cid?"cid='$cid' AND":'';

	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE $csql id='$id' AND idtype='$idtype' $addsql ORDER BY dateline DESC LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['authorid'], $value['author']);//实名
		$value["author"] = capi_realname($value['authorid']);
		$value["authorisonline"] = capi_isonline($value['authorid']);
		$value["isonline"] = capi_isonline($value['uid']);
		$value["avatar"] = capi_avatar($value["uid"]);
		$value["authoravatar"] = capi_avatar($value["authorid"]);
		$list[] = $value;
	}
	
	$multi = smulti($start, $perpage, $count, "do.php?ac=ajax&op=getcomment&id=$id&idtype=$idtype", $_REQUEST['ajaxdiv']);
	realname_get();

	capi_showmessage_by_data("rest_success", 0, array('comments'=>$list, 'count'=>count($list)));
	
} elseif($op == 'getfriendgroup') {
	
	$uid = intval($_REQUEST['uid']);
	if($_SGLOBAL['supe_uid'] && $uid) {
		$space = getspace($_SGLOBAL['supe_uid']);
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('friend')." WHERE uid='$_SGLOBAL[supe_uid]' AND fuid='$uid'");
		$value = $_SGLOBAL['db']->fetch_array($query);
	}
	
	//获取用户
	$groups = getfriendgroup();
	
	if(empty($value['gid'])) $value['gid'] = 0;
	$group =$groups[$value['gid']];
	
} elseif($op == 'getfriendname') {
	
	//获取用户的好友分组名
	$groupname = '';
	$group = intval($_REQUEST['group']);
	
	if($_SGLOBAL['supe_uid'] && $group) {
		$space = getspace($_SGLOBAL['supe_uid']);
		$groups = getfriendgroup();
		$groupname = $groups[$group];
	}
	
} elseif($op == 'getmtagmember') {
	
	//获取用户的好友分组名
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('tagspace')." WHERE tagid='$tagid' AND uid='$uid'");
	$tagspace = $_SGLOBAL['db']->fetch_array($query);
	
} elseif($op == 'share') {

	//评论
	$list = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('share')." WHERE uid='$_SGLOBAL[supe_uid]' ORDER BY dateline DESC LIMIT 0,1");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['uid'], $value['username']);
		$value = mkshare($value);
		$list[] = $value;
	}
	
	realname_get();
	
} elseif($op == 'post') {

	$pid = empty($_REQUEST['pid'])?0:intval($_REQUEST['pid']);

	if($pid) {
		$pidsql = " WHERE pid='$pid'";
		$ajax_edit = 1;
	} else {
		$pidsql = '';
		$ajax_edit = 0;
	}
	
	//评论
	$list = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('post')." $pidsql ORDER BY dateline DESC LIMIT 0,1");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['uid'], $value['username']);
		$list[] = $value;
	}
	
	realname_get();
	
} elseif($op == 'album') {
	
	$id = empty($_REQUEST['id'])?0:intval($_REQUEST['id']);
	$start = empty($_REQUEST['start'])?0:intval($_REQUEST['start']);

	if(empty($_SGLOBAL['supe_uid'])) {
		capi_showmessage_by_data('to_login', 'do.php?ac='.$_SCONFIG['login_action']);
	}
	
	$perpage = 10;
	//检查开始数
	ckstart($start, $perpage);

	$count = 0;
	
	$piclist = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE albumid='$id' AND uid='$_SGLOBAL[supe_uid]' ORDER BY dateline DESC LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['bigpic'] = pic_get($value['filepath'], $value['thumb'], $value['remote'], 0);
		$value['pic'] = pic_get($value['filepath'], $value['thumb'], $value['remote']);
		$piclist[] = $value;
		$count++;
	}
	$multi = smulti($start, $perpage, $count, "do.php?ac=ajax&op=album&id=$id", $_REQUEST['ajaxdiv']);

} elseif($op == 'docomment') {
	
	$doid = intval($_REQUEST['doid']);
	$clist = $do = array();
	$icon = $_REQUEST['icon'] == 'plus' ? 'minus' : 'plus';
	if($doid) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('doing')." WHERE doid='$doid'");
		if ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['uid'], $value['username']);
			$value['icon'] = 'plus';
			//自动展开最多20个评论
			if($value['replynum'] > 0 && ($value['replynum'] < 20 || $doid == $value['doid'])) {
				$doids[] = $value['doid'];
				$value['icon'] = 'minus';
			} elseif($value['replynum']<1) {
				$value['icon'] = 'minus';
			}
			$value['id'] = 0;
			$value['layer'] = 0;
			$clist[] = $value;
		}
	}
		
	if($_REQUEST['icon'] == 'plus' && $value['replynum']) {

		include_once(S_ROOT.'./source/class_tree.php');
		$tree = new tree();
		
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('docomment')." WHERE doid='$doid' ORDER BY dateline");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['uid'], $value['username']);
			if(empty($value['upid'])) {
				$value['upid'] = "do";
			}
			$tree->setNode($value['id'], $value['upid'], $value);
		}

		$values = $tree->getChilds("do");
		foreach ($values as $key => $id) {
			$one = $tree->getValue($id);
			$one['layer'] = $tree->getLayer($id) * 2;
			$clist[] = $one;
		}
	}
	
	realname_get();
	
} elseif($op == 'deluserapp') {
	
	if(empty($_SGLOBAL['supe_uid'])) {
		capi_showmessage_by_data('no_privilege');
	}
	$hash = trim($_REQUEST['hash']);
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('myinvite')." WHERE hash='$hash' AND touid='$_SGLOBAL[supe_uid]'");
	if($value = $_SGLOBAL['db']->fetch_array($query)) {
		$_SGLOBAL['db']->query("DELETE FROM ".tname('myinvite')." WHERE hash='$hash' AND touid='$_SGLOBAL[supe_uid]'");
		
		//统计更新
		$myinvitenum = getcount('myinvite', array('touid'=>$_SGLOBAL['supe_uid']));
		updatetable('space', array('myinvitenum'=>$myinvitenum), array('uid'=>$_SGLOBAL['supe_uid']));
		
		capi_showmessage_by_data('do_success');
	} else {
		capi_showmessage_by_data('no_privilege');
	}
} elseif($op == 'getreward') {
	$reward = '';
	if($_SCOOKIE['reward_log']) {
		$log = explode(',', $_SCOOKIE['reward_log']);
		if(count($log) == 2 && $log[1]) {
			@include_once(S_ROOT.'./data/data_creditrule.php');
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('creditlog')." WHERE clid='$log[1]'");
			$creditlog = $_SGLOBAL['db']->fetch_array($query);
			$rule = $_SGLOBAL['creditrule'][$log[0]];
			$rule['cyclenum'] = $rule['rewardnum']? $rule['rewardnum'] - $creditlog['cyclenum'] : 0;
		}
		ssetcookie('reward_log', '');
	}
	
}elseif($op == 'deletepic'){
	//删除
	$deleteids = array();
	$picid = empty($_REQUEST['picid'])?0:intval($_REQUEST['picid']);
	$deleteids[$picid] = $picid;
		
	if($deleteids) {
		include_once(S_ROOT.'./source/function_delete.php');
		deletepics($deleteids);
	}
	capi_showmessage_by_data('do_success');
}

include template('do_ajax');

?>

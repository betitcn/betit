<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space_friend.php 12880 2009-07-24 07:20:24Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

session_start();

//分页
$perpage = empty($_REQUEST['perpage'])?24:intval($_REQUEST['perpage']);
$perpage = mob_perpage($perpage);

$list = $ols = $fuids = array();
$count = 0;
$page = empty($_REQUEST['page'])?0:intval($_REQUEST['page']);
if($page<1) $page = 1;
$start = ($page-1)*$perpage;

$queryop = empty($_REQUEST['queryop'])?'up':$_REQUEST['queryop'];
$dateline = empty($_REQUEST['dateline'])?0:intval($_REQUEST['dateline']);
$credit = empty($_REQUEST['credit'])?0:intval($_REQUEST['credit']);
$order = empty($_REQUEST['order'])?"dateline":$_REQUEST['order'];

//检查开始数
ckstart($start, $perpage);

if($_REQUEST['view'] == 'online') {
	$theurl = "space.php?uid=$space[uid]&do=friend&view=online";
	$actives = array('me'=>' class="active"');

	$wheresql = '';
	if($_REQUEST['type']=='near') {
		$theurl = "space.php?uid=$space[uid]&do=friend&view=online&type=near";
		$wheresql = " WHERE main.ip='".getonlineip(1)."'";
	} elseif($_REQUEST['type']=='friend' && $space['feedfriend']) {
		$theurl = "space.php?uid=$space[uid]&do=friend&view=online&type=friend";
		$wheresql = " WHERE main.uid IN ($space[feedfriend])";
	} else {
		$_REQUEST['type']=='all';
		$theurl = "space.php?uid=$space[uid]&do=friend&view=online&type=all";
		$wheresql = ' WHERE 1';
	}

	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('session')." main $wheresql"), 0);
	if($count) {
		$query = $_SGLOBAL['db']->query("SELECT f.resideprovince, f.residecity, f.sex, f.note, f.spacenote, main.*
			FROM ".tname('session')." main
			LEFT JOIN ".tname('spacefield')." f ON f.uid=main.uid
			$wheresql
			ORDER BY main.lastactivity DESC
			LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {			
			if($value['magichidden']) {
				$count = $count - 1;
				continue;
			}
			if($_REQUEST['type']=='near') {
				if($value['uid'] == $space['uid']) {
					$count = $count-1;
					continue;
				}
			}
			$value['username'] = capi_realname($value['uid']);
			
			$value['p'] = rawurlencode($value['resideprovince']);
			$value['c'] = rawurlencode($value['residecity']);
			$value['isfriend'] = ($value['uid']==$space['uid'] || ($space['friends'] && in_array($value['uid'], $space['friends'])))?1:0;
			$ols[$value['uid']] = $value['lastactivity'];			
			$value['note'] = getstr($value['note'], 35, 0, 0, 0, 0, -1);
			$list[$value['uid']] = $value;
		}
	}
	$multi = multi($count, $perpage, $page, $theurl);

} elseif($_REQUEST['view'] == 'visitor' || $_REQUEST['view'] == 'trace') {

	$theurl = "space.php?uid=$space[uid]&do=friend&view=$_REQUEST[view]";
	$actives = array('me'=>' class="active"');

	if($_REQUEST['view'] == 'visitor') {//访客
		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('visitor')." main WHERE main.uid='$space[uid]'"), 0);
		$query = $_SGLOBAL['db']->query("SELECT f.resideprovince, f.residecity, f.note, f.spacenote, f.sex, main.vuid AS uid, main.vusername AS username, main.dateline
			FROM ".tname('visitor')." main
			LEFT JOIN ".tname('spacefield')." f ON f.uid=main.vuid
			WHERE main.uid='$space[uid]'
			ORDER BY main.dateline DESC
			LIMIT $start,$perpage");
	} else {//足迹
		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('visitor')." main WHERE main.vuid='$space[uid]'"), 0);
		$query = $_SGLOBAL['db']->query("SELECT s.username, s.name, s.namestatus, s.groupid, f.resideprovince, f.residecity, f.note, f.spacenote, f.sex, main.uid AS uid, main.dateline
			FROM ".tname('visitor')." main
			LEFT JOIN ".tname('space')." s ON s.uid=main.uid
			LEFT JOIN ".tname('spacefield')." f ON f.uid=main.uid
			WHERE main.vuid='$space[uid]'
			ORDER BY main.dateline DESC
			LIMIT $start,$perpage");
	}
	if($count) {
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			

			$value['username'] = capi_realname($value['uid']);
			$value['p'] = rawurlencode($value['resideprovince']);
			$value['c'] = rawurlencode($value['residecity']);
			$value['isfriend'] = ($value['uid']==$space['uid'] || ($space['friends'] && in_array($value['uid'], $space['friends'])))?1:0;
			$fuids[] = $value['uid'];
			$value['note'] = getstr($value['note'], 28, 0, 0, 0, 0, -1);
			$list[$value['uid']] = $value;
		}
	}
	$multi = multi($count, $perpage, $page, $theurl);

} elseif($_REQUEST['view'] == 'blacklist') {

	$theurl = "space.php?uid=$space[uid]&do=friend&view=$_REQUEST[view]";
	$actives = array('me'=>' class="active"');

	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('blacklist')." main WHERE main.uid='$space[uid]'"), 0);
	if($count) {
		$query = $_SGLOBAL['db']->query("SELECT s.username, s.name, s.namestatus, s.groupid, main.dateline, main.buid AS uid
			FROM ".tname('blacklist')." main
			LEFT JOIN ".tname('space')." s ON s.uid=main.buid
			WHERE main.uid='$space[uid]'
			ORDER BY main.dateline DESC
			LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$value['isfriend'] = 0;
			$value['username'] = capi_realname($value['uid']);
			$fuids[] = $value['uid'];
			$list[$value['uid']] = $value;
		}
	}
	$multi = multi($count, $perpage, $page, $theurl);

} else {

	//处理查询
	$theurl = "space.php?uid=$space[uid]&do=$do";
	$actives = array('me'=>' class="active"');
	
	$_REQUEST['view'] = 'me';

	//好友分组
	$wheresql = '';
	if($space['self']) {
		$groups = getfriendgroup();
		$group = !isset($_REQUEST['group'])?'-1':intval($_REQUEST['group']);
		if($group > -1) {
			$wheresql = "AND main.gid='$group'";
			$theurl .= "&group=$group";
		}
	}
	if($_REQUEST['searchkey']) {
		$wheresql = "AND main.fusername='$_REQUEST[searchkey]'";
		$theurl .= "&searchkey=$_REQUEST[searchkey]";
	}

	if($space['friendnum']) {
		if($wheresql) {
			$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('friend')." main WHERE main.uid='$space[uid]' AND main.status='1' $wheresql"), 0);
		} else {
			$count = $space['friendnum'];
		}

		$querysql = "";
		$ordersql = " ORDER BY s.credit DESC, main.dateline DESC ";
		if ($queryop=='up'){
			if ($order=="credit"){
				$querysql = " AND s.credit > ".$credit." ";
			}else{
				
				$querysql = " AND s.dateline > '".$dateline."' ";
				$ordersql = " ORDER BY s.dateline DESC , s.credit DESC ";
				
			}
		}elseif($queryop=='down'){
			if ($order=="credit"){
				$querysql = " AND s.credit < ".$credit." ";
			}else{
				
				$querysql = " AND s.dateline < '".$dateline."' ";
				$ordersql = " ORDER BY s.dateline DESC , s.credit DESC ";
				
			}
		}

		if($count) {
			/*$query = $_SGLOBAL['db']->query("SELECT s.*, f.resideprovince, f.residecity, f.note, f.spacenote, f.sex, main.gid, main.num
				FROM ".tname('friend')." main
				LEFT JOIN ".tname('space')." s ON s.uid=main.fuid
				LEFT JOIN ".tname('spacefield')." f ON f.uid=main.fuid
				WHERE main.uid='$space[uid]' AND main.status='1' $wheresql
				ORDER BY main.num DESC, main.dateline DESC
				LIMIT $start,$perpage");*/
			$query2 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid!=1");
		while($value2 = $_SGLOBAL['db']->fetch_array($query2)){
		foreach($value2 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}
			$query = $_SGLOBAL['db']->query("SELECT s.*, f.resideprovince, f.residecity, f.note, f.spacenote, f.sex, main.gid, main.num, (SELECT COUNT(credit) FROM ".tname('space')." WHERE credit>s.credit)+1 as creditrank, (SELECT COUNT(experience) FROM ".tname('space')." WHERE experience>s.experience)+1 as experiencerank
				FROM ".tname('friend')." main
				LEFT JOIN ".tname('space')." s ON s.uid=main.fuid
				LEFT JOIN ".tname('spacefield')." f ON f.uid=main.fuid
				WHERE main.uid='$space[uid]' AND s.uid IN('".implode("','",  $searcharr)."')  AND main.status='1' $wheresql $querysql
				$ordersql
				LIMIT $start,$perpage");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				$value['username'] = capi_realname($value['uid']);
				$value['p'] = rawurlencode($value['resideprovince']);
				$value['c'] = rawurlencode($value['residecity']);
				$value['group'] = $groups[$value['gid']];
				$value['isfriend'] = 1;
				$fuids[] = $value['uid'];
				$value['note'] = getstr($value['note'], 28, 0, 0, 0, 0, -1);
				$list[$value['uid']] = $value;
			}
		}
		$querysql = "";
		if ($queryop=='up'){
			$querysql = " AND f.dateline > ".$dateline." ";
		}elseif($queryop=='down'){
			$querysql = " AND f.dateline < ".$dateline." ";
		}

		//分页
		$multi = multi($count, $perpage, $page, $theurl);
		$friends = array();
		//取100好友用户名

		$query = $_SGLOBAL['db']->query("SELECT f.fusername, s.name, s.namestatus, s.groupid, f.dateline FROM ".tname('friend')." f
			LEFT JOIN ".tname('space')." s ON s.uid=f.fuid
			WHERE f.uid=$_SGLOBAL[supe_uid] AND f.status='1' ".$querysql."  ORDER BY f.num DESC, f.dateline DESC LIMIT 0,100");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$fusername = ($_SCONFIG['realname'] && $value['name'] && $value['namestatus'])?$value['name']:$value['fusername'];
			$friends[] = addslashes($fusername);
		}
		$friendstr = implode(',', $friends);
	}

	if($space['self']) {
		$groupselect = array($group => ' class="current"');

		//好友个数
		$maxfriendnum = checkperm('maxfriendnum');
		if($maxfriendnum) {
			$maxfriendnum = checkperm('maxfriendnum') + $space['addfriend'];
		}
	}
}

//在线状态
if($fuids) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." WHERE uid IN (".simplode($fuids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(!$value['magichidden']) {
			$ols[$value['uid']] = $value['lastactivity'];
		} elseif($list[$value['uid']] && !in_array($_REQUEST['view'], array('me', 'trace', 'blacklist'))) {
			unset($list[$value['uid']]);
			$count = $count - 1;
		}
	}
}

realname_get();
 
if(empty($_REQUEST['view']) || $_REQUEST['view'] == 'all') $_REQUEST['view'] = 'me';
$a_actives = array($_REQUEST['view'].$_REQUEST['type'] => ' class="current"');
$plist = array();

@include_once(S_ROOT.'./data/data_usergroup.php');
$isinsert = empty($_REQUEST['isinsert'])?false:$_REQUEST['isinsert'];
$space['username'] = capi_realname($space['uid']);
foreach ($list as $key=>$value)
{
	$tmpspace = getspace($value["uid"]);
	$value["avatar"] = capi_avatar($value["uid"]);
	$value["grouptitle"] = $_SGLOBAL["grouptitle"][$value["groupid"]]["grouptitle"];
	$value['username'] = capi_realname($value['uid'], $tmpspace);
	$value['isonline'] =  capi_isonline($value['uid'], $tmpspace);
	if ($space["credit"]>$value["credit"] && !$isinsert && (empty($_SESSION['hasinsert']) || $_SESSION['insertcredit'] == $credit||$_SESSION['insertdateline'] == $dateline) ){
		$tmpspace = getspace($space["uid"]);
		$space['isonline'] =  capi_isonline($space['uid'], $tmpspace);
		$space['username'] = capi_realname($space['uid'], $tmpspace);
		$space["avatar"] = capi_avatar($space["uid"]);
		$space["grouptitle"] = $_SGLOBAL["grouptitle"][$space["groupid"]]["grouptitle"];
		$plist[] = $space;
		$isinsert = true;
		if ($credit)
			$_SESSION['insertcredit'] = $credit;
		if($dateline)
			$_SESSION['insertdateline'] = $dateline;
	}
	$plist[] = $value;
}

if (!$isinsert && (empty($_SESSION['hasinsert']) || $_SESSION['insertcredit'] == $credit||$_SESSION['insertdateline'] == $dateline) ){
	$tmpspace = getspace($space["uid"]);
	$space["avatar"] = capi_avatar($space["uid"]);
	$space['username'] = capi_realname($space['uid'], $tmpspace);
	$space['isonline'] =  capi_isonline($space['uid'], $tmpspace);
	$space["grouptitle"] = $_SGLOBAL["grouptitle"][$space["groupid"]]["grouptitle"];
	$plist[] = $space;
	$isinsert = true;
}

capi_showmessage_by_data("rest_success", 0, array('friends'=>$plist, 'count'=>count($plist)));
//include_once template("space_friend");

?>

<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space_feed.php 13194 2009-08-18 07:44:40Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}


//显示全站动态的好友数
if(empty($_SCONFIG['showallfriendnum']) || $_SCONFIG['showallfriendnum']<1) $_SCONFIG['showallfriendnum'] = 10;
//默认热点天数
if(empty($_SCONFIG['feedhotday'])) $_SCONFIG['feedhotday'] = 2;

//网站近况
$isnewer = $space['friendnum']<$_SCONFIG['showallfriendnum']?1:0;
if(empty($_REQUEST['view']) && $space['self'] && $isnewer) {
	$_REQUEST['view'] = 'all';//默认显示
}

//分页
$perpage = $_REQUEST['perpage']?$_REQUEST['perpage']:20;
//$perpage = mob_perpage($perpage);

//if($_REQUEST['view'] == 'hot') {
//	$perpage = 50;
//}

$start = empty($_REQUEST['page'])?0:intval($_REQUEST['page'])*$perpage;
//检查开始数

$dateline = empty($_REQUEST['dateline'])?0:intval($_REQUEST['dateline']);

if ($dateline){
	$start = 0;
	$queryop = empty($_REQUEST['queryop'])?'up':'down';
}


ckstart($start, $perpage);

//今天时间开始线
$_SGLOBAL['today'] = sstrtotime(sgmdate('Y-m-d'));

//最少热度
$minhot = $_SCONFIG['feedhotmin']<1?3:$_SCONFIG['feedhotmin'];
$_SGLOBAL['gift_appid'] = '1027468';

if($_REQUEST['view'] == 'all') {

	$wheresql = "bf.id!=1";//没有隐私
	$ordersql = "b.dateline DESC";
	$theurl = "space.php?uid=$space[uid]&do=$do&view=all";
	$f_index = '';

}if($_REQUEST['view'] == 'quiz') {

	$wheresql = "b.idtype='quizid'  AND bf.id!=1";//没有隐私
	
	$ordersql = "b.dateline DESC";
	$theurl = "space.php?uid=$space[uid]&do=$do&view=quiz";
	$f_index = '';

}elseif($_REQUEST['view'] == 'quizhot') {
	$dateline1 = $_SGLOBAL['timestamp'];
	$wheresql = "b.idtype='quizid' and bf.endtime>=$dateline1  AND bf.id!=1";//没有隐私
	$ordersql = "b.hot DESC";
	$theurl = "space.php?uid=$space[uid]&do=$do&view=quizhot";
	$f_index = '';

}elseif($_REQUEST['view'] == 'hot') {

	$wheresql = "b.hot>='$minhot'  AND bf.id!=1";
	$ordersql = "b.dateline DESC";
	$theurl = "space.php?uid=$space[uid]&do=$do&view=hot";
	$f_index = '';

} elseif($_GET['view'] == 'open') {
	$dateline1 = $_SGLOBAL['timestamp'];
    $wheresql = "bf.endtime>=$dateline1  AND bf.id!=1";
	$ordersql = "bf.endtime ASC";
	$theurl = "space.php?uid=$space[uid]&do=$do&view=open";
	$f_index = '';


} else {

	if(empty($space['feedfriend'])) $_REQUEST['view'] = 'me';
	
	if( $_REQUEST['view'] == 'me') {
		$wheresql = "b.uid='$space[uid]'  AND bf.id!=1";
		$ordersql = "b.dateline DESC";
		$theurl = "space.php?uid=$space[uid]&do=$do&view=me";
		$f_index = '';
		
	} else {
		$wheresql = "b.uid IN ('0',$space[feedfriend]) AND b.qid!=1";
		$ordersql = "b.dateline DESC";
		$theurl = "space.php?uid=$space[uid]&do=$do&view=we";
		$f_index = '';
		$_REQUEST['view'] = 'we';
		//不显示时间
		$_TPL['hidden_time'] = 1;
	}
}


	if ($dateline){
		if ($_REQUEST['queryop'] == "up"){
			$wheresql .= " AND b.dateline > '$dateline'";
		}else{
			$wheresql .= " AND b.dateline < '$dateline'";
		}

	}


//过滤
$appid = empty($_REQUEST['appid'])?0:intval($_REQUEST['appid']);
if($appid) {
	$wheresql .= " AND b.appid='$appid'";
}
$icon = empty($_REQUEST['icon'])?'':trim($_REQUEST['icon']);
if($icon) {
	$wheresql .= " AND b.icon='$icon'";
}
$filter = empty($_REQUEST['filter'])?'':trim($_REQUEST['filter']);
if($filter == 'site') {
	$wheresql .= " AND b.appid>0";
} elseif($filter == 'myapp') {
	$wheresql .= " AND b.appid='0'";
}

$feed_list = $appfeed_list = $hiddenfeed_list = $filter_list = $hiddenfeed_num = $icon_num = array();
$count = $filtercount = 0;
$query = $_SGLOBAL['db']->query("SELECT bf.*, b.* FROM ".tname('feed')." b LEFT JOIN ".tname('quiz')." bf ON bf.quizid=b.id $f_index
	WHERE $wheresql
	ORDER BY $ordersql
	LIMIT $start,$perpage");

if($_REQUEST['view'] == 'me' || $_REQUEST['view'] == 'hot' || $_REQUEST['view'] == 'quiz' || $_REQUEST['view'] == 'we'|| $_REQUEST['view'] == 'open'|| $_REQUEST['view'] == 'quizhot'|| $_REQUEST['view'] == 'friend') {
	//个人动态
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
			realname_set($value['uid'], $value['username']);
			if ($value['idtype'] == 'quizid')
			{
				$value["commentnum"] =  $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('comment')." WHERE id='$value[id]' AND idtype='$value[idtype]' "),0);
				$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE id='$value[id]' AND idtype='$value[idtype]' ORDER BY dateline DESC LIMIT 0,2");
				while ($value2 = $_SGLOBAL['db']->fetch_array($query2)) {
					// realname_set($value2['authorid'], $value2['author']);//实名
					$value2['author'] = capi_realname($value2['authorid']);
					$value2["authoravatar"] = capi_avatar($value2["authorid"]);
					$value2["message"] = capi_fhtml($value2["message"]);
					$value["comments"][] = $value2;
				}
			}
			
			$feed_list[] = $value;
		}
		$count++;
	}
} else {
	//要折叠的动态
	$hidden_icons = array();
	if($_SCONFIG['feedhiddenicon']) {
		$_SCONFIG['feedhiddenicon'] = str_replace(' ', '', $_SCONFIG['feedhiddenicon']);
		$hidden_icons = explode(',', $_SCONFIG['feedhiddenicon']);
	}
	$space['filter_icon'] = empty($space['privacy']['filter_icon'])?array():array_keys($space['privacy']['filter_icon']);
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		/*if(empty($feed_list[$value['hash_data']][$value['uid']])) {
			if(ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
				realname_set($value['uid'], $value['username']);
				if(ckicon_uid($value)) {
					$ismyapp = is_numeric($value['icon'])?1:0;
					if($_SCONFIG['my_showgift'] && $value['icon'] == $_SGLOBAL['gift_appid']) $ismyapp = 0;
					if((($ismyapp && in_array('myop', $hidden_icons)) || in_array($value['icon'], $hidden_icons)) && !empty($icon_num[$value['icon']])) {
						$hiddenfeed_num[$value['icon']]++;
						$hiddenfeed_list[$value['icon']][] = $value;
					} else {
						if($ismyapp) {
							$appfeed_list[$value['hash_data']][$value['uid']] = $value;
						} else {
							$feed_list[$value['hash_data']][$value['uid']] = $value;
						}
					}
					$icon_num[$value['icon']]++;
				} else {
					$filtercount++;
					$filter_list[] = $value;
				}
			}
		}*/
		
		$feed_list[] = $value;
		
		$count++;
	}
}

$olfriendlist = $visitorlist = $task = $ols = $birthlist = $myapp = $hotlist = $guidelist = array();
$oluids = array();
$topiclist = array();
$newspacelist = array();

if($space['self'] && empty($start)) {

	//短消息
	$space['pmnum'] = $_SGLOBAL['member']['newpm'];

	//举报管理
	if(checkperm('managereport')) {
		$space['reportnum'] = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('report')." WHERE new='1'"), 0);
	}

	//审核活动
	if(checkperm('manageevent')) {
		$space['eventverifynum'] = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('event')." WHERE grade='0'"), 0);
	}

	//等待实名认证
	if($_SCONFIG['realname'] && checkperm('managename')) {
		$space['namestatusnum'] = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE namestatus='0' AND name!=''"), 0);
	}
	
	//欢迎新成员
	if($_SCONFIG['newspacenum']>0) {
		$newspacelist = unserialize(data_get('newspacelist'));
		if(!is_array($newspacelist)) $newspacelist = array();
		foreach ($newspacelist as $value) {
			$oluids[] = $value['uid'];
			realname_set($value['uid'], $value['username'], $value['name'], $value['namestatus']);
		}
	}

	//最近访客列表
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('visitor')." WHERE uid='$space[uid]' ORDER BY dateline DESC LIMIT 0,12");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['vuid'], $value['vusername']);
		$visitorlist[$value['vuid']] = $value;
		$oluids[] = $value['vuid'];
	}

	//访客在线
	if($oluids) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." WHERE uid IN (".simplode($oluids).")");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if(!$value['magichidden']) {
				$ols[$value['uid']] = 1;
			} elseif ($visitorlist[$value['uid']]) {
				unset($visitorlist[$value['uid']]);
			}
		}
	}
	//在线用户推荐
	$sql = "SELECT field.*, space.*, main.*
		FROM ".tname('session')." main USE INDEX (lastactivity)
		LEFT JOIN ".tname('space')." space ON space.uid=main.uid
		LEFT JOIN ".tname('spacefield')." field ON field.uid=main.uid where main.uid!=$space[uid] 
		ORDER BY main.lastactivity DESC ";
		$list2 = array();
	$query2 = $_SGLOBAL['db']->query("$sql LIMIT 0,6");
	while ($value = $_SGLOBAL['db']->fetch_array($query2)) {
		$list2[$value['uid']] = $value;
	}
	foreach($list2 as $key => $value) {
	$value['isfriend'] = ($value['uid']==$space['uid'] || ($space['friends'] && in_array($value['uid'], $space['friends'])))?1:0;
	realname_set($value['uid'], $value['username'], $value['name'], $value['namestatus']);
	$fuids[] = $value['uid'];
	$list2[$key] = $value;
}

	$oluids = array();
	$olfcount = 0;
	if($space['feedfriend']) {
		//在线好友
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." WHERE uid IN ($space[feedfriend]) ORDER BY lastactivity DESC LIMIT 0,15");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if(!$value['magichidden']) {
				realname_set($value['uid'], $value['username']);
				$olfriendlist[] = $value;
				$ols[$value['uid']] = 1;
				$oluids[$value['uid']] = $value['uid'];
				$olfcount++;
			}
		}
	}
	if($olfcount < 15) {
		//我的好友
		$query = $_SGLOBAL['db']->query("SELECT fuid AS uid, fusername AS username, num FROM ".tname('friend')." WHERE uid='$space[uid]' AND status='1' ORDER BY num DESC, dateline DESC LIMIT 0,30");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if(empty($oluids[$value['uid']])) {
				realname_set($value['uid'], $value['username']);
				$olfriendlist[] = $value;
				$olfcount++;
				if($olfcount == 15) break;
			}
		}
	}

	//获取任务
	include_once(S_ROOT.'./source/function_space.php');
	$task = gettask();

	//好友生日
	if($space['feedfriend']) {
		list($s_month, $s_day) = explode('-', sgmdate('n-j', $_SGLOBAL['timestamp']-3600*24*3));//过期3天
		list($n_month, $n_day) = explode('-', sgmdate('n-j', $_SGLOBAL['timestamp']));
		list($e_month, $e_day) = explode('-', sgmdate('n-j', $_SGLOBAL['timestamp']+3600*24*7));
		if($e_month == $s_month) {
			$wheresql = "sf.birthmonth='$s_month' AND sf.birthday>='$s_day' AND sf.birthday<='$e_day'";
		} else {
			$wheresql = "(sf.birthmonth='$s_month' AND sf.birthday>='$s_day') OR (sf.birthmonth='$e_month' AND sf.birthday<='$e_day' AND sf.birthday>'0')";
		}
		$query = $_SGLOBAL['db']->query("SELECT s.uid,s.username,s.name,s.namestatus,s.groupid,sf.birthyear,sf.birthmonth,sf.birthday
			FROM ".tname('spacefield')." sf
			LEFT JOIN ".tname('space')." s ON s.uid=sf.uid
			WHERE (sf.uid IN ($space[feedfriend])) AND ($wheresql)");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['uid'], $value['username'], $value['name'], $value['namestatus']);
			$value['istoday'] = 0;
			if($value['birthmonth'] == $n_month && $value['birthday'] == $n_day) {
				$value['istoday'] = 1;
			}
			$key = sprintf("%02d", $value['birthmonth']).sprintf("%02d", $value['birthday']);
			$birthlist[$key][] = $value;
			ksort($birthlist);
		}
	}

	//积分
	$space['star'] = getstar($space['experience']);

	//域名
	$space['domainurl'] = space_domain($space);

	//热点
	if($_SCONFIG['feedhotnum'] > 0 && ($_REQUEST['view'] == 'we' || $_REQUEST['view'] == 'all')) {
		$hotlist_all = array();
		$hotstarttime = $_SGLOBAL['timestamp'] - $_SCONFIG['feedhotday']*3600*24;
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('feed')." USE INDEX(hot) WHERE dateline>='$hotstarttime' ORDER BY hot DESC LIMIT 0,10");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if($value['hot']>0 && ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
				realname_set($value['uid'], $value['username']);
				if(empty($hotlist)) {
					$hotlist[$value['feedid']] = $value;
				} else {
					$hotlist_all[$value['feedid']] = $value;
				}
			}
		}
		$nexthotnum = $_SCONFIG['feedhotnum'] - 1;
		if($nexthotnum > 0) {
			if(count($hotlist_all)> $nexthotnum) {
				$hotlist_key = array_rand($hotlist_all, $nexthotnum);
				if($nexthotnum == 1) {
					$hotlist[$hotlist_key] = $hotlist_all[$hotlist_key];
				} else {
					foreach ($hotlist_key as $key) {
						$hotlist[$key] = $hotlist_all[$key];
					}
				}
			} else {
				$hotlist = $hotlist_all;
			}
		}
	}
	
	//热闹
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('topic')." ORDER BY lastpost DESC LIMIT 0,1");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['pic'] = $value['pic']?pic_get($value['pic'], $value['thumb'], $value['remote']):'';
		$topiclist[] = $value;
	}


	//提醒总数
	$space['allnum'] = 0;
	foreach (array('notenum', 'addfriendnum', 'mtaginvitenum', 'eventinvitenum', 'myinvitenum', 'pokenum', 'reportnum', 'namestatusnum', 'eventverifynum') as $value) {
		$space['allnum'] = $space['allnum'] + $space[$value];
	}
}

//实名处理
realname_get();

//feed合并
$list = array();

if($_REQUEST['view'] == 'hot') {
	//热点
	/*foreach ($feed_list as $value) {
		$value = mkfeed($value);
		$list['today'][] = $value;
	}*/

	foreach ($feed_list as $key=>$value) {
		// $value = mkfeed($value);
		// $feed_list[$key] = $value;
		// $value['dateline'] = date('m-d H:i',$value['dateline']);
		$value = capi_mkfeed($value);
		$value["avatar"] = capi_avatar($value["uid"]);
		$value = capi_data_filter($value, array("icon","uid", "username", "dateline", "friend", "title_template", "title_data", "body_template", "body_data",  "id", "idtype", "hot", "commentnum",  "comments", "avatar"));
		$value["body_template"] = capi_fhtml($value["body_template"]);
		$tmpspace = getspace($value['uid']);
		$value["isonline"] = capi_isonline($value["uid"], $tmpspace);

		if ($value["icon"] =="quiz")
		{
			$quiz = capi_getquiz($value["id"]);
			$value["body_data"]["subject"] = strip_tags($quiz["subject"]);
			$value["body_data"]["option"] = $quiz["options"];
			$value["body_data"]["endtime"] = $quiz["endtime"];
			$value["body_data"]["resulttime"] = $quiz["resulttime"];
			
			if($quiz['endtime'] && $quiz['endtime'] < $_SGLOBAL['timestamp']) {
					if ( intval($quiz["keyoid"]) == 0)
						$value["body_data"]["hasexceed"] = 1;
					else
						$value["body_data"]["hasexceed"] = 0;
			}else
			{
				$value["body_data"]["hasexceed"] = 0;
			}
			$value["body_data"]["keyoid"] = $quiz["keyoid"];
			foreach ($value["body_data"]["option"] as $okey=>$ovalue)
			{
					$value["body_data"]["option"][$okey] = capi_data_filter($ovalue, array("option", "votenum", "pic", "oid","relatedtime"));
			}
			$value["body_data"]["totalcost"] = $quiz["totalcost"];
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
			$value["title_data"]["subject"] = strip_tags($quiz["subject"]);

		}elseif ($value["icon"] =="click"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			@$dom->loadHTML($value["title_data"]["subject"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["subject"] = capi_fhtml($value["title_data"]["subject"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["id"] = $data["id"];
			$value["title_data"]["idtype"] = $data["do"];
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"] =="comment"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			@$dom->loadHTML($value["title_data"]["quiz"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["quiz"] = capi_fhtml($value["title_data"]["quiz"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["quizid"] = $data["id"];
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"] =="friend"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"]=="task"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["task"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["task"] = capi_fhtml($value["title_data"]["task"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["taskid"] = $data["taskid"];
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"]=="profile" || $value["icon"]=="doing"){
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"]=="wall"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}
		$feed_list[$key] = $value;
	} 

} elseif($_REQUEST['view'] == 'me') {
	//个人
	/*foreach ($feed_list as $value) {
		if($hotlist[$value['feedid']]) continue;
		$value = mkfeed($value);
		if($value['dateline']>=$_SGLOBAL['today']) {
			$list['today'][] = $value;
		} elseif ($value['dateline']>=$_SGLOBAL['today']-3600*24) {
			$list['yesterday'][] = $value;
		} else {
			$theday = sgmdate('Y-m-d', $value['dateline']);
			$list[$theday][] = $value;
		}
	}*/

	foreach ($feed_list as $key=>$value) {
		// if($hotlist[$value['feedid']]) continue;
		//$value = mkfeed($value);
		//$value['dateline'] = date('m-d H:i',$value['dateline']);
		//$feed_list[$key] = $value;
		//if($value['dateline']>=$_SGLOBAL['today']) {
		//	$list['today'][] = $value;
		//} elseif ($value['dateline']>=$_SGLOBAL['today']-3600*24) {
		//	$list['yesterday'][] = $value;
		//} else {
		//	$theday = sgmdate('Y-m-d', $value['dateline']);
		//	$list[$theday][] = $value;
		//}
		$value = capi_mkfeed($value);
		$value["avatar"] = capi_avatar($value["uid"]);
		$value = capi_data_filter($value, array("icon","uid", "username", "dateline", "friend", "title_template", "title_data", "body_template", "body_data", "id", "idtype", "hot", "commentnum", "comments", "avatar"));
		$value["body_template"] = capi_fhtml($value["body_template"]);
		$tmpspace = getspace($value['uid']);
		$value["isonline"] = capi_isonline($value["uid"], $tmpspace);

		if ($value["icon"] =="quiz")
		{
			$quiz = capi_getquiz($value["id"]);
			$value["body_data"]["subject"] = strip_tags($quiz["subject"]);
			$value["body_data"]["option"] = $quiz["options"];
			$value["body_data"]["totalcost"] = $quiz["totalcost"];
			$value["body_data"]["endtime"] = $quiz["endtime"];
			$value["body_data"]["resulttime"] = $quiz["resulttime"];
			if($quiz['endtime'] && $quiz['endtime'] < $_SGLOBAL['timestamp']) {
					if ( intval($quiz["keyoid"]) == 0)
						$value["body_data"]["hasexceed"] = 1;
					else
						$value["body_data"]["hasexceed"] = 0;
			}else
			{
				$value["body_data"]["hasexceed"] = 0;
			}
			$value["body_data"]["keyoid"] = $quiz["keyoid"];
			foreach ($value["body_data"]["option"] as $okey=>$ovalue)
			{
					$value["body_data"]["option"][$okey] = capi_data_filter($ovalue, array("option", "votenum", "pic", "oid","relatedtime"));

			}
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
			$value["title_data"]["subject"] = strip_tags($quiz["subject"]);
		}elseif ($value["icon"] =="click"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			@$dom->loadHTML($value["title_data"]["subject"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["subject"] = capi_fhtml($value["title_data"]["subject"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["id"] = $data["id"];
			$value["title_data"]["idtype"] = $data["do"];
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"] =="comment"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			@$dom->loadHTML($value["title_data"]["quiz"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["quiz"] = capi_fhtml($value["title_data"]["quiz"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["quizid"] = $data["id"];
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"] =="friend"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"]=="task"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["task"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["task"] = capi_fhtml($value["title_data"]["task"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["taskid"] = $data["taskid"];
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"]=="profile" || $value["icon"]=="doing"){
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"]=="wall"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}
		$feed_list[$key] = $value;
	}

} else {
	//好友、全站
	/*foreach ($feed_list as $values) {
		$actors = array();
		$a_value = array();
		foreach ($values as $value) {
			if(empty($a_value)) {
				$a_value = $value;
			}
			$actors[] = "<a href=\"space.php?uid=$value[uid]\">".$_SN[$value['uid']]."</a>";
		}
		if($hotlist[$a_value['feedid']]) continue;
		$a_value = mkfeed($a_value, $actors);
		if($a_value['dateline']>=$_SGLOBAL['today']) {
			$list['today'][] = $a_value;
		} elseif ($a_value['dateline']>=$_SGLOBAL['today']-3600*24) {
			$list['yesterday'][] = $a_value;
		} else {
			$theday = sgmdate('Y-m-d', $a_value['dateline']);
			$list[$theday][] = $a_value;
		}
	}
	//应用
	foreach ($appfeed_list as $values) {
		$actors = array();
		$a_value = array();
		foreach ($values as $value) {
			if(empty($a_value)) {
				$a_value = $value;
			}
			$actors[] = "<a href=\"space.php?uid=$value[uid]\">".$_SN[$value['uid']]."</a>";
		}
		$a_value = mkfeed($a_value, $actors);
		$list['app'][] = $a_value;
	}*/
	foreach ($feed_list as $key=>$value) {
		// if($hotlist[$value['feedid']]) continue;
		//$value = mkfeed($value);
		//$value['dateline'] = date('m-d H:i',$value['dateline']);
		//$feed_list[$key] = $value;
		//if($value['dateline']>=$_SGLOBAL['today']) {
		//	$list['today'][] = $value;
		//} elseif ($value['dateline']>=$_SGLOBAL['today']-3600*24) {
		//	$list['yesterday'][] = $value;
		//} else {
		//	$theday = sgmdate('Y-m-d', $value['dateline']);
		//	$list[$theday][] = $value;
		//}
		$value = capi_mkfeed($value);
		$value["avatar"] = capi_avatar($value["uid"]);
		$value = capi_data_filter($value, array("icon","uid", "username", "dateline", "friend", "title_template", "title_data", "body_template", "body_data", "id", "idtype", "hot", "commentnum", "comments", "avatar"));
		$value["body_template"] = capi_fhtml($value["body_template"]);
		$tmpspace = getspace($value['uid']);
		$value["isonline"] = capi_isonline($value["uid"], $tmpspace);

		if ($value["icon"] =="quiz")
		{
			if($value["idtype"]=="quizid"){
			$quiz = capi_getquiz($value["id"]);
			$value["body_data"]["subject"] = strip_tags($quiz["subject"]);
			$value["body_data"]["option"] = $quiz["options"];
			$value["body_data"]["totalcost"] = $quiz["totalcost"];
			$value["body_data"]["endtime"] = $quiz["endtime"];
			$value["body_data"]["resulttime"] = $quiz["resulttime"];
			if($quiz['endtime'] && $quiz['endtime'] < $_SGLOBAL['timestamp']) {
					if ( intval($quiz["keyoid"]) == 0)
						$value["body_data"]["hasexceed"] = 1;
					else
						$value["body_data"]["hasexceed"] = 0;
			}else
			{
				$value["body_data"]["hasexceed"] = 0;
			}
			$value["body_data"]["keyoid"] = $quiz["keyoid"];
			foreach ($value["body_data"]["option"] as $okey=>$ovalue)
			{
					$value["body_data"]["option"][$okey] = capi_data_filter($ovalue, array("option", "votenum", "pic", "oid","relatedtime"));

			}
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
			$value["title_data"]["subject"] = strip_tags($quiz["subject"]);
		}else{
			$url=$value["title_data"]["url"];
			preg_match("/(?<=&id=)([^&]*)(?=)/", $url, $a);
			$quiz = capi_getquiz($a[0]);
			$value["id"] = $a[0];
			$value["body_data"]["subject"] = strip_tags($quiz["subject"]);
			$value["body_data"]["option"] = $quiz["options"];
			$value["body_data"]["totalcost"] = $quiz["totalcost"];
			$value["body_data"]["endtime"] = $quiz["endtime"];
			$value["body_data"]["resulttime"] = $quiz["resulttime"];
			if($quiz['endtime'] && $quiz['endtime'] < $_SGLOBAL['timestamp']) {
					if ( intval($quiz["keyoid"]) == 0)
						$value["body_data"]["hasexceed"] = 1;
					else
						$value["body_data"]["hasexceed"] = 0;
			}else
			{
				$value["body_data"]["hasexceed"] = 0;
			}
			$value["body_data"]["keyoid"] = $quiz["keyoid"];
			foreach ($value["body_data"]["option"] as $okey=>$ovalue)
			{
					$value["body_data"]["option"][$okey] = capi_data_filter($ovalue, array("option", "votenum", "pic", "oid","relatedtime"));

			}
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
			$value["title_data"]["subject"] = strip_tags($quiz["subject"]);
		}
		}elseif ($value["icon"] =="click"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			@$dom->loadHTML($value["title_data"]["subject"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["subject"] = capi_fhtml($value["title_data"]["subject"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["id"] = $data["id"];
			$value["title_data"]["idtype"] = $data["do"];
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"] =="comment"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			@$dom->loadHTML($value["title_data"]["quiz"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["quiz"] = capi_fhtml($value["title_data"]["quiz"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["quizid"] = $data["id"];
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"] =="friend"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"]=="task"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["task"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["task"] = capi_fhtml($value["title_data"]["task"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["taskid"] = $data["taskid"];
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"]=="profile" || $value["icon"]=="doing"){
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}elseif ($value["icon"]=="wall"){
			$dom = new DomDocument();
			@$dom->loadHTML($value["title_data"]["touser"]);
			$urls = $dom->getElementsByTagName('a');
			$url = $urls->item(0);
			$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			$arr = parse_url($url->getAttribute('href'));
			$parameters = $arr["query"];
			parse_str($parameters, $data);
			$value["title_data"]["touseruid"] = $data["uid"];
			$value["title_data"]["touser"] = capi_realname($value["title_data"]["touseruid"]);
			$value["username"] = capi_realname($value["uid"],$tmpspace);
			$value["title_data"]["actor"] = $value["username"];
		}
		$feed_list[$key] = $value;
	}
	/*foreach ($feed_list as $key=>$values) {
		$actors = array();
		$a_value = array();
		foreach ($values as $value) {
			if(empty($a_value)) {
				$a_value = $value;
			}
			$actors[] = $_SN[$value['uid']];
		}
		//if($hotlist[$a_value['feedid']]) continue;
		//$a_value['dateline'] = date('m-d H:i',$a_value['dateline']);
		//$a_value = mkfeed($a_value, $actors);
		//$feed_list[$key] = $a_value;
		$a_value = capi_mkfeed($a_value);
		$a_value["avatar"] = capi_avatar($a_value["uid"]);
		$a_value =  capi_data_filter($a_value, array("icon", "uid", "username", "dateline", "friend", "title_template", "title_data", "body_template", "body_data",  "id", "idtype", "hot", "commentnum", "avatar"));
		if ($a_value["icon"] =="quiz")
		{
			$quiz = capi_getquiz($a_value["id"]);
			$a_value["body_data"]["subject"] = $quiz["subject"];
			$a_value["body_data"]["option"] = $quiz["options"];
			$a_value["body_data"]["totalcost"] = $quiz["totalcost"];
			$a_value["body_data"]["endtime"] = $quiz["endtime"];
			$a_value["body_data"]["resulttime"] = $quiz["resulttime"];
			foreach ($a_value["body_data"]["option"] as $okey=>$ovalue)
			{
					$a_value["body_data"]["option"][$okey] = capi_data_filter($ovalue, array("option", "votenum", "pic", "oid","relatedtime"));
			}
		}
		$feed_list[$key] = $a_value;
	}*/
}

//获得个性模板
/*$templates = $default_template = array();
$tpl_dir = sreaddir(S_ROOT.'./template');
foreach ($tpl_dir as $dir) {
	if(file_exists(S_ROOT.'./template/'.$dir.'/style.css')) {
		$tplicon = file_exists(S_ROOT.'./template/'.$dir.'/image/template.gif')?'template/'.$dir.'/image/template.gif':'image/tlpicon.gif';
		$tplvalue = array('name'=> $dir, 'icon'=>$tplicon);
		if($dir == $_SCONFIG['template']) {
			$default_template = $tplvalue;
		} else {
			$templates[$dir] = $tplvalue;
		}
	}
}
$_TPL['templates'] = $templates;
$_TPL['default_template'] = $default_template;

//标签激活
$my_actives = array(in_array($_REQUEST['filter'], array('site','myapp'))?$_REQUEST['filter']:'all' => ' class="active"');
$actives = array(in_array($_REQUEST['view'], array('me','all','hot'))?$_REQUEST['view']:'we' => ' class="active"');
*/

if(empty($cp_mode)) 
{
	capi_showmessage_by_data("rest_success", 0, array('feeds'=>$feed_list, 'count'=>count($feed_list)));
	// include_once template("space_feed");
}
//筛选
function ckicon_uid($feed) {
	global $_SGLOBAL, $space, $_SCONFIG;

	if($space['filter_icon']) {
		$key = $feed['icon'].'|0';
		if(in_array($key, $space['filter_icon'])) {
			return false;
		} else {
			$key = $feed['icon'].'|'.$feed['uid'];
			if(in_array($key, $space['filter_icon'])) {
				return false;
			}
		}
	}
	return true;
}

//推荐礼物
function my_showgift() {
	global $_SGLOBAL, $space, $_SCONFIG;
	if($_SCONFIG['my_showgift'] && $_SGLOBAL['my_userapp'][$_SGLOBAL['gift_appid']]) {
		echo '<script language="javascript" type="text/javascript" src="http://gift.manyou-apps.com/recommend.js"></script>';
	}
}

?>

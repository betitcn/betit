<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space_quiz.php 13208 2009-08-20 06:31:35Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$minhot = $_SCONFIG['feedhotmin']<1?3:$_SCONFIG['feedhotmin'];

$page = empty($_GET['page'])?1:intval($_GET['page']);
if($page<1) $page=1;
$id = empty($_GET['id'])?0:intval($_GET['id']);
$classid = empty($_GET['classid'])?0:intval($_GET['classid']);
	
//表态分类
@include_once(S_ROOT.'./data/data_click.php');

$clicks = empty($_SGLOBAL['click']['quizid'])?array():$_SGLOBAL['click']['quizid'];

if($id) {
	//读取日志
	$query = $_SGLOBAL['db']->query("SELECT bf.*, b.* FROM ".tname('quiz')." b LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=b.quizid WHERE b.quizid='$id' AND b.uid='$space[uid]'");
	$quiz = $_SGLOBAL['db']->fetch_array($query);

	//日志不存在
	if(empty($quiz)) {
		showmessage('view_to_info_did_not_exist');
	}
	//检查好友权限
	if(!ckfriend($quiz['uid'], $quiz['friend'], $quiz['target_ids'])) {
		//没有权限
		include template('space_privacy');
		exit();
	} elseif(!$space['self'] && $quiz['friend'] == 4) {
		//密码输入问题 // mask
		showmessage("测试");
		$cookiename = "view_pwd_quiz_$quiz[quizid]";
		$cookievalue = empty($_SCOOKIE[$cookiename])?'':$_SCOOKIE[$cookiename];
		if($cookievalue != md5(md5($quiz['password']))) {
			$invalue = $quiz;
			include template('do_inputpwd');
			exit();
		}
	}

	//整理
	$quiz['tag'] = empty($quiz['tag'])?array():unserialize($quiz['tag']);

	//处理视频标签
	include_once(S_ROOT.'./source/function_quiz.php');
	$quiz['message'] = quiz_bbcode($quiz['message']);

	$otherlist = $newlist = array();
	//竞猜删除提醒

	if($quiz['id']==1){
	showmessage("此竞猜已被发起者删除","space.php?do=quiz");
	}

	//有效期
	if($_SCONFIG['uc_tagrelatedtime'] && ($_SGLOBAL['timestamp'] - $quiz['relatedtime'] > $_SCONFIG['uc_tagrelatedtime'])) {
		$quiz['related'] = array();
	}
	if($quiz['tag'] && empty($quiz['related'])) {
		@include_once(S_ROOT.'./data/data_tagtpl.php');

		$b_tagids = $b_tags = $quiz['related'] = array();
		$tag_count = -1;
		foreach ($quiz['tag'] as $key => $value) {
			$b_tags[] = $value;
			$b_tagids[] = $key;
			$tag_count++;
		}
		if(!empty($_SCONFIG['uc_tagrelated']) && $_SCONFIG['uc_status']) {
			if(!empty($_SGLOBAL['tagtpl']['limit'])) {
				include_once(S_ROOT.'./uc_client/client.php');
				$tag_index = mt_rand(0, $tag_count);
				$quiz['related'] = uc_tag_get($b_tags[$tag_index], $_SGLOBAL['tagtpl']['limit']);
			}
		} else {
			//自身TAG
			$tag_quizids = array();
			$query = $_SGLOBAL['db']->query("SELECT DISTINCT quizid FROM ".tname('tagquiz')." WHERE tagid IN (".simplode($b_tagids).") AND quizid<>'$quiz[quizid]' ORDER BY quizid DESC LIMIT 0,10");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				$tag_quizids[] = $value['quizid'];
			}
			if($tag_quizids) {
				$query = $_SGLOBAL['db']->query("SELECT uid,username,subject,quizid FROM ".tname('quiz')." WHERE quizid IN (".simplode($tag_quizids).") and id!=1");
				while ($value = $_SGLOBAL['db']->fetch_array($query)) {
					realname_set($value['uid'], $value['username']);//实名
					$value['url'] = "space.php?uid=$value[uid]&do=quiz&id=$value[quizid]";
					$quiz['related'][UC_APPID]['data'][] = $value;
				}
				$quiz['related'][UC_APPID]['type'] = 'UCHOME';
			}
		}
		if(!empty($quiz['related']) && is_array($quiz['related'])) {
			foreach ($quiz['related'] as $appid => $values) {
				if(!empty($values['data']) && $_SGLOBAL['tagtpl']['data'][$appid]['template']) {
					foreach ($values['data'] as $itemkey => $itemvalue) {
						if(!empty($itemvalue) && is_array($itemvalue)) {
							$searchs = $replaces = array();
							foreach (array_keys($itemvalue) as $key) {
								$searchs[] = '{'.$key.'}';
								$replaces[] = $itemvalue[$key];
							}
							$quiz['related'][$appid]['data'][$itemkey]['html'] = stripslashes(str_replace($searchs, $replaces, $_SGLOBAL['tagtpl']['data'][$appid]['template']));
						} else {
							unset($quiz['related'][$appid]['data'][$itemkey]);
						}
					}
				} else {
					$quiz['related'][$appid]['data'] = '';
				}
				if(empty($quiz['related'][$appid]['data'])) {
					unset($quiz['related'][$appid]);
				}
			}
		}
		updatetable('quizfield', array('related'=>addslashes(serialize(sstripslashes($quiz['related']))), 'relatedtime'=>$_SGLOBAL['timestamp']), array('quizid'=>$quiz['quizid']));//更新
	} else {
		$quiz['related'] = empty($quiz['related'])?array():unserialize($quiz['related']);
	}

	//作者的其他最新日志
	$otherlist = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE uid='$space[uid]' and id!=1 ORDER BY dateline DESC LIMIT 0,6");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($value['quizid'] != $quiz['quizid'] && empty($value['friend'])) {
			$otherlist[] = $value;
		}
	}

	//最新的日志
	$newlist = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE hot>=3 and id!=1 ORDER BY dateline DESC LIMIT 0,6");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if($value['quizid'] != $quiz['quizid'] && empty($value['friend'])) {
			realname_set($value['uid'], $value['username']);
			$newlist[] = $value;
		}
	}

	//评论
	$perpage = 30;
	$perpage = mob_perpage($perpage);
	
	$start = ($page-1)*$perpage;

	//检查开始数
	ckstart($start, $perpage);

	$count = $quiz['replynum'];

	$list = array();
	if($count) {
		$cid = empty($_GET['cid'])?0:intval($_GET['cid']);
		$csql = $cid?"cid='$cid' AND":'';

		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE $csql id='$id' AND idtype='quizid' ORDER BY dateline LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['authorid'], $value['author']);//实名
			$list[] = $value;
		}
	}

	//分页
	$multi = multi($count, $perpage, $page, "space.php?uid=$quiz[uid]&do=$do&id=$id", '', 'content');

	//访问统计
	if(!$space['self'] && $_SCOOKIE['view_quizid'] != $quiz['quizid']) {
		$_SGLOBAL['db']->query("UPDATE ".tname('quiz')." SET viewnum=viewnum+1 WHERE quizid='$quiz[quizid]'");
		inserttable('log', array('id'=>$space['uid'], 'idtype'=>'uid'));//延迟更新
		ssetcookie('view_quizid', $quiz['quizid']);
	}

	//表态
	$hash = md5($quiz['uid']."\t".$quiz['dateline']);
	$id = $quiz['quizid'];
	$idtype = 'quizid';

	foreach ($clicks as $key => $value) {
		$value['clicknum'] = $quiz["click_$key"];
		$value['classid'] = mt_rand(1, 4);
		if($value['clicknum'] > $maxclicknum) $maxclicknum = $value['clicknum'];
		$clicks[$key] = $value;
	}

	//点评
	$clickuserlist = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('clickuser')."
		WHERE id='$id' AND idtype='$idtype'
		ORDER BY dateline DESC
		LIMIT 0,18");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['uid'], $value['username']);//实名
		$value['clickname'] = $clicks[$value['clickid']]['name'];
		$clickuserlist[] = $value;
	}

	//热点
	$topic = topic_get($quiz['topicid']);

	//实名
	realname_get();

	$_TPL['css'] = 'poll'; // mask
	
	//限制投票
	$allowedvote = true;
	
	//限制性别
	//if(!empty($quiz['sex']) && $poll['sex'] != $_SGLOBAL['member']['sex']) {
	//	$allowedvote = false;
	//}
	$expiration = false;
	
	//过期同样禁止投票
	if($quiz['endtime'] && $quiz['endtime'] < $_SGLOBAL['timestamp']) {
		$allowedvote = false;
		$expiration = true;
		/*if(empty($poll['summary']) && !$poll['notify']) {
			@include_once(S_ROOT.'./source/function_cp.php');
			$note = cplang('note_poll_finish', array("space.php?uid=$poll[uid]&do=poll&pid=$poll[pid]", $poll['subject']));
			$supe_uid = $_SGLOBAL['supe_uid'];
			$supe_username = $_SGLOBAL['supe_username'];
			$_SGLOBAL['supe_uid'] = 0;
			$_SGLOBAL['supe_username'] = '';
			notification_add($poll['uid'], 'poll', $note);
			$_SGLOBAL['supe_uid'] = $supe_uid;
			$_SGLOBAL['supe_username'] = $supe_username;
			updatetable('pollfield', array('notify'=>1), array('pid'=>$poll['pid']));
		}*/
	}
	
	$hasvoted = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('quizuser')."  WHERE uid='$_SGLOBAL[supe_uid]' AND quizid='$id' "),0);

	if ($hasvoted<$quiz['portion'])
	{
		$canvote = 1;
	}else{
		$canvote = 0;
	}
	
	//总投票数
	$allvote = 0;
	
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE quizid='$id' ORDER BY oid");
	while( $value = $_SGLOBAL['db']->fetch_array($query))
	{
		$allvote += intval($value['votenum']);
		if ($value['picid']){
			$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE picid='$value[picid]'");
			$value2 = $_SGLOBAL['db']->fetch_array($query2);
			$value['pic'] = pic_get($value2['filepath'], $value2['thumb'], $value2['remote']);
		}
		$quiz['options'][] = $value;
	
	}
	
	//计算百分比
	foreach($quiz['options'] as $key => $value) {
		if($value['votenum'] && $allvote) {
			$value['percent'] = round($value['votenum']/$allvote, 2);
			$value['width'] = round($value['percent']*160);
			$value['percent'] = $value['percent']*100;
		} else {
			$value['width'] = $value['percent'] = 0;
		}
		$quiz['options'][$key] = $value;
	}
	
	$isfriend = 1;
	if($quiz['noreply']) {
		//是否好友
		$isfriend = $space['self'];
		if($space['friends'] && in_array($_SGLOBAL['supe_uid'], $space['friends'])) {
			$isfriend = 1;//是好友
		}
	}
	
		//取出最新投票
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." where id!=1 ORDER BY dateline DESC LIMIT 0, 10");
	while($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['uid'], $value['username']);//实名
		$newquiz[] = $value;
	}
	
	//取出最热的投票
	$timerange = $_SGLOBAL['timestamp']-2592000;
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE lastvote >= '$timerange' and id!=1 ORDER BY voternum DESC LIMIT 0, 10");
	while($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['uid'], $value['username']);//实名
		$hotquiz[] = $value;
	}
	
	//相关热点
	$topic = topic_get($quiz['topicid']);
	
	//实名
	realname_get();
	
	include_once template("space_quiz_view2");

} else {
	//分页
	/*$perpage = 10;
	$perpage = mob_perpage($perpage);
	
	$start = ($page-1)*$perpage;

	//检查开始数
	ckstart($start, $perpage);

	//摘要截取
	$summarylen = 300;

	$classarr = array();
	$list = array();
	$userlist = array();
	$count = $pricount = 0;

	$ordersql = 'b.dateline';

	if(empty($_GET['view']) && ($space['friendnum']<$_SCONFIG['showallfriendnum'])) {
		$_GET['view'] = 'all';//默认显示
	}

	//处理查询
	$f_index = '';
	if($_GET['view'] == 'click') {
		//踩过的日志
		$theurl = "space.php?uid=$space[uid]&do=$do&view=click";
		$actives = array('click'=>' class="active"');

		$clickid = intval($_GET['clickid']);
		if($clickid) {
			$theurl .= "&clickid=$clickid";
			$wheresql = " AND c.clickid='$clickid'";
			$click_actives = array($clickid => ' class="current"');
		} else {
			$wheresql = '';
			$click_actives = array('all' => ' class="current"');
		}

		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('clickuser')." c WHERE c.uid='$space[uid]' AND c.idtype='quizid' $wheresql"),0);
		if($count) {
			$query = $_SGLOBAL['db']->query("SELECT b.*, bf.message, bf.target_ids, bf.magiccolor FROM ".tname('clickuser')." c
				LEFT JOIN ".tname('quiz')." b ON b.quizid=c.id
				LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=c.id
				WHERE c.uid='$space[uid]' AND c.idtype='quizid' $wheresql
				ORDER BY c.dateline DESC LIMIT $start,$perpage");
		}
	} else {
		
		if($_GET['view'] == 'all') {
			//大家的日志
			$wheresql = '1';

			$actives = array('all'=>' class="active"');

			//排序
			$orderarr = array('dateline','replynum','viewnum','hot');
			foreach ($clicks as $value) {
				$orderarr[] = "click_$value[clickid]";
			}
			if(!in_array($_GET['orderby'], $orderarr)) $_GET['orderby'] = '';

			//时间
			$_GET['day'] = intval($_GET['day']);
			$_GET['hotday'] = 7;

			if($_GET['orderby']) {
				$ordersql = 'b.'.$_GET['orderby'];

				$theurl = "space.php?uid=$space[uid]&do=quiz&view=all&orderby=$_GET[orderby]";
				$all_actives = array($_GET['orderby']=>' class="current"');

				if($_GET['day']) {
					$_GET['hotday'] = $_GET['day'];
					$daytime = $_SGLOBAL['timestamp'] - $_GET['day']*3600*24;
					$wheresql .= " AND b.dateline>='$daytime'";

					$theurl .= "&day=$_GET[day]";
					$day_actives = array($_GET['day']=>' class="active"');
				} else {
					$day_actives = array(0=>' class="active"');
				}
			} else {

				$theurl = "space.php?uid=$space[uid]&do=$do&view=all";

				$wheresql .= " AND b.hot>='$minhot'";
				$all_actives = array('all'=>' class="current"');
				$day_actives = array();
			}


		} else {
			
			if(empty($space['feedfriend']) || $classid) $_GET['view'] = 'me';
			
			if($_GET['view'] == 'me') {
				//查看个人的
				$wheresql = "b.uid='$space[uid]'";
				$theurl = "space.php?uid=$space[uid]&do=$do&view=me";
				$actives = array('me'=>' class="active"');
				//日志分类
				$query = $_SGLOBAL['db']->query("SELECT classid, classname FROM ".tname('class')." WHERE uid='$space[uid]'");
				while ($value = $_SGLOBAL['db']->fetch_array($query)) {
					$classarr[$value['classid']] = $value['classname'];
				}
			} else {
				$wheresql = "b.uid IN ($space[feedfriend])";
				$theurl = "space.php?uid=$space[uid]&do=$do&view=we";
				$f_index = 'USE INDEX(dateline)';
	
				$fuid_actives = array();
	
				//查看指定好友的
				$fusername = trim($_GET['fusername']);
				$fuid = intval($_GET['fuid']);
				if($fusername) {
					$fuid = getuid($fusername);
				}
				if($fuid && in_array($fuid, $space['friends'])) {
					$wheresql = "b.uid = '$fuid'";
					$theurl = "space.php?uid=$space[uid]&do=$do&view=we&fuid=$fuid";
					$f_index = '';
					$fuid_actives = array($fuid=>' selected');
				}
	
				$actives = array('we'=>' class="active"');
	
				//好友列表
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('friend')." WHERE uid='$space[uid]' AND status='1' ORDER BY num DESC, dateline DESC LIMIT 0,500");
				while ($value = $_SGLOBAL['db']->fetch_array($query)) {
					realname_set($value['fuid'], $value['fusername']);
					$userlist[] = $value;
				}
			}
		}

		//分类
		if($classid) {
			$wheresql .= " AND b.classid='$classid'";
			$theurl .= "&classid=$classid";
		}

		//设置权限
		$_GET['friend'] = intval($_GET['friend']);
		if($_GET['friend']) {
			$wheresql .= " AND b.friend='$_GET[friend]'";
			$theurl .= "&friend=$_GET[friend]";
		}

		//搜索
		if($searchkey = stripsearchkey($_GET['searchkey'])) {
			$wheresql .= " AND b.subject LIKE '%$searchkey%'";
			$theurl .= "&searchkey=$_GET[searchkey]";
			cksearch($theurl);
		}
		
		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('quiz')." b WHERE $wheresql"),0);
		//更新统计
		if($wheresql == "b.uid='$space[uid]'" && $space['quiznum'] != $count) {
			updatetable('space', array('quiznum' => $count), array('uid'=>$space['uid']));
		}
		if($count) {
			$query = $_SGLOBAL['db']->query("SELECT bf.message, bf.target_ids, bf.magiccolor, b.* FROM ".tname('quiz')." b $f_index
				LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=b.quizid
				WHERE $wheresql
				ORDER BY $ordersql DESC LIMIT $start,$perpage");
		}
	}

	if($count) {
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if(ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
				realname_set($value['uid'], $value['username']);
				if($value['friend'] == 4) {
					$value['message'] = $value['pic'] = '';
				} else {
					$value['message'] = getstr($value['message'], $summarylen, 0, 0, 0, 0, -1);
				}
				if($value['pic']) $value['pic'] = pic_cover_get($value['pic'], $value['picflag']);
				$list[] = $value;
			} else {
				$pricount++;
			}
		}
	}

	//分页
	$multi = multi($count, $perpage, $page, $theurl);

	//实名
	realname_get();

	$_TPL['css'] = 'blog';
	*/

	$_GET['view'] = $_GET['view'] ? trim($_GET['view']) : 'new';
	if($_GET['view'] == 'all') $_GET['view'] = 'new';
	//分页
	$perpage = 30;
	$start = ($page-1)*$perpage;
	
	//检查开始数

	
	$wherearr = $list = array();
	$userlist = array();
	$count = $pricount = 0;
	$wheresql = $indexsql = $leftsql = '';
	$ordersql = 'p.dateline';
	$counttable = tname('quiz').' p ';
	
	if($_GET['view'] == 'new') {
		$indexsql = 'USE INDEX (dateline)';
		$theurl = "space.php?uid=$space[uid]&do=$do&view=new";
		$wherearr[] = "p.id!=1";
	} elseif($_GET['view'] == 'hot') {
		$wherearr[] = "p.id!=1";
		$_GET['filtrate'] = empty($_GET['filtrate']) ? 'all' : trim($_GET['filtrate']);
		$indexsql = 'USE INDEX (voternum)';
		$ordersql = 'p.voternum';
		$timerange = 0;
		if($_GET['filtrate']=='week') {
			$timerange = $_SGLOBAL['timestamp']-604800;
		} elseif($_GET['filtrate']=='month') {
			$timerange = $_SGLOBAL['timestamp']-2592000;
		}
		if($timerange) {
			$wherearr[] = "p.lastvote >= '$timerange'";
		}
		$filtrate = array($_GET['filtrate']=>' class="active"');
		$theurl = "space.php?uid=$space[uid]&do=$do&view=hot";
		
	} elseif($_GET['view'] == 'friend') {
		
		$indexsql = 'USE INDEX (dateline)';
		$wherearr[] = "p.uid IN ($space[feedfriend]) AND p.id!=1";
		$theurl = "space.php?uid=$space[uid]&do=$do&view=friend";
		
	} else {
		
		$_GET['filtrate'] = empty($_GET['filtrate']) ? 'me' : trim($_GET['filtrate']);
		
		if($_GET['filtrate'] == 'join') {
			$leftsql = tname('quizuser')." pu LEFT JOIN ";
			
			$indexsql = ' ON p.quizid=pu.quizid ';
			
			$wherearr[] = "pu.uid='$space[uid]' and p.id!=1";
			$ordersql = 'p.dateline';
			$counttable = tname('quizuser').' pu, '.tname('quiz').' p';

		} elseif($_GET['filtrate'] == 'expiration') {
			$counttable = tname('quizuser').' pu, '.tname('quiz').' p';
			$ordersql = 'p.dateline';
			$wherearr[] = "p.uid='$space[uid]' AND pu.quizid=p.quizid  AND p.endtime>0 AND p.endtime<='$_SGLOBAL[timestamp]'";
		} else {
			$wherearr[] = "p.uid='$space[uid]' and p.id!=1";
		}
		
		$filtrate = array($_GET['filtrate']=>' class="active"');
		$theurl = "space.php?uid=$space[uid]&do=$do&view=me&filtrate=".$_GET['filtrate'];
		
	}
	
	//搜索
	if($searchkey = stripsearchkey($_GET['searchkey'])) {
		$query3 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid=1");
		while($value3 = $_SGLOBAL['db']->fetch_array($query3)){
		foreach($value3 as $key => $val) {
			$searcharr[] = intval($val);
			}
        }
		$wherearr[] = "p.subject LIKE '%$searchkey%'&&p.uid IN('".implode("','",  $searcharr)."')";
		$theurl .= "&searchkey=$_GET[searchkey]";
		cksearch($theurl);
		

	}
		
	if($wherearr) {
		$wheresql = ' WHERE '.implode(' AND ', $wherearr);
		
	}
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM $counttable $wheresql"),0);
    
	//更新统计
	if($wheresql == "p.uid='$space[uid]'" && $space['quiznum'] != $count) {
		updatetable('space', array('quiznum' => $count), array('uid'=>$space['uid']));
	}
		
	if($count) {
		if($_GET['filtrate'] == 'expiration') {
			$query = $_SGLOBAL['db']->query("SELECT p.*,pf.* FROM ".tname('quizuser')." pu, ".tname('quiz')." p,".tname('feed')." pf $wheresql AND p.quizid=pf.id AND p.id!=1	ORDER BY $ordersql DESC LIMIT $start,$perpage");
		} 
		elseif($_GET['searchkey']){
		$query = $_SGLOBAL['db']->query("SELECT p.*,pf.* FROM ".tname('quiz')." p, ".tname('feed')." pf $wheresql  AND p.quizid=pf.id  AND p.id!=1 ORDER BY $ordersql DESC LIMIT $start,$perpage");
		}else {
			$query = $_SGLOBAL['db']->query("SELECT p.*,pf.* FROM $leftsql ".tname('quiz')." p $indexsql
					LEFT JOIN ".tname('feed')." pf ON pf.id=p.quizid
					$wheresql AND p.id!=1
					ORDER BY $ordersql DESC LIMIT $start,$perpage");
		}
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$query3 = $_SGLOBAL['db']->query("SELECT uchome_quizfield.option FROM ".tname('quizfield')." WHERE quizid='$value[id]' ORDER BY quizid");
$value3=array();				
	while( $value3 = $_SGLOBAL['db']->fetch_array($query3))
	{
		
		$value['options'][]=unserialize($value3['option']);
		

		

	}
		$query4 = $_SGLOBAL['db']->query("SELECT body_data FROM ".tname('feed')." WHERE id='$value[id]' ORDER BY id");
$value3=array();				
	while( $value4 = $_SGLOBAL['db']->fetch_array($query4))
	{
		
		$value['votenum'][]=unserialize($value4['body_data']);
		



	}	
			if($value['credit'] && $value['percredit'] && $value['credit'] < $value['percredit']) {
				$value['percredit'] = $value['credit'];
			}
			realname_set($value['uid'], $value['username']);
			$value['option'] = unserialize($value['option']);
			$value['optioncount'] = array();
			foreach($value['option'] as $key=>$value2){
				
				$count2 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('quizuser')." q WHERE q.option='\"$value2\"' AND quizid='$value[quizid]'"), 0);
				$value['optioncount'][$key] = $count2;
			}
			$list[] = $value;
			$userlist[$value['uid']] = $value['username'];
		}
	}
	
	//分页
	$multi = multi($count, $perpage, $page, $theurl);

	//实名
	realname_get();
	
	$actives = array($_GET['view']=>' class="active"');

	$_TPL['css'] = 'poll';
	include_once template("space_quiz_list2");
}

?>

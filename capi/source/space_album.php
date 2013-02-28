<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space_album.php 13206 2009-08-20 02:31:30Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$minhot = $_SCONFIG['feedhotmin']<1?3:$_SCONFIG['feedhotmin'];

$id = empty($_REQUEST['id'])?0:intval($_REQUEST['id']);
$picid = empty($_REQUEST['picid'])?0:intval($_REQUEST['picid']);

$page = empty($_REQUEST['page'])?1:intval($_REQUEST['page']);
if($page<1) $page=1;

//表态分类
@include_once(S_ROOT.'./data/data_click.php');
$clicks = empty($_SGLOBAL['click']['picid'])?array():$_SGLOBAL['click']['picid'];

if($id) {
	//图片列表
	$perpage = 20;
	$perpage = mob_perpage($perpage);
	
	$start = ($page-1)*$perpage;
	
	//检查开始数
	ckstart($start, $perpage);

	//查询相册
	if($id > 0) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('album')." WHERE albumid='$id' AND uid='$space[uid]' LIMIT 1");
		$album = $_SGLOBAL['db']->fetch_array($query);
		//相册不存在
		if(empty($album)) {
			capi_showmessage_by_data('to_view_the_photo_does_not_exist');
		}

		//检查好友权限
		ckfriend_album($album);

		//查询
		$wheresql = "albumid='$id'";
		$count = $album['picnum'];
	} else {
		//默认相册
		$wheresql = "albumid='0' AND uid='$space[uid]'";
		$count = getcount('pic', array('albumid'=>0, 'uid'=>$space['uid']));

		$album = array(
			'uid' => $space['uid'],
			'albumid' => -1,
			'albumname' => lang('default_albumname'),
			'picnum' => $count
		);
	}

	//图片列表
	$list = array();
	if($count) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE $wheresql ORDER BY dateline DESC LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			$value['pic'] = pic_get($value['filepath'], $value['thumb'], $value['remote']);
			$list[] = $value;
		}
	}
	//分页
	$multi = multi($count, $perpage, $page, "space.php?uid=$album[uid]&do=$do&id=$id");

	$_TPL['css'] = 'album';
	include_once template("space_album_view");

} elseif ($picid) {

	if(empty($_REQUEST['goto'])) $_REQUEST['goto'] = '';

	$eventid = intval($eventid);
	if(empty($eventid)) {
		//检索图片
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE picid='$picid' AND uid='$space[uid]' LIMIT 1");
		$pic = $_SGLOBAL['db']->fetch_array($query);
	}
	
	if($_REQUEST['goto']=='up') {
		//上一张
		if($eventid) {
			$query = $_SGLOBAL['db']->query("SELECT pic.*, ep.* FROM ".tname('eventpic')." ep LEFT JOIN ".tname("pic")." pic ON ep.picid = pic.picid WHERE ep.eventid='$eventid' AND ep.picid > '$pic[picid]' ORDER BY ep.picid ASC LIMIT 0,1");
			if(!$newpic = $_SGLOBAL['db']->fetch_array($query)) {
				//到头转到最后一张
				$query = $_SGLOBAL['db']->query("SELECT pic.*, ep.* FROM ".tname('eventpic')." ep LEFT JOIN ".tname("pic")." pic ON ep.picid = pic.picid WHERE ep.eventid='$eventid' ORDER BY ep.picid ASC LIMIT 1");
				$pic = $_SGLOBAL['db']->fetch_array($query);
			} else {
				$pic = $newpic;
			}
		} else {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE albumid='$pic[albumid]' AND uid='$space[uid]' AND picid>$picid ORDER BY picid LIMIT 1");
			if(!$newpic = $_SGLOBAL['db']->fetch_array($query)) {
				//到头转到最早的一张
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE albumid='$pic[albumid]' AND uid='$space[uid]' ORDER BY picid LIMIT 1");
				$pic = $_SGLOBAL['db']->fetch_array($query);
			} else {
				$pic = $newpic;
			}
		}
	} elseif($_REQUEST['goto']=='down') {
		//下一张
		if($eventid) {
			$query = $_SGLOBAL['db']->query("SELECT pic.*, ep.* FROM ".tname('eventpic')." ep LEFT JOIN ".tname("pic")." pic ON ep.picid = pic.picid WHERE ep.eventid='$eventid' AND ep.picid < '$pic[picid]' ORDER BY ep.picid DESC LIMIT 0,1");
			if(!$newpic = $_SGLOBAL['db']->fetch_array($query)) {
				//到头转到第一张
				$query = $_SGLOBAL['db']->query("SELECT pic.*, ep.* FROM ".tname('eventpic')." ep LEFT JOIN ".tname("pic")." pic ON ep.picid = pic.picid WHERE ep.eventid='$eventid' ORDER BY ep.picid DESC LIMIT 1");
				$pic = $_SGLOBAL['db']->fetch_array($query);
			} else {
				$pic = $newpic;
			}
		} else {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE albumid='$pic[albumid]' AND uid='$space[uid]' AND picid<$picid ORDER BY picid DESC LIMIT 1");
			if(!$newpic = $_SGLOBAL['db']->fetch_array($query)) {
				//到头转到最新的一张
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE albumid='$pic[albumid]' AND uid='$space[uid]' ORDER BY picid DESC LIMIT 1");
				$pic = $_SGLOBAL['db']->fetch_array($query);
			} else {
				$pic = $newpic;
			}
		}
	}
	
	$picid = $pic['picid'];

	//图片不存在
	if(empty($picid)) {
		capi_showmessage_by_data('view_images_do_not_exist');
	}
	
	if($eventid) {
		$theurl = "space.php?do=event&id=$eventid&view=pic&picid=$picid";
	} else {
		$theurl = "space.php?uid=$pic[uid]&do=$do&picid=$picid";
	}

	//获取相册
	$album = array();
	if($pic['albumid']) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('album')." WHERE albumid='$pic[albumid]'");
		if(!$album = $_SGLOBAL['db']->fetch_array($query)) {
			updatetable('pic', array('albumid'=>0), array('albumid'=>$pic['albumid']));//相册丢失?
		}
	}

	if($album) {
		if($eventid) {
			//活动图片
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("eventpic")." WHERE eventid='$eventid' AND picid='$picid'");
			if (!$eventpic = $_SGLOBAL['db']->fetch_array($query)) {
				capi_showmessage_by_data('pic_not_share_to_event');// 图片没有共享到活动
			}
			$album['picnum'] = $piccount;
		} else {
			//相册好友权限
			ckfriend_album($album);	
		}
	} else {
		$album['picnum'] = getcount('pic', array('uid'=>$pic['uid'], 'albumid'=>0));
		$album['albumid'] = $pic['albumid'] = '-1';
	}
	
	if($album['picnum']) {
		//当前张数
		if($_REQUEST['goto']=='down') {
			$sequence = empty($_SCOOKIE['pic_sequence'])?$album['picnum']:intval($_SCOOKIE['pic_sequence']);
			$sequence++;
			if($sequence>$album['picnum']) {
				$sequence = 1;
			}
		} elseif($_REQUEST['goto']=='up') {
			$sequence = empty($_SCOOKIE['pic_sequence'])?$album['picnum']:intval($_SCOOKIE['pic_sequence']);
			$sequence--;
			if($sequence<1) {
				$sequence = $album['picnum'];
			}
		} else {
			$sequence = 1;
		}
		ssetcookie('pic_sequence', $sequence);
	}

	//图片地址
	$pic['pic'] = pic_get($pic['filepath'], $pic['thumb'], $pic['remote'], 0);
	$pic['size'] = formatsize($pic['size']);

	//图片的EXIF信息
	$exifs = array();
	$allowexif = function_exists('exif_read_data');
	if(isset($_REQUEST['exif']) && $allowexif) {
		include_once(S_ROOT.'./source/function_exif.php');
		$exifs = getexif($pic['pic']);
	}

	//图片评论
	$perpage = 50;
	$perpage = mob_perpage($perpage);
	
	$start = ($page-1)*$perpage;
	
	//检查开始数
	ckstart($start, $perpage);

	$cid = empty($_REQUEST['cid'])?0:intval($_REQUEST['cid']);
	$csql = $cid?"cid='$cid' AND":'';
	$siteurl = getsiteurl();
	$list = array();
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('comment')." WHERE $csql id='$pic[picid]' AND idtype='picid'"),0);
	if($count) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE $csql id='$pic[picid]' AND idtype='picid' ORDER BY dateline LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['authorid'], $value['author']);
			$value['author'] = capi_realname($value['authorid']);
			$list[] = $value;
		}
	}

	//分页
	$multi = multi($count, $perpage, $page, $theurl, '', 'pic_comment');

	//标题
	if(empty($album['albumname'])) $album['albumname'] = lang('default_albumname');

	//图片全路径
	$pic_url = $pic['pic'];
	if(!preg_match("/^http\:\/\/.+?/i", $pic['pic'])) {
		$pic_url = getsiteurl().$pic['pic'];
	}
	$pic_url2 = rawurlencode($pic['pic']);

	//访问统计
	if(!$space['self']) {
		inserttable('log', array('id'=>$space['uid'], 'idtype'=>'uid'));//延迟更新
	}
	
	//是否活动照片
	if(!$eventid) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("eventpic")." ep LEFT JOIN ".tname("event")." e ON ep.eventid=e.eventid WHERE ep.picid='$picid'");
		$event = $_SGLOBAL['db']->fetch_array($query);
	}
	
	//表态
	$hash = md5($pic['uid']."\t".$pic['dateline']);
	$id = $pic['picid'];
	$idtype = 'picid';
	
	foreach ($clicks as $key => $value) {
		$value['clicknum'] = $pic["click_$key"];
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
		$value['username'] = capi_realname($value['uid']);
		$value['clickname'] = $clicks[$value['clickid']]['name'];
		$clickuserlist[] = $value;
	}
	
	//热闹
	$topic = topic_get($pic['topicid']);
	
	if(empty($eventid)) {
		//实名
		realname_get();

		$_TPL['css'] = 'album';
		include_once template("space_album_pic");
	}

} else {
	//相册列表
	$perpage = 12;
	$perpage = mob_perpage($perpage);
	
	$start = ($page-1)*$perpage;
	
	//检查开始数
	ckstart($start, $perpage);

	//权限过滤
	$_REQUEST['friend'] = intval($_REQUEST['friend']);

	//处理查询
	$default = array();
	$f_index = '';
	$list = array();
	$pricount = 0;
	$picmode = 0;
	$dateline = empty($_REQUEST['dateline'])?0:intval($_REQUEST['dateline']);
	$queryop = $_REQUEST['queryop'];

	if(empty($_REQUEST['view']) && ($space['friendnum']<$_SCONFIG['showallfriendnum'])) {
		$_REQUEST['view'] = 'all';//默认显示
	}
	
	if($_REQUEST['view'] == 'click') {
		
		//表态的图片
		$theurl = "space.php?uid=$space[uid]&do=$do&view=click";
		$actives = array('click'=>' class="active"');
		
		$clickid = intval($_REQUEST['clickid']);
		if($clickid) {
			$theurl .= "&clickid=$clickid";
			$wheresql = " AND c.clickid='$clickid'";
			$click_actives = array($clickid => ' class="current"');
		} else {
			$wheresql = '';
			$click_actives = array('all' => ' class="current"');
		}

		$picmode = 1;
		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('clickuser')." c WHERE c.uid='$space[uid]' AND c.idtype='picid' $wheresql"),0);
		if($count) {
			$query = $_SGLOBAL['db']->query("SELECT p.*,a.albumname, a.username, c.clickid FROM ".tname('clickuser')." c
				LEFT JOIN ".tname('pic')." p ON p.picid=c.id
				LEFT JOIN ".tname('album')." a ON a.albumid=p.albumid
				WHERE c.uid='$space[uid]' AND c.idtype='picid' $wheresql
				ORDER BY c.dateline DESC LIMIT $start,$perpage");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				realname_set($value['uid'], $value['username']);
				$value['username'] = capi_realname($value['uid']);
				$value['pic'] = pic_get($value['filepath'], $value['thumb'], $value['remote']);
				$list[] = $value;
			}
		}

	} elseif($_REQUEST['view'] == 'all') {
		
		//大家的相册
		$theurl = "space.php?uid=$space[uid]&do=$do&view=all";
		$actives = array('all'=>' class="active"');
		
		$wheresql = '1';
		
		//排序
		$orderarr = array('hot','dateline');
		foreach ($clicks as $value) {
			$orderarr[] = "click_$value[clickid]";
		}
		if(!in_array($_REQUEST['orderby'], $orderarr)) $_REQUEST['orderby'] = '';
		
		//时间
		$_REQUEST['day'] = intval($_REQUEST['day']);
		$_REQUEST['hotday'] = 7;
		
		if($_REQUEST['orderby'] == 'dateline') {
			
			$all_actives = array('dateline'=>' class="current"');
			$day_actives = array();
			$theurl = "space.php?uid=$space[uid]&do=album&view=all&orderby=$_REQUEST[orderby]";
			
		} else {
			
			if ($_REQUEST['orderby']) {
				$ordersql = 'p.'.$_REQUEST['orderby'];
				
				$theurl = "space.php?uid=$space[uid]&do=album&view=all&orderby=$_REQUEST[orderby]";
				$all_actives = array($_REQUEST['orderby']=>' class="current"');
				
				if($_REQUEST['day']) {
					$_REQUEST['hotday'] = $_REQUEST['day'];
					$daytime = $_SGLOBAL['timestamp'] - $_REQUEST['day']*3600*24;
					$wheresql .= " AND p.dateline>='$daytime'";
					
					$theurl .= "&day=$_REQUEST[day]";
					$day_actives = array($_REQUEST['day']=>' class="active"');
				} else {
					$day_actives = array(0=>' class="active"');
				}
			} else {
				$ordersql = 'p.dateline';
				$wheresql .= " AND p.hot>='$minhot'";
				
				$theurl = "space.php?uid=$space[uid]&do=album&view=all";
				$all_actives = array('all'=>' class="current"');
			}
			
			$picmode = 1;
			$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('pic')." p WHERE $wheresql"),0);
			if($count) {
				$query = $_SGLOBAL['db']->query("SELECT p.*, a.username, a.albumname, a.friend, a.target_ids FROM ".tname('pic')." p
					LEFT JOIN ".tname('album')." a ON a.albumid=p.albumid
					WHERE $wheresql
					ORDER BY $ordersql DESC
					LIMIT $start,$perpage");
				while ($value = $_SGLOBAL['db']->fetch_array($query)) {
					if($value['friend'] != 4 && ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
						realname_set($value['uid'], $value['username']);
						$value['username'] = capi_realname($value['uid']);
						$value['pic'] = pic_get($value['filepath'], $value['thumb'], $value['remote']);
						$list[] = $value;
					} else {
						$pricount++;
					}
				}
			}
	
		}

	} else {
		
		if(empty($space['feedfriend'])) $_REQUEST['view'] = 'me';
		
		if($_REQUEST['view'] == 'me') {
		
			$wheresql = "uid='$space[uid]'";
			$theurl = "space.php?uid=$space[uid]&do=$do&view=me";
			$actives = array('me'=>' class="active"');
		} else {
			
			$wheresql = "uid IN ($space[feedfriend])";
			$theurl = "space.php?uid=$space[uid]&do=$do&view=we";
			$f_index = 'USE INDEX(updatetime)';
			$actives = array('we'=>' class="active"');
			
			$fuid_actives = array();
			
			//查看指定好友的
			$fusername = trim($_REQUEST['fusername']);
			$fuid = intval($_REQUEST['fuid']);
			if($fusername) {
				$fuid = getuid($fusername);
			}
			if($fuid && in_array($fuid, $space['friends'])) {
				$wheresql = "uid = '$fuid'";
				$theurl = "space.php?uid=$space[uid]&do=$do&fuid=$fuid";
				$f_index = '';
				$fuid_actives = array($fuid=>' selected');
			}
			
			//好友列表
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('friend')." WHERE uid='$space[uid]' AND status='1' ORDER BY num DESC, dateline DESC LIMIT 0,500");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				realname_set($value['fuid'], $value['fusername']);
				$value['fusername'] = capi_realname($value['fuid']);
				$userlist[] = $value;
			}
		}
	}

	if(empty($picmode)) {
		//设置权限
		if($_REQUEST['friend']) {
			$wheresql .= " AND friend='$_REQUEST[friend]'";
			$theurl .= "&friend=$_REQUEST[friend]";
		}
		if(empty($_REQUEST['searchkey'])||$_REQUEST['searchkey']=" "){
		capi_showmessage_by_data("searchkey is empty!",1);
		}
		//搜索
		if($searchkey = stripsearchkey($_REQUEST['searchkey'])) {
			$theurl .= "&searchkey=$_REQUEST[searchkey]";
			$count1 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('pic')." WHERE title LIKE '%$searchkey%'"),0);
		
			if($count1) {
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')."  WHERE  title LIKE '%$searchkey%' ORDER BY dateline DESC LIMIT $start,$perpage");
			while ($value1 = $_SGLOBAL['db']->fetch_array($query)) {
				realname_set($value1['uid'], $value1['username']);
				$value['username'] = capi_realname($value['uid']);
				if($value1) {
					$value2['uid']=$value1['uid'];
					$value2['picid'] = $value1['picid'];
					$value2['albumid'] = $value1['albumid'];
					$value2['pic'] = pic_cover_get($value1['filepath'], 2);
				
				} else {
					$value2['pic1'] = 'image/nopublish.jpg';
				}
				$list1[] = $value2;
			}
		}
$multi1 = multi($count1, $perpage, $page, $theurl);
		}
			
		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('album')." WHERE $wheresql"),0);
		if ($dateline){
		if ($queryop=="up"){
			$wheresql .= " AND dateline > '$dateline'";
		}else{
			$wheresql .= " AND dateline < '$dateline'";
		}
	}
		//更新统计
		if($wheresql == "uid='$space[uid]'" && $space['albumnum'] != $count) {
			updatetable('space', array('albumnum' => $count), array('uid'=>$space['uid']));
		}
		
		if($count) {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('album')." $f_index WHERE $wheresql ORDER BY updatetime DESC LIMIT $start,$perpage");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				realname_set($value['uid'], $value['username']);
				$value['username'] = capi_realname($value['uid']);
				if($value['friend'] != 4 && ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
					$value['pic'] = pic_cover_get($value['pic'], $value['picflag']);
				} else {
					$value['pic'] = 'image/nopublish.jpg';
				}
				$list[] = $value;

			}
			
		}
		
	}
	
	//分页
	$multi = multi($count, $perpage, $page, $theurl);

	//实名
	realname_get();
	$plist = array();
	foreach($list1 as $key=>$value) {
		if ($value["quizid"]){
			$value['name'] = capi_realname($value['uid']);
			$value['username'] = capi_realname($value['uid']);
			if($value['endtime'] && $value['endtime'] < $_SGLOBAL['timestamp']) {
					if ( intval($value["keyoid"]) == 0)
						$value["hasexceed"] = 1;
					else
						$value["hasexceed"] = 0;
			}else
			{
				$value["hasexceed"] = 0;
			}
			$plist[]= $value;
		}
	}

	$_TPL['css'] = 'album';
	if(!$searchkey)
		capi_showmessage_by_data("rest_success", 0, array('quizs'=>$list, 'count'=>count($list)));
	else
		capi_showmessage_by_data("rest_success", 0, array('quizs'=>$list1, 'count'=>count($list1), 'reward'=>$searchreward));
	include_once template("space_album_list");
}

//检查好友权限
function ckfriend_album($album) {
	global $_SGLOBAL, $_SC, $_SCONFIG, $_SCOOKIE, $space, $_SN;

	if(!ckfriend($album['uid'], $album['friend'], $album['target_ids'])) {
		//没有权限
		include template('space_privacy');
		exit();
	} elseif(!$space['self'] && $album['friend'] == 4) {
		//密码输入问题
		$cookiename = "view_pwd_album_$album[albumid]";
		$cookievalue = empty($_SCOOKIE[$cookiename])?'':$_SCOOKIE[$cookiename];
		if($cookievalue != md5(md5($album['password']))) {
			$invalue = $album;
			include template('do_inputpwd');
			exit();
		}
	}
}

?>

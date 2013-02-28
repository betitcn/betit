<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_credit.php 12210 2009-05-21 07:05:38Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//��ҳ
$perpage = $_REQUEST['perpage']?$_REQUEST['perpage']:20;
$page = empty($_REQUEST['page'])?1:intval($_REQUEST['page']);
if($page<1) $page=1;
$start = ($page-1)*$perpage;
if(empty($_SCONFIG['networkpage'])) $start = 0;

//��鿪ʼ��
ckstart($start, $perpage);

//��ͨ���ģʽ
$cache_file = '';
$cache_time = $_SCONFIG['topcachetime'];
if($cache_time<5) $cache_time = 5;
$fuids = array();
$count = 0;
$now_pos = 0;

if(!in_array($_REQUEST['view'], array('online','mm','gg','credit','experience','friendnum','viewnum','updatetime'))) $_REQUEST['view'] = 'show';

if ($_REQUEST['view'] == 'show') {
	$c_sql = "SELECT COUNT(*) FROM ".tname('show');
	$sql = "SELECT space.*, field.*, main.* FROM ".tname('show')." main
		LEFT JOIN ".tname('space')." space ON space.uid=main.uid
		LEFT JOIN ".tname('spacefield')." field ON field.uid=main.uid
		ORDER BY main.credit DESC";

	//����
	if(substr($_SGLOBAL['timestamp'], -1) == '0') {
		$_SGLOBAL['db']->query("DELETE FROM ".tname('show')." WHERE credit<1");//����С��1������
	}

	//�ҵľ��ۻ���
	$space['showcredit'] = getcount('show', array('uid'=>$space['uid']), 'credit');
	$space['showcredit'] = intval($space['showcredit']);

	//�ҵ�λ��
	$cookie_name = 'space_top_'.$_REQUEST['view'];
	if($_SCOOKIE[$cookie_name]) {
		$now_pos = $_SCOOKIE[$cookie_name];
	} else {
		$now_pos = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('show')." WHERE credit>'$space[showcredit]'"), 0);
		$now_pos++;
		ssetcookie($cookie_name, $now_pos);
	}

} elseif ($_REQUEST['view'] == 'mm') {
	if($multi_mode) {
		$c_sql = "SELECT COUNT(*) FROM ".tname('spacefield')." WHERE sex='2'";
	} else {
		$count = 100;
		$cache_file = S_ROOT.'./data/cache_top_mm.txt';
	}
	$query2 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid!=1");
		while($value2 = $_SGLOBAL['db']->fetch_array($query2)){
		foreach($value2 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}
	$sql = "SELECT main.*, field.* FROM ".tname('space')." main, ".tname('spacefield')." field
		WHERE field.sex='2' AND field.uid=main.uid AND main.uid IN('".implode("','",  $searcharr)."')
		ORDER BY main.viewnum DESC";

	//�ҵ�λ��
	$cookie_name = 'space_top_'.$_REQUEST['view'];
	if($_SCOOKIE[$cookie_name]) {
		$now_pos = $_SCOOKIE[$cookie_name];
	} else {
		if($space['sex']==2) {
			$pos_sql = "SELECT COUNT(*) FROM ".tname('space')." s, ".tname('spacefield')." f WHERE s.viewnum>'$space[viewnum]' AND f.sex='2' AND f.uid=s.uid";
			$now_pos = $_SGLOBAL['db']->result($_SGLOBAL['db']->query($pos_sql), 0);
			$now_pos++;
		} else {
			$now_pos = -1;
		}
		ssetcookie($cookie_name, $now_pos);
	}

} elseif ($_REQUEST['view'] == 'gg') {
	if($multi_mode) {
		$c_sql = "SELECT COUNT(*) FROM ".tname('spacefield')." WHERE sex='1'";
	} else {
		$count = 100;
		$cache_file = S_ROOT.'./data/cache_top_gg.txt';
	}
	$query2 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid!=1");
		while($value2 = $_SGLOBAL['db']->fetch_array($query2)){
		foreach($value2 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}
	$sql = "SELECT main.*, field.* FROM ".tname('space')." main, ".tname('spacefield')." field
		WHERE field.sex='1' AND field.uid=main.uid AND main.uid IN('".implode("','",  $searcharr)."')
		ORDER BY main.viewnum DESC";

	//�ҵ�λ��
	$cookie_name = 'space_top_'.$_REQUEST['view'];
	if($_SCOOKIE[$cookie_name]) {
		$now_pos = $_SCOOKIE[$cookie_name];
	} else {
		if($space['sex']==1) {
			$pos_sql = "SELECT COUNT(*) FROM ".tname('space')." s, ".tname('spacefield')." f WHERE s.viewnum>'$space[viewnum]' AND f.sex='1' AND f.uid=s.uid";
			$now_pos = $_SGLOBAL['db']->result($_SGLOBAL['db']->query($pos_sql), 0);
			$now_pos++;
		} else {
			$now_pos = -1;
		}
		ssetcookie($cookie_name, $now_pos);
	}

} elseif ($_REQUEST['view'] == 'credit') {
	if($multi_mode) {
		$c_sql = "SELECT COUNT(*) FROM ".tname('space');
	} else {
		$count = 100;
		$cache_file = S_ROOT.'./data/cache_top_credit.txt';
	}
	$query2 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid!=1");
		while($value2 = $_SGLOBAL['db']->fetch_array($query2)){
		foreach($value2 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}
	$sql = "SELECT main.*, field.* FROM ".tname('space')." main
		LEFT JOIN ".tname('spacefield')." field ON field.uid=main.uid where main.uid IN('".implode("','",  $searcharr)."')
		ORDER BY main.credit DESC";

	//�ҵ�λ��
	$cookie_name = 'space_top_'.$_REQUEST['view'];
	if($_SCOOKIE[$cookie_name]) {
		$now_pos = $_SCOOKIE[$cookie_name];
	} else {
		$pos_sql = "SELECT COUNT(*) FROM ".tname('space')." s WHERE s.credit>'$space[credit]'";
		$now_pos = $_SGLOBAL['db']->result($_SGLOBAL['db']->query($pos_sql), 0);
		$now_pos++;
		ssetcookie($cookie_name, $now_pos);
	}

} elseif ($_REQUEST['view'] == 'experience') {
	if($multi_mode) {
		$c_sql = "SELECT COUNT(*) FROM " .tname('space');
	} else {
		$count = 100;
		$cache_file = S_ROOT.'./data/cache_top_experience.txt';
	}
	$query2 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid!=1");
		while($value2 = $_SGLOBAL['db']->fetch_array($query2)){
		foreach($value2 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}
	$sql = "SELECT main.*, field.* FROM ".tname('space')." main
		LEFT JOIN ".tname('spacefield')." field ON field.uid=main.uid where main.uid IN('".implode("','",  $searcharr)."') 
		ORDER BY main.experience DESC";
	//�ҵ�λ��
	$cookie_name = 'space_top_'.$_REQUEST['view'];
	if($_SCOOKIE[$cookie_name]) {
		$now_pos = $_SCOOKIE[$cookie_name];
	} else {
		$pos_sql = "SELECT COUNT(*) FROM ".tname('space')." s WHERE s.experience>'$space[experience]'";
		$now_pos = $_SGLOBAL['db']->result($_SGLOBAL['db']->query($pos_sql), 0);
		$now_pos++;
		ssetcookie($cookie_name, $now_pos);
	}

} elseif ($_REQUEST['view'] == 'friendnum') {
	if($multi_mode) {
		$c_sql = "SELECT COUNT(*) FROM ".tname('space');
	} else {
		$count = 100;
		$cache_file = S_ROOT.'./data/cache_top_friendnum.txt';
	}
	$query2 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid!=1");
		while($value2 = $_SGLOBAL['db']->fetch_array($query2)){
		foreach($value2 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}

	$sql = "SELECT main.*, field.* FROM ".tname('space')." main
		LEFT JOIN ".tname('spacefield')." field ON field.uid=main.uid where main.uid IN('".implode("','",  $searcharr)."') 
		ORDER BY main.friendnum DESC";

	//�ҵ�λ��
	$cookie_name = 'space_top_'.$_REQUEST['view'];
	if($_SCOOKIE[$cookie_name]) {
		$now_pos = $_SCOOKIE[$cookie_name];
	} else {
		$pos_sql = "SELECT COUNT(*) FROM ".tname('space')." s WHERE s.friendnum>'$space[friendnum]'";
		$now_pos = $_SGLOBAL['db']->result($_SGLOBAL['db']->query($pos_sql), 0);
		$now_pos++;
		ssetcookie($cookie_name, $now_pos);
	}

} elseif ($_REQUEST['view'] == 'viewnum') {
	if($multi_mode) {
		$c_sql = "SELECT COUNT(*) FROM ".tname('space');
	} else {
		$count = 100;
		$cache_file = S_ROOT.'./data/cache_top_viewnum.txt';
	}
	$query2 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid!=1");
		while($value2 = $_SGLOBAL['db']->fetch_array($query2)){
		foreach($value2 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}
	$sql = "SELECT main.*, field.* FROM ".tname('space')." main
		LEFT JOIN ".tname('spacefield')." field ON field.uid=main.uid where main.uid IN('".implode("','",  $searcharr)."')
		ORDER BY main.viewnum DESC";

	//�ҵ�λ��
	$cookie_name = 'space_top_'.$_REQUEST['view'];
	if($_SCOOKIE[$cookie_name]) {
		$now_pos = $_SCOOKIE[$cookie_name];
	} else {
		$pos_sql = "SELECT COUNT(*) FROM ".tname('space')." s WHERE s.viewnum>'$space[viewnum]'";
		$now_pos = $_SGLOBAL['db']->result($_SGLOBAL['db']->query($pos_sql), 0);
		$now_pos++;
		ssetcookie($cookie_name, $now_pos);
	}

} elseif ($_REQUEST['view'] == 'online') {
	$query2 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid!=1");
		while($value2 = $_SGLOBAL['db']->fetch_array($query2)){
		foreach($value2 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}
	$c_sql = "SELECT COUNT(*) FROM ".tname('session');
	$sql = "SELECT field.*, space.*, main.*
		FROM ".tname('session')." main USE INDEX (lastactivity)
		LEFT JOIN ".tname('space')." space ON space.uid=main.uid
		LEFT JOIN ".tname('spacefield')." field ON field.uid=main.uid where main.uid IN('".implode("','",  $searcharr)."')
		ORDER BY main.lastactivity DESC";
		
		
	$now_pos = -1;
} elseif ($_REQUEST['view'] == 'updatetime') {
		$query2 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid!=1");
		while($value2 = $_SGLOBAL['db']->fetch_array($query2)){
		foreach($value2 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}
	$c_sql = "SELECT COUNT(*) FROM ".tname('space');
	$sql = "SELECT main.*, field.* FROM ".tname('space')." main USE INDEX (updatetime)
		LEFT JOIN ".tname('spacefield')." field ON field.uid=main.uid where main.uid IN('".implode("','",  $searcharr)."')
		ORDER BY main.updatetime DESC";
	$now_pos = -1;
}

$list = array();
if(empty($count)) {
	$cache_mode = false;
	$count = empty($_SCONFIG['networkpage'])?1:$_SGLOBAL['db']->result($_SGLOBAL['db']->query($c_sql),0);
	$multi = multi($count, $perpage, $page, "space.php?do=top&view=$_REQUEST[view]");
} else {
	$cache_mode = true;
	$multi = '';
	if($page<1) $page=1;
	$start = ($page-1)*$perpage;
	if(empty($_SCONFIG['networkpage'])) $start = 0;
	$page = empty($_REQUEST['page'])?1:intval($_REQUEST['page']);
	$perpage = $_REQUEST['perpage']?$_REQUEST['perpage']:20;

	if($cache_file && file_exists($cache_file) && $_SGLOBAL['timestamp'] - @filemtime($cache_file) < $cache_time*60) {
		$list_cache = sreadfile($cache_file);
		$list = unserialize($list_cache);
	}
}
	@include_once(S_ROOT.'./data/data_usergroup.php');
	$query1 = $_SGLOBAL['db']->query("$sql LIMIT $start,$perpage");
	
	while ($value = $_SGLOBAL['db']->fetch_array($query1)) {
		
		$value["avatar"] = capi_avatar($value["uid"]);
		$value["grouptitle"] = $_SGLOBAL["grouptitle"][$value["groupid"]]["grouptitle"];
		$list1[] = $value;
	}
	if($cache_mode && $cache_file) {
		swritefile($cache_file, serialize($list));
	}

capi_showmessage_by_data("rest_success", 0, array('top'=>$list1));

foreach($list as $key => $value) {
	$value['isfriend'] = ($value['uid']==$space['uid'] || ($space['friends'] && in_array($value['uid'], $space['friends'])))?1:0;
	realname_set($value['uid'], $value['username'], $value['name'], $value['namestatus']);
	$value['username'] = capi_realname($value['uid']);
	$fuids[] = $value['uid'];
	$list[$key] = $value;
}

//����״̬
$ols = array();
if($fuids) {
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." WHERE uid IN (".simplode($fuids).")");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(!$value['magichidden']) {
			$ols[$value['uid']] = $value['lastactivity'];
		} elseif ($_REQUEST['view'] == 'online' && $list[$value['uid']]) {
			unset($list[$value['uid']]);
		}
	}
}

$actives = array($_REQUEST['view'] => ' class="active"');

include_once template("space_top");

?>

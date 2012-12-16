<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_friend.php 13178 2009-08-17 02:36:39Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$op = empty($_REQUEST['op'])?'':$_REQUEST['op'];
$uid = empty($_REQUEST['uid'])?0:intval($_REQUEST['uid']);

$space['key'] = space_key($space);

$actives = array($op=>' class="active"');

if($op == 'add') {

	if(!checkperm('allowfriend')) {
		ckspacelog();
		capi_showmessage_by_data('no_privilege');
	}

	//����û�
	if($uid == $_SGLOBAL['supe_uid']) {
		capi_showmessage_by_data('friend_self_error');
	}
	
	if($space['friends'] && in_array($uid, $space['friends'])) {
		capi_showmessage_by_data('you_have_friends');
	}
	
	//ʵ����֤
	ckrealname('friend');

	$tospace = getspace($uid);
	realname_set($tospace['uid'], $tospace['username']);
	realname_get();
	if(empty($tospace)) {
		capi_showmessage_by_data('space_does_not_exist');
	}

	//������
	if(isblacklist($tospace['uid'])) {
		capi_showmessage_by_data('is_blacklist');
	}

	//�û���
	$groups = getfriendgroup();

	//�������״̬
	$status = getfriendstatus($_SGLOBAL['supe_uid'], $uid);
	if($status == 1) {
		capi_showmessage_by_data('you_have_friends');
	} else {
		//�����Ŀ
		$maxfriendnum = checkperm('maxfriendnum');
		if($maxfriendnum && $space['friendnum'] >= $maxfriendnum + $space['addfriend']) {
			if($_SGLOBAL['magic']['friendnum']) {
				capi_showmessage_by_data('enough_of_the_number_of_friends_with_magic');
			} else {
				capi_showmessage_by_data('enough_of_the_number_of_friends');
			}
		}
				
		//�Է��Ƿ���Լ���Ϊ�˺���
		$fstatus = getfriendstatus($uid, $_SGLOBAL['supe_uid']);
		if($fstatus == -1) {
			//�Է�û�мӺ��ѣ��Ҽӱ���
			if($status == -1) {
				
				//��Ƶ��֤
				if($tospace['videostatus']) {
					ckvideophoto('friend', $tospace);
				}
				
				//��ӵ������
				if(capi_submitcheck('addsubmit')) {
					$setarr = array(
						'uid' => $_SGLOBAL['supe_uid'],
						'fuid' => $uid,
						'fusername' => addslashes($tospace['username']),
						'gid' => intval($_REQUEST['gid']),
						'note' => getstr($_REQUEST['note'], 50, 1, 1),
						'dateline' => $_SGLOBAL['timestamp']
					);
					inserttable('friend', $setarr);
					
					//�����ʼ�֪ͨ
					smail($uid, '', cplang('friend_subject',array($_SN[$space['uid']], getsiteurl().'cp.php?ac=friend&amp;op=request')), '', 'friend_add');

					//���ӶԷ�����������
					$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET addfriendnum=addfriendnum+1 WHERE uid='$uid'");

					notification_add_push($uid, cplang('friend_subject',array($_SN[$space['uid']], getsiteurl().'cp.php?ac=friend&amp;op=request')));
					
					capi_showmessage_by_data('request_has_been_sent', 0);
				} else {
					// include_once template('cp_friend');
					exit();
				}
			} else {
				capi_showmessage_by_data('waiting_for_the_other_test');
			}
		} else {
			//�Է�������Ϊ���ѣ������ͨ��
			if(capi_submitcheck('add2submit')) {
				//��Ϊ����
				$gid = intval($_REQUEST['gid']);

				friend_update($space['uid'], $space['username'], $tospace['uid'], $tospace['username'], 'add', $gid);

				//�¼�����
				//�Ӻ��Ѳ������¼�
				if(ckprivacy('friend', 1)) {
					$fs = array();
					$fs['icon'] = 'friend';
	
					$fs['title_template'] = cplang('feed_friend_title');
					$fs['title_data'] = array('touser'=>"<a href=\"space.php?uid=$tospace[uid]\">".$_SN[$tospace['uid']]."</a>");
	
					$fs['body_template'] = '';
					$fs['body_data'] = array();
					$fs['body_general'] = '';

					feed_add($fs['icon'], $fs['title_template'], $fs['title_data'], $fs['body_template'], $fs['body_data'], $fs['body_general']);
				}
				
				//�ҵĺ������������б仯
				$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET addfriendnum=addfriendnum-1 WHERE uid='$space[uid]' AND addfriendnum>0");

				//֪ͨ
				notification_add($uid, 'friend', cplang('note_friend_add'));
				notification_add_push($uid, cplang('note_friend_add'), $_SGLOBAL['supe_uid']);
				$msg = '���� '.$_SN[$tospace['uid']].' ��Ϊ������';
				capi_showmessage_by_data($msg, 0,  array($_SN[$tospace['uid']]));
			} else {
				$op = 'add2';
				// include_once template('cp_friend');
				exit();
			}
		}
	}

} elseif($op == 'ignore') {

	//����û�
	if($uid) {
		if(capi_submitcheck('friendsubmit')) {
			//�Է����ҵĹ�ϵ
			$fstatus = getfriendstatus($uid, $space['uid']);
			if($fstatus == 1) {
				//ȡ��˫����ѹ�ϵ
				friend_update($_SGLOBAL['supe_uid'], $_SGLOBAL['supe_username'], $uid, '', 'ignore');
			} elseif ($fstatus == 0) {
				request_ignore($uid);
			}
			capi_showmessage_by_data('do_success',  0);
		}
	} elseif($_REQUEST['key'] == $space['key']) {
		//�������Ժ�������
		$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('friend')." WHERE fuid='$space[uid]' AND status='0' LIMIT 0,1");
		if($value = $_SGLOBAL['db']->fetch_array($query)) {
			//ɾ��
			$uid = $value['uid'];
			$username = getcount('space', array('uid'=>$uid), 'username');
			request_ignore($uid);
			
			capi_showmessage_by_data('friend_ignore_next', 0, array("url"=>'cp.php?ac=friend&op=ignore&confirm=1&key='.$space['key'],  "username"=>array($username)));
		} else {
			capi_showmessage_by_data('do_success',  0);
		}
	}
	
} elseif($op == 'addconfirm') {

	//if($_REQUEST['key'] == $space['key']) {
		
		//�����Ŀ
		$maxfriendnum = checkperm('maxfriendnum');
		if($maxfriendnum && $space['friendnum'] >= $maxfriendnum + $space['addfriend']) {
			if($_SGLOBAL['magic']['friendnum']) {
				capi_showmessage_by_data('enough_of_the_number_of_friends_with_magic');
			} else {
				capi_showmessage_by_data('enough_of_the_number_of_friends');
			}
		}
		
		//�������
		$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('friend')." WHERE fuid='$space[uid]' AND status='0' LIMIT 0,1");
		if($value = $_SGLOBAL['db']->fetch_array($query)) {
			
			$uid = $value['uid'];
			$username = getcount('space', array('uid'=>$uid), 'username');
			
			friend_update($space['uid'], $space['username'], $uid, $tospace['username'], 'add', 0);
			
			//�ҵĺ������������б仯
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET addfriendnum=addfriendnum-1 WHERE uid='$space[uid]' AND addfriendnum>0");

			//������feed
			capi_showmessage_by_data('friend_addconfirm_next', 'cp.php?ac=friend&op=addconfirm&key='.$space['key'], 1, array($username));
		}
	//}

	capi_showmessage_by_data('do_success',  0);

} elseif($op == 'syn') {

	//��ȡ�û������ҵ�fans�б�
	if(isset($_SCOOKIE['synfriend']) || empty($_SCONFIG['uc_status'])) {
		exit();
	}

	include_once S_ROOT.'./uc_client/client.php';
	$buddylist = uc_friend_ls($_SGLOBAL['supe_uid'], 1, 999, 999, 2);//���˼�����

	$havas = array();
	if($buddylist && is_array($buddylist)) {
		foreach($buddylist as $key => $buddy) {
			$uids[] = $buddy['uid'];
		}
		$members = array();
		if($uids) {
			$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." WHERE uid IN (".simplode($uids).")");
			while($member = $_SGLOBAL['db']->fetch_array($query)) {
				$members[] = $member['uid'];
			}
		}
		if($members) {
			foreach($buddylist as $key => $buddy) {
				if(in_array($buddy['uid'], $members)) {
					$havas[$buddy['uid']] = $buddy;
				}
			}
		}
	}

	//���ҵ�ǰ����
	if($havas) {
		$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('friend')." WHERE fuid='$_SGLOBAL[supe_uid]'");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if(isset($havas[$value['uid']])) {
				unset($havas[$value['uid']]);
			}
		}
	}
	
	//�ҵĺ�����
	$blacklist = array();
	$query = $_SGLOBAL['db']->query("SELECT buid FROM ".tname('blacklist')." WHERE uid='$_SGLOBAL[supe_uid]'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$blacklist[$value['buid']] = $value['buid'];
	}

	//��Ӻ���
	$addnum = 0;
	$inserts = array();
	if($havas) {
		foreach ($havas as $value) {
			if($_SGLOBAL['supe_uid'] != $value['uid'] && empty($blacklist[$value['uid']])) {
				$value['username'] = addslashes($value['username']);
				if($value['direction'] == 3) {//˫��
					$inserts[] = "('$_SGLOBAL[supe_uid]','$value[uid]','$value[username]','1','$_SGLOBAL[timestamp]')";
					$inserts[] = "('$value[uid]','$_SGLOBAL[supe_uid]','$_SGLOBAL[supe_username]','1','$_SGLOBAL[timestamp]')";
				} else {//���˼���
					$addnum++;
					$inserts[] = "('$value[uid]','$_SGLOBAL[supe_uid]','$_SGLOBAL[supe_username]','0','$_SGLOBAL[timestamp]')";
				}
			}
		}
	}
	if($inserts) {
		$_SGLOBAL['db']->query("REPLACE INTO ".tname('friend')." (uid,fuid,fusername,status,dateline) VALUES ".implode(',',$inserts));
		friend_cache($_SGLOBAL['supe_uid']);
	}
	if($addnum) {
		$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET addfriendnum=addfriendnum+$addnum WHERE uid='$_SGLOBAL[supe_uid]'");
	}

	ssetcookie('synfriend', 1, 1800);//30���Ӽ��һ��
	exit();

} elseif($op == 'find') {

	//�Զ��Һ���
	$maxnum = 18;
	
	$nouids = $space['friends'];
	$nouids[] = $space['uid'];

	//������������
	$nearlist = array();
	$i=0;
	$myip = getonlineip(1);
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')."
		WHERE ip='$myip' LIMIT 0,200");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(!in_array($value['uid'], $nouids)) {
			realname_set($value['uid'], $value['username']);
			$nearlist[] = $value;
			$i++;
			if($i>=$maxnum) break;
		}
	}
	
	//���ѵĺ���
	$i = 0;
	$friendlist = array();
	if($space['feedfriend']) {
		$query = $_SGLOBAL['db']->query("SELECT fuid AS uid, fusername AS username FROM ".tname('friend')."
			WHERE uid IN (".$space['feedfriend'].") LIMIT 0,200");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if(!in_array($value['uid'], $nouids) && $value['username']) {
				realname_set($value['uid'], $value['username']);
				$friendlist[$value['uid']] = $value;
				$i++;
				if($i>=$maxnum) break;
			}
		}
	}

	//��ǰ���ߵĺ���
	$i = 0;
	$onlinelist = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('session')." LIMIT 0,200");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		if(!in_array($value['uid'], $nouids)) {
			realname_set($value['uid'], $value['username']);
			$onlinelist[] = $value;
			$i++;
			if($i>=$maxnum) break;
		}
	}

	//ʵ��
	realname_get();

} elseif($op == 'changegroup') {

	if(capi_submitcheck('changegroupsubmit')) {
		updatetable('friend', array('gid'=>intval($_REQUEST['group'])), array('uid'=>$_SGLOBAL['supe_uid'], 'fuid'=>$uid));
		friend_cache($_SGLOBAL['supe_uid']);
		capi_showmessage_by_data('do_success', $_SGLOBAL['refer']);
	}

	//��õ�ǰ�û�group
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('friend')." WHERE uid='$_SGLOBAL[supe_uid]' AND fuid='$uid'");
	if(!$friend = $_SGLOBAL['db']->fetch_array($query)) {
		capi_showmessage_by_data('specified_user_is_not_your_friend');
	}
	$groupselect = array($friend['gid'] => ' checked');

	$groups = getfriendgroup();

} elseif($op == 'changenum') {

	if(capi_submitcheck('changenumsubmit')) {
		updatetable('friend', array('num'=>intval($_REQUEST['num'])), array('uid'=>$_SGLOBAL['supe_uid'], 'fuid'=>$uid));
		friend_cache($_SGLOBAL['supe_uid']);
		capi_showmessage_by_data('do_success', $_SGLOBAL['refer'], 0);
	}

	//��õ�ǰ�û�group
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('friend')." WHERE uid='$_SGLOBAL[supe_uid]' AND fuid='$uid'");
	if(!$friend = $_SGLOBAL['db']->fetch_array($query)) {
		capi_showmessage_by_data('specified_user_is_not_your_friend');
	}
	
} elseif($op == 'group') {

	if(capi_submitcheck('groupsubmin')) {
		if(empty($_REQUEST['fuids'])) {
			capi_showmessage_by_data('please_correct_choice_groups_friend');
		}
		$ids = simplode($_REQUEST['fuids']);
		$groupid = intval($_REQUEST['group']);
		updatetable('friend', array('gid'=>$groupid), "uid='$_SGLOBAL[supe_uid]' AND fuid IN ($ids) AND status='1'");
		friend_cache($_SGLOBAL['supe_uid']);
		capi_showmessage_by_data('do_success', $_SGLOBAL['refer']);
	}

	$perpage = 50;
	$page = empty($_REQUEST['page'])?1:intval($_REQUEST['page']);
	if($page<1) $page = 1;
	$start = ($page-1)*$perpage;

	$list = array();
	$multi = $wheresql = '';
	if($space['friendnum']) {
		$groups = getfriendgroup();

		$theurl = 'cp.php?ac=friend&op=group';
		$group = !isset($_REQUEST['group'])?'-1':intval($_REQUEST['group']);
		if($group > -1) {
			$wheresql = "AND main.gid='$group'";
			$theurl .= "&group=$group";
		}

		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('friend')." main
			WHERE main.uid='$space[uid]' AND main.status='1' $wheresql"), 0);
		$query = $_SGLOBAL['db']->query("SELECT main.fuid AS uid,main.fusername AS username, main.gid, main.num FROM ".tname('friend')." main
			WHERE main.uid='$space[uid]' AND main.status='1' $wheresql
			ORDER BY main.dateline DESC
			LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['uid'], $value['username']);
			$value['group'] = $groups[$value['gid']];
			$list[] = $value;
		}
		$multi = multi($count, $perpage, $page, $theurl);
	}
	$groups = getfriendgroup();

	$actives = array('group'=>' class="active"');

	//ʵ��
	realname_get();

} elseif($op == 'request') {

	if(capi_submitcheck('requestsubmin')) {
		capi_showmessage_by_data('do_success', 0);
	}
	
	$maxfriendnum = checkperm('maxfriendnum');
	if($maxfriendnum) {
		$maxfriendnum = $maxfriendnum + $space['addfriend'];
	}

	//��������
	$perpage = 20;
	$page = empty($_REQUEST['page'])?0:intval($_REQUEST['page']);
	if($page<1) $page = 1;
	$start = ($page-1)*$perpage;
	
	$friend1 = $space['friends'];
	$list = array();
	
	$count = getcount('friend', array('fuid'=>$space['uid'], 'status'=>0));
	
	if($count) {
		$query = $_SGLOBAL['db']->query("SELECT s.*, sf.friend, f.* FROM ".tname('friend')." f
			LEFT JOIN ".tname('space')." s ON s.uid=f.uid
			LEFT JOIN ".tname('spacefield')." sf ON sf.uid=f.uid
			WHERE f.fuid='$space[uid]' AND f.status='0'
			ORDER BY f.dateline DESC
			LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['uid'], $value['username']);
			//���еĺ���
			$cfriend = array();
			$friend2 = empty($value['friend'])?array():explode(',',$value['friend']);
			if($friend1 && $friend2) {
				$cfriend = array_intersect($friend1, $friend2);
			}
			$value['cfriend'] = implode(',', $cfriend);
			$value['cfcount'] = count($cfriend);
			$list[] = $value;
		}
	}
	
	//ͳ�Ƹ���
	if($count != $space['addfriendnum']) {
		updatetable('space', array('addfriendnum'=>$count), array('uid'=>$space['uid']));
	}
		
	//��ҳ
	$multi = multi($count, $perpage, $page, "cp.php?ac=friend&op=request");
	
	realname_get();

} elseif($op == 'groupname') {

	$groups = getfriendgroup();
	$group = intval($_REQUEST['group']);
	if(!isset($groups[$group])) {
		capi_showmessage_by_data('change_friend_groupname_error');
	}

	if(capi_submitcheck('groupnamesubmit')) {
		$space['privacy']['groupname'][$group] = getstr($_REQUEST['groupname'], 20, 1, 1);
		privacy_update();
		capi_showmessage_by_data('do_success', $_REQUEST['refer']);
	}
} elseif($op == 'groupignore') {

	$groups = getfriendgroup();
	$group = intval($_REQUEST['group']);
	if(!isset($groups[$group])) {
		capi_showmessage_by_data('change_friend_groupname_error');
	}

	if(capi_submitcheck('groupignoresubmit')) {
		if(isset($space['privacy']['filter_gid'][$group])) {
			unset($space['privacy']['filter_gid'][$group]);
		} else {
			$space['privacy']['filter_gid'][$group] = $group;
		}
		privacy_update();
		friend_cache($_SGLOBAL['supe_uid']);//�������

		capi_showmessage_by_data('do_success', $_REQUEST['refer'], 0);
	}

} elseif($op == 'blacklist') {

	if($_REQUEST['subop'] == 'delete') {
		$_REQUEST['uid'] = intval($_REQUEST['uid']);
		$_SGLOBAL['db']->query("DELETE FROM ".tname('blacklist')." WHERE uid='$space[uid]' AND buid='$_REQUEST[uid]'");
		capi_showmessage_by_data('do_success', "space.php?do=friend&view=blacklist&start=$_REQUEST[start]", 0);
	}

	if(capi_submitcheck('blacklistsubmit')) {
		$_REQUEST['username'] = trim($_REQUEST['username']);
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('space')." WHERE username='$_REQUEST[username]'");
		if(!$tospace = $_SGLOBAL['db']->fetch_array($query)) {
			capi_showmessage_by_data('space_does_not_exist');
		}
		if($tospace['uid'] == $space['uid']) {
			capi_showmessage_by_data('unable_to_manage_self');
		}
		//ɾ������
		if($space['friends'] && in_array($tospace['uid'], $space['friends'])) {
			friend_update($_SGLOBAL['supe_uid'], $_SGLOBAL['supe_username'], $tospace['uid'], '', 'ignore');
		}
		inserttable('blacklist', array('uid'=>$space['uid'], 'buid'=>$tospace['uid'], 'dateline'=>$_SGLOBAL['timestamp']), 0, true);

		capi_showmessage_by_data('do_success', "space.php?do=friend&view=blacklist&start=$_REQUEST[start]", 0);
	}
	
} elseif($op == 'rand') {
	
	$randuids = array();
	if($space['friendnum']<5) {
		//�������ߵ�����
		$onlinelist = array();
		$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('session')." LIMIT 0,100");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if($value['uid'] != $space['uid']) {
				$onlinelist[] = $value['uid'];
			}
		}
		$randuids = sarray_rand(array_merge($onlinelist, $space['friends']), 1);
	} else {
		$randuids = sarray_rand($space['friends'], 1);
	}
	capi_showmessage_by_data('do_success', "space.php?uid=".array_pop($randuids), 0);
	
} elseif ($op == 'getcfriend') {
	
	$fuids = empty($_REQUEST['fuid'])?array():explode(',', $_REQUEST['fuid']);
	$newfuids = array();
	foreach ($fuids as $value) {
		$value = intval($value);
		if($value) $newfuids[$value] = $value;
	}
	
	//��ͬ�ĺ���
	$list = array();
	if($newfuids) {
		$query = $_SGLOBAL['db']->query("SELECT uid,username,name,namestatus FROM ".tname('space')." WHERE uid IN (".simplode($newfuids).") LIMIT 0,15");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['uid'], $value['username'], $value['name'], $value['namestatus']);
			$list[] = $value;
		}
		realname_get();
	}
} elseif($op == 'search') {

	@include_once(S_ROOT.'./data/data_profilefield.php');
	$fields = empty($_SGLOBAL['profilefield'])?array():$_SGLOBAL['profilefield'];

	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page=1;
	//����
	$perpage = empty($_GET['perpage'])?10:intval($_GET['perpage']);
	$perpage = mob_perpage($perpage);
	
	$start = ($page-1)*$perpage;

	//��鿪ʼ��
	ckstart($start, $perpage);
		
	if(!empty($_REQUEST['searchsubmit']) || !empty($_REQUEST['searchmode'])) {
		$_REQUEST['searchsubmit'] = $_REQUEST['searchmode'] = 1;
		//����
		$wherearr = $fromarr = $uidjoin = array();
		$fsql = '';
		
		$fromarr['space'] = tname('space').' s';
		
		if($searchkey = stripsearchkey($_REQUEST['searchkey'])) {
			$wherearr[] = "(s.name like '%$searchkey%' OR s.username like '%$searchkey%')";
		} else {
			foreach (array('uid','username','name','videostatus','avatar') as $value) {
				if($_REQUEST[$value]) {
					$wherearr[] = "s.$value='{$_REQUEST[$value]}'";
				}
			}
		}
		//����
		foreach (array('sex','qq','msn','birthyear','birthmonth','birthday','blood','marry','birthprovince','birthcity','resideprovince','residecity') as $value) {
			if($_REQUEST[$value]) {
				$fromarr['spacefield'] = tname('spacefield').' sf';
				$wherearr['spacefield'] = "sf.uid=s.uid";
				$wherearr[] = "sf.$value='{$_REQUEST[$value]}'";
				$fsql .= ", sf.$value";
			}
		}
		//ת����ʵ�ʵ����
		$startage = $endage = 0;
		if($_REQUEST['endage']) {
			$startage = sgmdate('Y') - intval($_REQUEST['endage']);
		}
		if($_REQUEST['startage']) {
			$endage = sgmdate('Y') - intval($_REQUEST['startage']);
		}
		if($startage || $endage) {
			$fromarr['spacefield'] = tname('spacefield').' sf';
			$wherearr['spacefield'] = "sf.uid=s.uid";
		}
		if($startage && $endage && $endage > $startage) {
			$wherearr[] = '(sf.birthyear>='.$startage.' AND sf.birthyear<='.$endage.')';
		} else if($startage && empty($endage)) {
			$wherearr[] = 'sf.birthyear>='.$startage;
		} else if(empty($startage) && $endage) {
			$wherearr[] = 'sf.birthyear<='.$endage;
		}
		//�Զ���
		$havefield = 0;
		foreach ($fields as $fkey => $fvalue) {
			if($fvalue['allowsearch']) {
				$_REQUEST['field_'.$fkey] = empty($_REQUEST['field_'.$fkey])?'':stripsearchkey($_REQUEST['field_'.$fkey]);
				if($_REQUEST['field_'.$fkey]) {
					$havefield = 1;
					$wherearr[] = "sf.field_$fkey LIKE '%".$_REQUEST['field_'.$fkey]."%'";
				}
			}
		}
		if($havefield) {
			$fromarr['spacefield'] = tname('spacefield').' sf';
			$wherearr['spacefield'] = "sf.uid=s.uid";
		}
		
		//��չ
		if($_REQUEST['type'] == 'edu' || $_REQUEST['type'] == 'work') {
			foreach (array('type','title','subtitle','startyear') as $value) {
				if($_REQUEST[$value]) {
					$fromarr['spaceinfo'] = tname('spaceinfo').' si';
					$wherearr['spaceinfo'] = "si.uid=s.uid";
					$wherearr[] = "si.$value='{$_REQUEST[$value]}'";
				}
			}
		}
		
		$list = array();
		if($wherearr) {
			$query = $_SGLOBAL['db']->query("SELECT s.* $fsql FROM ".implode(',', $fromarr)." WHERE ".implode(' AND ', $wherearr)." LIMIT $start,$perpage");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				realname_set($value['uid'], $value['username'], $value['name'], $value['namestatus']);
				$value['isfriend'] = ($value['uid']==$space['uid'] || ($space['friends'] && in_array($value['uid'], $space['friends'])))?1:0;
				$list[$value['uid']] = $value;
			}
		}
		
		realname_get();
		
	} else {
		
		$yearhtml = '';
		$nowy = sgmdate('Y');
		for ($i=0; $i<50; $i++) {
			$they = $nowy - $i;
			$yearhtml .= "<option value=\"$they\">$they</option>";
		}
		
		//�Ա�
		$sexarr = array($space['sex']=>' checked');
		
		//����:��
		$birthyeayhtml = '';
		$nowy = sgmdate('Y');
		for ($i=0; $i<100; $i++) {
			$they = $nowy - $i;
			if(empty($_REQUEST['all'])) $selectstr = $they == $space['birthyear']?' selected':'';
			$birthyeayhtml .= "<option value=\"$they\"$selectstr>$they</option>";
		}
		//����:��
		$birthmonthhtml = '';
		for ($i=1; $i<13; $i++) {
			if(empty($_REQUEST['all'])) $selectstr = $i == $space['birthmonth']?' selected':'';
			$birthmonthhtml .= "<option value=\"$i\"$selectstr>$i</option>";
		}
		//����:��
		$birthdayhtml = '';
		for ($i=1; $i<29; $i++) {
			if(empty($_REQUEST['all'])) $selectstr = $i == $space['birthday']?' selected':'';
			$birthdayhtml .= "<option value=\"$i\"$selectstr>$i</option>";
		}
		//Ѫ��
		$bloodhtml = '';
		foreach (array('A','B','O','AB') as $value) {
			if(empty($_REQUEST['all'])) $selectstr = $value == $space['blood']?' selected':'';
			$bloodhtml .= "<option value=\"$value\"$selectstr>$value</option>";
		}
		//����
		$marryarr = array($space['marry'] => ' selected');
		
		//�Զ���
		foreach ($fields as $fkey => $fvalue) {
			if($fvalue['allowsearch']) {
				if($fvalue['formtype'] == 'text') {
					$fvalue['html'] = '<input type="text" name="field_'.$fkey.'" value="'.$gets["field_$fkey"].'" class="t_input">';
				} else {
					$fvalue['html'] = "<select name=\"field_$fkey\"><option value=\"\">---</option>";
					$optionarr = explode("\n", $fvalue['choice']);
					foreach ($optionarr as $ov) {
						$ov = trim($ov);
						if($ov) {
							$selectstr = $gets["field_$fkey"]==$ov?' selected':'';
							$fvalue['html'] .= "<option value=\"$ov\"$selectstr>$ov</option>";
						}
					}
					$fvalue['html'] .= "</select>";
				}
				$fields[$fkey] = $fvalue;
			} else {
				unset($fields[$fkey]);
			}
		}

	}
	
}
$plist = array();
foreach ($list as $key=>$value)
{
	$tmpspace = getspace($value["uid"]);
	$value["avatar"] = capi_avatar($value["uid"]);
	$value["isonline"] = capi_isonline($value["uid"],$tmpspace);
	$value["name"] = capi_realname($value["uid"],$tmpspace);
	$plist[] = $value;
}

capi_showmessage_by_data("rest_success", 0, array('friends'=>$plist, 'count'=>count($plist)));
//include template('cp_friend');

?>

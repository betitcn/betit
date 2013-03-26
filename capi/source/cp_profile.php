<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_profile.php 13149 2009-08-13 03:11:26Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

if(!in_array($_REQUEST['op'], array('base','contact','edu','work','info', 'name'))) {
	$_REQUEST['op'] = 'base';
}

$theurl = "cp.php?ac=profile&op=$_REQUEST[op]";

capi_showmessage_by_data("asdasd");

if($_REQUEST['op'] == 'base') {
	
	if(capi_submitcheck('profilesubmit') || capi_submitcheck('nextsubmit')) {
		
		if(!@include_once(S_ROOT.'./data/data_profilefield.php')) {
			include_once(S_ROOT.'./source/function_cache.php');
			profilefield_cache();
		}
		$profilefields = empty($_SGLOBAL['profilefield'])?array():$_SGLOBAL['profilefield'];
	
		//�ύ���
		$setarr = array(
			'birthyear' => intval($_REQUEST['birthyear']),
			'birthmonth' => intval($_REQUEST['birthmonth']),
			'birthday' => intval($_REQUEST['birthday']),
			'blood' => getstr($_REQUEST['blood'], 5, 1, 1),
			'marry' => intval($_REQUEST['marry']),
			'birthprovince' => getstr($_REQUEST['birthprovince'], 20, 1, 1),
			'birthcity' => getstr($_REQUEST['birthcity'], 20, 1, 1),
			'resideprovince' => getstr($_REQUEST['resideprovince'], 20, 1, 1),
			'residecity' => getstr($_REQUEST['residecity'], 20, 1, 1)
		);
		
		//�Ա�
		$_REQUEST['sex'] = intval($_REQUEST['sex']);
		if($_REQUEST['sex'] && empty($space['sex'])) $setarr['sex'] = $_REQUEST['sex'];
	
		foreach ($profilefields as $field => $value) {
			if($value['formtype'] == 'select') $value['maxsize'] = 255;
			$setarr['field_'.$field] = getstr($_REQUEST['field_'.$field], $value['maxsize'], 1, 1);
			if($value['required'] && empty($setarr['field_'.$field])) {
				capi_showmessage_by_data('field_required',  array($value['title']));
			}
		}
		
		updatetable('spacefield', $setarr, array('uid'=>$_SGLOBAL['supe_uid']));
		
		//��˽
		$inserts = array();
		foreach ($_REQUEST['friend'] as $key => $value) {
			$value = intval($value);
			$inserts[] = "('base','$key','$space[uid]','$value')";
		}
		if($inserts) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='base'");
			$_SGLOBAL['db']->query("INSERT INTO ".tname('spaceinfo')." (type,subtype,uid,friend)
				VALUES ".implode(',', $inserts));
		}

		//����ʵ��
		$setarr = array(
			'name' => getstr($_REQUEST['name'], 10, 1, 1, 1),
			'namestatus' => $_SCONFIG['namecheck']?0:1
		);
		if(checkperm('managename')) {
			 $setarr['namestatus'] = 1;
		}
	
		if($setarr['name'] && strlen($setarr['name']) < 4) {//����С��4���ַ�			
			capi_showmessage_by_data('realname_too_short');
		}
		if($setarr['name'] != $space['name'] || $setarr['namestatus']) {
			
			//��һ����дʵ��
			if($_SCONFIG['realname'] && empty($space['name']) &&  $setarr['name'] != $space['name'] && $setarr['namestatus']) {
				$reward = getreward('realname', 0);
				if($reward['credit']) {
					$setarr['credit'] = $space['credit'] + $reward['credit'];
				}
				if($reward['experience']) {
					$setarr['experience'] = $space['experience'] + $reward['experience'];
				}
			
			} elseif($_SCONFIG['realname'] && $space['namestatus'] && !checkperm('managename')) {	//�ۼ�����
				$reward = getreward('editrealname', 0);
				//����
				if($space['name'] && $setarr['name'] != $space['name'] && ($reward['credit'] || $reward['experience'])) {
					//��֤����ֵ
					if($space['experience'] >= $reward['experience']) {
						$setarr['experience'] = $space['experience'] - $reward['experience'];
					} else {
						capi_showmessage_by_data('experience_inadequate',  array($space['experience'], $reward['experience']));
					}
				
					if($space['credit'] >= $reward['credit']) {
						$setarr['credit'] = $space['credit'] - $reward['credit'];
					} else {
						capi_showmessage_by_data('integral_inadequate',  array($space['credit'],  $reward['credit']));
					}
				}
			}
			updatetable('space', $setarr, array('uid'=>$_SGLOBAL['supe_uid']));
		}
	
		//�����¼
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_SGLOBAL['supe_uid'], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp'], 'type'=>0), 0, true);
		}
		
		//����feed
		if(ckprivacy('profile', 1)) {
			feed_add('profile', cplang('feed_profile_update_base'));
		}
	
		if(capi_submitcheck('nextsubmit')) {
			$url = 'cp.php?ac=profile&op=contact';
		} else {
			$url = 'cp.php?ac=profile&op=base';
		}
		capi_showmessage_by_data('update_on_successful_individuals');
	}

	//�Ա�
	$sexarr = array($space['sex']=>' checked');
	
	//����:��
	$birthyeayhtml = '';
	$nowy = sgmdate('Y');
	for ($i=0; $i<100; $i++) {
		$they = $nowy - $i;
		$selectstr = $they == $space['birthyear']?' selected':'';
		$birthyeayhtml .= "<option value=\"$they\"$selectstr>$they</option>";
	}
	//����:��
	$birthmonthhtml = '';
	for ($i=1; $i<13; $i++) {
		$selectstr = $i == $space['birthmonth']?' selected':'';
		$birthmonthhtml .= "<option value=\"$i\"$selectstr>$i</option>";
	}
	//����:��
	$birthdayhtml = '';
	for ($i=1; $i<32; $i++) {
		$selectstr = $i == $space['birthday']?' selected':'';
		$birthdayhtml .= "<option value=\"$i\"$selectstr>$i</option>";
	}
	//Ѫ��
	$bloodhtml = '';
	foreach (array('A','B','O','AB') as $value) {
		$selectstr = $value == $space['blood']?' selected':'';
		$bloodhtml .= "<option value=\"$value\"$selectstr>$value</option>";
	}
	//����
	$marryarr = array($space['marry'] => ' selected');
	
	//��Ŀ��
	$profilefields = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('profilefield')." ORDER BY displayorder");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$fieldid = $value['fieldid'];
		$value['formhtml'] = '';
	
		if($value['formtype'] == 'text') {
			$value['formhtml'] = "<input type=\"text\" name=\"field_$fieldid\" value=\"".$space["field_$fieldid"]."\" class=\"t_input\">";
		} else {
			$value['formhtml'] .= "<select name=\"field_$fieldid\">";
			if(empty($value['required'])) {
				$value['formhtml'] .= "<option value=\"\"></option>";
			}
			$optionarr = explode("\n", $value['choice']);
			foreach ($optionarr as $ov) {
				$ov = trim($ov);
				if($ov) {
					$selectstr = $space["field_$fieldid"]==$ov?' selected':'';
					$value['formhtml'] .= "<option value=\"$ov\"$selectstr>$ov</option>";
				}
			}
			$value['formhtml'] .= "</select>";
		}
	
		$profilefields[$value['fieldid']] = $value;
	}
	
	if(empty($_SCONFIG['namechange'])) {
		$_REQUEST['namechange'] = 0;//�������޸�
	}
	
	//��˽
	$friendarr = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='base'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$friendarr[$value['subtype']][$value['friend']] = ' selected';
	}
	
} elseif ($_REQUEST['op'] == 'contact') {
	
	if($_REQUEST['resend']) {
		//���·���������֤
		$toemail = $space['newemail']?$space['newemail']:$space['email'];
		emailcheck_send($space['uid'], $toemail);
		capi_showmessage_by_data('do_success', "cp.php?ac=profile&op=contact");
	}
	
	if(capi_submitcheck('profilesubmit') || capi_submitcheck('nextsubmit')) {
		//�ύ���
		$setarr = array(
			'mobile' => getstr($_REQUEST['mobile'], 40, 1, 1),
			'qq' => getstr($_REQUEST['qq'], 20, 1, 1),
			'msn' => getstr($_REQUEST['msn'], 80, 1, 1),
		);
		
		//��������
		$newemail = isemail($_REQUEST['email'])?$_REQUEST['email']:'';
		if(isset($_REQUEST['email']) && $newemail != $space['email']) {
			
			//�������Ψһ��
			if($_SCONFIG['uniqueemail']) {
				if(getcount('spacefield', array('email'=>$newemail, 'emailcheck'=>1))) {
					capi_showmessage_by_data('uniqueemail_check');
				}
			}
			
			//��֤����
			if(!$passport = getpassport($_SGLOBAL['supe_username'], $_REQUEST['password'])) {
				capi_showmessage_by_data('password_is_not_passed');
			}
			
			//�����޸�
			if(empty($newemail)) {
				//����ɾ��
				$setarr['email'] = '';
				$setarr['emailcheck'] = 0;
			} elseif($newemail != $space['email']) {
				//֮ǰ�Ѿ���֤
				if($space['emailcheck']) {
					//�����ʼ���֤�����޸�����
					$setarr['newemail'] = $newemail;
				} else {
					//�޸�����
					$setarr['email'] = $newemail;
				}
				emailcheck_send($space['uid'], $newemail);
			}
		}
		
		updatetable('spacefield', $setarr, array('uid'=>$_SGLOBAL['supe_uid']));
		
		//��˽
		$inserts = array();
		foreach ($_REQUEST['friend'] as $key => $value) {
			$value = intval($value);
			$inserts[] = "('contact','$key','$space[uid]','$value')";
		}
		if($inserts) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='contact'");
			$_SGLOBAL['db']->query("INSERT INTO ".tname('spaceinfo')." (type,subtype,uid,friend)
				VALUES ".implode(',', $inserts));
		}

		//�����¼
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_SGLOBAL['supe_uid'], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp'], 'type'=>2), 0, true);
		}
		
		//����feed
		if(ckprivacy('profile', 1)) {
			feed_add('profile', cplang('feed_profile_update_contact'));
		}
		
		if(capi_submitcheck('nextsubmit')) {
			$url = 'cp.php?ac=profile&op=edu';
		} else {
			$url = 'cp.php?ac=profile&op=contact';
		}
		capi_showmessage_by_data('update_on_successful_individuals', $url);
	}
	
	//��˽
	$friendarr = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='contact'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$friendarr[$value['subtype']][$value['friend']] = ' selected';
	}
	
} elseif ($_REQUEST['op'] == 'edu') {
	
	if($_REQUEST['subop'] == 'delete') {
		$infoid = intval($_REQUEST['infoid']);
		if($infoid) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('spaceinfo')." WHERE infoid='$infoid' AND uid='$space[uid]' AND type='edu'");
		}
	}
	
	if(capi_submitcheck('profilesubmit') || capi_submitcheck('nextsubmit')) {
		//�ύ���
		$inserts = array();
		foreach ($_REQUEST['title'] as $key => $value) {
			$value = getstr($value, 100, 1, 1);
			if($value) {
				$subtitle= getstr($_REQUEST['subtitle'][$key], 20, 1, 1);
				$startyear = intval($_REQUEST['startyear'][$key]);
				$friend = intval($_REQUEST['friend'][$key]);
				$inserts[] = "('$space[uid]','edu','$value','$subtitle','$startyear','$friend')";
			}
		}
		if($inserts) {
			$_SGLOBAL['db']->query("INSERT INTO ".tname('spaceinfo')."(uid,type,title,subtitle,startyear,friend) VALUES ".implode(',', $inserts));
		}
		
		//�����¼
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_SGLOBAL['supe_uid'], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp'], 'type'=>2), 0, true);
		}
		
		//����feed
		if(ckprivacy('profile', 1)) {
			feed_add('profile', cplang('feed_profile_update_edu'));
		}

		if(capi_submitcheck('nextsubmit')) {
			$url = 'cp.php?ac=profile&op=work';
		} else {
			$url = 'cp.php?ac=profile&op=edu';
		}
		capi_showmessage_by_data('update_on_successful_individuals', $url);
	}
	
	//��ǰ�Ѿ����õ�ѧУ
	$list = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='edu'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['title_s'] = urlencode($value['title']);
		$list[] = $value;
	}
	
} elseif ($_REQUEST['op'] == 'work') {
	
	
	if($_REQUEST['subop'] == 'delete') {
		$infoid = intval($_REQUEST['infoid']);
		if($infoid) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('spaceinfo')." WHERE infoid='$infoid' AND uid='$space[uid]' AND type='work'");
		}
	}
	
	if(capi_submitcheck('profilesubmit') || capi_submitcheck('nextsubmit')) {
		//�ύ���
		$inserts = array();
		foreach ($_REQUEST['title'] as $key => $value) {
			$value = getstr($value, 100, 1, 1);
			if($value) {
				$subtitle= getstr($_REQUEST['subtitle'][$key], 20, 1, 1);
				$startyear = intval($_REQUEST['startyear'][$key]);
				$startmonth = intval($_REQUEST['startmonth'][$key]);
				$endyear = intval($_REQUEST['endyear'][$key]);
				$endmonth = $endyear?intval($_REQUEST['endmonth'][$key]):0;
				$friend = intval($_REQUEST['friend'][$key]);
				$inserts[] = "('$space[uid]','work','$value','$subtitle','$startyear','$startmonth','$endyear','$endmonth','$friend')";
			}
		}
		if($inserts) {
			$_SGLOBAL['db']->query("INSERT INTO ".tname('spaceinfo')."
				(uid,type,title,subtitle,startyear,startmonth,endyear,endmonth,friend)
				VALUES ".implode(',', $inserts));
		}

		//�����¼
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_SGLOBAL['supe_uid'], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp'], 'type'=>2), 0, true);
		}
		
		//����feed
		if(ckprivacy('profile', 1)) {
			feed_add('profile', cplang('feed_profile_update_work'));
		}


		if(capi_submitcheck('nextsubmit')) {
			$url = 'cp.php?ac=profile&op=info';
		} else {
			$url = 'cp.php?ac=profile&op=work';
		}
		capi_showmessage_by_data('update_on_successful_individuals', $url);
	}
	
	//��ǰ�Ѿ�����
	$list = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='work'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$value['title_s'] = urlencode($value['title']);
		$list[] = $value;
	}
	
} elseif ($_REQUEST['op'] == 'info') {
	
	if(capi_submitcheck('profilesubmit')) {
		
		$inserts = array();
		foreach ($_REQUEST['info'] as $key => $value) {
			$value = getstr($value, 500, 1, 1);
			$friend = intval($_REQUEST['info_friend'][$key]);
			$inserts[] = "('$space[uid]','info','$key','$value','$friend')";
		}
		
		if($inserts) {
			$_SGLOBAL['db']->query("DELETE FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='info'");
			$_SGLOBAL['db']->query("INSERT INTO ".tname('spaceinfo')."
				(uid,type,subtype,title,friend)
				VALUES ".implode(',', $inserts));
		}
	
		//�����¼
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_SGLOBAL['supe_uid'], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp'], 'type'=>2), 0, true);
		}
		
		//����feed
		if(ckprivacy('profile', 1)) {
			feed_add('profile', cplang('feed_profile_update_info'));
		}


		$url = 'cp.php?ac=profile&op=info';
		capi_showmessage_by_data('update_on_successful_individuals');
	}
	
	//��˽
	$list = $friends = array();
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('spaceinfo')." WHERE uid='$space[uid]' AND type='info'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$list[$value['subtype']] = $value;
		$friends[$value['subtype']][$value['friend']] = ' selected';
	}
	
}elseif($_REQUEST['op'] == 'name'){
	//����ʵ��
	$setarr = array(
		'name' => getstr($_REQUEST['name'], 10, 1, 1, 1),
		'namestatus' => $_SCONFIG['namecheck']?0:1
	);
	if(checkperm('managename')) {
		 $setarr['namestatus'] = 1;
	}

	if($setarr['name'] && strlen($setarr['name']) < 4) {//����С��4���ַ�			
		capi_showmessage_by_data('realname_too_short');
	}
	if($setarr['name'] != $space['name'] || $setarr['namestatus']) {
		
		//��һ����дʵ��
		if($_SCONFIG['realname'] && empty($space['name']) &&  $setarr['name'] != $space['name'] && $setarr['namestatus']) {
			$reward = getreward('realname', 0);
			if($reward['credit']) {
				$setarr['credit'] = $space['credit'] + $reward['credit'];
			}
			if($reward['experience']) {
				$setarr['experience'] = $space['experience'] + $reward['experience'];
			}
		
		} elseif($_SCONFIG['realname'] && $space['namestatus'] && !checkperm('managename')) {	//�ۼ�����
			$reward = getreward('editrealname', 0);
			//����
			if($space['name'] && $setarr['name'] != $space['name'] && ($reward['credit'] || $reward['experience'])) {
				//��֤����ֵ
				if($space['experience'] >= $reward['experience']) {
					$setarr['experience'] = $space['experience'] - $reward['experience'];
				} else {
					capi_showmessage_by_data('experience_inadequate',  array($space['experience'], $reward['experience']));
				}
			
				if($space['credit'] >= $reward['credit']) {
					$setarr['credit'] = $space['credit'] - $reward['credit'];
				} else {
					capi_showmessage_by_data('integral_inadequate',  array($space['credit'],  $reward['credit']));
				}
			}
		}
		updatetable('space', $setarr, array('uid'=>$_SGLOBAL['supe_uid']));
	}
	if (empty($reward)){
		$reward = array("credit"=>0, "experience"=>0);
	}
	capi_showmessage_by_data('do_success',0, $reward);
}

$cat_actives = array($_REQUEST['op'] => ' class="active"');


if($_REQUEST['op'] == 'edu' || $_REQUEST['op'] == 'work') {
	$yearhtml = '';
	$nowy = sgmdate('Y');
	for ($i=0; $i<50; $i++) {
		$they = $nowy - $i;
		$yearhtml .= "<option value=\"$they\">$they</option>";
	}
	
	$monthhtml = '';
	for ($i=1; $i<13; $i++) {
		$monthhtml .= "<option value=\"$i\">$i</option>";
	}
}

if (!empty($_REQUEST['uid']))
	$space = getspace($_REQUEST['uid']);
$space["avatar"] = capi_avatar($space["uid"]);

@include_once(S_ROOT.'./data/data_usergroup.php');

$space["grouptitle"] = $_SGLOBAL["grouptitle"][$space["groupid"]]["grouptitle"];
$space["tasknum"] = capi_gettasknum($space["uid"]);
$space["commentnum"] = capi_getspacecomment($space["uid"]);
$space["isonline"] = capi_isonline($space["uid"], $space);
if ($space['weibo'] == 1){
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('sina_bind_info')." WHERE uid='$space[uid]'");
	if ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$space["openid"] = $value["sina_uid"];
	}
}elseif ($space['weibo'] == 2){
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('qq_bind_info')." WHERE uid='$space[uid]'");
	if ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$space["openid"] = $value["qq_uid"];
	}
}

capi_showmessage_by_data('rest_success', 0, array("space"=>$space));
//include template("cp_profile");


?>

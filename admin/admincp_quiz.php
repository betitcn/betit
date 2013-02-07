<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: admincp_quiz.php 12568 2009-07-08 07:38:01Z zhengqingpeng $
*/

if(!defined('IN_UCHOME') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

//Ȩ��
if(!$allowmanage = checkperm('managequiz')) {
	$_GET['uid'] = $_SGLOBAL['supe_uid'];//ֻ�ܲ������˵�
	$_GET['username'] = '';
}

if(submitcheck('deletesubmit')) {
	include_once(S_ROOT.'./source/function_delete.php');
	if(!empty($_POST['ids']) && deletequizs($_POST['ids'])) {
		cpmessage('do_success', $_POST['mpurl']);
	} else {
		cpmessage('the_correct_choice_to_delete_the_quiz', $_POST['mpurl']);
	}
}

$mpurl = 'admincp.php?ac=quiz';
$op = empty($_GET['op']) ? '' : trim($_GET['op']);

if($op == 'delete') {
	
	include_once(S_ROOT.'./source/function_delete.php');
	deletequizs(array($_GET['quizid']));
	cpmessage('do_success', $mpurl);
	
} else {
	//��������
	if($_GET['endtime']) {
		$val = $_GET['endtime'] == 1 ? 1 : 2;
		$_GET['endtime'.$val] = $_SGLOBAL['timestamp'];
	}
	$intkeys = array('uid', 'noreply', 'quizid', 'sex','id');
	$strkeys = array('username');
	$randkeys = array(array('sstrtotime','dateline'), array('intval','voternum'), array('intval','replynum'), array('intval','percredit'), array('intval','endtime'), array('intval','hot'));
	$likekeys = array('subject');
	$results = getwheres($intkeys, $strkeys, $randkeys, $likekeys, '');
	$wherearr = $results['wherearr'];
	$mpurl .= '&'.implode('&', $results['urls']);
	
	$wheresql = empty($wherearr)?'1':implode(' AND ', $wherearr);
	
	//����
	$orders = getorders(array('dateline', 'viewnum', 'replynum', 'percredit', 'hot'), 'quizid', '');
	$ordersql = $orders['sql'];
	if($orders['urls']) $mpurl .= '&'.implode('&', $orders['urls']);
	$orderby = array($_GET['orderby']=>' selected');
	$ordersc = array($_GET['ordersc']=>' selected');
	
	$perpage = empty($_GET['perpage'])?0:intval($_GET['perpage']);
	if(!in_array($perpage, array(20,50,100,1000))) $perpage = 20;
	
	$page = empty($_GET['page'])?1:intval($_GET['page']);
	if($page<1) $page = 1;
	$start = ($page-1)*$perpage;
	//��鿪ʼ��
	ckstart($start, $perpage);
	
	//��ʾ��ҳ
	if($perpage > 100) {
		$count = 1;
		$selectsql = 'quizid';
	} else {
		$csql = "SELECT COUNT(*) FROM ".tname('quiz')." WHERE $wheresql";
		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query($csql), 0);
		$selectsql = '*';
	}
	
	$mpurl .= '&perpage='.$perpage;
	$perpages = array($perpage => ' selected');
	$managebatch = checkperm('managebatch');
	$allowbatch = true;
	$list = array();
	$multi = '';
	
	$qsql = "SELECT $selectsql FROM ".tname('quiz')." WHERE $wheresql $ordersql LIMIT $start,$perpage";
	
	if($count) {
		$query = $_SGLOBAL['db']->query($qsql);
		while($value = $_SGLOBAL['db']->fetch_array($query)) {
			$value['isexpired'] = $value['endtime'] && $value['endtime'] < $_SGLOBAL['timestamp'] ? true : false;
			if(!$managebatch && $value['uid'] != $_SGLOBAL['supe_uid']) {
				$allowbatch = false;
			}
			$list[] = $value;
		}
		$multi = multi($count, $perpage, $page, $mpurl);
	}
	
	//��ʾ��ҳ
	if($perpage > 100) {
		$count = count($list);
	}
}
?>

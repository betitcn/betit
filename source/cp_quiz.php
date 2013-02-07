<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_quiz.php 13026 2009-08-06 02:17:33Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}


	

//检查信息
$quizid = empty($_GET['quizid'])?0:intval($_GET['quizid']);
$fquizid = empty($_GET['fquizid'])?0:intval($_GET['fquizid']);
$op = empty($_GET['op'])?'':$_GET['op'];
$quiz = array();
if($fquizid) {
	
	$query = $_SGLOBAL['db']->query("SELECT bf.*, b.* FROM ".tname('quiz')." b 
		LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=b.quizid 
		WHERE b.quizid='$fquizid'");
	$quiz = $_SGLOBAL['db']->fetch_array($query);
	
	//选项
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE quizid='$fquizid'");
	while( $value = $_SGLOBAL['db']->fetch_array($query))
	{
		if ($value['picid']){
			$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE picid='$value[picid]'");
			$value2 = $_SGLOBAL['db']->fetch_array($query2);
			$value['pic'] = pic_get($value2['filepath'], $value2['thumb'], $value2['remote']);
		}
		$quiz['options'][] = $value;
	}
	if ($op!="key"){
		while(count($quiz['options'])<2){
			$quiz['options'][] = "";
		}
	}

}

if($quizid) {
	
	$query = $_SGLOBAL['db']->query("SELECT bf.*, b.* FROM ".tname('quiz')." b 
		LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=b.quizid 
		WHERE b.quizid='$quizid'");
	$quiz = $_SGLOBAL['db']->fetch_array($query);
	//选项
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE quizid='$quizid'");
	while( $value = $_SGLOBAL['db']->fetch_array($query))
	{
		if ($value['picid']){
			$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE picid='$value[picid]'");
			$value2 = $_SGLOBAL['db']->fetch_array($query2);
			$value['pic'] = pic_get($value2['filepath'], $value2['thumb'], $value2['remote']);
		}
		$quiz['options'][] = $value;
	}
	if ($op!="key"){
		while(count($quiz['options'])<2){
			$quiz['options'][] = "";
		}
	}
}
	//权限检查
if(empty($quiz)) {
	if(!checkperm('allowquiz')) {
		ckspacelog();
		showmessage('no_authority_to_add_bet');
	}

	
	//实名认证
	ckrealname('quiz');
	
	//视频认证
	ckvideophoto('quiz');
	
	//新用户见习
	cknewuser();
	
	//判断是否发布太快
	$waittime = interval_check('post');
	if($waittime > 0) {
		showmessage('operating_too_fast','',1,array($waittime));
	}

	//接收外部标题
	$quiz['subject'] = empty($_GET['subject'])?'':getstr($_GET['subject'], 80, 1, 0);
	$quiz['message'] = empty($_GET['message'])?'':getstr($_GET['message'], 5000, 1, 0);
	
}


//添加编辑操作
if(submitcheck('quizsubmit')) {

	if(empty($quiz['quizid'])) {
		$quiz = array();
	} else {
		if(!checkperm('allowquiz')) {
			ckspacelog();
			showmessage('no_authority_to_add_log');
		}
	}
	
	//验证码
	if(checkperm('seccode') && !ckseccode($_POST['seccode'])) {
		showmessage('incorrect_code');
	}

	if($_POST['endtime']) {
		$_POST['endtime'] = sstrtotime(trim($_POST['endtime']));
		if($_POST['endtime'] <= $_SGLOBAL['timestamp']) {
			showmessage('time_expired_error');
		}
	}
	if($_POST['resulttime']) {
		$_POST['resulttime'] = sstrtotime(trim($_POST['resulttime']));
		if($_POST['resulttime'] <= $_SGLOBAL['timestamp'] || $_POST['resulttime'] <= $_POST['endtime'] ) {
			showmessage('time_expired_error');
		}
	}
	
	include_once(S_ROOT.'./source/function_quiz.php');
	if($fquizid){
	if($quiz){
	if($_SGLOBAL['timestamp']>$quiz['endtime']){
		showmessage("亲！你知道吗！该竞猜已过期，不允许编辑或转发！");
		}
	}
	if($newquiz = quiz_post($_POST)) {
		if(empty($quiz) && $newquiz['topicid']) {
			$url = 'space.php?do=topic&topicid='.$newquiz['topicid'].'&view=quiz';
		} else {
			$url = 'space.php?uid='.$newquiz['uid'].'&do=quiz&id='.$newquiz['quizid'];
		}
	
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE quizid='$fquizid'");
		$fkey = $_SGLOBAL['db']->fetch_array($query);
		$newfquizid=$fkey['fquizid'];
		if($newfquizid!=0){
		updatetable('quiz', array('fquizid'=>$newfquizid), array('quizid'=>$newquiz['quizid']));
		showmessage('do_success', $url, 0);
			}else{
        updatetable('quiz', array('fquizid'=>$fquizid),array('quizid'=>$newquiz['quizid']));
		showmessage('do_success', $url, 0);
			}
		
	
	} else {
		showmessage('that_should_at_least_write_things');
	}
  }
else{
	if($quiz){
	if($_SGLOBAL['timestamp']>$quiz['endtime']){
		showmessage("亲！你知道吗！该竞猜已过期，不允许编辑或转发！");
		}
	}
	if($newquiz = quiz_post($_POST, $quiz)) {
		if(empty($quiz) && $newquiz['topicid']) {
			$url = 'space.php?do=topic&topicid='.$newquiz['topicid'].'&view=quiz';
		} else {
			$url = 'space.php?uid='.$newquiz['uid'].'&do=quiz&id='.$newquiz['quizid'];
		}
		showmessage('do_success', $url, 0);
	} else {
		showmessage('that_should_at_least_write_things');
	}
  }
}
if($_GET['op'] == 'delete') {
	//删除
	if(submitcheck('deletesubmit')) {
		//include_once(S_ROOT.'./source/function_delete.php');
		$setarr = array(
			'id' => '1',
			'endtime' =>'1',
			'resulttime' =>'1'
		);
		    updatetable('quiz',$setarr,array('quizid'=>$quizid));
			showmessage('do_success', "space.php?uid=$quiz[uid]&do=quiz&view=me");
		} 
	
	
}elseif($_GET['op'] == 'goto') {
	
	$id = intval($_GET['id']);
	$uid = $id?getcount('quiz', array('quizid'=>$id), 'uid'):0;

	showmessage('do_success', "space.php?uid=$uid&do=quiz&id=$id", 0);
	
}elseif($_GET['op'] == 'get') {

	//获得好友的feed
	$cp_mode = 1;
	$_GET['page'] = intval($_GET['page']);
	if($_GET['page'] < 1) {
		$_GET['page'] = $_SCONFIG['feedmaxnum']<50?50:$_SCONFIG['feedmaxnum'];
		$_GET['page'] = $_GET['page'] + 1;
	}
	$_TPL['getmore'] = 1;
	include_once(S_ROOT.'./source/space_quiz.php');
	include_once template('space_quiz_list2');
	exit();
	
} elseif($_GET['op'] == 'edithot') {
	//权限
	if(!checkperm('managequiz')) {
		showmessage('no_privilege');
	}
		
	if(submitcheck('hotsubmit')) {
		$_POST['hot'] = intval($_POST['hot']);
		updatetable('quiz', array('hot'=>$_POST['hot']), array('quizid'=>$quiz['quizid']));
		
		if($_POST['hot']>0) {
			include_once(S_ROOT.'./source/function_feed.php');
			feed_publish($quiz['quizid'], 'quizid');
		} else {
			updatetable('feed', array('hot'=>$_POST['hot']), array('id'=>$quiz['quizid'], 'idtype'=>'quizid'));
		}
		
		showmessage('do_success', "space.php?uid=$quiz[uid]&do=quiz&id=$quizid", 0);
	}
	
} elseif($op == 'vote') {
	
	//计票
	if(submitcheck('votesubmit')) {
		if(empty($quiz)) {
			showmessage("voting_does_not_exist");
		}
		if($space['credit']<=$quiz['joincost']){
			showmessage("voting_not_enough_credit");
		}
		//验证性别
		/*if($quiz['sex'] && $quiz['sex'] != $space['sex']) {
			showmessage('no_privilege');
		}*/
		//验证是否投过票
		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('quizuser')." WHERE uid='$_SGLOBAL[supe_uid]' AND quizid='$quizid'"),0);
		if($count>=$quiz['portion']) {
			showmessage("already_voted");
		}
		$list = $optionarr = $setarr = array();
		foreach($_POST['option'] as $key => $val) {
			$optionarr[] = intval($val);
			if(count($optionarr) >= $quiz['maxchoice']) {
				break;
			}
		}
			
		$query = $_SGLOBAL['db']->query("SELECT `option` FROM ".tname('quizoptions')." WHERE oid IN ('".implode("','", $optionarr)."') AND quizid='$quizid'");
		while($value = $_SGLOBAL['db']->fetch_array($query)) {
			
			$list[] = saddslashes($value['option']);
		}
		if(empty($list)) {
			showmessage('please_select_items_to_vote');
		}
		//累计投票数
		$_SGLOBAL['db']->query("UPDATE ".tname('quizoptions')." SET votenum=votenum+1 WHERE oid IN ('".implode("','", $optionarr)."') AND quizid='$quizid'");
		$setarr = array(
			'uid' => $_SGLOBAL['supe_uid'],
			'username' => $_POST['anonymous'] ? '': $_SGLOBAL['supe_username'],
			'quizid' => $quizid,
			'oid' => $optionarr[0],
			'option' => saddslashes('"'.implode(cplang('poll_separator'), $list).'"'),
			'dateline' => $_SGLOBAL['timestamp']
		);
		inserttable('quizuser', $setarr);
		
		$sql = '';
		//判断是否有悬常
		/*if($poll['credit'] && $poll['percredit'] && $poll['uid'] != $_SGLOBAL['supe_uid']) {
			if($poll['credit'] <= $poll['percredit']) {
				$poll['percredit'] = $poll['credit'];
				$sql = ',percredit=0';
			}
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET credit=credit+$poll[percredit] WHERE uid='$_SGLOBAL[supe_uid]'");
		} else {
			$poll['percredit'] = 0;
		}*/
		if($count)
			$_SGLOBAL['db']->query("UPDATE ".tname('quiz')." SET lastvote='$_SGLOBAL[timestamp]' $sql WHERE quizid='$quizid'");
		else{
			$_SGLOBAL['db']->query("UPDATE ".tname('quiz')." SET voternum=voternum+1, lastvote='$_SGLOBAL[timestamp]' $sql WHERE quizid='$quizid'");
			$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET voternum=voternum+1 WHERE uid='$_SGLOBAL[supe_uid]'");
			
		}
		
		

		
		//实名
		realname_get();
		//if($quiz['uid'] != $_SGLOBAL['supe_uid']) {
			//奖赏积分
		//	getreward('joinquiz', 1, 0, $pid);
		//}
		
		// 扣除积分
		$reward = array(
		'credit' => 0,
		'experience' => 0
		);
		$reward['credit'] = "-$quiz[joincost]";
		
		$setarr = array();
		if($reward['credit']) {
			$setarr['credit'] = "credit=credit+$reward[credit]";
		}
		if($reward['experience']) {
			$setarr['experience'] = "experience=experience+$reward[experience]";
		}
		$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET ".implode(',', $setarr)." WHERE uid='$_SGLOBAL[supe_uid]'");
		$setarr = array();
		$setarr['totalcost'] = "totalcost=totalcost+$quiz[joincost]";
		$_SGLOBAL['db']->query("UPDATE ".tname('quiz')." SET ".implode(',', $setarr)." WHERE quizid='$quizid'");
		//$reward['experience'] = "-$rule[experience]";
		
		//更新feed
		include_once(S_ROOT.'./source/function_feed.php');
		feed_publish($quizid, 'quizid', 0);

		//热点
		if($quiz['uid'] != $_SGLOBAL['supe_uid']) {
			hot_update('quizid', $quiz['quizid'], $quiz['hotuser']);
		}
		
		//统计
		updatestat('quizvote');

		//事件feed
		
		if(!isset($_POST['anonymous']) && $_SGLOBAL['supe_uid']!=$quiz['uid'] && ckprivacy('joinquiz', 1)) {
			$fs = array();
			$fs['icon'] = 'quiz';

			$fs['images'] = $fs['image_links'] = array();
				
			$fs['title_template'] = cplang('take_part_in_the_quiz');
			$fs['title_data'] = array(
				'touser' => "<a href=\"space.php?uid=$quiz[uid]\">".$_SN[$quiz['uid']]."</a>",
				'url' => "space.php?uid=$quiz[uid]&do=quiz&id=$quiz[quizid]",
				'subject' => $quiz['subject'],
				'reward' => $quiz['joincost'] ? cplang('reward') : ''
			);
	
			$fs['body_template'] = '';
			$fs['body_data'] = array();
			include_once(S_ROOT.'./source/function_cp.php');
			feed_add($fs['icon'], $fs['title_template'], $fs['title_data'], $fs['body_template'], $fs['body_data']);
		}
		
		$joinlist = array();
		$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('quizuser')." WHERE   quizid='$quizid'");
		while($value = $_SGLOBAL['db']->fetch_array($query)) {
			
			$joinlist[] = $value['uid'];
		}

		//发送通知
		foreach($space['friends'] as $key => $uid) {
			if($uid && $uid != $_SGLOBAL['supe_uid'] && in_array($uid , $joinlist)) {
				$note = cplang('note_quiz_join', array("space.php?uid=$quiz[uid]&do=quiz&id=$quiz[quizid]", $quiz['subject'], $quiz['joincost']?cplang('reward'):''));
				
				notification_add($uid, 'quiz_invite', $note);
				notification_add_push($uid,$note,$_SGLOBAL['supe_uid']);
			}
		}
	
		showmessage('do_success', 'space.php?uid='.$quiz['uid'].'&do=quiz&id='.$quizid);
	}
	
} elseif($op == 'publickey'){
	//公布结果
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE quizid='$quizid'");
			$fkey = $_SGLOBAL['db']->fetch_array($query);
			$newfquizid=$fkey['fquizid'];
			
			if($newfquizid==0){
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE fquizid='$quizid'");
				$ffkey = $_SGLOBAL['db']->fetch_array($query);
				if(!$ffkey){
					
	if(submitcheck('keysubmit')) {
		
		$keyid = empty($_POST['keyid'])?0:intval($_POST['keyid']);
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE oid='$keyid'");
		
			
		    if ($keyid){
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE oid='$keyid'");
			$key = $_SGLOBAL['db']->fetch_array($query);
			$quizarr = array(
				"keyoid"=>$keyid,
				"keyoption"=>$key["option"],
				"endtime"=>$_SGLOBAL["timestamp"]
			);
			updatetable('quiz', $quizarr, array('quizid'=>$quizid));
			
			if ($keyid==3){
				//竞猜流失
				$query = $_SGLOBAL['db']->query("SELECT uid, count(*)*$quiz[joincost] as credit FROM ".tname('quizuser')." WHERE quizid='$quizid'  group by uid");
				while( $value = $_SGLOBAL['db']->fetch_array($query))
				{
					$reward = array(
							'credit' => 0,
							'experience' => 0
							);
					$reward['credit'] = "$value[credit]";
					$setarr = array();
					if($reward['credit']) {
						$setarr['credit'] = "credit=credit+$reward[credit]";
					}
					if($reward['experience']) {
						$setarr['experience'] = "experience=experience+$reward[experience]";
					}
					
					
					$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET ".implode(',', $setarr)." WHERE uid='$value[uid]'");

					$note = cplang('note_quiz_invalid', array("space.php?uid=$quiz[uid]&do=quiz&id=$quizid", $quiz['subject'], $quiz['joincost']?cplang('reward'):'', $value["credit"]));
				
					notification_add($value['uid'], 'quizinvalid', $note);
					notification_add_push($value['uid'], $note, $_SGLOBAL['supe_uid']);
				}

				$fs = array();
				$fs['icon'] = 'quiz';

				$fs['images'] = $fs['image_links'] = array();
					
				$fs['title_template'] = cplang('publish_key_in_the_quiz2');
				$fs['title_data'] = array(
					'url' => "space.php?uid=$quiz[uid]&do=quiz&id=$quiz[quizid]",
					'subject' => $quiz['subject'],
					'key'=>$key["option"]
				);
				$fs['body_template'] = "";
				$fs['body_data'] = array();
				
				include_once(S_ROOT.'./source/function_cp.php');
				feed_add($fs['icon'], $fs['title_template'], $fs['title_data'], $fs['body_template'], $fs['body_data']);

			
			}else{
				//给分
				$win = array();
				$lost = array();
				$resultarr = array();
				$fitcount = getcount("quizuser",  array("quizid"=>$quizid, "oid"=>$keyid));
				$credit = intval(intval($quiz['totalcost'])/intval($fitcount));
				//原句:intval(intval($quiz['totalcost'])/intval($fitcount)-$quiz['joincost']);
				$winuid = '';
				$wincredit = 0;
				
				if ($fitcount){
					$query = $_SGLOBAL['db']->query("SELECT uid, username, count(*)*$credit as credit FROM ".tname('quizuser')." WHERE quizid='$quizid' and oid='$keyid' group by uid ");
					while( $value = $_SGLOBAL['db']->fetch_array($query))
					{
							$reward = array(
							'credit' => 0,
							'experience' => 0
							);
							$reward['credit'] = "$value[credit]";
							
							$setarr = array();
							if($reward['credit']) {
								$setarr['credit'] = "credit=credit+$reward[credit]";
							}
							if($reward['experience']) {
								$setarr['experience'] = "experience=experience+$reward[experience]";
							}
							
							if ($value["credit"]>$wincredit){
								$winuid = $value["uid"];
								$wincredit = $value["credit"];
							}
							realname_set($value['uid'], $value['username']);//实名
							$win[$value["uid"]] = $value["credit"];
							$resultarr[$value["uid"]]["totalwin"] = $value["credit"];
							$resultarr[$value["uid"]]["username"] = $value["username"];
                       
							if (strcmp(trim(implode(',', $setarr)),"")!=0){
								$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET ".implode(',', $setarr)." WHERE uid='$value[uid]'");	
							}
							
					}
				}
				
				

				$query = $_SGLOBAL['db']->query("SELECT uid, username, count(*)*$quiz[joincost] as credit FROM ".tname('quizuser')." WHERE quizid='$quizid' and oid<>'$keyid'  group by uid ");
				while( $value = $_SGLOBAL['db']->fetch_array($query))
				{
					$resultarr[$value["uid"]]["totalcost"] = $value["credit"];
					$resultarr[$value["uid"]]["username"] = $value["username"];
					if ($win[$value["uid"]])
					{	
						if (($win[$value["uid"]]-$value["credit"])<0){
							$lost[$value["uid"]] = $value["credit"]-$win[$value["uid"]];
							$resultarr[$value["uid"]]["winflag"] = 0;
							unset($win[$value["uid"]]);
						}elseif(($win[$value["uid"]]-$value["credit"])>0)
						{
							$win[$value["uid"]] = $win[$value["uid"]]-$value["credit"];
							$resultarr[$value["uid"]]["winflag"] = 1;
						}else
						{
							unset($win[$value["uid"]]);
							$resultarr[$value["uid"]]["winflag"] = 2;
						}
					}else{
						$lost[$value["uid"]] = $value["credit"];
						$resultarr[$value["uid"]]["winflag"] = 0;
					}
				}

				//实名
				realname_get();
				$fs = array();
				$fs['icon'] = 'quiz';

				$fs['images'] = $fs['image_links'] = array();
					
				if (count($win)){
					$fs['title_template'] = cplang('publish_key_in_the_quiz3');
					$fs['title_data'] = array(
						'attend' => $quiz['voternum'],
						'win' => count($win),
						'precost' => $credit,
						'touser' => "<a href='space.php?uid=$winuid'>$_SN[$winuid]<a>",
						'url' => "space.php?uid=$quiz[uid]&do=quiz&id=$quiz[quizid]",
					'subject' => $quiz['subject'],
					'key'=>$key["option"]
					);
				}else{
					$fs['title_template'] =cplang('publish_key_in_the_quiz4');
					$fs['title_data'] = array(
						'attend' => $quiz['voternum'],
						'url' => "space.php?uid=$quiz[uid]&do=quiz&id=$quiz[quizid]",
					'subject' => $quiz['subject'],
					'key'=>$key["option"]
					);
				}
					$fs['body_template'] = "";
				$fs['body_data'] = array();
				include_once(S_ROOT.'./source/function_cp.php');
				feed_add($fs['icon'], $fs['title_template'], $fs['title_data'], $fs['body_template'], $fs['body_data']);

				

				//发送通知
				foreach($win as $uid => $value) {
					// if($uid && $uid != $_SGLOBAL['supe_uid']) {
						$note = cplang('note_quiz_win', array("space.php?uid=$quiz[uid]&do=quiz&id=$quizid", $quiz['subject'], $quiz['joincost']?cplang('reward'):'',$value));
						
						notification_add($uid, 'quizwin', $note);
						notification_add_push($uid, $note, $_SGLOBAL['supe_uid']);
						$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET winnum=winnum+1 WHERE uid='$uid'");
					//}
				}

				//发送通知
				foreach($lost as $uid => $value) {
					// if($uid && $uid != $_SGLOBAL['supe_uid']) {
						$note = cplang('note_quiz_lost', array("space.php?uid=$quiz[uid]&do=quiz&id=$quizid", $quiz['subject'], $quiz['joincost']?cplang('reward'):'',$value));
						
						notification_add($uid, 'quizlost', $note);
						notification_add_push($uid, $note, $_SGLOBAL['supe_uid']);
						$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET lostnum=lostnum+1 WHERE uid='$uid'");
					//}
				}
				$dateline = $_SGLOBAL["timestamp"];
				foreach($resultarr as $uid=> $value){
					$value["uid"] = $uid;
					$value["quizid"] = $quizid;
					$value["dateline"] = $dateline;
					inserttable('quizresult', $value);
				}
				
			}
			
		}
	
		
		showmessage('do_success', "space.php?uid=$quiz[uid]&do=quiz&id=$quizid");
				}

			}else{
				if(submitcheck('keysubmit')) {
			$keyid = empty($_POST['keyid'])?0:intval($_POST['keyid']);
			
					
			if ($keyid==3){
				//竞猜流失

			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE fquizid='$quizid' or quizid='$quizid' order by quizid DESC");
			while($fffkey = $_SGLOBAL['db']->fetch_array($query)){
			$quizid=$fffkey['quizid'];
			$uid=$fffkey['uid'];
			$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE oid='$keyid'");
			$key = $_SGLOBAL['db']->fetch_array($query2);
			$quizarr = array(
				"keyoid"=>$keyid,
				"keyoption"=>$key["option"],
				"endtime"=>$_SGLOBAL["timestamp"]
			);
			updatetable('quiz', $quizarr, array('quizid'=>$quizid));
				$query3 = $_SGLOBAL['db']->query("SELECT uid, count(*)*$quiz[joincost] as credit FROM ".tname('quizuser')." WHERE quizid='$quizid'  group by uid");
				while( $value = $_SGLOBAL['db']->fetch_array($query3))
				{
					$reward = array(
							'credit' => 0,
							'experience' => 0
							);
					$reward['credit'] = "$value[credit]";
					$setarr = array();
					if($reward['credit']) {
						$setarr['credit'] = "credit=credit+$reward[credit]";
					}
					if($reward['experience']) {
						$setarr['experience'] = "experience=experience+$reward[experience]";
					}
					
					
					$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET ".implode(',', $setarr)." WHERE uid='$value[uid]'");

					$note = cplang('note_quiz_invalid', array("space.php?uid=$uid&do=quiz&id=$quizid", $quiz['subject'], $quiz['joincost']?cplang('reward'):'', $value["credit"]));
				
					notification_add($value['uid'], 'quizinvalid', $note);
					notification_add_push($value['uid'], $note, $_SGLOBAL['supe_uid']);
				}

				$fs = array();
				$fs['icon'] = 'quiz';

				$fs['images'] = $fs['image_links'] = array();
					
				$fs['title_template'] = cplang('publish_key_in_the_quiz2');
				$fs['title_data'] = array(
					'url' => "space.php?uid=$quiz[uid]&do=quiz&id=$quiz[quizid]",
					'subject' => $quiz['subject'],
					'key'=>$key["option"]
				);
				$fs['body_template'] = "";
				$fs['body_data'] = array();
				
				include_once(S_ROOT.'./source/function_cp.php');
				feed_add($fs['icon'], $fs['title_template'], $fs['title_data'], $fs['body_template'], $fs['body_data']);
			}
			}else{
				//给分
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE oid='$keyid'");
			$ke = $_SGLOBAL['db']->fetch_array($query);
			$option=$ke['option'];
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE fquizid='$quizid' or quizid='$quizid' order by quizid DESC");

			while($fffkey = $_SGLOBAL['db']->fetch_array($query)){
			$quizid=$fffkey['quizid'];
			$query8 = $_SGLOBAL['db']->query("SELECT bf.*, b.* FROM ".tname('quiz')." b 
		    LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=b.quizid 
		    WHERE b.quizid='$quizid'");
			$quiz = $_SGLOBAL['db']->fetch_array($query8);
			$query1 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE uchome_quizoptions.option='$option' and quizid='$quizid'");
			$ke1 = $_SGLOBAL['db']->fetch_array($query1);
			$keyid=$ke1['oid'];
			 
		    if ($keyid){
			$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE oid='$keyid'");
			$key = $_SGLOBAL['db']->fetch_array($query2);
			$quizarr = array(
				"keyoid"=>$keyid,
				"keyoption"=>$key["option"],
				"endtime"=>$_SGLOBAL["timestamp"]
			);
			updatetable('quiz', $quizarr, array('quizid'=>$quizid));
			}
				$win = array();
				$lost = array();
				$resultarr = array();
				$fitcount = getcount("quizuser",  array("quizid"=>$quizid, "oid"=>$keyid));
				$credit = intval(intval($quiz['totalcost'])/intval($fitcount));
				$winuid = '';
				$wincredit = 0;
				
				if ($fitcount){
					$query4 = $_SGLOBAL['db']->query("SELECT uid, username, count(*)*$credit as credit FROM ".tname('quizuser')." WHERE quizid='$quizid' and oid='$keyid' group by uid ");
					
					while( $value = $_SGLOBAL['db']->fetch_array($query4))
					{
							$reward = array(
							'credit' => 0,
							'experience' => 0
							);
							$reward['credit'] = "$value[credit]";
							$A=$reward['credit'];
							
							$setarr = array();
							if($reward['credit']) {
								$setarr['credit'] = "credit=credit+$reward[credit]";
							}
							if($reward['experience']) {
								$setarr['experience'] = "experience=experience+$reward[experience]";
							}
							if ($value["credit"]>$wincredit){
								$winuid = $value["uid"];
								$wincredit = $value["credit"];
							}
							realname_set($value['uid'], $value['username']);//实名
							$win[$value["uid"]] = $value["credit"];
							$resultarr[$value["uid"]]["totalwin"] = $value["credit"];
							$resultarr[$value["uid"]]["username"] = $value["username"];
                       
							if (strcmp(trim(implode(',', $setarr)),"")!=0){
								$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET ".implode(',', $setarr)." WHERE uid='$value[uid]'");	
							}
					}
				}
				
				

				$query5 = $_SGLOBAL['db']->query("SELECT uid, username, count(*)*$quiz[joincost] as credit FROM ".tname('quizuser')." WHERE quizid='$quizid' and oid<>'$keyid'  group by uid ");
				while( $value = $_SGLOBAL['db']->fetch_array($query5))
				{
					$resultarr[$value["uid"]]["totalcost"] = $value["credit"];
					$resultarr[$value["uid"]]["username"] = $value["username"];
					if ($win[$value["uid"]])
					{	
						if (($win[$value["uid"]]-$value["credit"])<0){
							$lost[$value["uid"]] = $value["credit"]-$win[$value["uid"]];
							$resultarr[$value["uid"]]["winflag"] = 0;
							unset($win[$value["uid"]]);
						}elseif(($win[$value["uid"]]-$value["credit"])>0)
						{
							$win[$value["uid"]] = $win[$value["uid"]]-$value["credit"];
							$resultarr[$value["uid"]]["winflag"] = 1;
						}else
						{
							unset($win[$value["uid"]]);
							$resultarr[$value["uid"]]["winflag"] = 2;
						}
					}else{
						$lost[$value["uid"]] = $value["credit"];
						$resultarr[$value["uid"]]["winflag"] = 0;
					}
				}

				//实名
				realname_get();
				$fs = array();
				$fs['icon'] = 'quiz';

				$fs['images'] = $fs['image_links'] = array();
					
				if (count($win)){
					$fs['title_template'] = cplang('publish_key_in_the_quiz3');
					$fs['title_data'] = array(
						'attend' => $quiz['voternum'],
						'win' => count($win),
						'precost' => $credit,
						'touser' => "<a href='space.php?uid=$winuid'>$_SN[$winuid]<a>",
						'url' => "space.php?uid=$quiz[uid]&do=quiz&id=$quiz[quizid]",
					'subject' => $quiz['subject'],
					'key'=>$key["option"]
					);
				}else{
					$fs['title_template'] =cplang('publish_key_in_the_quiz4');
					$fs['title_data'] = array(
						'attend' => $quiz['voternum'],
						'url' => "space.php?uid=$quiz[uid]&do=quiz&id=$quiz[quizid]",
					'subject' => $quiz['subject'],
					'key'=>$key["option"]
					);
				}
					$fs['body_template'] = "";
				$fs['body_data'] = array();
				include_once(S_ROOT.'./source/function_cp.php');
				feed_add($fs['icon'], $fs['title_template'], $fs['title_data'], $fs['body_template'], $fs['body_data']);

				

				//发送通知
				foreach($win as $uid => $value) {
					// if($uid && $uid != $_SGLOBAL['supe_uid']) {
						$note = cplang('note_quiz_win', array("space.php?uid=$quiz[uid]&do=quiz&id=$quizid", $quiz['subject'], $quiz['joincost']?cplang('reward'):'',$value));
						
						notification_add($uid, 'quizwin', $note);
						notification_add_push($uid, $note, $_SGLOBAL['supe_uid']);
						$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET winnum=winnum+1 WHERE uid='$uid'");
					//}
				}

				//发送通知
				foreach($lost as $uid => $value) {
					// if($uid && $uid != $_SGLOBAL['supe_uid']) {
						$note = cplang('note_quiz_lost', array("space.php?uid=$quiz[uid]&do=quiz&id=$quizid", $quiz['subject'], $quiz['joincost']?cplang('reward'):'',$value));
						
						notification_add($uid, 'quizlost', $note);
						notification_add_push($uid, $note, $_SGLOBAL['supe_uid']);
						$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET lostnum=lostnum+1 WHERE uid='$uid'");
					//}
				}
				$dateline = $_SGLOBAL["timestamp"];
				foreach($resultarr as $uid=> $value){
					$value["uid"] = $uid;
					$value["quizid"] = $quizid;
					$value["dateline"] = $dateline;
					inserttable('quizresult', $value);
				}
			}
			
			
					}
			
				showmessage('do_success', "space.php?uid=$quiz[uid]&do=quiz&id=$quizid");	
				}
				
			
			
				
				}
}else if($newfquizid!==0){
			showmessage('不好意思，此竞猜为转发，须原发布者公布答案！');
	}
	
	
	
}elseif($op == 'get') {
	
	$perpage = 20;
	$page = empty($_GET['page'])?0:intval($_GET['page']);
	if($page<1) $page=1;
	$start = ($page-1)*$perpage;
	//检查开始数
	ckstart($start, $perpage);

	//取出投票记录
	$_GET['filtrate'] = empty($_GET['filtrate']) ? 'new' : trim($_GET['filtrate']);
	
	if ($_GET['filtrate']=='new' || $_GET['filtrate']=='we'){
		$wherearr = $voteresult = array();
		$multi = '';
		
		if($_GET['filtrate'] == 'we') {
			if(empty($space['feedfriend']))	$space['feedfriend'] = 0;	//返回空内容
			$wherearr[] = "uid IN ($space[feedfriend])";
		}
		$wherearr[] = "quizid='$quizid'";
		$wheresql = ' WHERE '.implode(' AND ', $wherearr);

		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('quizuser')." $wheresql"),0);
		if($count) {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizuser')." $wheresql ORDER BY dateline DESC LIMIT $start,$perpage");
			while($value = $_SGLOBAL['db']->fetch_array($query)) {
				realname_set($value['uid'], $value['username']);//实名
				$voteresult[] = $value;
			}
			$multi = multi($count, $perpage, $page, "cp.php?ac=quiz&op=get&quizid=$quizid&filtrate=".$_GET['filtrate'], 'showvoter');
			//实名
			realname_get();
		}
	}else{
		$wherearr = $voteresult = array();
		$wherearr[] = "quizid='$quizid'";
		if($_GET['filtrate'] == 'win') {
			$wherearr[] = "winflag='1'";
		}elseif($_GET['filtrate'] == 'lost') {
			$wherearr[] = "winflag='0'";
		}
		$wheresql = ' WHERE '.implode(' AND ', $wherearr);
		$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('quizresult')." $wheresql"),0);
		if($count) {
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizresult')." $wheresql ORDER BY totalwin DESC LIMIT $start,$perpage");
			while($value = $_SGLOBAL['db']->fetch_array($query)) {
				realname_set($value['uid'], $value['username']);//实名
				$voteresult[] = $value;
			}
			$multi = multi($count, $perpage, $page, "cp.php?ac=quiz&op=get&quizid=$quizid&filtrate=".$_GET['filtrate'], 'showvoter');
			//实名
			realname_get();
		}
		
		
	}
	
} elseif($op == 'invite') {
	//邀请
	
	$uidarr = explode(',', $quiz['invite']);
	//反转数组
	$newuid = array_flip($uidarr);
	if(submitcheck('invitesubmit')) {
		$ids = empty($_POST['ids'])?array():$_POST['ids'];
		if($ids) {
			//过滤已邀请的用户
			foreach($ids as $key => $uid) {
				if(isset($newuid[$uid])) {
					unset($ids[$key]);
				} else {
					$ids[$key] = intval($uid);
				}
			}
			
			//验证用户的真实性
			$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." WHERE uid IN (".simplode($ids).")");
			$ids = array();
			while($value = $_SGLOBAL['db']->fetch_array($query)) {
				$ids[$value['uid']] = $value['uid'];
			}
			
			//过滤已投票的用户
			$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('quizuser')." WHERE uid IN (".simplode($ids).") AND quizid='$quizid'");
			while($value = $_SGLOBAL['db']->fetch_array($query)) {
				unset($ids[$value['uid']]);
			}
			//合并新数组
			$newinvite = array_merge($uidarr, $ids);
			
			//存入数据库
			if($newinvite) {
				$_SGLOBAL['db']->query("UPDATE ".tname('quizfield')." SET invite='".implode(',', $newinvite)."' WHERE quizid='$quizid'");
			}
			//通知
			$note = cplang('note_quiz_invite', array("space.php?uid=$quiz[uid]&do=quiz&id=$quiz[quizid]", $quiz['subject'], $quiz['joincost']?cplang('reward'):''));
			foreach($ids as $key => $uid) {
				if($uid && $uid != $_SGLOBAL['supe_uid']) {
					notification_add($uid, 'quizinvite', $note);
					notification_add_push($uid, $note, $_SGLOBAL['supe_uid']);
				}
			}
		}
		showmessage('do_success', 'space.php?uid='.$quiz['uid'].'&do=quiz&id='.$quizid);
	}
	
	//分页
	$perpage = 20;
	$page = empty($_GET['page'])?0:intval($_GET['page']);
	if($page<1) $page = 1;
	$start = ($page-1)*$perpage;
		
	//检查开始数
	ckstart($start, $perpage);
		
	$list = array();

	$wherearr = array();
	$_GET['key'] = stripsearchkey($_GET['key']);
	if($_GET['key']) {
		$wherearr[] = " fusername LIKE '%$_GET[key]%' ";
	}
		
	$_GET['group'] = isset($_GET['group'])?intval($_GET['group']):-1;
	if($_GET['group'] >= 0) {
		$wherearr[] = " gid='$_GET[group]'";
	}

	$sql = $wherearr ? 'AND'.implode(' AND ', $wherearr) : '';
		
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('friend')." WHERE uid='$_SGLOBAL[supe_uid]' AND status='1' $sql"), 0);
		
	$fuids = array();
	if($count) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('friend')." WHERE uid='$_SGLOBAL[supe_uid]' AND status='1' $sql ORDER BY num DESC, dateline DESC LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			realname_set($value['fuid'], $value['fusername']);
			$list[] = $value;
			$fuids[] = $value['fuid'];
		}
	}
	$invitearr = array();
	
	//已经参于投票
	$query = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('quizuser')." WHERE uid IN (".simplode($fuids).") AND quizid='$quizid'");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		$invitearr[$value['uid']] = $value['uid'];
	}
	
	//已邀请
	foreach($uidarr as $key => $uid) {
		$invitearr[$uid] = $uid;
	}
	
	realname_get();
		
	//用户组
	$groups = getfriendgroup();
	$groupselect = array($_GET['group'] => ' selected');
		
	$multi = multi($count, $perpage, $page, "cp.php?ac=quiz&op=invite&quizid=$quiz[quizid]&group=$_GET[group]&key=$_GET[key]");
	
}else {
	//添加编辑
	//获取个人分类
	$classarr = $quiz['uid']?getclassarr($quiz['uid']):getclassarr($_SGLOBAL['supe_uid']);
	//获取相册
	$albums = getalbums($_SGLOBAL['supe_uid']);
	
	$tags = empty($quiz['tag'])?array():unserialize($quiz['tag']);
	$quiz['tag'] = implode(' ', $tags);
	
	$quiz['target_names'] = '';
	
	$friendarr = array($quiz['friend'] => ' selected');
	
	$passwordstyle = $selectgroupstyle = 'display:none';
	if($quiz['friend'] == 4) {
		$passwordstyle = '';
	} elseif($quiz['friend'] == 2) {
		$selectgroupstyle = '';
		if($quiz['target_ids']) {
			$names = array();
			$query = $_SGLOBAL['db']->query("SELECT username FROM ".tname('space')." WHERE uid IN ($quiz[target_ids])");
			while ($value = $_SGLOBAL['db']->fetch_array($query)) {
				$names[] = $value['username'];
			}
			$quiz['target_names'] = implode(' ', $names);
		}
	}
	
	
	$quiz['message'] = str_replace('&amp;', '&amp;amp;', $quiz['message']);
	$quiz['message'] = shtmlspecialchars($quiz['message']);
	
	$allowhtml = checkperm('allowhtml');
	
	//好友组
	$groups = getfriendgroup();
	
	//参与热点
	$topic = array();
	$topicid = $_GET['topicid'] = intval($_GET['topicid']);
	if($topicid) {
		$topic = topic_get($topicid);
	}
	if($topic) {
		$actives = array('quiz' => ' class="active"');
	}
	
	//菜单激活
	$menuactives = array('space'=>' class="active"');

	if (empty($quizid)){
		$quiz['joincost'] = $_SGLOBAL['quiz']['joincost'];
		$quiz['portion'] = $_SGLOBAL['quiz']['portion'];
		while(count($quiz['options'])<2){
			$quiz['options'][] = "";
		}
	}
}

$oindex=array("1"=>"A", "2"=>"B");

include_once template("cp_quiz");

?>

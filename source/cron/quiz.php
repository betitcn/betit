<?php

/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: quiz.php 11425 2009-03-05 05:11:17Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//更新缓存
include_once(S_ROOT.'./source/function_cache.php');
config_cache();

//查找到了竞猜截止时间的竞猜
$keytime = $_SGLOBAL['timestamp'];
$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE hasremind='0' AND keyoid='0' AND endtime<='$keytime' ");
while ($value = $_SGLOBAL['db']->fetch_array($query)) {
	if($value['fquizid']==0){
			$note = cplang('note_quiz_remind', array("space.php?uid=$value[uid]&do=quiz&id=$value[quizid]", $value['subject']));
			}else{
			$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE quizid=$value[fquizid] ");
			$value2 = $_SGLOBAL['db']->fetch_array($query2);
			$note = cplang('note_quiz_remind2', array("space.php?uid=$value[uid]&do=quiz&id=$value[quizid]", $value['subject'],"space.php?uid=$value2[uid]&do=quiz&id=$value[fquizid]"));
			}
			admin_notification_add($value['uid'], 'quiz', $note);
			
	$_SGLOBAL['db']->query("UPDATE ".tname('quiz')." SET hasremind='1' WHERE quizid='$value[quizid]'");
	 
}




//超过一天没有公布答案的竞猜

$exceedtime = $_SGLOBAL['timestamp']-86400;
$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE hasexceed='0' AND keyoid='0' AND resulttime<'$exceedtime' ");


while($value = $_SGLOBAL['db']->fetch_array($query)) {
	if($value['fquizid']==0){
	$note = cplang('note_quiz_exceed', array("space.php?uid=$value[uid]&do=quiz&id=$value[quizid]", $value['subject'], 20));
	}else{
	$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quiz')." WHERE quizid=$value[fquizid] ");
	$value2 = $_SGLOBAL['db']->fetch_array($query2);
	$note = cplang('note_quiz_exceed3', array("space.php?uid=$value[uid]&do=quiz&id=$value[quizid]", $value['subject'],"space.php?uid=$value2[uid]&do=quiz&id=$value[fquizid]"));
	}
	admin_notification_add($value['uid'], 'quizexceed', $note);
	notification_add_push($value['uid'],  $note, 1);
	$_SGLOBAL['db']->query("UPDATE ".tname('quiz')." SET hasexceed='1' WHERE quizid='$value[quizid]'");
	  

	//获取参与用户
	$query3 = $_SGLOBAL['db']->query("SELECT uid, count(*)*$value[joincost] as credit FROM ".tname('quizuser')." WHERE quizid='$value[quizid]' group by uid");
	runlog("qqqqqq","SELECT uid, count(*)*$value[joincost] as credit FROM ".tname('quizuser')." WHERE quizid='$value[quizid]' group by uid");
	while($value3 = $_SGLOBAL['db']->fetch_array($query3)) {
	
		//返还积分
		$reward = array(
			'credit' => 0,
			'experience' => 0
		);
		$reward['credit'] = "$value3[credit]";

		$setarr = array();
		if($reward['credit']) {
			$setarr['credit'] = "credit=credit+$reward[credit]";
		}
	
		if($reward['experience']) {
			$setarr['experience'] = "experience=experience+$reward[experience]";
		}
	

	
		$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET ".implode(',', $setarr)." WHERE uid='$value3[uid]'");	
		
		
		$note = cplang('note_quiz_exceed2', array("space.php?uid=$value[uid]&do=quiz&id=$value[quizid]", $value['subject'],$value3['credit']));
		admin_notification_add($value3['uid'], 'quiz', $note);
		notification_add_push($value3['uid'],  $note, 1);
	}	
	
}


?>

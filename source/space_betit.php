<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: space_betit.php 13208 2009-08-20 06:31:35Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}
$minhot = $_SCONFIG['feedhotmin']<1?3:$_SCONFIG['feedhotmin'];
$page = empty($_GET['page'])?1:intval($_GET['page']);
$start = empty($_GET['start'])?0:intval($_GET['start']);
if($page<1) $page=1;
	$perpage = 20;
	$start = ($page-1)*$perpage;
	$minhot = $_SCONFIG['feedhotmin']<1?3:$_SCONFIG['feedhotmin'];
	//��鿪ʼ��
	ckstart($start, $perpage);
$op = empty($_GET['op'])?'':$_GET['op'];

if($op=='download'){
	include_once template("space_download");
}elseif($op=='team'){
	include_once template("space_team");
	}elseif($op=='hot'){
	$query2 = $_SGLOBAL['db']->query("SELECT distinct b.tagid,bf.tagname,sum(b.tagid) as gp FROM ".tname('tagquiz')." b LEFT JOIN ".tname('tag')." bf ON bf.tagid=b.tagid group by b.tagid order by gp desc limit 0,8");
			while ($value2 = $_SGLOBAL['db']->fetch_array($query2)) {
				$view[] = $value2;
		}
		
		 $count1 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>0 and experience<500"),0);
		 $count2 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>500 and experience<1000"),0);
		 $count3 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>1000 and experience<1500"),0);
		 $count4 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>1500 and experience<2000"),0);
		 $count5 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>2000 and experience<2500"),0);
		 $count6 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>2500 and experience<3000"),0);
		 $count7 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>3000 and experience<3500"),0);
		 $count8 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>3500 and experience<4000"),0);
		 $count9 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>4000 and experience<4500"),0);
		  $count10 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>4500 and experience<5000"),0);
		  $count11 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('space')." WHERE experience>5000"),0);
		

  
	$query = $_SGLOBAL['db']->query("SELECT p.*,pf.* FROM  ".tname('quiz')." p USE INDEX (voternum)
					LEFT JOIN ".tname('feed')." pf ON pf.id=p.quizid
					ORDER BY p.voternum DESC LIMIT $start,$perpage");
		while ($value = $_SGLOBAL['db']->fetch_array($query)) {
			if(ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
				realname_set($value['uid'], $value['username']);
			//if ($value['idtype'] == 'quizid')
			//{
				$value["commentnum"] =  $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('comment')." WHERE id='$value[quizid]'  "),0);
				$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE id='$value[id]'  ORDER BY dateline DESC LIMIT 0,3");
				while ($value2 = $_SGLOBAL['db']->fetch_array($query2)) {
					realname_set($value2['authorid'], $value2['author']);//ʵ��
					
					$value["comments"][] = $value2;
				
				//}
			}
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
			
			
			$feed[] = $value;
		}
		$count++;
	}



	include_once template("space_hot");
	}elseif($op=='weibo'){
		include_once template("space_weibo");
	}else{
	include_once template("space_home");
}

	?>
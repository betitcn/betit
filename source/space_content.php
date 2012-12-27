<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.

	$Id: space_feed.php 13194 2009-08-18 07:44:40Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$csql = $cid?"cid='$cid' AND":'';
$page =$_GET['Page'];

	$perpage = 20;
	$perpage = mob_perpage($perpage);
	
	$start = ($page-1)*$perpage;


	
		$query = $_SGLOBAL['db']->query("SELECT p.*,pf.* FROM  ".tname('quiz')." p USE INDEX (voternum)
					LEFT JOIN ".tname('feed')." pf ON pf.id=p.quizid
					ORDER BY p.voternum DESC LIMIT $start,$perpage");
	while ($value = $_SGLOBAL['db']->fetch_array($query)) {
		realname_set($value['uid'], $value['username']);
		if(ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
			realname_set($value['uid'], $value['username']);
			//if ($value['idtype'] == 'quizid')
			//{
				$value["commentnum"] =  $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('comment')." WHERE id='$value[quizid]'  "),0);
				$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE id='$value[quizid]'  ORDER BY dateline LIMIT 0,3");
				while ($value2 = $_SGLOBAL['db']->fetch_array($query2)) {
					realname_set($value2['authorid'], $value2['author']);//สตร๛
					
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

/*$query1 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE  id='$feed[id]' AND idtype='quizid' ORDER BY dateline LIMIT 0,3");
while ($value1 = $_SGLOBAL['db']->fetch_array($query1)) {
					$list[]=$value1;
						}

	*/
 realname_get();
include_once template("space_content");
?>
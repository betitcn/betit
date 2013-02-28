<?php
$keywords = $_REQUEST['keywords'];
$perpage = $_REQUEST['perpage']?$_REQUEST['perpage']:20;
$start = empty($_REQUEST['page'])?0:intval($_REQUEST['page'])*$perpage;
if($keywords){
	$query3 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid=1");
		while($value3 = $_SGLOBAL['db']->fetch_array($query3)){
		foreach($value3 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}
$query4 = $_SGLOBAL['db']->query("SELECT p.*,pf.* FROM  ".tname('quiz')." p USE INDEX (voternum)  LEFT JOIN ".tname('feed')." pf ON pf.id=p.quizid
     where p.subject like '%$keywords%' AND p.uid IN('".implode("','",  $searcharr)."') AND p.id!=1 limit $start,$perpage;"); 
	
while($value4 = $_SGLOBAL['db']->fetch_array($query4))
 { 
$a=$value4['quizid'];
if($a){
$query = $_SGLOBAL['db']->query("SELECT bf.*, b.* FROM ".tname('quiz')." b 
  LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=b.quizid 
  WHERE b.quizid='$a'");
 $quiz = $_SGLOBAL['db']->fetch_array($query);
 $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE quizid='$a'");

 while($value = $_SGLOBAL['db']->fetch_array($query)){
 
  if ($value['picid']){
   $query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE picid='$value[picid]'");
   $value2 = $_SGLOBAL['db']->fetch_array($query2);
   $value['pic'] = pic_get($value2['filepath'], $value2['thumb'], $value2['remote']);
		}
  $quiz['options'][] = $value;
  $quiz['avatar'] = capi_avatar($value["uid"]);
	 $quiz["title_data"]["actor"] = $quiz["username"];
	$quiz["title_data"]["subject"] = strip_tags($quiz["subject"]);
	}
}
 $b[]=$quiz;


 }
capi_showmessage_by_data('rest_success',0,array("feeds"=>$b)); 
}
?>
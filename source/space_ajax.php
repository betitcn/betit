<?php
$keywords = $_POST['keywords'];
if($keywords){
	$query2 = $_SGLOBAL['db']->query("SELECT uid FROM ".tname('space')." where groupid=1");
		while($value2 = $_SGLOBAL['db']->fetch_array($query2)){
		foreach($value2 as $key => $val) {
			$searcharr[] = intval($val);
			}
		}
$query = $_SGLOBAL['db']->query("SELECT p.*,pf.* FROM  ".tname('quiz')." p USE INDEX (voternum)  LEFT JOIN ".tname('feed')." pf ON pf.id=p.quizid
     where p.subject like '%$keywords%' AND p.uid IN('".implode("','",  $searcharr)."') AND p.id!=1 limit 0,10;"); 
	
while($value = $_SGLOBAL['db']->fetch_array($query))
 { 
     echo "<li><span style='display:none'>".$value['quizid'] . '|</span>'.$value['subject']. '</li>';
  
 }
 
}
?>
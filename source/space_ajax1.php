<?php
$id = $_POST['id'];
if($id){
$query = $_SGLOBAL['db']->query("SELECT bf.*, b.* FROM ".tname('quiz')." b 
  LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=b.quizid 
  WHERE b.quizid='$id'");
 $quiz = $_SGLOBAL['db']->fetch_array($query);
 $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE quizid='$id'");
 while( $value = $_SGLOBAL['db']->fetch_array($query))
 {
  if ($value['picid']){
   $query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE picid='$value[picid]'");
   $value2 = $_SGLOBAL['db']->fetch_array($query2);
   $value['pic'] = pic_get($value2['filepath'], $value2['thumb'], $value2['remote']);
  }
  $quiz['options'][] = $value;
 }
 echo json_encode($quiz);
}
if($id){
 $query = $_SGLOBAL['db']->query("SELECT bf.*, b.* FROM ".tname('quiz')." b 
  LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=b.quizid 
  WHERE b.quizid='$id'");
 $quiz = $_SGLOBAL['db']->fetch_array($query);
 
 //СЎПо
 $query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE quizid='$id'");
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
?>
<?php

$milliSecond = strftime("%H%M%S",time());
$rndFileName = "./tmpfile/".$milliSecond.".jpg";
file_put_contents($rndFileName, file_get_contents($_GET["url"]));

$url = "http://www.familyday.com.cn/dapi/cp.php?ac=upload";
$file_name_with_full_path = realpath($rndFileName);
$data = array(
	"op"=>"uploadphoto",
	"topicid"=>"0",
	"pic_title"=>"",
	"m_auth"=>$_GET["m_auth"],
	"Filedata"  => "@".$file_name_with_full_path,    
);
$result = uploadByCURL($data,$url);
runlog('upload', $result);

$result = json_decode($result);
$picid = $result->data->picid;

$url = "http://www.familyday.com.cn/dapi/cp.php?ac=photo&m_auth=".$_GET["m_auth"]."&title[".$picid."]=&message=".$_GET["message"]."&friend=0&come=wx&makefeed=1&photosubmit=1&tags=д╛хо©у╪Д";
$result = file_get_contents($url);
runlog('upload', $result);

unlink($file_name_with_full_path);
?>
<?php
/**
  * wechat php test
  */

include_once('./../common.php');

include_once( 'botutil.php' );


//define your token
define("TOKEN", "betit");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();


class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

	public function welcome($toUsername) {
        if($toUsername=="gh_71e78c3b0890"){
            return      "你好！欢迎来到“小赢家”，绑定微信机器人，帮你快速了解最新，最热竞猜，及时掌握最新最好玩的竞猜并进行有趣的评论！
•回复【1】——查看大赢家竞猜；
•回复【2】——查看我的好友排行榜；
•回复【3】——登陆大赢家；
";
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		
      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
				$picUrl = $postObj->PicUrl;
                $keyword = trim($postObj->Content);
                $time = time();
				
				if(!empty( $keyword ))
                {
					
					if ($keyword=='Hello2BizUser'){
						$msgType = "text";
						$contentStr = "你好！欢迎来到“小赢家”，绑定微信机器人，帮你快速了解最新，最热竞猜，及时掌握最新最好玩的竞猜并进行有趣的评论！
•回复【1】——查看大赢家竞猜；
•回复【2】——查看我的好友排行榜；
•回复【3】——登陆大赢家；
";
						$resultStr = makeText($fromUsername, $toUsername, $time, $msgType, $contentStr); 
					}elseif($keyword == "1"){
						$msgType = "news";
						$con = mysql_connect("localhost","betit","mrrealbetit");
						if (!$con)
						  {
						  die('Could not connect: ' . mysql_error());
						  }
						mysql_select_db("betit", $con);
						$result = mysql_query("SELECT * FROM uchome_space WHERE wxkey='".$fromUsername."'");
						if($con)
						{					
							mysql_close($con);
							$jsonurl = "http://www.betit.cn/capi/space.php?do=feed&page=0&perpage=10&view=quiz";
							$json = file_get_contents($jsonurl,0,null,null);
							$json_output = json_decode($json);

							if ($json_output->code==0){
								$articles = array();
								
								$pic = "http://www.betit.cn/image/org_img/logo.jpg";
								$url = "http://www.betit.cn/wx/wx.php";
								$articles[] = makeArticleItem("大赢家竞猜", "大赢家竞猜", $pic, $url);
								$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "大赢家竞猜",$articles);	



									$msg = $json_output->data->feeds[0]->username .":" . $json_output->data->feeds[0]->body_data->subject;
									$pic = "";
								
									$url = "http://www.betit.cn/wx/wx.php?do=feed&id=".$json_output->data->feeds[0]->id ."&uid=".$json_output->data->feeds[0]->uid."&wxkey=".$fromUsername;
									$articles[] = makeArticleItem($msg, $msg, $pic, $url);
									$url = "http://www.betit.cn/wx/wx.php?do=feed&id=".$json_output->data->feeds[0]->id."&uid=".$json_output->data->feeds[0]->uid."&wxkey=".$fromUsername;
									$pic = $json_output->data->feeds[0]->body_data->option[0]->pic;
									$option="A选项:".$json_output->data->feeds[0]->body_data->option[0]->option;
									$articles[] = makeArticleItem($option,$option, $pic, $url);
									$url = "http://www.betit.cn/wx/wx.php?do=feed&id=".$json_output->data->feeds[0]->id."&uid=".$json_output->data->feeds[0]->uid."&wxkey=".$fromUsername;
									$pic = $json_output->data->feeds[0]->body_data->option[1]->pic;
									$option="B选项:".$json_output->data->feeds[0]->body_data->option[1]->option;
									$articles[] = makeArticleItem($option,$option, $pic, $url);
									$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "大赢家竞猜1",$articles); 


									$msg = $json_output->data->feeds[1]->username .":" . $json_output->data->feeds[1]->body_data->subject;

									$pic = "";
									$url = "http://www.betit.cn/wx/wx.php?do=feed&id=".$json_output->data->feeds[1]->id ."&uid=".$json_output->data->feeds[1]->uid."&wxkey=".$fromUsername;
									$articles[] = makeArticleItem($msg, $msg, $pic, $url);
									$url = "http://www.betit.cn/wx/wx.php?do=feed&id=".$json_output->data->feeds[1]->id."&uid=".$json_output->data->feeds[1]->uid."&wxkey=".$fromUsername;
									$pic = $json_output->data->feeds[1]->body_data->option[0]->pic;
									$option="A选项:".$json_output->data->feeds[1]->body_data->option[0]->option;
									$articles[] = makeArticleItem($option,$option, $pic, $url);
									$url = "http://www.betit.cn/wx/wx.php?do=feed&id=".$json_output->data->feeds[1]->id."&uid=".$json_output->data->feeds[1]->uid."&wxkey=".$fromUsername;
									$pic = $json_output->data->feeds[1]->body_data->option[1]->pic;
									$option="B选项:".$json_output->data->feeds[1]->body_data->option[1]->option;
									$articles[] = makeArticleItem($option,$option, $pic, $url);
									$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "大赢家竞猜2",$articles); 

									$msg = $json_output->data->feeds[2]->username .":" . $json_output->data->feeds[2]->body_data->subject;
									$pic = "";
									$url = "http://www.betit.cn/wx/wx.php?do=feed&id=".$json_output->data->feeds[2]->id ."&uid=".$json_output->data->feeds[2]->uid."&wxkey=".$fromUsername;
									$articles[] = makeArticleItem($msg, $msg, $pic, $url);
									$url = "http://www.betit.cn/wx/wx.php?do=feed&id=".$json_output->data->feeds[2]->id."&uid=".$json_output->data->feeds[2]->uid."&wxkey=".$fromUsername;
									$pic = $json_output->data->feeds[2]->body_data->option[0]->pic;
									$option="A选项:".$json_output->data->feeds[2]->body_data->option[0]->option;
									$articles[] = makeArticleItem($option,$option, $pic, $url);
									$url = "http://www.betit.cn/wx/wx.php?do=feed&id=".$json_output->data->feeds[2]->id."&uid=".$json_output->data->feeds[2]->uid."&wxkey=".$fromUsername;
									$pic = $json_output->data->feeds[2]->body_data->option[1]->pic;
									$option="B选项:".$json_output->data->feeds[2]->body_data->option[1]->option;
									$articles[] = makeArticleItem($option,$option, $pic, $url);
									$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "大赢家竞猜3",$articles);
									 
								}
								$url = "http://www.betit.cn/wx/wx.php?wxkey=".$fromUsername;
								$pic = "http://www.betit.cn/image/org_img/logo.jpg";
								$articles[] = makeArticleItem("更多竞猜...", "更多竞猜...", $pic, $url);
								$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "大赢家动态",$articles); 

				}else{
							mysql_close($con);
							$url = "http://www.betit.cn/wx/wx.php?do=login&wxkey=".$fromUsername;
							$pic = "http://www.familyday.com.cn/wx/images/bind.jpg";
							$articles[] = makeArticleItem("大赢家登陆", "你还没有登陆大赢家，请点击进入大赢家登录页", $pic, $url);
							$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "登陆大赢家帐号",$articles); 
						}
						

					}elseif($keyword == "2"){

						$con = mysql_connect("localhost","betit","mrrealbetit");
						if (!$con)
						{
							die('Could not connect: ' . mysql_error());
						}
						mysql_select_db("betit", $con);
						$result = mysql_query("SELECT * FROM uchome_space WHERE wxkey='".$fromUsername."'");
						$device = "";
						if($row = mysql_fetch_array($result))
						{	
							$msgType = "news";
							$uid=$row['uid'];
							$jsonurl = "http://www.betit.cn/capi/space.php?uid=$uid&do=friend&wxkey=".$fromUsername;
							$json = file_get_contents($jsonurl,0,null,null);
							$json_output = json_decode($json);
						if ($json_output->code==0){
								$url = "http://www.betit.cn/wx/wx.php?do=billboard&wxkey=".$fromUsername;
								$pic = $json_output->data->friends[0]->avatar;
								$option="好友排行榜";
								$articles[] = makeArticleItem($option, $option, $pic, $url);
								$url = "http://www.betit.cn/wx/wx.php?do=billboard&wxkey=".$fromUsername;
								$pic = $json_output->data->friends[1]->avatar;
							if($json_output->data->friends[1]->name){
								$option="N0.1  ".$json_output->data->friends[1]->name;
							}else{
								$option="N0.1  ".$json_output->data->friends[1]->username;
							}
								$articles[] = makeArticleItem($option, $option, $pic, $url);
								$url = "http://www.betit.cn/wx/wx.php?do=billboard&wxkey=".$fromUsername;
								$pic = $json_output->data->friends[2]->avatar;
							if($json_output->data->friends[2]->name){
								$option="N0.2  ".$json_output->data->friends[2]->name;
							}else{
								$option="N0.2  ".$json_output->data->friends[2]->username;
							}
								$articles[] = makeArticleItem($option, $option, $pic, $url);
								$url = "http://www.betit.cn/wx/wx.php?do=billboard&wxkey=".$fromUsername;
								$pic = $json_output->data->friends[3]->avatar;
							if($json_output->data->friends[3]->name){
								$option="N0.3  ".$json_output->data->friends[3]->name;
							}else{
								$option="N0.3  ".$json_output->data->friends[3]->username;
							}
								$articles[] = makeArticleItem($option, $option, $pic, $url);
								$url = "http://www.betit.cn/wx/wx.php?do=billboard&wxkey=".$fromUsername;
								$pic = "http://www.betit.cn/image/org_img/logo.jpg";
								$articles[] = makeArticleItem("更多...", "更多...", $pic, $url);
								$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "好友排行榜",$articles); 
							}

						}else{
						
						$msgType = "news";
							$url = "http://www.betit.cn/wx/wx.php?do=login&wxkey=".$fromUsername;
							$pic = "http://www.familyday.com.cn/wx/images/bind.jpg";
							$articles[] = makeArticleItem("绑定微信帐号", "你还没有绑定微信号，请点击进入微信绑定页", $pic, $url);
							$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "绑定微信帐号",$articles);
							mysql_close($con); 
						
						}

					}elseif($keyword == "3"){
						$msgType = "news";
						$url = "http://www.betit.cn/wx/wx.php?do=login&wxkey=".$fromUsername;
						$pic = "http://www.familyday.com.cn/wx/images/bind.jpg";
						$articles[] = makeArticleItem("小赢家要通过微博把你的微信号绑定到大赢家", "小赢家要通过新浪微博把你的微信号绑定到大赢家", $pic, $url);
						$url = "http://www.betit.cn/wx/connect.php?site=weibo&ac=index&wxkey=".$fromUsername;
						$pic = "http://www.familyday.com.cn/wx/images/bind-icon.jpg";
						$articles[] = makeArticleItem("小赢家新浪微博登陆", "小赢家新浪微博登陆", $pic, $url);
						$url = "http://www.betit.cn/wx/connect.php?site=qq&ac=index&wxkey=".$fromUsername;
						$pic = "http://www.familyday.com.cn/wx/images/bind-icon.jpg";
						$articles[] = makeArticleItem("小赢家腾讯微博登陆", "小赢家腾讯微博登陆", $pic, $url);
						$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "绑定微信帐号",$articles);
					}else{

						$con = mysql_connect("localhost","betit","mrrealbetit");
						if (!$con)
						{
							die('Could not connect: ' . mysql_error());
						}
						mysql_select_db("betit", $con);
						$result = mysql_query("SELECT * FROM uchome_space WHERE wxkey='".$fromUsername."'");
						$device = "";
						if($row = mysql_fetch_array($result))
						{	
							$device = object2array(json_decode($row["device"]));
						}
						
						if(isset($device['cp'])&&$device['cp']==23)
						{
							$device['picUrl'] = object2array($device['picUrl']);
							$device['picUrl'] = $device['picUrl']["0"];
							$path = "/wx/wx.php?do=upload&url=".$device['picUrl']."&m_auth=".rawurlencode($device["auth"])."&message=".$keyword;
							asyn_get($path);
							unset($device['cp']);
							unset($device['picUrl']);
							$result = mysql_query("UPDATE  uchome_space SET device='".json_encode($device)."' WHERE wxkey='".$fromUsername."'");
							$msgType = "text";
							$contentStr = "上传可能需要点时间，过一会你输入【1】就可以看到了";
							$resultStr = makeText($fromUsername, $toUsername, $time, $msgType, $contentStr);
							echo $resultStr;
						}else{
							$msgType = "text";
							$contentStr = "你好！欢迎来到“小赢家”，绑定微信机器人，帮你快速了解最新，最热竞猜，及时掌握最新最好玩的竞猜并进行有趣的评论！
•回复【1】——查看大赢家竞猜；
•回复【2】——查看我的好友排行榜；
•回复【3】——登陆大赢家；
";
							$resultStr = makeText($fromUsername, 

$toUsername, $time, $msgType, $contentStr); 
						}
						mysql_close($con);
					}
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
	
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}




?> 
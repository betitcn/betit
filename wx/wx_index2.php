<?php
/**
  * wechat php test
  */

include_once('./../common.php');
include_once( 'botutil.php' );


//define your token
define("TOKEN", "family");
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
            return      "你好！欢迎来到“家庭圈”，绑定微信机器人，帮你快速了解家人动态、发布照片和日记、立即跟家人分享！

•回复【1】——查看你的家庭圈动态；
•回复【2】——发表照片或日记；
•回复【3】——绑定微信、注册家庭圈；
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
						$contentStr = "你好！欢迎来到“家庭圈”，绑定微信机器人，帮你快速了解家人动态、发布照片和日记、立即跟家人分享！

•回复【1】——查看你的家庭圈动态；
•回复【2】——发表照片或日记；
•回复【3】——绑定微信、注册家庭圈；
";
						$resultStr = makeText($fromUsername, $toUsername, $time, $msgType, $contentStr); 
					}elseif($keyword == "1"){
						$msgType = "news";
						$con = mysql_connect("localhost","familyday","fmd30991261");
						if (!$con)
						  {
						  die('Could not connect: ' . mysql_error());
						  }
						mysql_select_db("familyday", $con);
						$result = mysql_query("SELECT * FROM uchome_space WHERE wxkey='".$fromUsername."'");
						if($row = mysql_fetch_array($result))
						{					
							mysql_close($con);
							$jsonurl = "http://www.familyday.com.cn/dapi/space.php?do=wxfeed&perpage=5&page=1&wxkey=".$fromUsername;
							$json = file_get_contents($jsonurl,0,null,null);
							$json_output = json_decode($json);

							if ($json_output->data->error==0){
								$articles = array();
								foreach ($json_output->data as $key => $obj)
								{
									$obj->message = html_entity_decode($obj->message);
									$obj->message = html_entity_decode($obj->message);
									$obj->message = strip_tags($obj->message);
									$msg = $obj->username.":".$obj->title;

									if ($obj->image_1)
									{
										$pic = $obj->image_1;
									}else{
										$pic = "http://www.familyday.com.cn/wx/image/nopic.gif";
									}
									$url = "http://www.familyday.com.cn/wx/wx.php?do=detail&id=".$obj->id."&uid=".$obj->uid."&idtype=".$obj->idtype."&wxkey=".$fromUsername;
									$articles[] = makeArticleItem($msg, $msg, $pic, $url);
								}
								$url = "http://www.familyday.com.cn/wx/wx.php?do=feed&wxkey=".$fromUsername;
								$pic = "http://www.familyday.com.cn/wx/images/feed-icon.jpg";
								$articles[] = makeArticleItem("更多...", "更多...", $pic, $url);
								$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "家庭动态",$articles); 
							}
						}else{
							mysql_close($con);
							$url = "http://www.familyday.com.cn/wx/wx.php?do=bind&wxkey=".$fromUsername;
							$pic = "http://www.familyday.com.cn/wx/images/bind.jpg";
							$articles[] = makeArticleItem("绑定微信帐号", "你还没有绑定微信号，请点击进入微信绑定页", $pic, $url);
							$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "绑定微信帐号",$articles); 
						}
						

					}elseif($keyword == "2"){

						$con = mysql_connect("localhost","familyday","fmd30991261");
						if (!$con)
						{
							die('Could not connect: ' . mysql_error());
						}
						mysql_select_db("familyday", $con);
						$result = mysql_query("SELECT * FROM uchome_space WHERE wxkey='".$fromUsername."'");
						$device = "";
						if($row = mysql_fetch_array($result))
						{	
							$device = object2array(json_decode($row["device"]));
						}
						
						if ($device["os"]  == "ios6" ){
							$msgType = "news";
						
							$jsonurl = "http://www.familyday.com.cn/dapi/space.php?do=wxfeed&perpage=5&page=1&wxkey=".$fromUsername;;
							$json = file_get_contents($jsonurl,0,null,null);
							$json_output = json_decode($json);

							if ($json_output->data->error==0){
								$url = "http://www.familyday.com.cn/wx/wx.php?do=cp&op=photo&wxkey=".$fromUsername;
								$pic = "http://www.familyday.com.cn/wx/images/image-icon.jpg";
								$articles[] = makeArticleItem("发布图片，分享给家人", "发布图片，分享给家人", $pic, $url);

								$url = "http://www.familyday.com.cn/wx/wx.php?do=cp&op=photo&wxkey=".$fromUsername;
								$pic = "http://www.familyday.com.cn/wx/images/image2-icon.jpg";
								$articles[] = makeArticleItem("发一张图片", "发一张图片", $pic, $url);
								
								$url = "http://www.familyday.com.cn/wx/wx.php?do=cp&op=blog&wxkey=".$fromUsername;
								$pic = "http://www.familyday.com.cn/wx/images/blog-icon.jpg";
								$articles[] = makeArticleItem("发一篇日记", "发一篇日记", $pic, $url);

								$url = "http://www.familyday.com.cn/wx/wx.php?do=feed&wxkey=".$fromUsername;
								$pic = "http://www.familyday.com.cn/wx/images/feed-icon.jpg";
								$articles[] = makeArticleItem("全部家庭圈动态", "全部家庭圈动态", $pic, $url);
								$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "发布",$articles); 

							}else{
								
								$url = "http://www.familyday.com.cn/wx/wx.php?do=bind&wxkey=".$fromUsername;
								$pic = "http://www.familyday.com.cn/wx/images/bind.jpg";
								$articles[] = makeArticleItem("绑定微信帐号", "你还没有绑定微信号，请点击进入微信绑定页", $pic, $url);
								$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "绑定微信帐号",$articles);  
							}
						}elseif(empty($device)||$device['os']=''){
							$msgType = "news";
							$url = "http://www.familyday.com.cn/wx/wx.php?do=bind&wxkey=".$fromUsername;
							$pic = "http://www.familyday.com.cn/wx/images/bind.jpg";
							$articles[] = makeArticleItem("绑定微信帐号", "你还没有绑定微信号，请点击进入微信绑定页", $pic, $url);
							$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "绑定微信帐号",$articles);  
						}else{
							
							if(isset($device['cp']))
							{
								$op = $device['cp'];
							}else{
								$op = 21;
							}
							if ($op==21){
								$msgType = "text";
								$contentStr = "上传图片,发到你的家庭圈";
								$resultStr = makeText($fromUsername, $toUsername, $time, $msgType, $contentStr);
							}
							$op = $op+1;
							$device['cp'] = $op;
							
							$result = mysql_query("UPDATE  uchome_space SET device='".json_encode($device)."' WHERE wxkey='".$fromUsername."'");
							
						}
						mysql_close($con);

					}elseif($keyword == "3"){
						$msgType = "news";
						$url = "http://www.familyday.com.cn/wx/wx.php?do=bind&wxkey=".$fromUsername;
						$pic = "http://www.familyday.com.cn/wx/images/bind.jpg";
						$articles[] = makeArticleItem("把微信号绑定到我的家庭圈", "把微信号绑定到我的家庭圈", $pic, $url);

						$url = "http://www.familyday.com.cn/wx/wx.php?do=bind&wxkey=".$fromUsername;
						$pic = "http://www.familyday.com.cn/wx/images/bind-icon.jpg";
						$articles[] = makeArticleItem("绑定微信", "绑定微信", $pic, $url);

						$url = "http://www.familyday.com.cn/wx/wx.php?do=reg&wxkey=".$fromUsername;
						$pic = "http://www.familyday.com.cn/wx/images/reg-icon.jpg";
						$articles[] = makeArticleItem("注册到家庭圈帐号\n（会自动绑定微信）", "注册到家庭圈帐号\n（会自动绑定微信）", $pic, $url);

						$url = "http://www.familyday.com.cn/wx/wx.php?do=invite&wxkey=".$fromUsername;
						$pic = "http://www.familyday.com.cn/wx/images/invite-icon.jpg";
						$articles[] = makeArticleItem("邀请家人", "邀请家人", $pic, $url);

						$url = "http://www.familyday.com.cn/wx/about/family.html?wxkey=".$fromUsername;
						$pic = "http://www.familyday.com.cn/wx/images/about-icon.jpg";
						$articles[] = makeArticleItem("关于微信家庭圈", "关于微信家庭圈", $pic, $url);

						$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "绑定微信帐号",$articles); 
					}elseif($keyword == "9"){
						$con = mysql_connect("localhost","familyday","fmd30991261");
						if (!$con)
						{
							die('Could not connect: ' . mysql_error());
						}
						mysql_select_db("familyday", $con);
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
							$path = "/wx/wx.php?do=upload&url=".$device['picUrl']."&m_auth=".rawurlencode($device["auth"])."&message=";
							asyn_get($path);
							unset($device['cp']);
							unset($device['picUrl']);
							$result = mysql_query("UPDATE  uchome_space SET device='".json_encode($device)."' WHERE wxkey='".$fromUsername."'");
							$msgType = "text";
							$contentStr = "正在上传，请输入1刷新动态";
							$resultStr = makeText($fromUsername, $toUsername, $time, $msgType, $contentStr);
							echo $resultStr;
						}
						mysql_close($con);
					}else{

						$con = mysql_connect("localhost","familyday","fmd30991261");
						if (!$con)
						{
							die('Could not connect: ' . mysql_error());
						}
						mysql_select_db("familyday", $con);
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
							$contentStr = "你好！欢迎来到“家庭圈”，绑定微信机器人，帮你快速了解家人动态、发布照片和日记、立即跟家人分享！

•回复【1】——查看你的家庭圈动态；
•回复【2】——发表照片或日记；
•回复【3】——绑定微信、注册家庭圈；
";
							$resultStr = makeText($fromUsername, $toUsername, $time, $msgType, $contentStr); 
						}
						mysql_close($con);
					}
                	echo $resultStr;
                }elseif(!empty($picUrl)){
					/* $msgType = "text";
					$contentStr = $picUrl;
					$resultStr = makeText($fromUsername, $toUsername, $time, $msgType, $contentStr);
					echo $resultStr; */

					$con = mysql_connect("localhost","familyday","fmd30991261");
					if (!$con)
					{
						die('Could not connect: ' . mysql_error());
					}
					mysql_select_db("familyday", $con);
					$result = mysql_query("SELECT * FROM uchome_space WHERE wxkey='".$fromUsername."'");
					$device = "";
					if($row = mysql_fetch_array($result))
					{	
						$device = object2array(json_decode($row["device"]));
					}
					
					$device["cp"]=23;
					$device["picUrl"]=$picUrl;
					$result = mysql_query("UPDATE  uchome_space SET device='".json_encode($device)."' WHERE wxkey='".$fromUsername."'");
					mysql_close($con);

					// asyn_get($path);
					$msgType = "text";
					$contentStr = "给图片增加一句有趣的文字描述
（输入【9】回到主菜单）";
					$resultStr = makeText($fromUsername, $toUsername, $time, $msgType, $contentStr);
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
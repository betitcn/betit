<?php
/**
  * wechat php test
  */


include_once( 'botutil.php' );


$welcome = '你好！欢迎来到"我家"，微信客户端的智能机器人，可以帮你迅速查询家人动态。你也可以直接通过微信快速发布照片和文字，立即跟家人分享有爱的每一刻!\n\n*
回复"1"，查看家人动态;\n(回复“家人电话号码”可以查看指定家人的动态信息。)\n*回复“2”,发表;\n回复"0"，注册或绑定账号;';
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
        if($toUsername=="gh_51b7466306d9"){//微信原始id
            return      $welcome;//欢迎语
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
                $keyword = trim($postObj->Content);
                $time = time();
                addUser2($fromUsername);
				
				if(!empty( $keyword ))
                {
					
					if ($key=='Hello2BizUser'){
						$msgType = "text";
						$contentStr = $this->welcome($toUsername);
						$resultStr = makeText($fromUsername, $toUsername, $time, $msgType, $contentStr); 
					}elseif($keyword == "0"){
						$msgType = "news";
						$url = "http://www.familyday.com.cn/wx.php?do=bind&username=".$fromUsername;
						$pic = "http://www.familyday.com.cn/wx/template/css/images/logo2-2x.jpg";
						
						$articles[] = makeArticleItem("绑定微信帐号", "请点击进入微信绑定页", $pic, $url);
						$resultStr = makeArticles($fromUsername, $toUsername, $time, $msgType, "绑定微信帐号",$articles); 
					}else{
						$msgType = "text";
						$contentStr = $this->welcome($toUsername);
						$resultStr = makeText($fromUsername, $toUsername, $time, $msgType, $contentStr); 
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
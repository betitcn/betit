<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: do.php 12354 2009-06-11 08:14:06Z liguode $
*/


function capi_mkjson($response='', $callback=''){
	global $_SGLOBAL;
	$response = empty($response)?$_SGLOBAL['mresponse']:$response;
	if ($callback){
		header('Cache-Control: no-cache, must-revalidate');
		header('Content-Type: text/javascript;charset=utf-8');
		echo $callback.'('.json_encode($response).');';
	}else{
		// application/x-json will make error in iphone, so I use the text/json
		// instead of the orign mine type
		header('Cache-Control: no-cache, must-revalidate');
		header('Content-Type: text/json;'); 
		
		echo json_encode($response);
		

	}
	exit();
}

function capi_showmessage_by_data($msgkey, $code=1, $data=array()){
	obclean();

	//去掉广告
	$_SGLOBAL['ad'] = array();
	
	//语言
	include_once(S_ROOT.'./language/lang_showmessage.php');
	if(isset($_SGLOBAL['msglang'][$msgkey])) {
		$message = lang_replace($_SGLOBAL['msglang'][$msgkey], $values);
	} else {
		$message = $msgkey;
	}
	$r = array();
	$r['code'] = $code;
	$r['data'] = $data;
	$r['msg'] = $message;
	$r['action'] = $msgkey;
	capi_mkjson($r, $_REQUEST['callback'] );
}

function capi_mkfeed($feed)
{
	$feed['title_data'] = empty($feed['title_data'])?array():unserialize($feed['title_data']);
	if(!is_array($feed['title_data'])) $feed['title_data'] = array();
	$feed['body_data'] = empty($feed['body_data'])?array():unserialize($feed['body_data']);
	if(!is_array($feed['body_data'])) $feed['body_data'] = array();
	return $feed;
}

function capi_mkfeedtitle($feed)
{
	global $_SGLOBAL, $_SN, $_SCONFIG;


	$feed['title_data'] = empty($feed['title_data'])?array():unserialize($feed['title_data']);
	if(!is_array($feed['title_data'])) $feed['title_data'] = array();
	
	//title
	$searchs = $replaces = array();
	if($feed['title_data'] && is_array($feed['title_data'])) {
		foreach (array_keys($feed['title_data']) as $key) {

			if ($key==="touser"){
				$dom = new DomDocument();
				@$dom->loadHTML($feed["title_data"]["touser"]);
				$urls = $dom->getElementsByTagName('a');
				$url = $urls->item(0);
				$value["title_data"]["touser"] = capi_fhtml($value["title_data"]["touser"]);
			}
			$searchs[] = '{'.$key.'}';
			$replaces[] = $feed['title_data'][$key];
		}
	}

	$searchs[] = '{actor}';
	$replaces[] = empty($actors)?$_SN[$feed['uid']]:implode(lang('dot'), $actors);

	$feed['title_template'] = mktarget(str_replace($searchs, $replaces, $feed['title_template']));

	return $feed;
}

function capi_data_filter($array, $filter)
{
	foreach ($array as $key=>$value){
		if (!in_array($key , $filter)){
			unset($array[$key]);
		}
	}
	
	return $array;
	
}

//对话框
/*function capi_showmessage($msgkey, $url_forward='', $second=1, $values=array(), $code=0) {
	global $_SGLOBAL, $_SC, $_SCONFIG, $_TPL, $space, $_SN;

	obclean();

	//去掉广告
	$_SGLOBAL['ad'] = array();
	
	//语言
	include_once(S_ROOT.'./language/lang_showmessage.php');
	if(isset($_SGLOBAL['msglang'][$msgkey])) {
		$message = lang_replace($_SGLOBAL['msglang'][$msgkey], $values);
	} else {
		$message = $msgkey;
	}
	$_SGLOBAL['mresponse']['ac'] = $_SGLOBAL['mresponse']['ac'] + 1;
	$_SGLOBAL['mresponse']['code'] = $code;
	$_SGLOBAL['mresponse']['action'][] = array("name"=>"showmessage", "msg"=>$message);
	if($url_forward) { //还没有完成
		$url_forward = parse_url($url_forward);
		parse_str($url_forward['query'], $_SGLOBAL['m_post']);
		$_SGLOBAL['mresponse']['ac'] = $_SGLOBAL['mresponse']['ac'] + 1;
		$_SGLOBAL['mresponse']['action'][] = array("name"=>"forward", "path"=>$url_forward['path']);
		include_once($url_forward['path']);
	}else{
		capi_mkjson();
	}
	
	exit();
}*/

function capi_showmessage_error($msgkey, $url_forward='', $second=1, $values=array()) {
	global $_SGLOBAL, $_SC, $_SCONFIG, $_TPL, $space, $_SN;
	capi_showmessage($msgkey, $url_forward, $second, $values, 1);
}

function capi_showmessage_success($msgkey, $url_forward='', $second=1, $values=array()) {
	global $_SGLOBAL, $_SC, $_SCONFIG, $_TPL, $space, $_SN;
	capi_showmessage($msgkey, $url_forward, $second, $values, 0);
}

function capi_submitcheck($var) {
	
	if(!empty($_REQUEST[$var])) {
			return true; //允许来路不明的提交
	} else {
		return false;
	}
}

function capi_runlog($file, $log, $halt=0) {
	global $_SGLOBAL, $_SERVER;

	$nowurl = $_SERVER['REQUEST_URI']?$_SERVER['REQUEST_URI']:($_SERVER['PHP_SELF']?$_SERVER['PHP_SELF']:$_SERVER['SCRIPT_NAME']);
	$log = sgmdate('Y-m-d H:i:s', $_SGLOBAL['timestamp'])."\t$type\t".getonlineip()."\t$_SGLOBAL[supe_uid]\t{$nowurl}\t".str_replace(array("\r", "\n"), array(' ', ' '), trim($log))."\n";
	$yearmonth = sgmdate('Ym', $_SGLOBAL['timestamp']);
	$logdir = '.././data/log/';
	if(!is_dir($logdir)) mkdir($logdir, 0777);
	$logfile = $logdir.$yearmonth.'_'.$file.'.php';
	if(@filesize($logfile) > 2048000) {
		$dir = opendir($logdir);
		$length = strlen($file);
		$maxid = $id = 0;
		while($entry = readdir($dir)) {
			if(strexists($entry, $yearmonth.'_'.$file)) {
				$id = intval(substr($entry, $length + 8, -4));
				$id > $maxid && $maxid = $id;
			}
		}
		closedir($dir);
		$logfilebak = $logdir.$yearmonth.'_'.$file.'_'.($maxid + 1).'.php';
		@rename($logfile, $logfilebak);
	}
	if($fp = @fopen($logfile, 'a')) {
		@flock($fp, 2);
		fwrite($fp, "<?PHP exit;?>\t".str_replace(array('<?', '?>', "\r", "\n"), '', $log)."\n");
		fclose($fp);
	}
	if($halt) exit();
}

//实名认证
function capi_ckrealname($type, $return=0) {
	global $_SCONFIG, $_SGLOBAL;
	$result = true;
	if($_SCONFIG['realname'] && empty($_SGLOBAL['member']['namestatus']) && empty($_SCONFIG['name_allow'.$type])) {
		if(empty($return)) capi_showmessage_by_data('no_privilege_realname');
		$result = false;
	}
	return $result;
}

//视频认证
function capi_ckvideophoto($type, $tospace=array(), $return=0) {
	global $_SCONFIG, $_SGLOBAL;
	
	if(empty($_SCONFIG['videophoto']) || $_SGLOBAL['member']['videostatus']) {
		return true;
	}
	
	$result = true;
	if(empty($tospace) || empty($tospace['privacy']['view']['video'.$type])) {//站点默认
		if(!checkperm('videophotoignore') && empty($_SCONFIG['video_allow'.$type])) {
			if($type != 'viewphoto' || $type == 'viewphoto' && !checkperm('allowviewvideopic')) {
				$result = false;
			}
		}
	} elseif ($tospace['privacy']['view']['video'.$type] == 2) {//用户禁止
		$result = false;
	}
	if($return) {
		return $result;
	} elseif(!$result) {
		capi_showmessage_by_data('no_privilege_videophoto');
	}
}

//新用户发言
function capi_cknewuser($return=0) {
	global $_SGLOBAL, $_SCONFIG, $space;
	$result = true;
	
	//不受防灌水限制
	if(checkperm('spamignore')) {
		return $result;
	}
	//见习时间
	if($_SCONFIG['newusertime'] && $_SGLOBAL['timestamp']-$space['dateline']<$_SCONFIG['newusertime']*3600) {
		if(empty($return)) capi_showmessage_by_data('no_privilege_newusertime',  1, array($_SCONFIG['newusertime']));
		$result = false;
	}
	//需要上传头像
	if($_SCONFIG['need_avatar'] && empty($space['avatar'])) {
		if(empty($return)) capi_showmessage_by_data('no_privilege_avatar');
		$result = false;
	}
	//强制新用户好友个数
	if($_SCONFIG['need_friendnum'] && $space['friendnum']<$_SCONFIG['need_friendnum']) {
		if(empty($return)) capi_showmessage_by_data('no_privilege_friendnum',  1, array($_SCONFIG['need_friendnum']));
		$result = false;
	}
	//强制新用户好友个数
	if($_SCONFIG['need_email'] && empty($space['emailcheck'])) {
		if(empty($return)) capi_showmessage_by_data('no_privilege_email');
		$result = false;
	}
	return $result;
}

function capi_avatar($uid, $size='small') {
	global $_SCONFIG, $_SN;
	
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'small';
	$avatarfile = avatar_file($uid, $size);
	$url = UC_API.'/data/avatar/'.$avatarfile;
	

	/*if (file_get_contents($url,0,null,0,1)){
		return $url;
	}*/if(file_exists(S_ROOT.'./center/data/avatar/'.$avatarfile)){
		return $url;
	}else{
		return UC_API.'/images/noavatar_'.$size.'.gif';
	}
}

function capi_getquiz($quizid) {
	global $_SGLOBAL, $_SCONFIG, $_SN;

	$query = $_SGLOBAL['db']->query("SELECT bf.*, b.* FROM ".tname('quiz')." b 
		LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=b.quizid 
		WHERE b.quizid='$quizid'");
	$quiz = $_SGLOBAL['db']->fetch_array($query);
	//选项
	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('quizoptions')." WHERE quizid='$quizid'");
	while( $value = $_SGLOBAL['db']->fetch_array($query))
	{
		if ($value['picid']){
			$query2 = $_SGLOBAL['db']->query("SELECT * FROM ".tname('pic')." WHERE picid='$value[picid]'");
			$value2 = $_SGLOBAL['db']->fetch_array($query2);
			$value['pic'] = pic_get($value2['filepath'], 0, 1);
		}else{
			$value['pic'] = "";
		}
		$quiz['options'][] = $value;
	}
	
	return $quiz;
}

 //发送短信
//function capi_SendMessage($mobile,$message){
	/*$targetno = '10657120840030001';
	$sms_sr = "http://211.139.194.245:3344/sr/smsmt";
	include_once(S_ROOT.'./source/smssend.class.php');
	$sms = new SmsSend ();
	$sms->SetContent ( $message );
	$sms->SetDestTermID ( $mobile );
	$sms->SetFeeTermID ( $mobile );
	$sms->SetSrcTermID ($targetno);
	$sms->SetSendURL ($sms_sr);
	$sms->SetXMLValue ();
	$sms->SendSMS ();*/
	/*return file_get_contents("http://www.atfaxian.com/do.php?ac=register&op=sendmessage&username=".$mobile."&message=".$message);
	
	//capi_runlog("sms", $mobile);
}*/



//检查搜索
function capi_cksearch($theurl) {
	global $_SGLOBAL, $_SCONFIG, $space;
	
	$theurl = stripslashes($theurl)."&page=".$_GET['page'];
	
	if(!checkperm('searchignore')) {
		$reward = getreward('search', 0);
		if($reward['credit'] || $reward['experience']) {
			
			if($space['credit'] < $reward['credit'] || $space['experience'] < $reward['experience']) {
				capi_showmessage_by_data('points_search_error');
			} else {
				//扣分
				$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET lastsearch='$_SGLOBAL[timestamp]', credit=credit-$reward[credit], experience=experience-$reward[experience] WHERE uid='$_SGLOBAL[supe_uid]'");
			}
			
		}
		return $reward;
	}
}

//检查验证码
function capi_ckseccode($seccode, $auth) {
	global $_SGLOBAL, $_SCOOKIE, $_SCONFIG;

	$check = true;
	$cookie_seccode = authcode($auth, 'DECODE');
	if(empty($cookie_seccode) || strtolower($cookie_seccode) != strtolower($seccode)) {
				$check = false;
	}

	
	return $check;
}

function capi_fhtml($html){
	$html = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $html);
	$html = shtmlspecialchars($html);

	return $html;
}

function capi_realname($uid, $tmpspace=''){
	$tmpspace = empty($tmpspace)?getspace($uid):$tmpspace;
	if (intval($tmpspace['namestatus']))return $tmpspace['name'];
	else return $tmpspace['username'];
}

function capi_bindtype($uid, $tmpspace=''){
	$tmpspace = empty($tmpspace)?getspace($uid):$tmpspace;
	return $tmpspace['weibo'];
}

function capi_isonline($uid, $tmpspace=''){
	global $_SGLOBAL;

	$query = $_SGLOBAL['db']->query('SELECT * FROM '.tname('session')." WHERE uid = '$uid'");
	$value = $_SGLOBAL['db']->fetch_array($query);
	if(!$value["client"])$isonline = 3;
	else
		$isonline = (empty($value) || $value['magichidden']) ? 0 : capi_bindtype($uid, $tmpspace);

	return $isonline;
}

function capi_getspacecomment($uid){
	global $_SGLOBAL;

	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('comment')." main WHERE main.uid='$uid' AND main.idtype='uid'"),0);
	return $count;
}

function capi_gettasknum($uid){
	global $_SGLOBAL;
	$count1 = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('task')),0);
	$count = $_SGLOBAL['db']->result($_SGLOBAL['db']->query("SELECT COUNT(*) FROM ".tname('usertask')." main WHERE main.uid='$uid' AND main.isignore='0'"),0);
	return $count1 - $count;
}



?>

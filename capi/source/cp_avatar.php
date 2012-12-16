<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_avatar.php 13149 2009-08-13 03:11:26Z liguode $
*/

include_once S_ROOT.'./uc_client/client.php';
if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

if(capi_submitcheck('avatarsubmit')) {

	$return = 0;
	$uid = $_SGLOBAL['supe_uid'];
	list($width, $height, $type, $attr) = getimagesize($_FILES['Filedata']['tmp_name']);
	$imgtype = array(1 => '.gif', 2 => '.jpg', 3 => '.png');
	$filetype = $imgtype[$type];
	if(!$filetype) $filetype = '.jpg';
	$tmpavatar = UC_DATADIR.'../../center/data/./tmp/upload'.$uid.$filetype;
	
	if(empty($uid)) {
		$return =  -1;
	}
	if(empty($_FILES['Filedata'])) {
		$return = -3;
	}
	$data = file_get_contents($_FILES['Filedata']['tmp_name']);
	file_exists($tmpavatar) && @unlink($tmpavatar);
	if(@copy($_FILES['Filedata']['tmp_name'], $tmpavatar) || @move_uploaded_file($_FILES['Filedata']['tmp_name'], $tmpavatar)) {
		@unlink($_FILES['Filedata']['tmp_name']);
		list($width, $height, $type, $attr) = getimagesize($tmpavatar);
		if($width < 10 || $height < 10 || $type == 4) {
			@unlink($tmpavatar);
			$return = -2;
		}
	} else {
		@unlink($_FILES['Filedata']['tmp_name']);
		$return = -4;
	}
	
	$avatarurl = UC_DATAURL.'../../center/data/tmp/upload'.$uid.$filetype;
	$avatartype = $_REQUEST["avatartype"] == 'real' ? 'real' : 'virtual';
	$bigavatarfile = UC_DATADIR.'../../center/data/./avatar/'.get_avatar($uid, 'big', $avatartype);
	$middleavatarfile = UC_DATADIR.'../../center/data/./avatar/'.get_avatar($uid, 'middle', $avatartype);
	$smallavatarfile = UC_DATADIR.'../../center/data/./avatar/'.get_avatar($uid, 'small', $avatartype);
	
	$im = imagecreatefromstring($data);
		
	$bigavatar = resizeImage($im, 200,200);
	imagejpeg($bigavatar, $bigavatarfile);
	$middleavatar = resizeImage($im, 120,120);
	imagejpeg($middleavatar, $middleavatarfile);
	//$smallavatar = $this->resizeImage($im, 48,48);
	$smallavatar = imagecreatetruecolor(48,48);
	$pic_width = imagesx($im);
	$pic_height = imagesy($im);
	if ($pic_height>$pic_width)
	{
		imagecopyresampled($smallavatar,$im,0,0,0,intval(($pic_height-$pic_width)/2),48,48,$pic_width,$pic_width);
	}else{
		imagecopyresampled($smallavatar,$im,0,0,0,0,48,48,$pic_width,$pic_width);
	}
	imagejpeg($smallavatar, $smallavatarfile);

	$space = getspace($_REQUEST["uid"]);
	$setarr = array();
	$avatar_exists = ckavatar($space["uid"]);
	if($avatar_exists) {
		if(!$space['avatar']) {
			//奖励成长
			$reward = getreward('setavatar', 0);
			if($reward['credit']) {
				$setarr['credit'] = "credit=credit+$reward[credit]";
			}
			if($reward['experience']) {
				$setarr['experience'] = "experience=experience+$reward[experience]";
			}
			
			$setarr['avatar'] = 'avatar=1';
			$setarr['updatetime'] = "updatetime=$_SGLOBAL[timestamp]";
		}
	} else {
		if($space['avatar']) {
			$setarr['avatar'] = 'avatar=0';
		}
	}
	if (empty($reward))
	{
		$reward['credit'] = 0;
		$reward['experience'] = 0;
	}
	if($setarr) {
		$_SGLOBAL['db']->query("UPDATE ".tname('space')." SET ".implode(',', $setarr)." WHERE uid='$_REQUEST[uid]'");
		//变更记录
		if($_SCONFIG['my_status']) {
			inserttable('userlog', array('uid'=>$_REQUEST["uid"], 'action'=>'update', 'dateline'=>$_SGLOBAL['timestamp']), 0, true);
		}
	}

	if ($return==-1)
	{
		capi_showmessage_by_data("do_faild", 1, array("url"=>$return, "msg"=>"用户ID为空","reward"=>$reward,'dateline'=>$_SGLOBAL['timestamp']));
	}elseif($return==-3){
		capi_showmessage_by_data("do_faild", 1, array("url"=>$return, "msg"=>"请上传头像","reward"=>$reward,'dateline'=>$_SGLOBAL['timestamp']));
	}elseif($return==-2){
		capi_showmessage_by_data("do_faild", 1, array("url"=>$return, "msg"=>"上传头像尺寸过小","reward"=>$reward,'dateline'=>$_SGLOBAL['timestamp']));
	}elseif($return==-4){
		capi_showmessage_by_data("do_success", 1, array("url"=>$return, "msg"=>"上传失败","reward"=>$reward,'dateline'=>$_SGLOBAL['timestamp']));
	}else{
		$url['big'] = capi_avatar($_REQUEST["uid"], "big");
		$url['middle'] = capi_avatar($_REQUEST["uid"], "middle");
		$url['small'] = capi_avatar($_REQUEST["uid"], "small");
		capi_showmessage_by_data("do_success", 0, array("url"=>$url,"msg"=>"上传成功","reward"=>$reward,'dateline'=>$_SGLOBAL['timestamp']));
	}

}



//include template("cp_avatar");
function get_avatar($uid, $size = 'middle', $type = '') {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$typeadd = $type == 'real' ? '_real' : '';
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
}


function resizeImage($im,$maxwidth,$maxheight)
{
	$pic_width = imagesx($im);
	$pic_height = imagesy($im);

	if(($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight))
	{
		if($maxwidth && $pic_width>$maxwidth)
		{
			$widthratio = $maxwidth/$pic_width;
			$resizewidth_tag = true;
		}

		if($maxheight && $pic_height>$maxheight)
		{
			$heightratio = $maxheight/$pic_height;
			$resizeheight_tag = true;
		}

		if($resizewidth_tag && $resizeheight_tag)
		{
			if($widthratio<$heightratio)
				$ratio = $widthratio;
			else
				$ratio = $heightratio;
		}

		if($resizewidth_tag && !$resizeheight_tag)
			$ratio = $widthratio;
		if($resizeheight_tag && !$resizewidth_tag)
			$ratio = $heightratio;

		$newwidth = $pic_width * $ratio;
		$newheight = $pic_height * $ratio;

		if(function_exists("imagecopyresampled"))
		{
			$newim = imagecreatetruecolor($newwidth,$newheight);
		   imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
		}
		else
		{
			$newim = imagecreate($newwidth,$newheight);
		   imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$pic_width,$pic_height);
		}

		return $newim;
	}
	else
	{
		return $newim;
	}           
}
?>

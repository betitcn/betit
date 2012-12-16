<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: profile.php 13217 2009-08-21 06:57:53Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//判断用户是否全部设置了个人资料
$nones = array();
$profile_lang = array(
	'name' => '姓名',
	'sex' => '性别',
	'birthyear' => '生日(年)',
	'birthmonth' => '生日(月)',
	'birthday' => '生日(日)',
	'blood' => '血型',
	'marry' => '婚恋状态',
	'birthprovince' => '家乡(省)',
	'birthcity' => '家乡(市)',
	'resideprovince' => '居住地(省)',
	'residecity' => '居住地(市)'
);
foreach (array('name','sex','birthyear','birthmonth','birthday','marry','birthprovince','birthcity','resideprovince','residecity') as $key) {
	$value = trim($space[$key]);
	if(empty($value)) {
		$nones[] = $profile_lang[$key];
	}
}
//站长扩展
@include_once(S_ROOT.'./data/data_profilefield.php');
foreach ($_SGLOBAL['profilefield'] as $field => $value) {
	if($value['required'] && empty($space['field_'.$field])) {
		$nones[] = $value['title'];
	}
}

if(empty($nones)) {

	capi_showmessage_by_data('do_success', 0, array("done"=>1));


} else {

	capi_showmessage_by_data('do_success', 0, array("done"=>0));
}

?>

<?php

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

//获取任务
include_once(S_ROOT.'./source/function_space.php');
$task = gettask();
$task['note'] = capi_fhtml($task['note']);
capi_showmessage_by_data('rest_success',  0, $task);

?>
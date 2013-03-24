<?php
if(!defined('IN_UCHOME')) exit('Access Denied');
$_SGLOBAL['task']=Array
	(
	3 => Array
		(
		'taskid' => 3,
		'available' => 1,
		'name' => '发表第一个竞猜',
		'note' => '现在，就发表第一个竞猜吧。<br>与大家一起游玩。完成后即可获得50个金币!',
		'num' => 8,
		'maxnum' => '0',
		'image' => 'image/task/blog.gif',
		'filename' => 'quiz.php',
		'starttime' => '0',
		'endtime' => '0',
		'nexttime' => '0',
		'nexttype' => '',
		'credit' => 50,
		'displayorder' => 3
		),
	4 => Array
		(
		'taskid' => 4,
		'available' => 1,
		'name' => '添加5个好友',
		'note' => '有了好友，您就可以和他们一起玩竞猜了；<br>您也会方便地看到好友的最新动态。完成任务后即可获得200个金币。',
		'num' => 2,
		'maxnum' => '0',
		'image' => 'image/task/friend.gif',
		'filename' => 'friend.php',
		'starttime' => '0',
		'endtime' => '0',
		'nexttime' => '0',
		'nexttype' => '',
		'credit' => 200,
		'displayorder' => 4
		),
	6 => Array
		(
		'taskid' => 6,
		'available' => 1,
		'name' => '邀请10个新朋友加入',
		'note' => '邀请一下自己的新浪微博、腾讯微博、电话本、微信好友，让朋友们一起来加入竞猜吧。完成任务即可获得300个金币!',
		'num' => '0',
		'maxnum' => '0',
		'image' => 'image/task/friend.gif',
		'filename' => 'invite.php',
		'starttime' => '0',
		'endtime' => '0',
		'nexttime' => '0',
		'nexttype' => '',
		'credit' => 300,
		'displayorder' => 6
		),
	7 => Array
		(
		'taskid' => 7,
		'available' => 1,
		'name' => '领取每日访问大礼包',
		'note' => '每天玩竞猜，就可领取大礼包——20个金币!',
		'num' => 13,
		'maxnum' => '0',
		'image' => 'image/task/gift.gif',
		'filename' => 'gift.php',
		'starttime' => '0',
		'endtime' => '0',
		'nexttime' => '0',
		'nexttype' => 'day',
		'credit' => 20,
		'displayorder' => 99
		)
	)
?>
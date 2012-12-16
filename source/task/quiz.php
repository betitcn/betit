<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: blog.php 11056 2009-02-09 01:59:47Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

$quizcount = getcount('quiz', array('uid'=>$space['uid']));
if($quizcount) {

	$task['done'] = 1;//任务完成

} else {

	//任务完成向导
	$task['guide'] = '
		<strong>请按照以下的说明来参与本任务：</strong>
		<ul>
		<li>1. <a href="cp.php?ac=quiz" target="_blank">新窗口打开发表竞猜页面</a>；</li>
		<li>2. 在新打开的页面中，书写自己的第一篇竞猜，并进行发布。</li>
		</ul>';

}

?>

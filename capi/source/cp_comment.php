<?php
/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: cp_comment.php 13000 2009-08-05 05:58:30Z liguode $
*/

if(!defined('IN_UCHOME')) {
	exit('Access Denied');
}

include_once(S_ROOT.'./source/function_bbcode.php');

//���ñ���
$tospace = $pic = $blog = $album = $share = $event = $poll = $quiz = array();

if(capi_submitcheck('commentsubmit')) {

	$idtype = $_REQUEST['idtype'];
	
	if(!checkperm('allowcomment')) {
		ckspacelog();
		capi_showmessage_by_data('no_privilege');
	}

	//ʵ����֤
	ckrealname('comment');

	//���û���ϰ
	cknewuser();

	//�ж��Ƿ񷢲�̫��
	$waittime = interval_check('post');
	if($waittime > 0) {
		capi_showmessage_by_data('operating_too_fast',1,array($waittime));
	}

	$message = getstr($_REQUEST['message'], 0, 1, 1, 1, 2);
	if(strlen($message) < 2) {
		capi_showmessage_by_data('content_is_too_short');
	}

	//ժҪ
	$summay = getstr($message, 150, 1, 1, 0, 0, -1);

	$id = intval($_REQUEST['id']);

	//��������
	$cid = empty($_REQUEST['cid'])?0:intval($_REQUEST['cid']);
	$comment = array();
	if($cid) {
		$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE cid='$cid' AND id='$id' AND idtype='$_REQUEST[idtype]'");
		$comment = $_SGLOBAL['db']->fetch_array($query);
		if($comment && $comment['authorid'] != $_SGLOBAL['supe_uid']) {
			//ʵ��
			if($comment['author'] == '') {
				$_SN[$comment['authorid']] = lang('hidden_username');
			} else {
				realname_set($comment['authorid'], $comment['author']);
				realname_get();
			}
			$comment['message'] = preg_replace("/\<div class=\"quote\"\>\<span class=\"q\"\>.*?\<\/span\>\<\/div\>/is", '', $comment['message']);
			//bbcodeת��
			$comment['message'] = html2bbcode($comment['message']);
			$message = addslashes("<div class=\"quote\"><span class=\"q\"><b>".$_SN[$comment['authorid']]."</b>: ".getstr($comment['message'], 150, 0, 0, 0, 2, 1).'</span></div>').$message;
			if($comment['idtype']=='uid') {
				$id = $comment['authorid'];
			}
		} else {
			$comment = array();
		}
	}

	$hotarr = array();
	$stattype = '';

	//���Ȩ��
	switch ($idtype) {
		case 'uid':
			//�����ռ�
			$tospace = getspace($id);
			$stattype = 'wall';//ͳ��
			break;
		case 'picid':
			//����ͼƬ
			$query = $_SGLOBAL['db']->query("SELECT p.*, pf.hotuser
				FROM ".tname('pic')." p
				LEFT JOIN ".tname('picfield')." pf
				ON pf.picid=p.picid
				WHERE p.picid='$id'");
			$pic = $_SGLOBAL['db']->fetch_array($query);
			//ͼƬ������
			if(empty($pic)) {
				capi_showmessage_by_data('view_images_do_not_exist');
			}

			//�����ռ�
			$tospace = getspace($pic['uid']);

			//��ȡ���
			$album = array();
			if($pic['albumid']) {
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('album')." WHERE albumid='$pic[albumid]'");
				if(!$album = $_SGLOBAL['db']->fetch_array($query)) {
					updatetable('pic', array('albumid'=>0), array('albumid'=>$pic['albumid']));//��ᶪʧ
				}
			}
			//��֤��˽
			if(!ckfriend($album['uid'], $album['friend'], $album['target_ids'])) {
				capi_showmessage_by_data('no_privilege');
			} elseif(!$tospace['self'] && $album['friend'] == 4) {
				//������������
				$cookiename = "view_pwd_album_$album[albumid]";
				$cookievalue = empty($_SCOOKIE[$cookiename])?'':$_SCOOKIE[$cookiename];
				if($cookievalue != md5(md5($album['password']))) {
					capi_showmessage_by_data('no_privilege');
				}
			}
			
			$hotarr = array('picid', $pic['picid'], $pic['hotuser']);
			$stattype = 'piccomment';//ͳ��
			break;
		case 'blogid':
			//��ȡ��־
			$query = $_SGLOBAL['db']->query("SELECT b.*, bf.target_ids, bf.hotuser
				FROM ".tname('blog')." b
				LEFT JOIN ".tname('blogfield')." bf ON bf.blogid=b.blogid
				WHERE b.blogid='$id'");
			$blog = $_SGLOBAL['db']->fetch_array($query);
			//��־������
			if(empty($blog)) {
				capi_showmessage_by_data('view_to_info_did_not_exist');
			}
			
			//�����ռ�
			$tospace = getspace($blog['uid']);
			
			//��֤��˽
			if(!ckfriend($blog['uid'], $blog['friend'], $blog['target_ids'])) {
				//û��Ȩ��
				capi_showmessage_by_data('no_privilege');
			} elseif(!$tospace['self'] && $blog['friend'] == 4) {
				//������������
				$cookiename = "view_pwd_blog_$blog[blogid]";
				$cookievalue = empty($_SCOOKIE[$cookiename])?'':$_SCOOKIE[$cookiename];
				if($cookievalue != md5(md5($blog['password']))) {
					capi_showmessage_by_data('no_privilege');
				}
			}

			//�Ƿ���������
			if(!empty($blog['noreply'])) {
				capi_showmessage_by_data('do_not_accept_comments');
			}
			if($blog['target_ids']) {
				$blog['target_ids'] .= ",$blog[uid]";
			}
			
			$hotarr = array('blogid', $blog['blogid'], $blog['hotuser']);
			$stattype = 'blogcomment';//ͳ��
			break;
		case 'quizid':
			//��ȡ��־
			$query = $_SGLOBAL['db']->query("SELECT b.*, bf.target_ids, bf.hotuser
				FROM ".tname('quiz')." b
				LEFT JOIN ".tname('quizfield')." bf ON bf.quizid=b.quizid
				WHERE b.quizid='$id'");
			$quiz = $_SGLOBAL['db']->fetch_array($query);
			//��־������
			if(empty($quiz)) {
				capi_showmessage_by_data('view_to_info_did_not_exist');
			}
			
			//�����ռ�
			$tospace = getspace($quiz['uid']);
			
			//��֤��˽
			if(!ckfriend($quiz['uid'], $quiz['friend'], $quiz['target_ids'])) {
				//û��Ȩ��
				capi_showmessage_by_data('no_privilege');
			} elseif(!$tospace['self'] && $quiz['friend'] == 4) {
				//������������
				$cookiename = "view_pwd_quiz_$quiz[quizid]";
				$cookievalue = empty($_SCOOKIE[$cookiename])?'':$_SCOOKIE[$cookiename];
				if($cookievalue != md5(md5($quiz['password']))) {
					capi_showmessage_by_data('no_privilege');
				}
			}

			//�Ƿ���������
			if(!empty($quiz['noreply'])) {
				capi_showmessage_by_data('do_not_accept_comments');
			}
			if($quiz['target_ids']) {
				$quiz['target_ids'] .= ",$quiz[uid]";
			}
			
			$hotarr = array('quizid', $quiz['quizid'], $quiz['hotuser']);
			$stattype = 'quizcomment';//ͳ��
			break;
		case 'sid':
			//��ȡ��־
			$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('share')." WHERE sid='$id'");
			$share = $_SGLOBAL['db']->fetch_array($query);
			//��־������
			if(empty($share)) {
				capi_showmessage_by_data('sharing_does_not_exist');
			}

			//�����ռ�
			$tospace = getspace($share['uid']);
			
			$hotarr = array('sid', $share['sid'], $share['hotuser']);
			$stattype = 'sharecomment';//ͳ��
			break;
		case 'pid':
			$query = $_SGLOBAL['db']->query("SELECT p.*, pf.hotuser
				FROM ".tname('poll')." p
				LEFT JOIN ".tname('pollfield')." pf ON pf.pid=p.pid
				WHERE p.pid='$id'");
			$poll = $_SGLOBAL['db']->fetch_array($query);
			if(empty($poll)) {
				capi_showmessage_by_data('voting_does_not_exist');
			}
			//�Ƿ���������
			$tospace = getspace($poll['uid']);
			if($poll['noreply']) {
				//�Ƿ����
				if(!$tospace['self'] && !in_array($_SGLOBAL['supe_uid'], $tospace['friends'])) {
					capi_showmessage_by_data('the_vote_only_allows_friends_to_comment');
				}
			}
			
			$hotarr = array('pid', $poll['pid'], $poll['hotuser']);
			$stattype = 'pollcomment';//ͳ��
			break;
		case 'eventid':
		    // ��ȡ�
		    $query = $_SGLOBAL['db']->query("SELECT e.*, ef.* FROM ".tname('event')." e LEFT JOIN ".tname("eventfield")." ef ON e.eventid=ef.eventid WHERE e.eventid='$id'");
			$event = $_SGLOBAL['db']->fetch_array($query);

			if(empty($event)) {
				capi_showmessage_by_data('event_does_not_exist');
			}
			
			if($event['grade'] < -1){
				capi_showmessage_by_data('event_is_closed');//��Ѿ��ر�
			} elseif($event['grade'] <= 0){
				capi_showmessage_by_data('event_under_verify');//�δͨ�����
			}
			
			if(!$event['allowpost']){
				$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname("userevent")." WHERE eventid='$id' AND uid='$_SGLOBAL[supe_uid]' LIMIT 1");
				$value = $_SGLOBAL['db']->fetch_array($query);
				if(empty($value) || $value['status'] < 2){
					capi_showmessage_by_data('event_only_allows_members_to_comment');//ֻ�л��Ա����������
				}
			}

			//�����ռ�
			$tospace = getspace($event['uid']);
			
			$hotarr = array('eventid', $event['eventid'], $event['hotuser']);
			$stattype = 'eventcomment';//ͳ��
			break;
		default:
			capi_showmessage_by_data('non_normal_operation');
			break;
	}
	
	if(empty($tospace)) {
		capi_showmessage_by_data('space_does_not_exist');
	}
	
	//��Ƶ��֤
	if($tospace['videostatus']) {
		if($idtype == 'uid') {
			ckvideophoto('wall', $tospace);
		} else {
			ckvideophoto('comment', $tospace);
		}
	}
	
	//������
	if(isblacklist($tospace['uid'])) {
		capi_showmessage_by_data('is_blacklist');
	}
	
	//�ȵ�
	if($hotarr && $tospace['uid'] != $_SGLOBAL['supe_uid']) {
		hot_update($hotarr[0], $hotarr[1], $hotarr[2]);
	}

	//�¼�
	$fs = array();
	$fs['icon'] = 'comment';
	$fs['target_ids'] = $fs['friend'] = '';

	switch ($_REQUEST['idtype']) {
		case 'uid':
			//�¼�
			$fs['icon'] = 'wall';
			$fs['title_template'] = cplang('feed_comment_space');
			$fs['title_data'] = array('touser'=>"<a href=\"space.php?uid=$tospace[uid]\">".$_SN[$tospace['uid']]."</a>");
			$fs['body_template'] = '';
			$fs['body_data'] = array();
			$fs['body_general'] = '';
			$fs['images'] = array();
			$fs['image_links'] = array();
			break;
		case 'picid':
			//�¼�
			$fs['title_template'] = cplang('feed_comment_image');
			$fs['title_data'] = array('touser'=>"<a href=\"space.php?uid=$tospace[uid]\">".$_SN[$tospace['uid']]."</a>");
			$fs['body_template'] = '{pic_title}';
			$fs['body_data'] = array('pic_title'=>$pic['title']);
			$fs['body_general'] = $summay;
			$fs['images'] = array(pic_get($pic['filepath'], $pic['thumb'], $pic['remote']));
			$fs['image_links'] = array("space.php?uid=$tospace[uid]&do=album&picid=$pic[picid]");
			$fs['target_ids'] = $album['target_ids'];
			$fs['friend'] = $album['friend'];
			break;
		case 'blogid':
			//��������ͳ��
			$_SGLOBAL['db']->query("UPDATE ".tname('blog')." SET replynum=replynum+1 WHERE blogid='$id'");
			//�¼�
			$fs['title_template'] = cplang('feed_comment_blog');
			$fs['title_data'] = array('touser'=>"<a href=\"space.php?uid=$tospace[uid]\">".$_SN[$tospace['uid']]."</a>", 'blog'=>"<a href=\"space.php?uid=$tospace[uid]&do=blog&id=$id\">$blog[subject]</a>");
			$fs['body_template'] = '';
			$fs['body_data'] = array();
			$fs['body_general'] = '';
			$fs['target_ids'] = $blog['target_ids'];
			$fs['friend'] = $blog['friend'];
			break;
		case 'quizid':
			//��������ͳ��
			$_SGLOBAL['db']->query("UPDATE ".tname('quiz')." SET replynum=replynum+1 WHERE quizid='$id'");
			//�¼�
			$fs['title_template'] = cplang('feed_comment_quiz');
			$fs['title_data'] = array('touser'=>"<a href=\"space.php?uid=$tospace[uid]\">".$_SN[$tospace['uid']]."</a>", 'quiz'=>"<a href=\"space.php?uid=$tospace[uid]&do=quiz&id=$id\">$quiz[subject]</a>");
			$fs['body_template'] = '';
			$fs['body_data'] = array();
			$fs['body_general'] = '';
			$fs['target_ids'] = $quiz['target_ids'];
			$fs['friend'] = $quiz['friend'];
			break;
		case 'sid':
			//�¼�
			$fs['title_template'] = cplang('feed_comment_share');
			$fs['title_data'] = array('touser'=>"<a href=\"space.php?uid=$tospace[uid]\">".$_SN[$tospace['uid']]."</a>", 'share'=>"<a href=\"space.php?uid=$tospace[uid]&do=share&id=$id\">".str_replace(cplang('share_action'), '', $share['title_template'])."</a>");
			$fs['body_template'] = '';
			$fs['body_data'] = array();
			$fs['body_general'] = '';
			break;
		case 'eventid':
		    // �
		    $fs['title_template'] = cplang('feed_comment_event');
			$fs['title_data'] = array('touser'=>"<a href=\"space.php?uid=$tospace[uid]\">".$_SN[$tospace['uid']]."</a>", 'event'=>'<a href="space.php?do=event&id='.$event['eventid'].'">'.$event['title'].'</a>');
			$fs['body_template'] = '';
			$fs['body_data'] = array();
			$fs['body_general'] = '';
			break;
		case 'pid':
			// ͶƱ
			//��������ͳ��
			$_SGLOBAL['db']->query("UPDATE ".tname('poll')." SET replynum=replynum+1 WHERE pid='$id'");
			$fs['title_template'] = cplang('feed_comment_poll');
			$fs['title_data'] = array('touser'=>"<a href=\"space.php?uid=$tospace[uid]\">".$_SN[$tospace['uid']]."</a>", 'poll'=>"<a href=\"space.php?uid=$tospace[uid]&do=poll&pid=$id\">$poll[subject]</a>");
			$fs['body_template'] = '';
			$fs['body_data'] = array();
			$fs['body_general'] = '';
			$fs['friend'] = '';
			break;
	}

	$setarr = array(
		'uid' => $tospace['uid'],
		'id' => $id,
		'idtype' => $_REQUEST['idtype'],
		'authorid' => $_SGLOBAL['supe_uid'],
		'author' => $_SGLOBAL['supe_username'],
		'dateline' => $_SGLOBAL['timestamp'],
		'message' => $message,
		'ip' => getonlineip()
	);
	//���
	$cid = inserttable('comment', $setarr, 1);
	$action = 'comment';
	$becomment = 'getcomment';
	switch ($_REQUEST['idtype']) {
		case 'uid':
			$n_url = "space.php?uid=$tospace[uid]&do=wall&cid=$cid";
			$note_type = 'wall';
			$note = cplang('note_wall', array($n_url));
			$q_note = cplang('note_wall_reply', array($n_url));
			if($comment) {
				$msg = 'note_wall_reply_success';
				$magvalues = array($_SN[$tospace['uid']]);
				$becomment = '';
			} else {
				$msg = 'do_success';
				$magvalues = array();
				$becomment = 'getguestbook';
			}
			$msgtype = 'comment_friend';
			$q_msgtype = 'comment_friend_reply';
			$action = 'guestbook';
			break;
		case 'picid':
			$n_url = "space.php?uid=$tospace[uid]&do=album&picid=$id&cid=$cid";
			$note_type = 'piccomment';
			$note = cplang('note_pic_comment', array($n_url));
			$q_note = cplang('note_pic_comment_reply', array($n_url));
			$msg = 'do_success';
			$magvalues = array();
			$msgtype = 'photo_comment';
			$q_msgtype = 'photo_comment_reply';
			break;
		case 'blogid':
			//֪ͨ
			$n_url = "space.php?uid=$tospace[uid]&do=blog&id=$id&cid=$cid";
			$note_type = 'blogcomment';
			$note = cplang('note_blog_comment', array($n_url, $blog['subject']));
			$q_note = cplang('note_blog_comment_reply', array($n_url));
			$msg = 'do_success';
			$magvalues = array();
			$msgtype = 'blog_comment';
			$q_msgtype = 'blog_comment_reply';
			break;
		case 'quizid':
			//֪ͨ
			$n_url = "space.php?uid=$tospace[uid]&do=quiz&id=$id&cid=$cid";
			$note_type = 'quizcomment';
			$note = cplang('note_quiz_comment', array($n_url, $quiz['subject']));
			$q_note = cplang('note_quiz_comment_reply', array($n_url));
			$msg = 'do_success';
			$magvalues = array();
			$msgtype = 'quiz_comment';
			$q_msgtype = 'quiz_comment_reply';
			break;
		case 'sid':
			//����
			$n_url = "space.php?uid=$tospace[uid]&do=share&id=$id&cid=$cid";
			$note_type = 'sharecomment';
			$note = cplang('note_share_comment', array($n_url));
			$q_note = cplang('note_share_comment_reply', array($n_url));
			$msg = 'do_success';
			$magvalues = array();
			$msgtype = 'share_comment';
			$q_msgtype = 'share_comment_reply';
			break;
		case 'pid':
			$n_url = "space.php?uid=$tospace[uid]&do=poll&pid=$id&cid=$cid";
			$note_type = 'pollcomment';
			$note = cplang('note_poll_comment', array($n_url, $poll['subject']));
			$q_note = cplang('note_poll_comment_reply', array($n_url));
			$msg = 'do_success';
			$magvalues = array();
			$msgtype = 'poll_comment';
			$q_msgtype = 'poll_comment_reply';
			break;
		case 'eventid':
		    // �
		    $n_url = "space.php?do=event&id=$id&view=comment&cid=$cid";
		    $note_type = 'eventcomment';
		    $note = cplang('note_event_comment', array($n_url));
		    $q_note = cplang('note_event_comment_reply', array($n_url));
		    $msg = 'do_success';
		    $magvalues = array();
		    $msgtype = 'event_comment';
		    $q_msgtype = 'event_comment_reply';
		    break;
	}

	if(empty($comment)) {
		
		//����������
		if($tospace['uid'] != $_SGLOBAL['supe_uid']) {
			//�¼�����
			if(ckprivacy('comment', 1)) {
				feed_add($fs['icon'], $fs['title_template'], $fs['title_data'], $fs['body_template'], $fs['body_data'], $fs['body_general'],$fs['images'], $fs['image_links'], $fs['target_ids'], $fs['friend']);
			}


			$realname = capi_realname($setarr['authorid']);
			//����֪ͨ
			notification_add($tospace['uid'], $note_type, $note.":".$message);

			notification_add_push($tospace['uid'], $realname.$note.":".$message);
			
			//���Է��Ͷ���Ϣ
			if($_REQUEST['idtype'] == 'uid' && $tospace['updatetime'] == $tospace['dateline']) {
				include_once S_ROOT.'./uc_client/client.php';
				uc_pm_send($_SGLOBAL['supe_uid'], $tospace['uid'], cplang('wall_pm_subject'), cplang('wall_pm_message', array(addslashes(getsiteurl().$n_url))), 1, 0, 0);
			}
			
			//�����ʼ�֪ͨ
			smail($tospace['uid'], '', cplang($msgtype, array($_SN[$space['uid']], shtmlspecialchars(getsiteurl().$n_url))), '', $msgtype);
		}
		
	} elseif($comment['authorid'] != $_SGLOBAL['supe_uid']) {
		
		//�����ʼ�֪ͨ
		smail($comment['authorid'], '', cplang($q_msgtype, array($_SN[$space['uid']], shtmlspecialchars(getsiteurl().$n_url))), '', $q_msgtype);
		$realname = capi_realname($comment['authorid']);

		notification_add($comment['authorid'], $note_type, $q_note.":".$message);

		notification_add_push($comment['authorid'],$realname.$q_note.":".$message);
		
	}
	
	//ͳ��
	if($stattype) {
		updatestat($stattype);
	}

	//����
	if($tospace['uid'] != $_SGLOBAL['supe_uid']) {
		$needle = $id;
		if($_REQUEST['idtype'] != 'uid') {
			$needle = $_REQUEST['idtype'].$id;
		} else {
			$needle = $tospace['uid'];
		}
		//�������۷�����
		$reward = getreward($action, 1, 0, $needle);
		//������������
		if($becomment) {
			if($_REQUEST['idtype'] == 'uid') {
				$needle = $_SGLOBAL['supe_uid'];
			}
			getreward($becomment, 1, $tospace['uid'], $needle, 0);
		}
	}

	capi_showmessage_by_data( 'rest_success', 0, $reward );
}

$cid = empty($_REQUEST['cid'])?0:intval($_REQUEST['cid']);

//�༭
if($_REQUEST['op'] == 'edit') {

	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE cid='$cid' AND authorid='$_SGLOBAL[supe_uid]'");
	if(!$comment = $_SGLOBAL['db']->fetch_array($query)) {
		capi_showmessage_by_data('no_privilege');
	}

	//�ύ�༭
	if(capi_submitcheck('editsubmit')) {

		$message = getstr($_REQUEST['message'], 0, 1, 1, 1, 2);
		if(strlen($message) < 2) capi_showmessage_by_data('content_is_too_short');

		updatetable('comment', array('message'=>$message), array('cid'=>$comment['cid']));

		capi_showmessage_by_data('do_success', $_REQUEST['refer'], 0);
	}

	//bbcodeת��
	$comment['message'] = html2bbcode($comment['message']);//��ʾ��

} elseif($_REQUEST['op'] == 'delete') {

	if(capi_submitcheck('deletesubmit')) {
		include_once(S_ROOT.'./source/function_delete.php');
		if(deletecomments(array($cid))) {
			capi_showmessage_by_data('do_success', $_REQUEST['refer'], 0);
		} else {
			capi_showmessage_by_data('no_privilege');
		}
	}

} elseif($_REQUEST['op'] == 'reply') {

	$query = $_SGLOBAL['db']->query("SELECT * FROM ".tname('comment')." WHERE cid='$cid'");
	if(!$comment = $_SGLOBAL['db']->fetch_array($query)) {
		capi_showmessage_by_data('comments_do_not_exist');
	}

} else {

	capi_showmessage_by_data('no_privilege');
}

include template('cp_comment');

?>

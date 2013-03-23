<?php

/**
 * 在附属站点注册新用户
 * @author jtee<sianke731@126.com>
 * $Id: $
 *
 */
class siteUserRegister{
	
	var $uid = -999;
	var $username = '';
	var $password = '';
	var $email = '';
	var $groupid = -999;
	var $auth = '';
	
	/**
	 * 资源初始化
	 * @access public
	 * @return xwbSiteUserRegister
	 */
	function siteUserRegister(){
		global $_SGLOBAL;
		if($_SGLOBAL['closeregister']){
			if($_SGLOBAL['mobile']){
				capi_showmessage_by_data('对不起，网站已关闭注册！');
			}
			else
				showmessage('对不起，网站已关闭注册！');	
		}
		loaducenter();
	}

	function getAuth(){
		return $this->auth;
	}
	
	
	/**
	 * 注册一个新帐户
	 * @access public
	 * @param string $name 和论坛编码相符合的用户名
	 * @param string $email 和论坛编码相符合的Email
	 * @param mixed $pwd
	 * @return integer 
	 */
	function reg( $name, $email, $pwd= false ){
		global $_SCONFIG, $_SGLOBAL;
		if(strlen(trim($name))>15){
			return -1;	
		}
		$this->username = mysql_escape_string(trim($name));
		$this->email = mysql_escape_string(trim($email));
		$this->password = $pwd ? mysql_escape_string($pwd) : rand(100000,999999);
		$result_name = uc_user_checkname($this->username);
		if($result_name < 1){  //检测用户名
			return $result_name;
		}
		
		$result_email = uc_user_checkemail($this->email, $this->username);
		if($result_email < 1){  //检测邮箱
			return $result_email;
		}
		
		$this->uid = (int)uc_user_register($this->username, $this->password, $this->email, $this->questionid, $this->answer);
		if ($this->uid>0){
			$setarr = array(
				'uid' => $this->uid,
				'username' => $this->username,
				'password' => md5($this->uid."|".$_SGLOBAL[timestamp])//本地密码随机生成
			);
			//更新本地用户库
			inserttable('member', $setarr, 0, true);

			//开通空间
			include_once(S_ROOT.'./source/function_space.php');
			$space = space_open($this->uid, $this->username, 5, $this->email);

			//默认好友
			$flog = $inserts = $fuids = $pokes = array();
			if(!empty($_SCONFIG['defaultfusername'])) {
				$query = $_SGLOBAL['db']->query("SELECT uid,username FROM ".tname('space')." WHERE username IN (".simplode(explode(',', $_SCONFIG['defaultfusername'])).")");
				while ($value = $_SGLOBAL['db']->fetch_array($query)) {
					$value = saddslashes($value);
					$fuids[] = $value['uid'];
					$inserts[] = "('".$this->uid."','$value[uid]','$value[username]','1','$_SGLOBAL[timestamp]')";
					$inserts[] = "('$value[uid]','".$this->uid."','".$this->username."','1','$_SGLOBAL[timestamp]')";
					$pokes[] = "('".$this->uid."','$value[uid]','$value[username]','".addslashes($_SCONFIG['defaultpoke'])."','$_SGLOBAL[timestamp]')";
					//添加好友变更记录
					$flog[] = "('$value[uid]','".$this->uid."','add','$_SGLOBAL[timestamp]')";
				}
				if($inserts) {
					$_SGLOBAL['db']->query("REPLACE INTO ".tname('friend')." (uid,fuid,fusername,status,dateline) VALUES ".implode(',', $inserts));
					$_SGLOBAL['db']->query("REPLACE INTO ".tname('poke')." (uid,fromuid,fromusername,note,dateline) VALUES ".implode(',', $pokes));
					$_SGLOBAL['db']->query("REPLACE INTO ".tname('friendlog')." (uid,fuid,action,dateline) VALUES ".implode(',', $flog));

					//添加到附加表
					$friendstr = empty($fuids)?'':implode(',', $fuids);
					updatetable('space', array('friendnum'=>count($fuids), 'pokenum'=>count($pokes)), array('uid'=>$newuid));
					updatetable('spacefield', array('friend'=>$friendstr, 'feedfriend'=>$friendstr), array('uid'=>$newuid));

					//更新默认用户好友缓存
					include_once(S_ROOT.'./source/function_cp.php');
					foreach ($fuids as $fuid) {
						friend_cache($fuid);
					}
				}
			}

			$this->auth = setSession($this->uid, $this->username);
			//变更记录
			if($_SCONFIG['my_status']) inserttable('userlog', array('uid'=>$newuid, 'action'=>'add', 'dateline'=>$_SGLOBAL['timestamp']), 0, true);
			
			return $this->uid;
		}else{
			return -7;
		}
		
	}
}
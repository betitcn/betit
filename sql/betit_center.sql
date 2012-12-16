/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50051
Source Host           : localhost:3306
Source Database       : betit_center

Target Server Type    : MYSQL
Target Server Version : 50051
File Encoding         : 65001

Date: 2012-07-31 17:04:45
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `uc_admins`
-- ----------------------------
DROP TABLE IF EXISTS `uc_admins`;
CREATE TABLE `uc_admins` (
  `uid` mediumint(8) unsigned NOT NULL auto_increment,
  `username` char(15) NOT NULL default '',
  `allowadminsetting` tinyint(1) NOT NULL default '0',
  `allowadminapp` tinyint(1) NOT NULL default '0',
  `allowadminuser` tinyint(1) NOT NULL default '0',
  `allowadminbadword` tinyint(1) NOT NULL default '0',
  `allowadmintag` tinyint(1) NOT NULL default '0',
  `allowadminpm` tinyint(1) NOT NULL default '0',
  `allowadmincredits` tinyint(1) NOT NULL default '0',
  `allowadmindomain` tinyint(1) NOT NULL default '0',
  `allowadmindb` tinyint(1) NOT NULL default '0',
  `allowadminnote` tinyint(1) NOT NULL default '0',
  `allowadmincache` tinyint(1) NOT NULL default '0',
  `allowadminlog` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_admins
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_applications`
-- ----------------------------
DROP TABLE IF EXISTS `uc_applications`;
CREATE TABLE `uc_applications` (
  `appid` smallint(6) unsigned NOT NULL auto_increment,
  `type` varchar(16) NOT NULL default '',
  `name` varchar(20) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  `authkey` varchar(255) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `viewprourl` varchar(255) NOT NULL,
  `apifilename` varchar(30) NOT NULL default 'uc.php',
  `charset` varchar(8) NOT NULL default '',
  `dbcharset` varchar(8) NOT NULL default '',
  `synlogin` tinyint(1) NOT NULL default '0',
  `recvnote` tinyint(1) default '0',
  `extra` text NOT NULL,
  `tagtemplates` text NOT NULL,
  `allowips` text NOT NULL,
  PRIMARY KEY  (`appid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_applications
-- ----------------------------
INSERT INTO `uc_applications` VALUES ('1', 'UCHOME', 'betit', 'http://localhost:8080/betit', '03b2AN84CIIXVP/jtADwI9H/E0lll8neezkMC6mBu0dM/jfL2M4WYDOyEhysJ2wXqZHO4LfkETSD8SRGmO0IXb9wGDBFQUCyD9HhZ9rk2qbJLj+Kw5QZNoUeF6c9', '', '', 'uc.php', '', '', '0', '0', 'a:1:{s:7:\"apppath\";s:3:\"../\";}', '<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n<root>\r\n	<item id=\"template\"><![CDATA[]]></item>\r\n</root>', '');

-- ----------------------------
-- Table structure for `uc_badwords`
-- ----------------------------
DROP TABLE IF EXISTS `uc_badwords`;
CREATE TABLE `uc_badwords` (
  `id` smallint(6) unsigned NOT NULL auto_increment,
  `admin` varchar(15) NOT NULL default '',
  `find` varchar(255) NOT NULL default '',
  `replacement` varchar(255) NOT NULL default '',
  `findpattern` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `find` (`find`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_badwords
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_domains`
-- ----------------------------
DROP TABLE IF EXISTS `uc_domains`;
CREATE TABLE `uc_domains` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `domain` char(40) NOT NULL default '',
  `ip` char(15) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_domains
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_failedlogins`
-- ----------------------------
DROP TABLE IF EXISTS `uc_failedlogins`;
CREATE TABLE `uc_failedlogins` (
  `ip` char(15) NOT NULL default '',
  `count` tinyint(1) unsigned NOT NULL default '0',
  `lastupdate` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_failedlogins
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_feeds`
-- ----------------------------
DROP TABLE IF EXISTS `uc_feeds`;
CREATE TABLE `uc_feeds` (
  `feedid` mediumint(8) unsigned NOT NULL auto_increment,
  `appid` varchar(30) NOT NULL default '',
  `icon` varchar(30) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `hash_template` varchar(32) NOT NULL default '',
  `hash_data` varchar(32) NOT NULL default '',
  `title_template` text NOT NULL,
  `title_data` text NOT NULL,
  `body_template` text NOT NULL,
  `body_data` text NOT NULL,
  `body_general` text NOT NULL,
  `image_1` varchar(255) NOT NULL default '',
  `image_1_link` varchar(255) NOT NULL default '',
  `image_2` varchar(255) NOT NULL default '',
  `image_2_link` varchar(255) NOT NULL default '',
  `image_3` varchar(255) NOT NULL default '',
  `image_3_link` varchar(255) NOT NULL default '',
  `image_4` varchar(255) NOT NULL default '',
  `image_4_link` varchar(255) NOT NULL default '',
  `target_ids` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`feedid`),
  KEY `uid` (`uid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_feeds
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_friends`
-- ----------------------------
DROP TABLE IF EXISTS `uc_friends`;
CREATE TABLE `uc_friends` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `friendid` mediumint(8) unsigned NOT NULL default '0',
  `direction` tinyint(1) NOT NULL default '0',
  `version` int(10) unsigned NOT NULL auto_increment,
  `delstatus` tinyint(1) NOT NULL default '0',
  `comment` char(255) NOT NULL default '',
  PRIMARY KEY  (`version`),
  KEY `uid` (`uid`),
  KEY `friendid` (`friendid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_friends
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_mailqueue`
-- ----------------------------
DROP TABLE IF EXISTS `uc_mailqueue`;
CREATE TABLE `uc_mailqueue` (
  `mailid` int(10) unsigned NOT NULL auto_increment,
  `touid` mediumint(8) unsigned NOT NULL default '0',
  `tomail` varchar(32) NOT NULL,
  `frommail` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `charset` varchar(15) NOT NULL,
  `htmlon` tinyint(1) NOT NULL default '0',
  `level` tinyint(1) NOT NULL default '1',
  `dateline` int(10) unsigned NOT NULL default '0',
  `failures` tinyint(3) unsigned NOT NULL default '0',
  `appid` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`mailid`),
  KEY `appid` (`appid`),
  KEY `level` (`level`,`failures`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_mailqueue
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_memberfields`
-- ----------------------------
DROP TABLE IF EXISTS `uc_memberfields`;
CREATE TABLE `uc_memberfields` (
  `uid` mediumint(8) unsigned NOT NULL,
  `blacklist` text NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_memberfields
-- ----------------------------
INSERT INTO `uc_memberfields` VALUES ('4', '');

-- ----------------------------
-- Table structure for `uc_members`
-- ----------------------------
DROP TABLE IF EXISTS `uc_members`;
CREATE TABLE `uc_members` (
  `uid` mediumint(8) unsigned NOT NULL auto_increment,
  `username` char(15) NOT NULL default '',
  `password` char(32) NOT NULL default '',
  `email` char(32) NOT NULL default '',
  `myid` char(30) NOT NULL default '',
  `myidkey` char(16) NOT NULL default '',
  `regip` char(15) NOT NULL default '',
  `regdate` int(10) unsigned NOT NULL default '0',
  `lastloginip` int(10) NOT NULL default '0',
  `lastlogintime` int(10) unsigned NOT NULL default '0',
  `salt` char(6) NOT NULL,
  `secques` char(8) NOT NULL default '',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_members
-- ----------------------------
INSERT INTO `uc_members` VALUES ('4', 'admin', 'f22dee7362770582525bbe5b94bfde98', 'admin@qq.com', '', '', '127.0.0.1', '1343725030', '0', '0', '60bfcf', '');

-- ----------------------------
-- Table structure for `uc_mergemembers`
-- ----------------------------
DROP TABLE IF EXISTS `uc_mergemembers`;
CREATE TABLE `uc_mergemembers` (
  `appid` smallint(6) unsigned NOT NULL,
  `username` char(15) NOT NULL,
  PRIMARY KEY  (`appid`,`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_mergemembers
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_newpm`
-- ----------------------------
DROP TABLE IF EXISTS `uc_newpm`;
CREATE TABLE `uc_newpm` (
  `uid` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_newpm
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_notelist`
-- ----------------------------
DROP TABLE IF EXISTS `uc_notelist`;
CREATE TABLE `uc_notelist` (
  `noteid` int(10) unsigned NOT NULL auto_increment,
  `operation` char(32) NOT NULL,
  `closed` tinyint(4) NOT NULL default '0',
  `totalnum` smallint(6) unsigned NOT NULL default '0',
  `succeednum` smallint(6) unsigned NOT NULL default '0',
  `getdata` mediumtext NOT NULL,
  `postdata` mediumtext NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  `pri` tinyint(3) NOT NULL default '0',
  `app1` tinyint(4) NOT NULL,
  PRIMARY KEY  (`noteid`),
  KEY `closed` (`closed`,`pri`,`noteid`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_notelist
-- ----------------------------
INSERT INTO `uc_notelist` VALUES ('1', 'updateapps', '1', '0', '0', '', '<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\r\n<root>\r\n	<item id=\"1\">\r\n		<item id=\"appid\"><![CDATA[1]]></item>\r\n		<item id=\"type\"><![CDATA[UCHOME]]></item>\r\n		<item id=\"name\"><![CDATA[betit]]></item>\r\n		<item id=\"url\"><![CDATA[http://localhost:8080/betit]]></item>\r\n		<item id=\"ip\"><![CDATA[]]></item>\r\n		<item id=\"viewprourl\"><![CDATA[]]></item>\r\n		<item id=\"apifilename\"><![CDATA[uc.php]]></item>\r\n		<item id=\"charset\"><![CDATA[]]></item>\r\n		<item id=\"synlogin\"><![CDATA[0]]></item>\r\n		<item id=\"extra\">\r\n			<item id=\"apppath\"><![CDATA[../]]></item>\r\n		</item>\r\n		<item id=\"recvnote\"><![CDATA[0]]></item>\r\n	</item>\r\n	<item id=\"UC_API\"><![CDATA[http://localhost:8080/betit/center]]></item>\r\n</root>', '0', '0', '0');
INSERT INTO `uc_notelist` VALUES ('2', 'deleteuser', '1', '0', '0', 'ids=\'1\',\'2\'', '', '0', '0', '0');
INSERT INTO `uc_notelist` VALUES ('3', 'deleteuser', '1', '0', '0', 'ids=\'3\'', '', '0', '0', '0');

-- ----------------------------
-- Table structure for `uc_pm_indexes`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_indexes`;
CREATE TABLE `uc_pm_indexes` (
  `pmid` mediumint(8) unsigned NOT NULL auto_increment,
  `plid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_indexes
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_lists`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_lists`;
CREATE TABLE `uc_pm_lists` (
  `plid` mediumint(8) unsigned NOT NULL auto_increment,
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `pmtype` tinyint(1) unsigned NOT NULL default '0',
  `subject` varchar(80) NOT NULL,
  `members` smallint(5) unsigned NOT NULL default '0',
  `min_max` varchar(17) NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  `lastmessage` text NOT NULL,
  PRIMARY KEY  (`plid`),
  KEY `pmtype` (`pmtype`),
  KEY `min_max` (`min_max`),
  KEY `authorid` (`authorid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_lists
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_members`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_members`;
CREATE TABLE `uc_pm_members` (
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `isnew` tinyint(1) unsigned NOT NULL default '0',
  `pmnum` int(10) unsigned NOT NULL default '0',
  `lastupdate` int(10) unsigned NOT NULL default '0',
  `lastdateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`plid`,`uid`),
  KEY `isnew` (`isnew`),
  KEY `lastdateline` (`uid`,`lastdateline`),
  KEY `lastupdate` (`uid`,`lastupdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_members
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_messages_0`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_messages_0`;
CREATE TABLE `uc_pm_messages_0` (
  `pmid` mediumint(8) unsigned NOT NULL default '0',
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`,`delstatus`,`dateline`),
  KEY `dateline` (`plid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_messages_0
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_messages_1`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_messages_1`;
CREATE TABLE `uc_pm_messages_1` (
  `pmid` mediumint(8) unsigned NOT NULL default '0',
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`,`delstatus`,`dateline`),
  KEY `dateline` (`plid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_messages_1
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_messages_2`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_messages_2`;
CREATE TABLE `uc_pm_messages_2` (
  `pmid` mediumint(8) unsigned NOT NULL default '0',
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`,`delstatus`,`dateline`),
  KEY `dateline` (`plid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_messages_2
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_messages_3`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_messages_3`;
CREATE TABLE `uc_pm_messages_3` (
  `pmid` mediumint(8) unsigned NOT NULL default '0',
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`,`delstatus`,`dateline`),
  KEY `dateline` (`plid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_messages_3
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_messages_4`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_messages_4`;
CREATE TABLE `uc_pm_messages_4` (
  `pmid` mediumint(8) unsigned NOT NULL default '0',
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`,`delstatus`,`dateline`),
  KEY `dateline` (`plid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_messages_4
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_messages_5`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_messages_5`;
CREATE TABLE `uc_pm_messages_5` (
  `pmid` mediumint(8) unsigned NOT NULL default '0',
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`,`delstatus`,`dateline`),
  KEY `dateline` (`plid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_messages_5
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_messages_6`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_messages_6`;
CREATE TABLE `uc_pm_messages_6` (
  `pmid` mediumint(8) unsigned NOT NULL default '0',
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`,`delstatus`,`dateline`),
  KEY `dateline` (`plid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_messages_6
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_messages_7`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_messages_7`;
CREATE TABLE `uc_pm_messages_7` (
  `pmid` mediumint(8) unsigned NOT NULL default '0',
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`,`delstatus`,`dateline`),
  KEY `dateline` (`plid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_messages_7
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_messages_8`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_messages_8`;
CREATE TABLE `uc_pm_messages_8` (
  `pmid` mediumint(8) unsigned NOT NULL default '0',
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`,`delstatus`,`dateline`),
  KEY `dateline` (`plid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_messages_8
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_pm_messages_9`
-- ----------------------------
DROP TABLE IF EXISTS `uc_pm_messages_9`;
CREATE TABLE `uc_pm_messages_9` (
  `pmid` mediumint(8) unsigned NOT NULL default '0',
  `plid` mediumint(8) unsigned NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `delstatus` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmid`),
  KEY `plid` (`plid`,`delstatus`,`dateline`),
  KEY `dateline` (`plid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_pm_messages_9
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_protectedmembers`
-- ----------------------------
DROP TABLE IF EXISTS `uc_protectedmembers`;
CREATE TABLE `uc_protectedmembers` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `appid` tinyint(1) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `admin` char(15) NOT NULL default '0',
  UNIQUE KEY `username` (`username`,`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_protectedmembers
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_settings`
-- ----------------------------
DROP TABLE IF EXISTS `uc_settings`;
CREATE TABLE `uc_settings` (
  `k` varchar(32) NOT NULL default '',
  `v` text NOT NULL,
  PRIMARY KEY  (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_settings
-- ----------------------------
INSERT INTO `uc_settings` VALUES ('accessemail', '');
INSERT INTO `uc_settings` VALUES ('censoremail', '');
INSERT INTO `uc_settings` VALUES ('censorusername', '');
INSERT INTO `uc_settings` VALUES ('dateformat', 'y-n-j');
INSERT INTO `uc_settings` VALUES ('doublee', '0');
INSERT INTO `uc_settings` VALUES ('nextnotetime', '0');
INSERT INTO `uc_settings` VALUES ('timeoffset', '28800');
INSERT INTO `uc_settings` VALUES ('privatepmthreadlimit', '25');
INSERT INTO `uc_settings` VALUES ('chatpmthreadlimit', '30');
INSERT INTO `uc_settings` VALUES ('chatpmmemberlimit', '35');
INSERT INTO `uc_settings` VALUES ('pmfloodctrl', '15');
INSERT INTO `uc_settings` VALUES ('pmcenter', '1');
INSERT INTO `uc_settings` VALUES ('sendpmseccode', '1');
INSERT INTO `uc_settings` VALUES ('pmsendregdays', '0');
INSERT INTO `uc_settings` VALUES ('maildefault', 'username@21cn.com');
INSERT INTO `uc_settings` VALUES ('mailsend', '1');
INSERT INTO `uc_settings` VALUES ('mailserver', 'smtp.21cn.com');
INSERT INTO `uc_settings` VALUES ('mailport', '25');
INSERT INTO `uc_settings` VALUES ('mailauth', '1');
INSERT INTO `uc_settings` VALUES ('mailfrom', 'UCenter <username@21cn.com>');
INSERT INTO `uc_settings` VALUES ('mailauth_username', 'username@21cn.com');
INSERT INTO `uc_settings` VALUES ('mailauth_password', 'password');
INSERT INTO `uc_settings` VALUES ('maildelimiter', '0');
INSERT INTO `uc_settings` VALUES ('mailusername', '1');
INSERT INTO `uc_settings` VALUES ('mailsilent', '1');
INSERT INTO `uc_settings` VALUES ('version', '1.6.0');

-- ----------------------------
-- Table structure for `uc_sqlcache`
-- ----------------------------
DROP TABLE IF EXISTS `uc_sqlcache`;
CREATE TABLE `uc_sqlcache` (
  `sqlid` char(6) NOT NULL default '',
  `data` char(100) NOT NULL,
  `expiry` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`sqlid`),
  KEY `expiry` (`expiry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_sqlcache
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_tags`
-- ----------------------------
DROP TABLE IF EXISTS `uc_tags`;
CREATE TABLE `uc_tags` (
  `tagname` char(20) NOT NULL,
  `appid` smallint(6) unsigned NOT NULL default '0',
  `data` mediumtext,
  `expiration` int(10) unsigned NOT NULL,
  KEY `tagname` (`tagname`,`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_tags
-- ----------------------------

-- ----------------------------
-- Table structure for `uc_vars`
-- ----------------------------
DROP TABLE IF EXISTS `uc_vars`;
CREATE TABLE `uc_vars` (
  `name` char(32) NOT NULL default '',
  `value` char(255) NOT NULL default '',
  PRIMARY KEY  (`name`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uc_vars
-- ----------------------------
INSERT INTO `uc_vars` VALUES ('noteexists', '1');
INSERT INTO `uc_vars` VALUES ('noteexists1', '0');

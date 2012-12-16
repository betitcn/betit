/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50051
Source Host           : localhost:3306
Source Database       : betit

Target Server Type    : MYSQL
Target Server Version : 50051
File Encoding         : 65001

Date: 2012-07-31 17:04:32
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `uchome_ad`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_ad`;
CREATE TABLE `uchome_ad` (
  `adid` smallint(6) unsigned NOT NULL auto_increment,
  `available` tinyint(1) NOT NULL default '1',
  `title` varchar(50) NOT NULL default '',
  `pagetype` varchar(20) NOT NULL default '',
  `adcode` text NOT NULL,
  `system` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`adid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_ad
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_adminsession`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_adminsession`;
CREATE TABLE `uchome_adminsession` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `ip` char(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `errorcount` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_adminsession
-- ----------------------------
INSERT INTO `uchome_adminsession` VALUES ('1', '127.0.0.1', '1343724165', '-1');

-- ----------------------------
-- Table structure for `uchome_album`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_album`;
CREATE TABLE `uchome_album` (
  `albumid` mediumint(8) unsigned NOT NULL auto_increment,
  `albumname` varchar(50) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `updatetime` int(10) unsigned NOT NULL default '0',
  `picnum` smallint(6) unsigned NOT NULL default '0',
  `pic` varchar(60) NOT NULL default '',
  `picflag` tinyint(1) NOT NULL default '0',
  `friend` tinyint(1) NOT NULL default '0',
  `password` varchar(10) NOT NULL default '',
  `target_ids` text NOT NULL,
  PRIMARY KEY  (`albumid`),
  KEY `uid` (`uid`,`updatetime`),
  KEY `updatetime` (`updatetime`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_album
-- ----------------------------
INSERT INTO `uchome_album` VALUES ('1', '我的相册', '1', 'admin', '1343201659', '1343201659', '0', '', '1', '0', '', '');

-- ----------------------------
-- Table structure for `uchome_appcreditlog`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_appcreditlog`;
CREATE TABLE `uchome_appcreditlog` (
  `logid` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `appid` mediumint(8) unsigned NOT NULL default '0',
  `appname` varchar(60) NOT NULL default '',
  `type` tinyint(1) NOT NULL default '0',
  `credit` mediumint(8) unsigned NOT NULL default '0',
  `note` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`logid`),
  KEY `uid` (`uid`,`dateline`),
  KEY `appid` (`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_appcreditlog
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_blacklist`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_blacklist`;
CREATE TABLE `uchome_blacklist` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `buid` mediumint(8) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`,`buid`),
  KEY `uid` (`uid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_blacklist
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_block`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_block`;
CREATE TABLE `uchome_block` (
  `bid` smallint(6) unsigned NOT NULL auto_increment,
  `blockname` varchar(40) NOT NULL default '',
  `blocksql` text NOT NULL,
  `cachename` varchar(30) NOT NULL default '',
  `cachetime` smallint(6) unsigned NOT NULL default '0',
  `startnum` tinyint(3) unsigned NOT NULL default '0',
  `num` tinyint(3) unsigned NOT NULL default '0',
  `perpage` tinyint(3) unsigned NOT NULL default '0',
  `htmlcode` text NOT NULL,
  PRIMARY KEY  (`bid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_block
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_blog`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_blog`;
CREATE TABLE `uchome_blog` (
  `blogid` mediumint(8) unsigned NOT NULL auto_increment,
  `topicid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `subject` char(80) NOT NULL default '',
  `classid` smallint(6) unsigned NOT NULL default '0',
  `viewnum` mediumint(8) unsigned NOT NULL default '0',
  `replynum` mediumint(8) unsigned NOT NULL default '0',
  `hot` mediumint(8) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `pic` char(120) NOT NULL default '',
  `picflag` tinyint(1) NOT NULL default '0',
  `noreply` tinyint(1) NOT NULL default '0',
  `friend` tinyint(1) NOT NULL default '0',
  `password` char(10) NOT NULL default '',
  `click_1` smallint(6) unsigned NOT NULL default '0',
  `click_2` smallint(6) unsigned NOT NULL default '0',
  `click_3` smallint(6) unsigned NOT NULL default '0',
  `click_4` smallint(6) unsigned NOT NULL default '0',
  `click_5` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`blogid`),
  KEY `uid` (`uid`,`dateline`),
  KEY `topicid` (`topicid`,`dateline`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_blog
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_blogfield`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_blogfield`;
CREATE TABLE `uchome_blogfield` (
  `blogid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `tag` varchar(255) NOT NULL default '',
  `message` mediumtext NOT NULL,
  `postip` varchar(20) NOT NULL default '',
  `related` text NOT NULL,
  `relatedtime` int(10) unsigned NOT NULL default '0',
  `target_ids` text NOT NULL,
  `hotuser` text NOT NULL,
  `magiccolor` tinyint(6) NOT NULL default '0',
  `magicpaper` tinyint(6) NOT NULL default '0',
  `magiccall` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`blogid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_blogfield
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_cache`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_cache`;
CREATE TABLE `uchome_cache` (
  `cachekey` varchar(16) NOT NULL default '',
  `value` mediumtext NOT NULL,
  `mtime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cachekey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_cache
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_class`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_class`;
CREATE TABLE `uchome_class` (
  `classid` mediumint(8) unsigned NOT NULL auto_increment,
  `classname` char(40) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`classid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_class
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_click`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_click`;
CREATE TABLE `uchome_click` (
  `clickid` smallint(6) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `icon` varchar(100) NOT NULL default '',
  `idtype` varchar(15) NOT NULL default '',
  `displayorder` tinyint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`clickid`),
  KEY `idtype` (`idtype`,`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_click
-- ----------------------------
INSERT INTO `uchome_click` VALUES ('1', '路过', 'luguo.gif', 'blogid', '0');
INSERT INTO `uchome_click` VALUES ('2', '雷人', 'leiren.gif', 'blogid', '0');
INSERT INTO `uchome_click` VALUES ('3', '握手', 'woshou.gif', 'blogid', '0');
INSERT INTO `uchome_click` VALUES ('4', '鲜花', 'xianhua.gif', 'blogid', '0');
INSERT INTO `uchome_click` VALUES ('5', '鸡蛋', 'jidan.gif', 'blogid', '0');
INSERT INTO `uchome_click` VALUES ('6', '漂亮', 'piaoliang.gif', 'picid', '0');
INSERT INTO `uchome_click` VALUES ('7', '酷毙', 'kubi.gif', 'picid', '0');
INSERT INTO `uchome_click` VALUES ('8', '雷人', 'leiren.gif', 'picid', '0');
INSERT INTO `uchome_click` VALUES ('9', '鲜花', 'xianhua.gif', 'picid', '0');
INSERT INTO `uchome_click` VALUES ('10', '鸡蛋', 'jidan.gif', 'picid', '0');
INSERT INTO `uchome_click` VALUES ('11', '搞笑', 'gaoxiao.gif', 'tid', '0');
INSERT INTO `uchome_click` VALUES ('12', '迷惑', 'mihuo.gif', 'tid', '0');
INSERT INTO `uchome_click` VALUES ('13', '雷人', 'leiren.gif', 'tid', '0');
INSERT INTO `uchome_click` VALUES ('14', '鲜花', 'xianhua.gif', 'tid', '0');
INSERT INTO `uchome_click` VALUES ('15', '鸡蛋', 'jidan.gif', 'tid', '0');

-- ----------------------------
-- Table structure for `uchome_clickuser`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_clickuser`;
CREATE TABLE `uchome_clickuser` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `id` mediumint(8) unsigned NOT NULL default '0',
  `idtype` varchar(15) NOT NULL default '',
  `clickid` smallint(6) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  KEY `id` (`id`,`idtype`,`dateline`),
  KEY `uid` (`uid`,`idtype`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_clickuser
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_comment`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_comment`;
CREATE TABLE `uchome_comment` (
  `cid` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `id` mediumint(8) unsigned NOT NULL default '0',
  `idtype` varchar(20) NOT NULL default '',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `author` varchar(15) NOT NULL default '',
  `ip` varchar(20) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `magicflicker` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `authorid` (`authorid`,`idtype`),
  KEY `id` (`id`,`idtype`,`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_comment
-- ----------------------------
INSERT INTO `uchome_comment` VALUES ('4', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719133', '佣兵', '0');
INSERT INTO `uchome_comment` VALUES ('5', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719136', '佣兵', '0');
INSERT INTO `uchome_comment` VALUES ('6', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719139', '手指', '0');
INSERT INTO `uchome_comment` VALUES ('7', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719142', '手摇和', '0');
INSERT INTO `uchome_comment` VALUES ('8', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719147', '奇才', '0');
INSERT INTO `uchome_comment` VALUES ('9', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719150', '地', '0');
INSERT INTO `uchome_comment` VALUES ('10', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719155', '4234234234', '0');
INSERT INTO `uchome_comment` VALUES ('11', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719159', '234324', '0');
INSERT INTO `uchome_comment` VALUES ('12', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719162', '23423423423423', '0');
INSERT INTO `uchome_comment` VALUES ('13', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719169', '234234234324', '0');
INSERT INTO `uchome_comment` VALUES ('14', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719173', '234234234234324324', '0');
INSERT INTO `uchome_comment` VALUES ('15', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719177', '234234234324234', '0');
INSERT INTO `uchome_comment` VALUES ('16', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719181', '2344444444444444444444444444444', '0');
INSERT INTO `uchome_comment` VALUES ('17', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719185', '234444444444444444444', '0');
INSERT INTO `uchome_comment` VALUES ('18', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719189', '2344444444444444444444444', '0');
INSERT INTO `uchome_comment` VALUES ('19', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719197', '435', '0');
INSERT INTO `uchome_comment` VALUES ('20', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719199', '345', '0');
INSERT INTO `uchome_comment` VALUES ('21', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719201', '435', '0');
INSERT INTO `uchome_comment` VALUES ('22', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719203', '345', '0');
INSERT INTO `uchome_comment` VALUES ('23', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719205', '435', '0');
INSERT INTO `uchome_comment` VALUES ('24', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719207', '435', '0');
INSERT INTO `uchome_comment` VALUES ('25', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719210', '345435', '0');
INSERT INTO `uchome_comment` VALUES ('26', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719211', '435435', '0');
INSERT INTO `uchome_comment` VALUES ('27', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719215', '345435435', '0');
INSERT INTO `uchome_comment` VALUES ('28', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719216', '34543543', '0');
INSERT INTO `uchome_comment` VALUES ('29', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719230', '345555555555555555555555555555', '0');
INSERT INTO `uchome_comment` VALUES ('30', '1', '13', 'quizid', '2', 'summit', '127.0.0.1', '1343719233', '555555555555555555555555555555', '0');

-- ----------------------------
-- Table structure for `uchome_config`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_config`;
CREATE TABLE `uchome_config` (
  `var` varchar(30) NOT NULL default '',
  `datavalue` text NOT NULL,
  PRIMARY KEY  (`var`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_config
-- ----------------------------
INSERT INTO `uchome_config` VALUES ('sitename', '我的空间');
INSERT INTO `uchome_config` VALUES ('template', 'default');
INSERT INTO `uchome_config` VALUES ('adminemail', 'webmaster@localhost:8080');
INSERT INTO `uchome_config` VALUES ('onlinehold', '1800');
INSERT INTO `uchome_config` VALUES ('timeoffset', '8');
INSERT INTO `uchome_config` VALUES ('maxpage', '100');
INSERT INTO `uchome_config` VALUES ('starcredit', '100');
INSERT INTO `uchome_config` VALUES ('starlevelnum', '5');
INSERT INTO `uchome_config` VALUES ('cachemode', 'database');
INSERT INTO `uchome_config` VALUES ('cachegrade', '0');
INSERT INTO `uchome_config` VALUES ('allowcache', '1');
INSERT INTO `uchome_config` VALUES ('allowdomain', '0');
INSERT INTO `uchome_config` VALUES ('allowrewrite', '0');
INSERT INTO `uchome_config` VALUES ('allowwatermark', '0');
INSERT INTO `uchome_config` VALUES ('allowftp', '0');
INSERT INTO `uchome_config` VALUES ('holddomain', 'www|*blog*|*space*|x');
INSERT INTO `uchome_config` VALUES ('mtagminnum', '5');
INSERT INTO `uchome_config` VALUES ('feedday', '7');
INSERT INTO `uchome_config` VALUES ('feedmaxnum', '100');
INSERT INTO `uchome_config` VALUES ('feedfilternum', '10');
INSERT INTO `uchome_config` VALUES ('importnum', '100');
INSERT INTO `uchome_config` VALUES ('maxreward', '10');
INSERT INTO `uchome_config` VALUES ('singlesent', '50');
INSERT INTO `uchome_config` VALUES ('groupnum', '8');
INSERT INTO `uchome_config` VALUES ('closeregister', '0');
INSERT INTO `uchome_config` VALUES ('closeinvite', '0');
INSERT INTO `uchome_config` VALUES ('close', '0');
INSERT INTO `uchome_config` VALUES ('networkpublic', '1');
INSERT INTO `uchome_config` VALUES ('networkpage', '1');
INSERT INTO `uchome_config` VALUES ('seccode_register', '1');
INSERT INTO `uchome_config` VALUES ('uc_tagrelated', '1');
INSERT INTO `uchome_config` VALUES ('manualmoderator', '1');
INSERT INTO `uchome_config` VALUES ('linkguide', '1');
INSERT INTO `uchome_config` VALUES ('showall', '1');
INSERT INTO `uchome_config` VALUES ('sendmailday', '0');
INSERT INTO `uchome_config` VALUES ('realname', '0');
INSERT INTO `uchome_config` VALUES ('namecheck', '0');
INSERT INTO `uchome_config` VALUES ('namechange', '0');
INSERT INTO `uchome_config` VALUES ('name_allowviewspace', '1');
INSERT INTO `uchome_config` VALUES ('name_allowfriend', '1');
INSERT INTO `uchome_config` VALUES ('name_allowpoke', '1');
INSERT INTO `uchome_config` VALUES ('name_allowdoing', '1');
INSERT INTO `uchome_config` VALUES ('name_allowblog', '0');
INSERT INTO `uchome_config` VALUES ('name_allowalbum', '0');
INSERT INTO `uchome_config` VALUES ('name_allowthread', '0');
INSERT INTO `uchome_config` VALUES ('name_allowshare', '0');
INSERT INTO `uchome_config` VALUES ('name_allowcomment', '0');
INSERT INTO `uchome_config` VALUES ('name_allowpost', '0');
INSERT INTO `uchome_config` VALUES ('showallfriendnum', '10');
INSERT INTO `uchome_config` VALUES ('feedtargetblank', '1');
INSERT INTO `uchome_config` VALUES ('feedread', '1');
INSERT INTO `uchome_config` VALUES ('feedhotnum', '3');
INSERT INTO `uchome_config` VALUES ('feedhotday', '2');
INSERT INTO `uchome_config` VALUES ('feedhotmin', '3');
INSERT INTO `uchome_config` VALUES ('feedhiddenicon', 'friend,profile,task,wall');
INSERT INTO `uchome_config` VALUES ('uc_tagrelatedtime', '86400');
INSERT INTO `uchome_config` VALUES ('privacy', 'a:2:{s:4:\"view\";a:12:{s:5:\"index\";i:0;s:6:\"friend\";i:0;s:4:\"wall\";i:0;s:4:\"feed\";i:0;s:4:\"mtag\";i:0;s:5:\"event\";i:0;s:5:\"doing\";i:0;s:4:\"blog\";i:0;s:4:\"quiz\";i:0;s:5:\"album\";i:0;s:5:\"share\";i:0;s:4:\"poll\";i:0;}s:4:\"feed\";a:22:{s:5:\"doing\";i:1;s:4:\"blog\";i:1;s:4:\"quiz\";i:1;s:8:\"joinquiz\";i:1;s:6:\"upload\";i:1;s:5:\"share\";i:1;s:4:\"poll\";i:1;s:8:\"joinpoll\";i:1;s:6:\"thread\";i:1;s:4:\"post\";i:1;s:4:\"mtag\";i:1;s:5:\"event\";i:1;s:4:\"join\";i:1;s:6:\"friend\";i:1;s:7:\"comment\";i:1;s:4:\"show\";i:1;s:6:\"credit\";i:1;s:9:\"spaceopen\";i:1;s:6:\"invite\";i:1;s:4:\"task\";i:1;s:7:\"profile\";i:1;s:5:\"click\";i:1;}}');
INSERT INTO `uchome_config` VALUES ('cronnextrun', '1343725500');
INSERT INTO `uchome_config` VALUES ('my_status', '0');
INSERT INTO `uchome_config` VALUES ('uniqueemail', '1');
INSERT INTO `uchome_config` VALUES ('updatestat', '1');
INSERT INTO `uchome_config` VALUES ('my_showgift', '1');
INSERT INTO `uchome_config` VALUES ('topcachetime', '60');
INSERT INTO `uchome_config` VALUES ('newspacenum', '3');
INSERT INTO `uchome_config` VALUES ('sitekey', '7f4daaM2tfZfFjdP');
INSERT INTO `uchome_config` VALUES ('name_allowquiz', '0');

-- ----------------------------
-- Table structure for `uchome_creditlog`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_creditlog`;
CREATE TABLE `uchome_creditlog` (
  `clid` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `rid` mediumint(8) unsigned NOT NULL default '0',
  `total` mediumint(8) unsigned NOT NULL default '0',
  `cyclenum` mediumint(8) unsigned NOT NULL default '0',
  `credit` mediumint(8) unsigned NOT NULL default '0',
  `experience` mediumint(8) unsigned NOT NULL default '0',
  `starttime` int(10) unsigned NOT NULL default '0',
  `info` text NOT NULL,
  `user` text NOT NULL,
  `app` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`clid`),
  KEY `uid` (`uid`,`rid`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_creditlog
-- ----------------------------
INSERT INTO `uchome_creditlog` VALUES ('1', '1', '1', '1', '1', '10', '0', '0', '', '', '', '1342699314');
INSERT INTO `uchome_creditlog` VALUES ('2', '1', '10', '9', '1', '15', '15', '0', '', '', '', '1343714339');
INSERT INTO `uchome_creditlog` VALUES ('3', '1', '16', '2', '1', '5', '5', '0', '', '', '', '1343116247');
INSERT INTO `uchome_creditlog` VALUES ('4', '1', '21', '1', '1', '2', '2', '0', '', '', '', '1342966949');
INSERT INTO `uchome_creditlog` VALUES ('5', '1', '17', '1', '1', '2', '2', '0', '', '', '', '1343201659');
INSERT INTO `uchome_creditlog` VALUES ('6', '2', '1', '1', '1', '10', '0', '0', '', '', '', '1343631108');
INSERT INTO `uchome_creditlog` VALUES ('7', '2', '10', '2', '1', '15', '15', '0', '', '', '', '1343700493');
INSERT INTO `uchome_creditlog` VALUES ('8', '2', '11', '1', '1', '1', '1', '0', '', '1', '', '1343631111');
INSERT INTO `uchome_creditlog` VALUES ('9', '2', '27', '1', '1', '1', '1', '0', 'quizid13', '', '', '1343707719');
INSERT INTO `uchome_creditlog` VALUES ('10', '1', '28', '1', '1', '1', '0', '0', 'quizid13', '', '', '1343707719');
INSERT INTO `uchome_creditlog` VALUES ('11', '2', '26', '1', '1', '2', '2', '0', '', '', '', '1343718233');
INSERT INTO `uchome_creditlog` VALUES ('12', '4', '1', '1', '1', '10', '0', '0', '', '', '', '1343725030');
INSERT INTO `uchome_creditlog` VALUES ('13', '4', '10', '1', '1', '15', '15', '0', '', '', '', '1343725030');

-- ----------------------------
-- Table structure for `uchome_creditrule`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_creditrule`;
CREATE TABLE `uchome_creditrule` (
  `rid` mediumint(8) unsigned NOT NULL auto_increment,
  `rulename` char(20) NOT NULL default '',
  `action` char(20) NOT NULL default '',
  `cycletype` tinyint(1) NOT NULL default '0',
  `cycletime` int(10) NOT NULL default '0',
  `rewardnum` tinyint(2) NOT NULL default '1',
  `rewardtype` tinyint(1) NOT NULL default '1',
  `norepeat` tinyint(1) NOT NULL default '0',
  `credit` mediumint(8) unsigned NOT NULL default '0',
  `experience` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rid`),
  KEY `action` (`action`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_creditrule
-- ----------------------------
INSERT INTO `uchome_creditrule` VALUES ('1', '开通空间', 'register', '0', '0', '1', '1', '0', '10', '0');
INSERT INTO `uchome_creditrule` VALUES ('2', '实名认证', 'realname', '0', '0', '1', '1', '0', '20', '20');
INSERT INTO `uchome_creditrule` VALUES ('3', '邮箱认证', 'realemail', '0', '0', '1', '1', '0', '40', '40');
INSERT INTO `uchome_creditrule` VALUES ('4', '成功邀请好友', 'invitefriend', '4', '0', '20', '1', '0', '10', '10');
INSERT INTO `uchome_creditrule` VALUES ('5', '设置头像', 'setavatar', '0', '0', '1', '1', '0', '15', '15');
INSERT INTO `uchome_creditrule` VALUES ('6', '视频认证', 'videophoto', '0', '0', '1', '1', '0', '40', '40');
INSERT INTO `uchome_creditrule` VALUES ('7', '成功举报', 'report', '4', '0', '0', '1', '0', '2', '2');
INSERT INTO `uchome_creditrule` VALUES ('8', '更新心情', 'updatemood', '1', '0', '3', '1', '0', '3', '3');
INSERT INTO `uchome_creditrule` VALUES ('9', '热点信息', 'hotinfo', '4', '0', '0', '1', '0', '10', '10');
INSERT INTO `uchome_creditrule` VALUES ('10', '每天登陆', 'daylogin', '1', '0', '1', '1', '0', '15', '15');
INSERT INTO `uchome_creditrule` VALUES ('11', '访问别人空间', 'visit', '1', '0', '10', '1', '2', '1', '1');
INSERT INTO `uchome_creditrule` VALUES ('12', '打招呼', 'poke', '1', '0', '10', '1', '2', '1', '1');
INSERT INTO `uchome_creditrule` VALUES ('13', '留言', 'guestbook', '1', '0', '20', '1', '2', '2', '2');
INSERT INTO `uchome_creditrule` VALUES ('14', '被留言', 'getguestbook', '1', '0', '5', '1', '2', '1', '0');
INSERT INTO `uchome_creditrule` VALUES ('15', '发表记录', 'doing', '1', '0', '5', '1', '0', '1', '1');
INSERT INTO `uchome_creditrule` VALUES ('16', '发表日志', 'publishblog', '1', '0', '3', '1', '0', '5', '5');
INSERT INTO `uchome_creditrule` VALUES ('17', '上传图片', 'uploadimage', '1', '0', '10', '1', '0', '2', '2');
INSERT INTO `uchome_creditrule` VALUES ('18', '拍大头贴', 'camera', '1', '0', '5', '1', '0', '3', '3');
INSERT INTO `uchome_creditrule` VALUES ('19', '发表话题', 'publishthread', '1', '0', '5', '1', '0', '5', '5');
INSERT INTO `uchome_creditrule` VALUES ('20', '回复话题', 'replythread', '1', '0', '10', '1', '1', '1', '1');
INSERT INTO `uchome_creditrule` VALUES ('21', '创建投票', 'createpoll', '1', '0', '5', '1', '0', '2', '2');
INSERT INTO `uchome_creditrule` VALUES ('22', '参与投票', 'joinpoll', '1', '0', '10', '1', '1', '1', '1');
INSERT INTO `uchome_creditrule` VALUES ('23', '发起活动', 'createevent', '1', '0', '1', '1', '0', '3', '3');
INSERT INTO `uchome_creditrule` VALUES ('24', '参与活动', 'joinevent', '1', '0', '1', '1', '1', '1', '1');
INSERT INTO `uchome_creditrule` VALUES ('25', '推荐活动', 'recommendevent', '4', '0', '0', '1', '0', '10', '10');
INSERT INTO `uchome_creditrule` VALUES ('26', '发起分享', 'createshare', '1', '0', '3', '1', '0', '2', '2');
INSERT INTO `uchome_creditrule` VALUES ('27', '评论', 'comment', '1', '0', '40', '1', '1', '1', '1');
INSERT INTO `uchome_creditrule` VALUES ('28', '被评论', 'getcomment', '1', '0', '20', '1', '1', '1', '0');
INSERT INTO `uchome_creditrule` VALUES ('29', '安装应用', 'installapp', '4', '0', '0', '1', '3', '5', '5');
INSERT INTO `uchome_creditrule` VALUES ('30', '使用应用', 'useapp', '1', '0', '10', '1', '3', '1', '1');
INSERT INTO `uchome_creditrule` VALUES ('31', '信息表态', 'click', '1', '0', '10', '1', '1', '1', '1');
INSERT INTO `uchome_creditrule` VALUES ('32', '修改实名', 'editrealname', '0', '0', '1', '0', '0', '5', '0');
INSERT INTO `uchome_creditrule` VALUES ('33', '更改邮箱认证', 'editrealemail', '0', '0', '1', '0', '0', '5', '0');
INSERT INTO `uchome_creditrule` VALUES ('34', '头像被删除', 'delavatar', '0', '0', '1', '0', '0', '10', '10');
INSERT INTO `uchome_creditrule` VALUES ('35', '获取邀请码', 'invitecode', '0', '0', '1', '0', '0', '0', '0');
INSERT INTO `uchome_creditrule` VALUES ('36', '搜索一次', 'search', '0', '0', '1', '0', '0', '1', '0');
INSERT INTO `uchome_creditrule` VALUES ('37', '日志导入', 'blogimport', '0', '0', '1', '0', '0', '10', '0');
INSERT INTO `uchome_creditrule` VALUES ('38', '修改域名', 'modifydomain', '0', '0', '1', '0', '0', '5', '0');
INSERT INTO `uchome_creditrule` VALUES ('39', '日志被删除', 'delblog', '0', '0', '1', '0', '0', '10', '10');
INSERT INTO `uchome_creditrule` VALUES ('40', '记录被删除', 'deldoing', '0', '0', '1', '0', '0', '2', '2');
INSERT INTO `uchome_creditrule` VALUES ('41', '图片被删除', 'delimage', '0', '0', '1', '0', '0', '4', '4');
INSERT INTO `uchome_creditrule` VALUES ('42', '投票被删除', 'delpoll', '0', '0', '1', '0', '0', '4', '4');
INSERT INTO `uchome_creditrule` VALUES ('43', '话题被删除', 'delthread', '0', '0', '1', '0', '0', '4', '4');
INSERT INTO `uchome_creditrule` VALUES ('44', '活动被删除', 'delevent', '0', '0', '1', '0', '0', '6', '6');
INSERT INTO `uchome_creditrule` VALUES ('45', '分享被删除', 'delshare', '0', '0', '1', '0', '0', '4', '4');
INSERT INTO `uchome_creditrule` VALUES ('46', '留言被删除', 'delguestbook', '0', '0', '1', '0', '0', '4', '4');
INSERT INTO `uchome_creditrule` VALUES ('47', '评论被删除', 'delcomment', '0', '0', '1', '0', '0', '2', '2');
INSERT INTO `uchome_creditrule` VALUES ('48', '发表竞猜', 'publishquiz', '1', '0', '3', '1', '0', '5', '5');
INSERT INTO `uchome_creditrule` VALUES ('49', '竞猜被删除', 'delquiz', '0', '0', '1', '0', '0', '10', '10');
INSERT INTO `uchome_creditrule` VALUES ('50', '竞猜导入', 'quizimport', '0', '0', '1', '0', '0', '10', '0');
INSERT INTO `uchome_creditrule` VALUES ('51', '参与竞猜', 'joinquiz', '1', '0', '10', '1', '1', '1', '1');

-- ----------------------------
-- Table structure for `uchome_cron`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_cron`;
CREATE TABLE `uchome_cron` (
  `cronid` smallint(6) unsigned NOT NULL auto_increment,
  `available` tinyint(1) NOT NULL default '0',
  `type` enum('user','system') NOT NULL default 'user',
  `name` char(50) NOT NULL default '',
  `filename` char(50) NOT NULL default '',
  `lastrun` int(10) unsigned NOT NULL default '0',
  `nextrun` int(10) unsigned NOT NULL default '0',
  `weekday` tinyint(1) NOT NULL default '0',
  `day` tinyint(2) NOT NULL default '0',
  `hour` tinyint(2) NOT NULL default '0',
  `minute` char(36) NOT NULL default '',
  PRIMARY KEY  (`cronid`),
  KEY `nextrun` (`available`,`nextrun`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_cron
-- ----------------------------
INSERT INTO `uchome_cron` VALUES ('1', '1', 'system', '更新浏览数统计', 'log.php', '1343725300', '1343725500', '-1', '-1', '-1', '0	5	10	15	20	25	30	35	40	45	50	55');
INSERT INTO `uchome_cron` VALUES ('2', '1', 'system', '清理过期feed', 'cleanfeed.php', '1343700505', '1343761440', '-1', '-1', '3', '4');
INSERT INTO `uchome_cron` VALUES ('3', '1', 'system', '清理个人通知', 'cleannotification.php', '1343700508', '1343768760', '-1', '-1', '5', '6');
INSERT INTO `uchome_cron` VALUES ('4', '1', 'system', '同步UC的feed', 'getfeed.php', '1343725341', '1343725620', '-1', '-1', '-1', '2	7	12	17	22	27	32	37	42	47	52');
INSERT INTO `uchome_cron` VALUES ('5', '1', 'system', '清理脚印和最新访客', 'cleantrace.php', '1343700503', '1343757780', '-1', '-1', '2', '3');

-- ----------------------------
-- Table structure for `uchome_data`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_data`;
CREATE TABLE `uchome_data` (
  `var` varchar(20) NOT NULL default '',
  `datavalue` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`var`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_data
-- ----------------------------
INSERT INTO `uchome_data` VALUES ('mail', 'a:3:{s:8:\"mailsend\";s:1:\"1\";s:13:\"maildelimiter\";s:1:\"0\";s:12:\"mailusername\";s:1:\"1\";}', '1342699306');
INSERT INTO `uchome_data` VALUES ('setting', 'a:4:{s:10:\"thumbwidth\";s:3:\"100\";s:11:\"thumbheight\";s:3:\"100\";s:12:\"watermarkpos\";s:1:\"4\";s:14:\"watermarktrans\";s:2:\"75\";}', '1342699306');
INSERT INTO `uchome_data` VALUES ('network', 'a:5:{s:4:\"blog\";a:2:{s:4:\"hot1\";s:1:\"3\";s:5:\"cache\";s:3:\"600\";}s:3:\"pic\";a:2:{s:4:\"hot1\";s:1:\"3\";s:5:\"cache\";s:3:\"700\";}s:6:\"thread\";a:2:{s:4:\"hot1\";s:1:\"3\";s:5:\"cache\";s:3:\"800\";}s:5:\"event\";a:1:{s:5:\"cache\";s:3:\"900\";}s:4:\"poll\";a:1:{s:5:\"cache\";s:3:\"500\";}}', '1342699306');
INSERT INTO `uchome_data` VALUES ('newspacelist', 'a:1:{i:0;a:6:{s:3:\"uid\";s:1:\"4\";s:8:\"username\";s:5:\"admin\";s:4:\"name\";s:0:\"\";s:10:\"namestatus\";s:1:\"0\";s:11:\"videostatus\";s:1:\"0\";s:8:\"dateline\";s:10:\"1343725030\";}}', '1343725030');

-- ----------------------------
-- Table structure for `uchome_docomment`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_docomment`;
CREATE TABLE `uchome_docomment` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `upid` int(10) unsigned NOT NULL default '0',
  `doid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `ip` varchar(20) NOT NULL default '',
  `grade` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `doid` (`doid`,`dateline`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_docomment
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_doing`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_doing`;
CREATE TABLE `uchome_doing` (
  `doid` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `from` varchar(20) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `ip` varchar(20) NOT NULL default '',
  `replynum` int(10) unsigned NOT NULL default '0',
  `mood` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`doid`),
  KEY `uid` (`uid`,`dateline`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_doing
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_event`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_event`;
CREATE TABLE `uchome_event` (
  `eventid` mediumint(8) unsigned NOT NULL auto_increment,
  `topicid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `title` varchar(80) NOT NULL default '',
  `classid` smallint(6) unsigned NOT NULL default '0',
  `province` varchar(20) NOT NULL default '',
  `city` varchar(20) NOT NULL default '',
  `location` varchar(80) NOT NULL default '',
  `poster` varchar(60) NOT NULL default '',
  `thumb` tinyint(1) NOT NULL default '0',
  `remote` tinyint(1) NOT NULL default '0',
  `deadline` int(10) unsigned NOT NULL default '0',
  `starttime` int(10) unsigned NOT NULL default '0',
  `endtime` int(10) unsigned NOT NULL default '0',
  `public` tinyint(3) NOT NULL default '0',
  `membernum` mediumint(8) unsigned NOT NULL default '0',
  `follownum` mediumint(8) unsigned NOT NULL default '0',
  `viewnum` mediumint(8) unsigned NOT NULL default '0',
  `grade` tinyint(3) NOT NULL default '0',
  `recommendtime` int(10) unsigned NOT NULL default '0',
  `tagid` mediumint(8) unsigned NOT NULL default '0',
  `picnum` mediumint(8) unsigned NOT NULL default '0',
  `threadnum` mediumint(8) unsigned NOT NULL default '0',
  `updatetime` int(10) unsigned NOT NULL default '0',
  `hot` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`eventid`),
  KEY `grade` (`grade`,`recommendtime`),
  KEY `membernum` (`membernum`),
  KEY `uid` (`uid`,`eventid`),
  KEY `tagid` (`tagid`,`eventid`),
  KEY `topicid` (`topicid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_event
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_eventclass`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_eventclass`;
CREATE TABLE `uchome_eventclass` (
  `classid` smallint(6) unsigned NOT NULL auto_increment,
  `classname` varchar(80) NOT NULL default '',
  `poster` tinyint(1) NOT NULL default '0',
  `template` text NOT NULL,
  `displayorder` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`classid`),
  UNIQUE KEY `classname` (`classname`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_eventclass
-- ----------------------------
INSERT INTO `uchome_eventclass` VALUES ('1', '生活/聚会', '0', '费用说明：\r\n集合地点：\r\n着装要求：\r\n联系方式：\r\n注意事项：', '1');
INSERT INTO `uchome_eventclass` VALUES ('2', '出行/旅游', '0', '路线说明:\r\n费用说明:\r\n装备要求:\r\n交通工具:\r\n集合地点:\r\n联系方式:\r\n注意事项:', '2');
INSERT INTO `uchome_eventclass` VALUES ('3', '比赛/运动', '0', '费用说明：\r\n集合地点：\r\n着装要求：\r\n场地介绍：\r\n联系方式：\r\n注意事项：', '4');
INSERT INTO `uchome_eventclass` VALUES ('4', '电影/演出', '0', '剧情介绍：\r\n费用说明：\r\n集合地点：\r\n联系方式：\r\n注意事项：', '3');
INSERT INTO `uchome_eventclass` VALUES ('5', '教育/讲座', '0', '主办单位：\r\n活动主题：\r\n费用说明：\r\n集合地点：\r\n联系方式：\r\n注意事项：', '5');
INSERT INTO `uchome_eventclass` VALUES ('6', '其它', '0', '', '6');

-- ----------------------------
-- Table structure for `uchome_eventfield`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_eventfield`;
CREATE TABLE `uchome_eventfield` (
  `eventid` mediumint(8) unsigned NOT NULL auto_increment,
  `detail` text NOT NULL,
  `template` varchar(255) NOT NULL default '',
  `limitnum` mediumint(8) unsigned NOT NULL default '0',
  `verify` tinyint(1) NOT NULL default '0',
  `allowpic` tinyint(1) NOT NULL default '0',
  `allowpost` tinyint(1) NOT NULL default '0',
  `allowinvite` tinyint(1) NOT NULL default '0',
  `allowfellow` tinyint(1) NOT NULL default '0',
  `hotuser` text NOT NULL,
  PRIMARY KEY  (`eventid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_eventfield
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_eventinvite`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_eventinvite`;
CREATE TABLE `uchome_eventinvite` (
  `eventid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `touid` mediumint(8) unsigned NOT NULL default '0',
  `tousername` char(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`eventid`,`touid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_eventinvite
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_eventpic`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_eventpic`;
CREATE TABLE `uchome_eventpic` (
  `picid` mediumint(8) unsigned NOT NULL default '0',
  `eventid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`picid`),
  KEY `eventid` (`eventid`,`picid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_eventpic
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_feed`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_feed`;
CREATE TABLE `uchome_feed` (
  `feedid` int(10) unsigned NOT NULL auto_increment,
  `appid` smallint(6) unsigned NOT NULL default '0',
  `icon` varchar(30) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `friend` tinyint(1) NOT NULL default '0',
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
  `target_ids` text NOT NULL,
  `id` mediumint(8) unsigned NOT NULL default '0',
  `idtype` varchar(15) NOT NULL default '',
  `hot` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`feedid`),
  KEY `uid` (`uid`,`dateline`),
  KEY `dateline` (`dateline`),
  KEY `hot` (`hot`),
  KEY `id` (`id`,`idtype`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_feed
-- ----------------------------
INSERT INTO `uchome_feed` VALUES ('22', '1', 'profile', '4', 'admin', '1343725030', '0', '3a7101a64ea7927f0b3f5179b7a457b3', 'ec7d775d9211880bca2ba1d401e3bcb9', '{actor} 开通了自己的个人主页', 'a:0:{}', '', 'a:0:{}', '', '', '', '', '', '', '', '', '', '', '0', '', '0');

-- ----------------------------
-- Table structure for `uchome_friend`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_friend`;
CREATE TABLE `uchome_friend` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `fuid` mediumint(8) unsigned NOT NULL default '0',
  `fusername` varchar(15) NOT NULL default '',
  `status` tinyint(1) NOT NULL default '0',
  `gid` smallint(6) unsigned NOT NULL default '0',
  `note` varchar(50) NOT NULL default '',
  `num` mediumint(8) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`,`fuid`),
  KEY `fuid` (`fuid`),
  KEY `status` (`uid`,`status`,`num`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_friend
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_friendguide`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_friendguide`;
CREATE TABLE `uchome_friendguide` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `fuid` mediumint(8) unsigned NOT NULL default '0',
  `fusername` char(15) NOT NULL default '',
  `num` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`,`fuid`),
  KEY `uid` (`uid`,`num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_friendguide
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_friendlog`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_friendlog`;
CREATE TABLE `uchome_friendlog` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `fuid` mediumint(8) unsigned NOT NULL default '0',
  `action` varchar(10) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`,`fuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_friendlog
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_invite`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_invite`;
CREATE TABLE `uchome_invite` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `code` varchar(20) NOT NULL default '',
  `fuid` mediumint(8) unsigned NOT NULL default '0',
  `fusername` varchar(15) NOT NULL default '',
  `type` tinyint(1) NOT NULL default '0',
  `email` varchar(100) NOT NULL default '',
  `appid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_invite
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_log`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_log`;
CREATE TABLE `uchome_log` (
  `logid` mediumint(8) unsigned NOT NULL auto_increment,
  `id` mediumint(8) unsigned NOT NULL default '0',
  `idtype` char(20) NOT NULL default '',
  PRIMARY KEY  (`logid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_log
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_magic`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_magic`;
CREATE TABLE `uchome_magic` (
  `mid` varchar(15) NOT NULL default '',
  `name` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `forbiddengid` text NOT NULL,
  `charge` smallint(6) unsigned NOT NULL default '0',
  `experience` smallint(6) unsigned NOT NULL default '0',
  `provideperoid` int(10) unsigned NOT NULL default '0',
  `providecount` smallint(6) unsigned NOT NULL default '0',
  `useperoid` int(10) unsigned NOT NULL default '0',
  `usecount` smallint(6) unsigned NOT NULL default '0',
  `displayorder` smallint(6) unsigned NOT NULL default '0',
  `custom` text NOT NULL,
  `close` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_magic
-- ----------------------------
INSERT INTO `uchome_magic` VALUES ('invisible', '隐身草', '让自己隐身登录，不显示在线，24小时内有效', '', '50', '5', '86400', '10', '86400', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('friendnum', '好友增容卡', '在允许添加的最多好友数限制外，增加10个好友名额', '', '30', '3', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('attachsize', '附件增容卡', '使用一次，可以给自己增加 10M 的附件空间', '', '30', '3', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('thunder', '雷鸣之声', '发布一条全站信息，让大家知道自己上线了', '', '500', '5', '86400', '5', '86400', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('updateline', '救生圈', '把指定对象的发布时间更新为当前时间', '', '200', '5', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('downdateline', '时空机', '把指定对象的发布时间修改为过去的时间', '', '250', '5', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('color', '彩色灯', '把指定对象的标题变成彩色的', '', '50', '5', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('hot', '热点灯', '把指定对象的热度增加站点推荐的热点值', '', '50', '5', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('visit', '互访卡', '随机选择10个好友，向其打招呼、留言或访问空间', '', '20', '2', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('icon', '彩虹蛋', '给指定对象的标题前面增加图标（最多8个图标）', '', '20', '2', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('flicker', '彩虹炫', '让评论、留言的文字闪烁起来', '', '30', '3', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('gift', '红包卡', '在自己的空间埋下积分红包送给来访者', '', '20', '2', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('superstar', '超级明星', '在个人主页，给自己的头像增加超级明星标识', '', '30', '3', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('viewmagiclog', '八卦镜', '查看指定用户最近使用的道具记录', '', '100', '5', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('viewmagic', '透视镜', '查看指定用户当前持有的道具', '', '100', '5', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('viewvisitor', '偷窥镜', '查看指定用户最近访问过的10个空间', '', '100', '5', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('call', '点名卡', '发通知给自己的好友，让他们来查看指定的对象', '', '50', '5', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('coupon', '代金券', '购买道具时折换一定量的积分', '', '0', '0', '0', '0', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('frame', '相框', '给自己的照片添上相框', '', '30', '3', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('bgimage', '信纸', '给指定的对象添加信纸背景', '', '30', '3', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('doodle', '涂鸦板', '允许在留言、评论等操作时使用涂鸦板', '', '30', '3', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('anonymous', '匿名卡', '在指定的地方，让自己的名字显示为匿名', '', '50', '5', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('reveal', '照妖镜', '可以查看一次匿名用户的真实身份', '', '100', '5', '86400', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('license', '道具转让许可证', '使用许可证，将道具赠送给指定好友', '', '10', '1', '3600', '999', '0', '1', '0', '', '0');
INSERT INTO `uchome_magic` VALUES ('detector', '探测器', '探测埋了红包的空间', '', '10', '1', '86400', '999', '0', '1', '0', '', '0');

-- ----------------------------
-- Table structure for `uchome_magicinlog`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_magicinlog`;
CREATE TABLE `uchome_magicinlog` (
  `logid` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `mid` varchar(15) NOT NULL default '',
  `count` smallint(6) unsigned NOT NULL default '0',
  `type` tinyint(3) unsigned NOT NULL default '0',
  `fromid` mediumint(8) unsigned NOT NULL default '0',
  `credit` smallint(6) unsigned NOT NULL default '0',
  `dateline` int(10) NOT NULL default '0',
  PRIMARY KEY  (`logid`),
  KEY `uid` (`uid`,`dateline`),
  KEY `type` (`type`,`fromid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_magicinlog
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_magicstore`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_magicstore`;
CREATE TABLE `uchome_magicstore` (
  `mid` varchar(15) NOT NULL default '',
  `storage` smallint(6) unsigned NOT NULL default '0',
  `lastprovide` int(10) unsigned NOT NULL default '0',
  `sellcount` int(8) unsigned NOT NULL default '0',
  `sellcredit` int(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_magicstore
-- ----------------------------
INSERT INTO `uchome_magicstore` VALUES ('updateline', '999', '1342874290', '0', '0');
INSERT INTO `uchome_magicstore` VALUES ('flicker', '999', '1343719236', '0', '0');

-- ----------------------------
-- Table structure for `uchome_magicuselog`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_magicuselog`;
CREATE TABLE `uchome_magicuselog` (
  `logid` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `mid` varchar(15) NOT NULL default '',
  `id` mediumint(8) unsigned NOT NULL default '0',
  `idtype` varchar(20) NOT NULL default '',
  `count` mediumint(8) unsigned NOT NULL default '0',
  `data` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  `expire` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`logid`),
  KEY `uid` (`uid`,`mid`),
  KEY `id` (`id`,`idtype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_magicuselog
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_mailcron`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_mailcron`;
CREATE TABLE `uchome_mailcron` (
  `cid` mediumint(8) unsigned NOT NULL auto_increment,
  `touid` mediumint(8) unsigned NOT NULL default '0',
  `email` varchar(100) NOT NULL default '',
  `sendtime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `sendtime` (`sendtime`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_mailcron
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_mailqueue`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_mailqueue`;
CREATE TABLE `uchome_mailqueue` (
  `qid` mediumint(8) unsigned NOT NULL auto_increment,
  `cid` mediumint(8) unsigned NOT NULL default '0',
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`qid`),
  KEY `mcid` (`cid`,`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_mailqueue
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_member`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_member`;
CREATE TABLE `uchome_member` (
  `uid` mediumint(8) unsigned NOT NULL auto_increment,
  `username` char(15) NOT NULL default '',
  `password` char(32) NOT NULL default '',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_member
-- ----------------------------
INSERT INTO `uchome_member` VALUES ('4', 'admin', 'b3b42d591a0791c4a4cf2fd7e310c8f5');

-- ----------------------------
-- Table structure for `uchome_mtag`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_mtag`;
CREATE TABLE `uchome_mtag` (
  `tagid` mediumint(8) unsigned NOT NULL auto_increment,
  `tagname` varchar(40) NOT NULL default '',
  `fieldid` smallint(6) NOT NULL default '0',
  `membernum` mediumint(8) unsigned NOT NULL default '0',
  `threadnum` mediumint(8) unsigned NOT NULL default '0',
  `postnum` mediumint(8) unsigned NOT NULL default '0',
  `close` tinyint(1) NOT NULL default '0',
  `announcement` text NOT NULL,
  `pic` varchar(150) NOT NULL default '',
  `closeapply` tinyint(1) NOT NULL default '0',
  `joinperm` tinyint(1) NOT NULL default '0',
  `viewperm` tinyint(1) NOT NULL default '0',
  `threadperm` tinyint(1) NOT NULL default '0',
  `postperm` tinyint(1) NOT NULL default '0',
  `recommend` tinyint(1) NOT NULL default '0',
  `moderator` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`tagid`),
  KEY `tagname` (`tagname`),
  KEY `threadnum` (`threadnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_mtag
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_mtaginvite`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_mtaginvite`;
CREATE TABLE `uchome_mtaginvite` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `tagid` mediumint(8) unsigned NOT NULL default '0',
  `fromuid` mediumint(8) unsigned NOT NULL default '0',
  `fromusername` char(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`,`tagid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_mtaginvite
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_myapp`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_myapp`;
CREATE TABLE `uchome_myapp` (
  `appid` mediumint(8) unsigned NOT NULL default '0',
  `appname` varchar(60) NOT NULL default '',
  `narrow` tinyint(1) NOT NULL default '0',
  `flag` tinyint(1) NOT NULL default '0',
  `version` mediumint(8) unsigned NOT NULL default '0',
  `displaymethod` tinyint(1) NOT NULL default '0',
  `displayorder` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`appid`),
  KEY `flag` (`flag`,`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_myapp
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_myinvite`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_myinvite`;
CREATE TABLE `uchome_myinvite` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `typename` varchar(100) NOT NULL default '',
  `appid` mediumint(8) NOT NULL default '0',
  `type` tinyint(1) NOT NULL default '0',
  `fromuid` mediumint(8) unsigned NOT NULL default '0',
  `touid` mediumint(8) unsigned NOT NULL default '0',
  `myml` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  `hash` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `hash` (`hash`),
  KEY `uid` (`touid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_myinvite
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_notification`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_notification`;
CREATE TABLE `uchome_notification` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `new` tinyint(1) NOT NULL default '0',
  `authorid` mediumint(8) unsigned NOT NULL default '0',
  `author` varchar(15) NOT NULL default '',
  `note` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`,`new`,`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_notification
-- ----------------------------
INSERT INTO `uchome_notification` VALUES ('1', '2', 'friend', '0', '1', 'admin', '和你成为了好友', '1343631278');
INSERT INTO `uchome_notification` VALUES ('2', '2', 'quizinvite', '0', '1', 'admin', '邀请你一起参与 <a href=\"space.php?uid=1&do=quiz&quizid=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜', '1343631469');
INSERT INTO `uchome_notification` VALUES ('3', '1', 'quizinvite', '0', '2', 'summit', '邀请你一起参与 <a href=\"space.php?uid=2\" target=\"_blank\">《summit》</a>的space.php?uid=1&do=quiz&id=13竞猜', '1343633618');
INSERT INTO `uchome_notification` VALUES ('4', '2', 'quizinvite', '0', '1', 'admin', '你的好友<a href=\"admin\" target=\"_blank\">space.php?uid=1参与了 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜', '1343633785');
INSERT INTO `uchome_notification` VALUES ('5', '1', 'quizinvite', '0', '2', 'summit', '我参与了 <a href=\"悬赏\" target=\"_blank\">《4》</a>的5竞猜，你也快来吧', '1343634334');
INSERT INTO `uchome_notification` VALUES ('6', '1', 'quizinvite', '0', '2', 'summit', '我参与了 <a href=\"悬赏\" target=\"_blank\">《4》</a>的5竞猜，你也快来吧', '1343634338');
INSERT INTO `uchome_notification` VALUES ('7', '2', 'quizinvite', '0', '1', 'admin', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343634559');
INSERT INTO `uchome_notification` VALUES ('8', '2', 'quizinvite', '0', '1', 'admin', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343634568');
INSERT INTO `uchome_notification` VALUES ('9', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=&do=quiz&id=13\" target=\"_blank\">《》</a>的竞猜中得胜，获得积分4', '1343635894');
INSERT INTO `uchome_notification` VALUES ('10', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=&do=quiz&id=13\" target=\"_blank\">《》</a>的竞猜中得胜，获得积分4', '1343635919');
INSERT INTO `uchome_notification` VALUES ('11', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343636342');
INSERT INTO `uchome_notification` VALUES ('12', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343636356');
INSERT INTO `uchome_notification` VALUES ('13', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343636360');
INSERT INTO `uchome_notification` VALUES ('14', '2', 'quizjoin', '0', '1', 'admin', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343636387');
INSERT INTO `uchome_notification` VALUES ('15', '2', 'quizjoin', '0', '1', 'admin', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343636390');
INSERT INTO `uchome_notification` VALUES ('16', '2', 'quizjoin', '0', '1', 'admin', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343636393');
INSERT INTO `uchome_notification` VALUES ('17', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=&do=quiz&id=13\" target=\"_blank\">《》</a>的竞猜中得胜，获得积分4', '1343636457');
INSERT INTO `uchome_notification` VALUES ('18', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=&do=quiz&id=13\" target=\"_blank\">《》</a>的竞猜中得胜，获得积分4', '1343636457');
INSERT INTO `uchome_notification` VALUES ('19', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=&do=quiz&id=13\" target=\"_blank\">《》</a>的竞猜中得胜，获得积分4', '1343636482');
INSERT INTO `uchome_notification` VALUES ('20', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=&do=quiz&id=13\" target=\"_blank\">《》</a>的竞猜中得胜，获得积分4', '1343636482');
INSERT INTO `uchome_notification` VALUES ('21', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分4', '1343636636');
INSERT INTO `uchome_notification` VALUES ('22', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分4', '1343636636');
INSERT INTO `uchome_notification` VALUES ('23', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分4', '1343636675');
INSERT INTO `uchome_notification` VALUES ('24', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分4', '1343636675');
INSERT INTO `uchome_notification` VALUES ('25', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分4。', '1343636735');
INSERT INTO `uchome_notification` VALUES ('26', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分4。', '1343636735');
INSERT INTO `uchome_notification` VALUES ('27', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分900。', '1343636789');
INSERT INTO `uchome_notification` VALUES ('28', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分300。', '1343636789');
INSERT INTO `uchome_notification` VALUES ('29', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分900。', '1343636967');
INSERT INTO `uchome_notification` VALUES ('30', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分300。', '1343636967');
INSERT INTO `uchome_notification` VALUES ('31', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分900。', '1343637004');
INSERT INTO `uchome_notification` VALUES ('32', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分300。', '1343637004');
INSERT INTO `uchome_notification` VALUES ('33', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分900。', '1343637016');
INSERT INTO `uchome_notification` VALUES ('34', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分300。', '1343637016');
INSERT INTO `uchome_notification` VALUES ('35', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分900。', '1343637049');
INSERT INTO `uchome_notification` VALUES ('36', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分300。', '1343637049');
INSERT INTO `uchome_notification` VALUES ('37', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分900。', '1343637102');
INSERT INTO `uchome_notification` VALUES ('38', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分300。', '1343637102');
INSERT INTO `uchome_notification` VALUES ('39', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分900。', '1343637282');
INSERT INTO `uchome_notification` VALUES ('40', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分300。', '1343637282');
INSERT INTO `uchome_notification` VALUES ('41', '1', 'quizlost', '0', '1', 'admin', '很不幸你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中没有获胜，输了积分600，请继续努力o~下次得奖的就是你！。', '1343637282');
INSERT INTO `uchome_notification` VALUES ('42', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分300。', '1343637490');
INSERT INTO `uchome_notification` VALUES ('43', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分900。', '1343637490');
INSERT INTO `uchome_notification` VALUES ('44', '1', 'quizlost', '0', '1', 'admin', '很不幸你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中没有获胜，输了积分200，请继续努力o~下次得奖的就是你！。', '1343637490');
INSERT INTO `uchome_notification` VALUES ('45', '1', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分300。', '1343637775');
INSERT INTO `uchome_notification` VALUES ('46', '2', 'quizwin', '0', '1', 'admin', '恭喜你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中得胜，获得积分900。', '1343637775');
INSERT INTO `uchome_notification` VALUES ('47', '1', 'quizlost', '0', '1', 'admin', '很不幸你在 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜中没有获胜，输了积分200，请继续努力o~下次得奖的就是你！。', '1343637775');
INSERT INTO `uchome_notification` VALUES ('48', '1', 'quizinvite', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=2&do=quiz&id=14\" target=\"_blank\">《中国奥运能有多少金牌》</a>的悬赏竞猜，你也快来吧', '1343643809');
INSERT INTO `uchome_notification` VALUES ('49', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=2&do=quiz&id=14\" target=\"_blank\">《中国奥运能有多少金牌》</a>的悬赏竞猜，你也快来吧', '1343643918');
INSERT INTO `uchome_notification` VALUES ('50', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=2&do=quiz&id=14\" target=\"_blank\">《中国奥运能有多少金牌》</a>的悬赏竞猜，你也快来吧', '1343643949');
INSERT INTO `uchome_notification` VALUES ('51', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=2&do=quiz&id=14\" target=\"_blank\">《中国奥运能有多少金牌》</a>的悬赏竞猜，你也快来吧', '1343643952');
INSERT INTO `uchome_notification` VALUES ('52', '2', 'quizwin', '0', '2', 'summit', '恭喜你在 <a href=\"space.php?uid=2&do=quiz&id=14\" target=\"_blank\">《中国奥运能有多少金牌》</a>的悬赏竞猜中得胜，获得积分300。', '1343643977');
INSERT INTO `uchome_notification` VALUES ('53', '2', 'quizlost', '0', '2', 'summit', '很不幸你在 <a href=\"space.php?uid=2&do=quiz&id=14\" target=\"_blank\">《中国奥运能有多少金牌》</a>的悬赏竞猜中没有获胜，输了积分100，请继续努力o~下次得奖的就是你！。', '1343643977');
INSERT INTO `uchome_notification` VALUES ('54', '1', 'quizinvite', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=2&do=quiz&id=15\" target=\"_blank\">《测试优惠券》</a>的悬赏竞猜，你也快来吧', '1343644320');
INSERT INTO `uchome_notification` VALUES ('55', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=2&do=quiz&id=15\" target=\"_blank\">《测试优惠券》</a>的悬赏竞猜，你也快来吧', '1343644324');
INSERT INTO `uchome_notification` VALUES ('56', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=2&do=quiz&id=15\" target=\"_blank\">《测试优惠券》</a>的悬赏竞猜，你也快来吧', '1343644327');
INSERT INTO `uchome_notification` VALUES ('57', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=2&do=quiz&id=15\" target=\"_blank\">《测试优惠券》</a>的悬赏竞猜，你也快来吧', '1343644331');
INSERT INTO `uchome_notification` VALUES ('58', '2', 'quizwin', '0', '2', 'summit', '恭喜你在 <a href=\"space.php?uid=2&do=quiz&id=15\" target=\"_blank\">《测试优惠券》</a>的悬赏竞猜中得胜，获得积分300。', '1343644339');
INSERT INTO `uchome_notification` VALUES ('59', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=12\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343701178');
INSERT INTO `uchome_notification` VALUES ('60', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=12\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343701182');
INSERT INTO `uchome_notification` VALUES ('61', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=12\" target=\"_blank\">《男子团体体操》</a>的悬赏竞猜，你也快来吧', '1343701184');
INSERT INTO `uchome_notification` VALUES ('62', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=9\" target=\"_blank\">《今天吃什么》</a>的悬赏竞猜，你也快来吧', '1343701199');
INSERT INTO `uchome_notification` VALUES ('63', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=1&do=quiz&id=11\" target=\"_blank\">《竞猜11》</a>的悬赏竞猜，你也快来吧', '1343701473');
INSERT INTO `uchome_notification` VALUES ('64', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=3\" target=\"_blank\">男子团体体操</a>', '1343707719');
INSERT INTO `uchome_notification` VALUES ('65', '1', 'sharenotice', '0', '2', 'summit', '分享了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13\" target=\"_blank\">男子团体体操</a>', '1343718233');
INSERT INTO `uchome_notification` VALUES ('66', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=4\" target=\"_blank\">男子团体体操</a>', '1343719133');
INSERT INTO `uchome_notification` VALUES ('67', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=5\" target=\"_blank\">男子团体体操</a>', '1343719136');
INSERT INTO `uchome_notification` VALUES ('68', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=6\" target=\"_blank\">男子团体体操</a>', '1343719139');
INSERT INTO `uchome_notification` VALUES ('69', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=7\" target=\"_blank\">男子团体体操</a>', '1343719142');
INSERT INTO `uchome_notification` VALUES ('70', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=8\" target=\"_blank\">男子团体体操</a>', '1343719147');
INSERT INTO `uchome_notification` VALUES ('71', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=9\" target=\"_blank\">男子团体体操</a>', '1343719150');
INSERT INTO `uchome_notification` VALUES ('72', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=10\" target=\"_blank\">男子团体体操</a>', '1343719155');
INSERT INTO `uchome_notification` VALUES ('73', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=11\" target=\"_blank\">男子团体体操</a>', '1343719159');
INSERT INTO `uchome_notification` VALUES ('74', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=12\" target=\"_blank\">男子团体体操</a>', '1343719162');
INSERT INTO `uchome_notification` VALUES ('75', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=13\" target=\"_blank\">男子团体体操</a>', '1343719169');
INSERT INTO `uchome_notification` VALUES ('76', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=14\" target=\"_blank\">男子团体体操</a>', '1343719173');
INSERT INTO `uchome_notification` VALUES ('77', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=15\" target=\"_blank\">男子团体体操</a>', '1343719177');
INSERT INTO `uchome_notification` VALUES ('78', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=16\" target=\"_blank\">男子团体体操</a>', '1343719181');
INSERT INTO `uchome_notification` VALUES ('79', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=17\" target=\"_blank\">男子团体体操</a>', '1343719185');
INSERT INTO `uchome_notification` VALUES ('80', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=18\" target=\"_blank\">男子团体体操</a>', '1343719189');
INSERT INTO `uchome_notification` VALUES ('81', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=19\" target=\"_blank\">男子团体体操</a>', '1343719197');
INSERT INTO `uchome_notification` VALUES ('82', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=20\" target=\"_blank\">男子团体体操</a>', '1343719199');
INSERT INTO `uchome_notification` VALUES ('83', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=21\" target=\"_blank\">男子团体体操</a>', '1343719201');
INSERT INTO `uchome_notification` VALUES ('84', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=22\" target=\"_blank\">男子团体体操</a>', '1343719203');
INSERT INTO `uchome_notification` VALUES ('85', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=23\" target=\"_blank\">男子团体体操</a>', '1343719205');
INSERT INTO `uchome_notification` VALUES ('86', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=24\" target=\"_blank\">男子团体体操</a>', '1343719207');
INSERT INTO `uchome_notification` VALUES ('87', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=25\" target=\"_blank\">男子团体体操</a>', '1343719210');
INSERT INTO `uchome_notification` VALUES ('88', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=26\" target=\"_blank\">男子团体体操</a>', '1343719211');
INSERT INTO `uchome_notification` VALUES ('89', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=27\" target=\"_blank\">男子团体体操</a>', '1343719215');
INSERT INTO `uchome_notification` VALUES ('90', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=28\" target=\"_blank\">男子团体体操</a>', '1343719216');
INSERT INTO `uchome_notification` VALUES ('91', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=29\" target=\"_blank\">男子团体体操</a>', '1343719230');
INSERT INTO `uchome_notification` VALUES ('92', '1', 'quizcomment', '0', '2', 'summit', '评论了你的竞猜 <a href=\"space.php?uid=1&do=quiz&id=13&cid=30\" target=\"_blank\">男子团体体操</a>', '1343719233');
INSERT INTO `uchome_notification` VALUES ('93', '1', 'quizinvite', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=2&do=quiz&id=17\" target=\"_blank\">《女子团体体操决赛》</a>的悬赏竞猜，你也快来吧', '1343721979');
INSERT INTO `uchome_notification` VALUES ('94', '1', 'quizjoin', '0', '2', 'summit', '我参与了 <a href=\"space.php?uid=2&do=quiz&id=17\" target=\"_blank\">《女子团体体操决赛》</a>的悬赏竞猜，你也快来吧', '1343722073');

-- ----------------------------
-- Table structure for `uchome_pic`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_pic`;
CREATE TABLE `uchome_pic` (
  `picid` mediumint(8) NOT NULL auto_increment,
  `albumid` mediumint(8) unsigned NOT NULL default '0',
  `topicid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `postip` varchar(20) NOT NULL default '',
  `filename` varchar(100) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `type` varchar(20) NOT NULL default '',
  `size` int(10) unsigned NOT NULL default '0',
  `filepath` varchar(60) NOT NULL default '',
  `thumb` tinyint(1) NOT NULL default '0',
  `remote` tinyint(1) NOT NULL default '0',
  `hot` mediumint(8) unsigned NOT NULL default '0',
  `click_6` smallint(6) unsigned NOT NULL default '0',
  `click_7` smallint(6) unsigned NOT NULL default '0',
  `click_8` smallint(6) unsigned NOT NULL default '0',
  `click_9` smallint(6) unsigned NOT NULL default '0',
  `click_10` smallint(6) unsigned NOT NULL default '0',
  `magicframe` tinyint(6) NOT NULL default '0',
  PRIMARY KEY  (`picid`),
  KEY `albumid` (`albumid`,`dateline`),
  KEY `topicid` (`topicid`,`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_pic
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_picfield`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_picfield`;
CREATE TABLE `uchome_picfield` (
  `picid` mediumint(8) unsigned NOT NULL default '0',
  `hotuser` text NOT NULL,
  PRIMARY KEY  (`picid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_picfield
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_poke`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_poke`;
CREATE TABLE `uchome_poke` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `fromuid` mediumint(8) unsigned NOT NULL default '0',
  `fromusername` varchar(15) NOT NULL default '',
  `note` varchar(255) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `iconid` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`,`fromuid`),
  KEY `uid` (`uid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_poke
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_poll`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_poll`;
CREATE TABLE `uchome_poll` (
  `pid` mediumint(8) unsigned NOT NULL auto_increment,
  `topicid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `subject` char(80) NOT NULL default '',
  `voternum` mediumint(8) unsigned NOT NULL default '0',
  `replynum` mediumint(8) unsigned NOT NULL default '0',
  `multiple` tinyint(1) NOT NULL default '0',
  `maxchoice` tinyint(3) NOT NULL default '0',
  `sex` tinyint(1) NOT NULL default '0',
  `noreply` tinyint(1) NOT NULL default '0',
  `credit` mediumint(8) unsigned NOT NULL default '0',
  `percredit` mediumint(8) unsigned NOT NULL default '0',
  `expiration` int(10) unsigned NOT NULL default '0',
  `lastvote` int(10) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `hot` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pid`),
  KEY `uid` (`uid`,`dateline`),
  KEY `topicid` (`topicid`,`dateline`),
  KEY `voternum` (`voternum`),
  KEY `dateline` (`dateline`),
  KEY `lastvote` (`lastvote`),
  KEY `hot` (`hot`),
  KEY `percredit` (`percredit`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_poll
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_pollfield`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_pollfield`;
CREATE TABLE `uchome_pollfield` (
  `pid` mediumint(8) unsigned NOT NULL default '0',
  `notify` tinyint(1) NOT NULL default '0',
  `message` text NOT NULL,
  `summary` text NOT NULL,
  `option` text NOT NULL,
  `invite` text NOT NULL,
  `hotuser` text NOT NULL,
  PRIMARY KEY  (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_pollfield
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_polloption`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_polloption`;
CREATE TABLE `uchome_polloption` (
  `oid` mediumint(8) unsigned NOT NULL auto_increment,
  `pid` mediumint(8) unsigned NOT NULL default '0',
  `votenum` mediumint(8) unsigned NOT NULL default '0',
  `option` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`oid`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_polloption
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_polluser`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_polluser`;
CREATE TABLE `uchome_polluser` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `pid` mediumint(8) unsigned NOT NULL default '0',
  `option` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`,`pid`),
  KEY `pid` (`pid`,`dateline`),
  KEY `uid` (`uid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_polluser
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_post`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_post`;
CREATE TABLE `uchome_post` (
  `pid` int(10) unsigned NOT NULL auto_increment,
  `tagid` mediumint(8) unsigned NOT NULL default '0',
  `tid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `ip` varchar(20) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `pic` varchar(255) NOT NULL default '',
  `isthread` tinyint(1) NOT NULL default '0',
  `hotuser` text NOT NULL,
  PRIMARY KEY  (`pid`),
  KEY `tid` (`tid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_post
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_profield`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_profield`;
CREATE TABLE `uchome_profield` (
  `fieldid` smallint(6) unsigned NOT NULL auto_increment,
  `title` varchar(80) NOT NULL default '',
  `note` varchar(255) NOT NULL default '',
  `formtype` varchar(20) NOT NULL default '0',
  `inputnum` smallint(3) unsigned NOT NULL default '0',
  `choice` text NOT NULL,
  `mtagminnum` smallint(6) unsigned NOT NULL default '0',
  `manualmoderator` tinyint(1) NOT NULL default '0',
  `manualmember` tinyint(1) NOT NULL default '0',
  `displayorder` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_profield
-- ----------------------------
INSERT INTO `uchome_profield` VALUES ('1', '自由联盟', '', 'text', '100', '', '0', '0', '1', '0');
INSERT INTO `uchome_profield` VALUES ('2', '地区联盟', '', 'text', '100', '', '0', '0', '1', '0');
INSERT INTO `uchome_profield` VALUES ('3', '兴趣联盟', '', 'text', '100', '', '0', '0', '1', '0');

-- ----------------------------
-- Table structure for `uchome_profilefield`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_profilefield`;
CREATE TABLE `uchome_profilefield` (
  `fieldid` smallint(6) unsigned NOT NULL auto_increment,
  `title` varchar(80) NOT NULL default '',
  `note` varchar(255) NOT NULL default '',
  `formtype` varchar(20) NOT NULL default '0',
  `maxsize` tinyint(3) unsigned NOT NULL default '0',
  `required` tinyint(1) NOT NULL default '0',
  `invisible` tinyint(1) NOT NULL default '0',
  `allowsearch` tinyint(1) NOT NULL default '0',
  `choice` text NOT NULL,
  `displayorder` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_profilefield
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_quiz`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_quiz`;
CREATE TABLE `uchome_quiz` (
  `quizid` mediumint(8) unsigned NOT NULL auto_increment,
  `topicid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `subject` char(80) NOT NULL default '',
  `classid` smallint(6) unsigned NOT NULL default '0',
  `viewnum` mediumint(8) unsigned NOT NULL default '0',
  `replynum` mediumint(8) unsigned NOT NULL default '0',
  `hot` mediumint(8) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `pic` char(120) NOT NULL default '',
  `picflag` tinyint(1) NOT NULL default '0',
  `noreply` tinyint(1) NOT NULL default '0',
  `friend` tinyint(1) NOT NULL default '0',
  `password` char(10) NOT NULL default '',
  `click_1` smallint(6) unsigned NOT NULL default '0',
  `click_2` smallint(6) unsigned NOT NULL default '0',
  `click_3` smallint(6) unsigned NOT NULL default '0',
  `click_4` smallint(6) unsigned NOT NULL default '0',
  `click_5` smallint(6) unsigned NOT NULL default '0',
  `joincost` mediumint(8) unsigned NOT NULL,
  `portion` mediumint(8) unsigned NOT NULL,
  `endtime` int(10) unsigned NOT NULL,
  `resulttime` int(10) NOT NULL,
  `lastvote` int(10) unsigned NOT NULL default '0',
  `voternum` mediumint(8) unsigned NOT NULL default '0',
  `maxchoice` tinyint(3) NOT NULL default '0',
  `sex` tinyint(1) NOT NULL default '0',
  `keyoid` int(16) unsigned NOT NULL,
  `keyoption` char(80) NOT NULL default '',
  `totalcost` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`quizid`),
  KEY `uid` (`uid`,`dateline`),
  KEY `topicid` (`topicid`,`dateline`),
  KEY `dateline` (`dateline`),
  KEY `voternum` (`voternum`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_quiz
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_quizfield`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_quizfield`;
CREATE TABLE `uchome_quizfield` (
  `quizid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `tag` varchar(255) NOT NULL default '',
  `message` mediumtext NOT NULL,
  `postip` varchar(20) NOT NULL default '',
  `related` text NOT NULL,
  `relatedtime` int(10) unsigned NOT NULL default '0',
  `target_ids` text NOT NULL,
  `hotuser` text NOT NULL,
  `magiccolor` tinyint(6) NOT NULL default '0',
  `magicpaper` tinyint(6) NOT NULL default '0',
  `magiccall` tinyint(1) NOT NULL default '0',
  `option` text NOT NULL,
  PRIMARY KEY  (`quizid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_quizfield
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_quizoptions`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_quizoptions`;
CREATE TABLE `uchome_quizoptions` (
  `oid` int(16) unsigned NOT NULL auto_increment,
  `quizid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `option` char(80) NOT NULL default '',
  `relatedtime` int(10) unsigned NOT NULL default '0',
  `picid` mediumint(8) NOT NULL,
  `votenum` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`oid`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_quizoptions
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_quizuser`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_quizuser`;
CREATE TABLE `uchome_quizuser` (
  `jid` int(10) NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `quizid` mediumint(8) unsigned NOT NULL default '0',
  `option` text NOT NULL,
  `dateline` int(10) unsigned NOT NULL default '0',
  `oid` int(16) unsigned NOT NULL,
  PRIMARY KEY  (`jid`,`uid`,`quizid`),
  KEY `pid` (`quizid`,`dateline`),
  KEY `uid` (`uid`,`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_quizuser
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_report`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_report`;
CREATE TABLE `uchome_report` (
  `rid` mediumint(8) unsigned NOT NULL auto_increment,
  `id` mediumint(8) unsigned NOT NULL default '0',
  `idtype` varchar(15) NOT NULL default '',
  `new` tinyint(1) NOT NULL default '0',
  `num` smallint(6) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `reason` text NOT NULL,
  `uids` text NOT NULL,
  PRIMARY KEY  (`rid`),
  KEY `id` (`id`,`idtype`,`num`,`dateline`),
  KEY `new` (`new`,`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_report
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_session`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_session`;
CREATE TABLE `uchome_session` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `password` char(32) NOT NULL default '',
  `lastactivity` int(10) unsigned NOT NULL default '0',
  `ip` int(10) unsigned NOT NULL default '0',
  `magichidden` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  KEY `lastactivity` (`lastactivity`),
  KEY `ip` (`ip`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_session
-- ----------------------------
INSERT INTO `uchome_session` VALUES ('1', 'admin', '219b0e35c4a3efe4ad4e9ce07feff009', '1343725021', '127000000', '0');
INSERT INTO `uchome_session` VALUES ('4', 'admin', 'b3b42d591a0791c4a4cf2fd7e310c8f5', '1343725341', '127000000', '0');

-- ----------------------------
-- Table structure for `uchome_share`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_share`;
CREATE TABLE `uchome_share` (
  `sid` mediumint(8) unsigned NOT NULL auto_increment,
  `topicid` mediumint(8) unsigned NOT NULL default '0',
  `type` varchar(30) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `title_template` text NOT NULL,
  `body_template` text NOT NULL,
  `body_data` text NOT NULL,
  `body_general` text NOT NULL,
  `image` varchar(255) NOT NULL default '',
  `image_link` varchar(255) NOT NULL default '',
  `hot` mediumint(8) unsigned NOT NULL default '0',
  `hotuser` text NOT NULL,
  PRIMARY KEY  (`sid`),
  KEY `uid` (`uid`,`dateline`),
  KEY `topicid` (`topicid`,`dateline`),
  KEY `hot` (`hot`),
  KEY `dateline` (`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_share
-- ----------------------------
INSERT INTO `uchome_share` VALUES ('1', '0', 'quiz', '2', 'summit', '1343718233', '分享了一篇竞猜', '<b>{subject}</b><br>{username}<br>{message}', 'a:3:{s:7:\"subject\";s:62:\"<a href=\"space.php?uid=1&do=quiz&id=13\">男子团体体操</a>\";s:8:\"username\";s:35:\"<a href=\"space.php?uid=1\">admin</a>\";s:7:\"message\";s:0:\"\";}', '大家赶快参加呀！！！', '', '', '0', '');

-- ----------------------------
-- Table structure for `uchome_show`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_show`;
CREATE TABLE `uchome_show` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `credit` int(10) unsigned NOT NULL default '0',
  `note` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`uid`),
  KEY `credit` (`credit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_show
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_space`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_space`;
CREATE TABLE `uchome_space` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `groupid` smallint(6) unsigned NOT NULL default '0',
  `credit` int(10) NOT NULL default '0',
  `experience` int(10) NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `name` char(20) NOT NULL default '',
  `namestatus` tinyint(1) NOT NULL default '0',
  `videostatus` tinyint(1) NOT NULL default '0',
  `domain` char(15) NOT NULL default '',
  `friendnum` int(10) unsigned NOT NULL default '0',
  `viewnum` int(10) unsigned NOT NULL default '0',
  `notenum` int(10) unsigned NOT NULL default '0',
  `addfriendnum` smallint(6) unsigned NOT NULL default '0',
  `mtaginvitenum` smallint(6) unsigned NOT NULL default '0',
  `eventinvitenum` smallint(6) unsigned NOT NULL default '0',
  `myinvitenum` smallint(6) unsigned NOT NULL default '0',
  `pokenum` smallint(6) unsigned NOT NULL default '0',
  `doingnum` smallint(6) unsigned NOT NULL default '0',
  `blognum` smallint(6) unsigned NOT NULL default '0',
  `albumnum` smallint(6) unsigned NOT NULL default '0',
  `threadnum` smallint(6) unsigned NOT NULL default '0',
  `pollnum` smallint(6) unsigned NOT NULL default '0',
  `eventnum` smallint(6) unsigned NOT NULL default '0',
  `sharenum` smallint(6) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `updatetime` int(10) unsigned NOT NULL default '0',
  `lastsearch` int(10) unsigned NOT NULL default '0',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `lastlogin` int(10) unsigned NOT NULL default '0',
  `lastsend` int(10) unsigned NOT NULL default '0',
  `attachsize` int(10) unsigned NOT NULL default '0',
  `addsize` int(10) unsigned NOT NULL default '0',
  `addfriend` smallint(6) unsigned NOT NULL default '0',
  `flag` tinyint(1) NOT NULL default '0',
  `newpm` smallint(6) unsigned NOT NULL default '0',
  `avatar` tinyint(1) NOT NULL default '0',
  `regip` char(15) NOT NULL default '',
  `ip` int(10) unsigned NOT NULL default '0',
  `mood` smallint(6) unsigned NOT NULL default '0',
  `quiznum` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  KEY `username` (`username`),
  KEY `domain` (`domain`),
  KEY `ip` (`ip`),
  KEY `updatetime` (`updatetime`),
  KEY `mood` (`mood`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_space
-- ----------------------------
INSERT INTO `uchome_space` VALUES ('4', '5', '25', '15', 'admin', '', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1343725030', '1343725370', '0', '0', '1343725299', '0', '297215', '0', '0', '0', '0', '0', '127.0.0.1', '127000000', '0', '0');

-- ----------------------------
-- Table structure for `uchome_spacefield`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_spacefield`;
CREATE TABLE `uchome_spacefield` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `sex` tinyint(1) NOT NULL default '0',
  `email` varchar(100) NOT NULL default '',
  `newemail` varchar(100) NOT NULL default '',
  `emailcheck` tinyint(1) NOT NULL default '0',
  `mobile` varchar(40) NOT NULL default '',
  `qq` varchar(20) NOT NULL default '',
  `msn` varchar(80) NOT NULL default '',
  `msnrobot` varchar(15) NOT NULL default '',
  `msncstatus` tinyint(1) NOT NULL default '0',
  `videopic` varchar(32) NOT NULL default '',
  `birthyear` smallint(6) unsigned NOT NULL default '0',
  `birthmonth` tinyint(3) unsigned NOT NULL default '0',
  `birthday` tinyint(3) unsigned NOT NULL default '0',
  `blood` varchar(5) NOT NULL default '',
  `marry` tinyint(1) NOT NULL default '0',
  `birthprovince` varchar(20) NOT NULL default '',
  `birthcity` varchar(20) NOT NULL default '',
  `resideprovince` varchar(20) NOT NULL default '',
  `residecity` varchar(20) NOT NULL default '',
  `note` text NOT NULL,
  `spacenote` text NOT NULL,
  `authstr` varchar(20) NOT NULL default '',
  `theme` varchar(20) NOT NULL default '',
  `nocss` tinyint(1) NOT NULL default '0',
  `menunum` smallint(6) unsigned NOT NULL default '0',
  `css` text NOT NULL,
  `privacy` text NOT NULL,
  `friend` mediumtext NOT NULL,
  `feedfriend` mediumtext NOT NULL,
  `sendmail` text NOT NULL,
  `magicstar` tinyint(1) NOT NULL default '0',
  `magicexpire` int(10) unsigned NOT NULL default '0',
  `timeoffset` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_spacefield
-- ----------------------------
INSERT INTO `uchome_spacefield` VALUES ('4', '0', 'admin@qq.com', '', '0', '', '', '', '', '0', '', '0', '0', '0', '', '0', '', '', '', '', '', '', '', '', '0', '0', '', '', '', '', '', '0', '0', '');

-- ----------------------------
-- Table structure for `uchome_spaceinfo`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_spaceinfo`;
CREATE TABLE `uchome_spaceinfo` (
  `infoid` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `type` varchar(20) NOT NULL default '',
  `subtype` varchar(20) NOT NULL default '',
  `title` text NOT NULL,
  `subtitle` varchar(255) NOT NULL default '',
  `friend` tinyint(1) NOT NULL default '0',
  `startyear` smallint(6) unsigned NOT NULL default '0',
  `endyear` smallint(6) unsigned NOT NULL default '0',
  `startmonth` smallint(6) unsigned NOT NULL default '0',
  `endmonth` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`infoid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_spaceinfo
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_spacelog`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_spacelog`;
CREATE TABLE `uchome_spacelog` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `opuid` mediumint(8) unsigned NOT NULL default '0',
  `opusername` char(15) NOT NULL default '',
  `flag` tinyint(1) NOT NULL default '0',
  `expiration` int(10) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`),
  KEY `flag` (`flag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_spacelog
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_stat`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_stat`;
CREATE TABLE `uchome_stat` (
  `daytime` int(10) unsigned NOT NULL default '0',
  `login` smallint(6) unsigned NOT NULL default '0',
  `register` smallint(6) unsigned NOT NULL default '0',
  `invite` smallint(6) unsigned NOT NULL default '0',
  `appinvite` smallint(6) unsigned NOT NULL default '0',
  `doing` smallint(6) unsigned NOT NULL default '0',
  `blog` smallint(6) unsigned NOT NULL default '0',
  `pic` smallint(6) unsigned NOT NULL default '0',
  `poll` smallint(6) unsigned NOT NULL default '0',
  `event` smallint(6) unsigned NOT NULL default '0',
  `share` smallint(6) unsigned NOT NULL default '0',
  `thread` smallint(6) unsigned NOT NULL default '0',
  `docomment` smallint(6) unsigned NOT NULL default '0',
  `blogcomment` smallint(6) unsigned NOT NULL default '0',
  `piccomment` smallint(6) unsigned NOT NULL default '0',
  `pollcomment` smallint(6) unsigned NOT NULL default '0',
  `pollvote` smallint(6) unsigned NOT NULL default '0',
  `eventcomment` smallint(6) unsigned NOT NULL default '0',
  `eventjoin` smallint(6) unsigned NOT NULL default '0',
  `sharecomment` smallint(6) unsigned NOT NULL default '0',
  `post` smallint(6) unsigned NOT NULL default '0',
  `wall` smallint(6) unsigned NOT NULL default '0',
  `poke` smallint(6) unsigned NOT NULL default '0',
  `click` smallint(6) unsigned NOT NULL default '0',
  `quiz` smallint(6) unsigned NOT NULL default '0',
  `quizcomment` smallint(6) unsigned NOT NULL default '0',
  `quizvote` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`daytime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_stat
-- ----------------------------
INSERT INTO `uchome_stat` VALUES ('20120731', '1', '1', '0', '0', '0', '0', '3', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');

-- ----------------------------
-- Table structure for `uchome_statuser`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_statuser`;
CREATE TABLE `uchome_statuser` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `daytime` int(10) unsigned NOT NULL default '0',
  `type` char(20) NOT NULL default '',
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_statuser
-- ----------------------------
INSERT INTO `uchome_statuser` VALUES ('4', '0', 'login');

-- ----------------------------
-- Table structure for `uchome_tag`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_tag`;
CREATE TABLE `uchome_tag` (
  `tagid` mediumint(8) unsigned NOT NULL auto_increment,
  `tagname` char(30) NOT NULL default '',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `blognum` smallint(6) unsigned NOT NULL default '0',
  `close` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`tagid`),
  KEY `tagname` (`tagname`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_tag
-- ----------------------------
INSERT INTO `uchome_tag` VALUES ('3', '的', '1', '1342872788', '1', '0');
INSERT INTO `uchome_tag` VALUES ('4', '今天', '1', '1343116598', '1', '0');
INSERT INTO `uchome_tag` VALUES ('5', '体操', '1', '1343625199', '4', '0');
INSERT INTO `uchome_tag` VALUES ('6', '男子团体', '1', '1343625199', '2', '0');
INSERT INTO `uchome_tag` VALUES ('7', '多少', '2', '1343643809', '1', '0');
INSERT INTO `uchome_tag` VALUES ('8', '金牌', '2', '1343643809', '1', '0');
INSERT INTO `uchome_tag` VALUES ('9', '中国', '2', '1343643809', '1', '0');
INSERT INTO `uchome_tag` VALUES ('10', '优惠券', '2', '1343644320', '1', '0');
INSERT INTO `uchome_tag` VALUES ('11', '测试', '2', '1343644320', '1', '0');
INSERT INTO `uchome_tag` VALUES ('12', '女子团体', '2', '1343721883', '2', '0');
INSERT INTO `uchome_tag` VALUES ('13', '决赛', '2', '1343721883', '2', '0');

-- ----------------------------
-- Table structure for `uchome_tagblog`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_tagblog`;
CREATE TABLE `uchome_tagblog` (
  `tagid` mediumint(8) unsigned NOT NULL default '0',
  `blogid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tagid`,`blogid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_tagblog
-- ----------------------------
INSERT INTO `uchome_tagblog` VALUES ('2', '1');

-- ----------------------------
-- Table structure for `uchome_tagquiz`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_tagquiz`;
CREATE TABLE `uchome_tagquiz` (
  `tagid` mediumint(8) unsigned NOT NULL default '0',
  `quizid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tagid`,`quizid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_tagquiz
-- ----------------------------
INSERT INTO `uchome_tagquiz` VALUES ('3', '8');
INSERT INTO `uchome_tagquiz` VALUES ('4', '9');
INSERT INTO `uchome_tagquiz` VALUES ('5', '12');
INSERT INTO `uchome_tagquiz` VALUES ('5', '13');
INSERT INTO `uchome_tagquiz` VALUES ('5', '16');
INSERT INTO `uchome_tagquiz` VALUES ('5', '17');
INSERT INTO `uchome_tagquiz` VALUES ('6', '12');
INSERT INTO `uchome_tagquiz` VALUES ('6', '13');
INSERT INTO `uchome_tagquiz` VALUES ('7', '14');
INSERT INTO `uchome_tagquiz` VALUES ('8', '14');
INSERT INTO `uchome_tagquiz` VALUES ('9', '14');
INSERT INTO `uchome_tagquiz` VALUES ('10', '15');
INSERT INTO `uchome_tagquiz` VALUES ('11', '15');
INSERT INTO `uchome_tagquiz` VALUES ('12', '16');
INSERT INTO `uchome_tagquiz` VALUES ('12', '17');
INSERT INTO `uchome_tagquiz` VALUES ('13', '16');
INSERT INTO `uchome_tagquiz` VALUES ('13', '17');

-- ----------------------------
-- Table structure for `uchome_tagspace`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_tagspace`;
CREATE TABLE `uchome_tagspace` (
  `tagid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `grade` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`tagid`,`uid`),
  KEY `grade` (`tagid`,`grade`),
  KEY `uid` (`uid`,`grade`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_tagspace
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_task`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_task`;
CREATE TABLE `uchome_task` (
  `taskid` smallint(6) unsigned NOT NULL auto_increment,
  `available` tinyint(1) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `note` text NOT NULL,
  `num` mediumint(8) unsigned NOT NULL default '0',
  `maxnum` mediumint(8) unsigned NOT NULL default '0',
  `image` varchar(150) NOT NULL default '',
  `filename` varchar(50) NOT NULL default '',
  `starttime` int(10) unsigned NOT NULL default '0',
  `endtime` int(10) unsigned NOT NULL default '0',
  `nexttime` int(10) unsigned NOT NULL default '0',
  `nexttype` varchar(20) NOT NULL default '',
  `credit` smallint(6) NOT NULL default '0',
  `displayorder` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`taskid`),
  KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_task
-- ----------------------------
INSERT INTO `uchome_task` VALUES ('1', '1', '更新一下自己的头像', '头像就是你在这里的个人形象。<br>设置自己的头像后，会让更多的朋友记住您。', '0', '0', 'image/task/avatar.gif', 'avatar.php', '0', '0', '0', '', '20', '1');
INSERT INTO `uchome_task` VALUES ('2', '1', '将个人资料补充完整', '把自己的个人资料填写完整吧。<br>这样您会被更多的朋友找到的，系统也会帮您找到朋友。', '0', '0', 'image/task/profile.gif', 'profile.php', '0', '0', '0', '2', '20', '0');
INSERT INTO `uchome_task` VALUES ('3', '1', '发表自己的第一篇日志', '现在，就写下自己的第一篇日志吧。<br>与大家一起分享自己的生活感悟。', '0', '0', 'image/task/blog.gif', 'blog.php', '0', '0', '0', '', '5', '3');
INSERT INTO `uchome_task` VALUES ('4', '1', '寻找并添加五位好友', '有了好友，您发的日志、图片等会被好友及时看到并传播出去；<br>您也会在首页方便及时的看到好友的最新动态。', '0', '0', 'image/task/friend.gif', 'friend.php', '0', '0', '0', '', '50', '4');
INSERT INTO `uchome_task` VALUES ('5', '1', '验证激活自己的邮箱', '填写自己真实的邮箱地址并验证通过。<br>您可以在忘记密码的时候使用该邮箱取回自己的密码；<br>还可以及时接受站内的好友通知等等。', '0', '0', 'image/task/email.gif', 'email.php', '0', '0', '0', '', '10', '5');
INSERT INTO `uchome_task` VALUES ('6', '1', '邀请10个新朋友加入', '邀请一下自己的QQ好友或者邮箱联系人，让亲朋好友一起来加入我们吧。', '0', '0', 'image/task/friend.gif', 'invite.php', '0', '0', '0', '', '100', '6');
INSERT INTO `uchome_task` VALUES ('7', '1', '领取每日访问大礼包', '每天登录访问自己的主页，就可领取大礼包。', '0', '0', 'image/task/gift.gif', 'gift.php', '0', '0', '0', 'day', '5', '99');

-- ----------------------------
-- Table structure for `uchome_thread`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_thread`;
CREATE TABLE `uchome_thread` (
  `tid` mediumint(8) unsigned NOT NULL auto_increment,
  `topicid` mediumint(8) unsigned NOT NULL default '0',
  `tagid` mediumint(8) unsigned NOT NULL default '0',
  `eventid` mediumint(8) unsigned NOT NULL default '0',
  `subject` char(80) NOT NULL default '',
  `magiccolor` tinyint(6) unsigned NOT NULL default '0',
  `magicegg` tinyint(6) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `viewnum` mediumint(8) unsigned NOT NULL default '0',
  `replynum` mediumint(8) unsigned NOT NULL default '0',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `lastauthor` char(15) NOT NULL default '',
  `lastauthorid` mediumint(8) unsigned NOT NULL default '0',
  `displayorder` tinyint(1) unsigned NOT NULL default '0',
  `digest` tinyint(1) NOT NULL default '0',
  `hot` mediumint(8) unsigned NOT NULL default '0',
  `click_11` smallint(6) unsigned NOT NULL default '0',
  `click_12` smallint(6) unsigned NOT NULL default '0',
  `click_13` smallint(6) unsigned NOT NULL default '0',
  `click_14` smallint(6) unsigned NOT NULL default '0',
  `click_15` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tid`),
  KEY `tagid` (`tagid`,`displayorder`,`lastpost`),
  KEY `uid` (`uid`,`lastpost`),
  KEY `lastpost` (`lastpost`),
  KEY `topicid` (`topicid`,`dateline`),
  KEY `eventid` (`eventid`,`lastpost`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_thread
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_topic`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_topic`;
CREATE TABLE `uchome_topic` (
  `topicid` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `subject` varchar(80) NOT NULL default '',
  `message` mediumtext NOT NULL,
  `jointype` varchar(255) NOT NULL default '',
  `joingid` varchar(255) NOT NULL default '',
  `pic` varchar(100) NOT NULL default '',
  `thumb` tinyint(1) NOT NULL default '0',
  `remote` tinyint(1) NOT NULL default '0',
  `joinnum` mediumint(8) unsigned NOT NULL default '0',
  `lastpost` int(10) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `endtime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`topicid`),
  KEY `lastpost` (`lastpost`),
  KEY `joinnum` (`joinnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_topic
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_topicuser`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_topicuser`;
CREATE TABLE `uchome_topicuser` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `topicid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`,`dateline`),
  KEY `topicid` (`topicid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_topicuser
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_userapp`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_userapp`;
CREATE TABLE `uchome_userapp` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `appid` mediumint(8) unsigned NOT NULL default '0',
  `appname` varchar(60) NOT NULL default '',
  `privacy` tinyint(1) NOT NULL default '0',
  `allowsidenav` tinyint(1) NOT NULL default '0',
  `allowfeed` tinyint(1) NOT NULL default '0',
  `allowprofilelink` tinyint(1) NOT NULL default '0',
  `narrow` tinyint(1) NOT NULL default '0',
  `menuorder` smallint(6) NOT NULL default '0',
  `displayorder` smallint(6) NOT NULL default '0',
  KEY `uid` (`uid`,`appid`),
  KEY `menuorder` (`uid`,`menuorder`),
  KEY `displayorder` (`uid`,`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_userapp
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_userappfield`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_userappfield`;
CREATE TABLE `uchome_userappfield` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `appid` mediumint(8) unsigned NOT NULL default '0',
  `profilelink` text NOT NULL,
  `myml` text NOT NULL,
  KEY `uid` (`uid`,`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_userappfield
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_userevent`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_userevent`;
CREATE TABLE `uchome_userevent` (
  `eventid` mediumint(8) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `fellow` mediumint(8) unsigned NOT NULL default '0',
  `template` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`eventid`,`uid`),
  KEY `uid` (`uid`,`dateline`),
  KEY `eventid` (`eventid`,`status`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_userevent
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_usergroup`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_usergroup`;
CREATE TABLE `uchome_usergroup` (
  `gid` smallint(6) unsigned NOT NULL auto_increment,
  `grouptitle` varchar(20) NOT NULL default '',
  `system` tinyint(1) NOT NULL default '0',
  `banvisit` tinyint(1) NOT NULL default '0',
  `explower` int(10) NOT NULL default '0',
  `maxfriendnum` smallint(6) unsigned NOT NULL default '0',
  `maxattachsize` int(10) unsigned NOT NULL default '0',
  `allowhtml` tinyint(1) NOT NULL default '0',
  `allowcomment` tinyint(1) NOT NULL default '0',
  `searchinterval` smallint(6) unsigned NOT NULL default '0',
  `searchignore` tinyint(1) NOT NULL default '0',
  `postinterval` smallint(6) unsigned NOT NULL default '0',
  `spamignore` tinyint(1) NOT NULL default '0',
  `videophotoignore` tinyint(1) NOT NULL default '0',
  `allowblog` tinyint(1) NOT NULL default '0',
  `allowdoing` tinyint(1) NOT NULL default '0',
  `allowupload` tinyint(1) NOT NULL default '0',
  `allowshare` tinyint(1) NOT NULL default '0',
  `allowmtag` tinyint(1) NOT NULL default '0',
  `allowthread` tinyint(1) NOT NULL default '0',
  `allowpost` tinyint(1) NOT NULL default '0',
  `allowcss` tinyint(1) NOT NULL default '0',
  `allowpoke` tinyint(1) NOT NULL default '0',
  `allowfriend` tinyint(1) NOT NULL default '0',
  `allowpoll` tinyint(1) NOT NULL default '0',
  `allowclick` tinyint(1) NOT NULL default '0',
  `allowevent` tinyint(1) NOT NULL default '0',
  `allowmagic` tinyint(1) NOT NULL default '0',
  `allowpm` tinyint(1) NOT NULL default '0',
  `allowviewvideopic` tinyint(1) NOT NULL default '0',
  `allowmyop` tinyint(1) NOT NULL default '0',
  `allowtopic` tinyint(1) NOT NULL default '0',
  `allowstat` tinyint(1) NOT NULL default '0',
  `magicdiscount` tinyint(1) NOT NULL default '0',
  `verifyevent` tinyint(1) NOT NULL default '0',
  `edittrail` tinyint(1) NOT NULL default '0',
  `domainlength` smallint(6) unsigned NOT NULL default '0',
  `closeignore` tinyint(1) NOT NULL default '0',
  `seccode` tinyint(1) NOT NULL default '0',
  `color` varchar(10) NOT NULL default '',
  `icon` varchar(100) NOT NULL default '',
  `manageconfig` tinyint(1) NOT NULL default '0',
  `managenetwork` tinyint(1) NOT NULL default '0',
  `manageprofilefield` tinyint(1) NOT NULL default '0',
  `manageprofield` tinyint(1) NOT NULL default '0',
  `manageusergroup` tinyint(1) NOT NULL default '0',
  `managefeed` tinyint(1) NOT NULL default '0',
  `manageshare` tinyint(1) NOT NULL default '0',
  `managedoing` tinyint(1) NOT NULL default '0',
  `manageblog` tinyint(1) NOT NULL default '0',
  `managetag` tinyint(1) NOT NULL default '0',
  `managetagtpl` tinyint(1) NOT NULL default '0',
  `managealbum` tinyint(1) NOT NULL default '0',
  `managecomment` tinyint(1) NOT NULL default '0',
  `managemtag` tinyint(1) NOT NULL default '0',
  `managethread` tinyint(1) NOT NULL default '0',
  `manageevent` tinyint(1) NOT NULL default '0',
  `manageeventclass` tinyint(1) NOT NULL default '0',
  `managecensor` tinyint(1) NOT NULL default '0',
  `managead` tinyint(1) NOT NULL default '0',
  `managesitefeed` tinyint(1) NOT NULL default '0',
  `managebackup` tinyint(1) NOT NULL default '0',
  `manageblock` tinyint(1) NOT NULL default '0',
  `managetemplate` tinyint(1) NOT NULL default '0',
  `managestat` tinyint(1) NOT NULL default '0',
  `managecache` tinyint(1) NOT NULL default '0',
  `managecredit` tinyint(1) NOT NULL default '0',
  `managecron` tinyint(1) NOT NULL default '0',
  `managename` tinyint(1) NOT NULL default '0',
  `manageapp` tinyint(1) NOT NULL default '0',
  `managetask` tinyint(1) NOT NULL default '0',
  `managereport` tinyint(1) NOT NULL default '0',
  `managepoll` tinyint(1) NOT NULL default '0',
  `manageclick` tinyint(1) NOT NULL default '0',
  `managemagic` tinyint(1) NOT NULL default '0',
  `managemagiclog` tinyint(1) NOT NULL default '0',
  `managebatch` tinyint(1) NOT NULL default '0',
  `managedelspace` tinyint(1) NOT NULL default '0',
  `managetopic` tinyint(1) NOT NULL default '0',
  `manageip` tinyint(1) NOT NULL default '0',
  `managehotuser` tinyint(1) NOT NULL default '0',
  `managedefaultuser` tinyint(1) NOT NULL default '0',
  `managespacegroup` tinyint(1) NOT NULL default '0',
  `managespaceinfo` tinyint(1) NOT NULL default '0',
  `managespacecredit` tinyint(1) NOT NULL default '0',
  `managespacenote` tinyint(1) NOT NULL default '0',
  `managevideophoto` tinyint(1) NOT NULL default '0',
  `managelog` tinyint(1) NOT NULL default '0',
  `magicaward` text NOT NULL,
  `allowquiz` tinyint(1) NOT NULL default '0',
  `managequiz` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_usergroup
-- ----------------------------
INSERT INTO `uchome_usergroup` VALUES ('1', '站点管理员', '-1', '0', '0', '0', '0', '1', '1', '0', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '1', '1', '0', 'red', 'image/group/admin.gif', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'a:0:{}', '1', '1');
INSERT INTO `uchome_usergroup` VALUES ('2', '信息管理员', '-1', '0', '0', '0', '0', '1', '1', '0', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '3', '1', '0', 'blue', 'image/group/infor.gif', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '1');
INSERT INTO `uchome_usergroup` VALUES ('3', '贵宾VIP', '1', '0', '0', '0', '0', '1', '1', '0', '1', '0', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '3', '0', '0', 'green', 'image/group/vip.gif', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0');
INSERT INTO `uchome_usergroup` VALUES ('4', '受限会员', '0', '0', '-999999999', '10', '10', '0', '0', '600', '0', '300', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '0', '0', '1', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0');
INSERT INTO `uchome_usergroup` VALUES ('5', '普通会员', '0', '0', '0', '100', '20', '0', '1', '60', '0', '60', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');
INSERT INTO `uchome_usergroup` VALUES ('6', '中级会员', '0', '0', '100', '200', '50', '0', '1', '30', '0', '30', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '5', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');
INSERT INTO `uchome_usergroup` VALUES ('7', '高级会员', '0', '0', '1000', '300', '100', '1', '1', '10', '1', '10', '0', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '3', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');
INSERT INTO `uchome_usergroup` VALUES ('8', '禁止发言', '-1', '0', '0', '1', '1', '0', '0', '9999', '0', '9999', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '99', '0', '1', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0');
INSERT INTO `uchome_usergroup` VALUES ('9', '禁止访问', '-1', '1', '0', '1', '1', '0', '0', '9999', '0', '9999', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '99', '0', '1', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0');

-- ----------------------------
-- Table structure for `uchome_userlog`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_userlog`;
CREATE TABLE `uchome_userlog` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `action` char(10) NOT NULL default '',
  `type` tinyint(1) NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_userlog
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_usermagic`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_usermagic`;
CREATE TABLE `uchome_usermagic` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` char(15) NOT NULL default '',
  `mid` varchar(15) NOT NULL default '',
  `count` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`,`mid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_usermagic
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_usertask`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_usertask`;
CREATE TABLE `uchome_usertask` (
  `uid` mediumint(8) unsigned NOT NULL,
  `username` char(15) NOT NULL default '',
  `taskid` smallint(6) unsigned NOT NULL default '0',
  `credit` smallint(6) NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `isignore` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`taskid`),
  KEY `isignore` (`isignore`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_usertask
-- ----------------------------

-- ----------------------------
-- Table structure for `uchome_visitor`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_visitor`;
CREATE TABLE `uchome_visitor` (
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `vuid` mediumint(8) unsigned NOT NULL default '0',
  `vusername` char(15) NOT NULL default '',
  `dateline` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`,`vuid`),
  KEY `dateline` (`uid`,`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_visitor
-- ----------------------------
INSERT INTO `uchome_visitor` VALUES ('1', '2', 'summit', '1343631111');

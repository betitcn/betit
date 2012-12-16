/*
MySQL Data Transfer
Source Host: localhost
Source Database: betit
Target Host: localhost
Target Database: betit
Date: 2012-8-2 16:56:50
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for uchome_usergroup
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
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `uchome_usergroup` VALUES ('1', '站点管理员', '-1', '0', '0', '0', '0', '1', '1', '0', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '1', '1', '0', 'red', 'image/group/admin.gif', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', 'a:0:{}', '1', '1');
INSERT INTO `uchome_usergroup` VALUES ('2', '信息管理员', '-1', '0', '0', '0', '0', '1', '1', '0', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '3', '1', '0', 'blue', 'image/group/infor.gif', '0', '0', '0', '0', '0', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '1');
INSERT INTO `uchome_usergroup` VALUES ('3', '贵宾VIP', '1', '0', '0', '0', '0', '1', '1', '0', '1', '0', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '3', '0', '0', 'green', 'image/group/vip.gif', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0');
INSERT INTO `uchome_usergroup` VALUES ('4', '受限会员', '0', '0', '-999999999', '10', '10', '0', '0', '600', '0', '300', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '0', '0', '1', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0');
INSERT INTO `uchome_usergroup` VALUES ('5', '赌鬼', '0', '0', '0', '100', '20', '0', '1', '60', '0', '60', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');
INSERT INTO `uchome_usergroup` VALUES ('6', '赌徒', '0', '0', '100', '200', '50', '0', '1', '30', '0', '30', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '5', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');
INSERT INTO `uchome_usergroup` VALUES ('7', '赌民', '0', '0', '200', '50', '10', '0', '1', '60', '0', '60', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');
INSERT INTO `uchome_usergroup` VALUES ('8', '禁止发言', '-1', '0', '0', '1', '1', '0', '0', '9999', '0', '9999', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '99', '0', '1', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0');
INSERT INTO `uchome_usergroup` VALUES ('9', '禁止访问', '-1', '1', '0', '1', '1', '0', '0', '9999', '0', '9999', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '99', '0', '1', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0');
INSERT INTO `uchome_usergroup` VALUES ('10', '赌棍', '0', '0', '500', '50', '10', '0', '1', '60', '0', '60', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');
INSERT INTO `uchome_usergroup` VALUES ('11', '赌霸', '0', '0', '1000', '50', '10', '0', '1', '60', '0', '60', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');
INSERT INTO `uchome_usergroup` VALUES ('12', '赌侠', '0', '0', '3000', '50', '10', '0', '1', '60', '0', '60', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');
INSERT INTO `uchome_usergroup` VALUES ('13', '赌圣', '0', '0', '8000', '50', '10', '0', '0', '60', '0', '60', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '0', '0');
INSERT INTO `uchome_usergroup` VALUES ('14', '赌神', '0', '0', '20000', '50', '10', '0', '1', '60', '0', '60', '0', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '1', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');
INSERT INTO `uchome_usergroup` VALUES ('15', '赌仙', '0', '0', '50000', '50', '10', '0', '1', '60', '0', '60', '0', '0', '1', '1', '1', '1', '0', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '0', '1', '0', '0', '0', '0', '', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'a:0:{}', '1', '0');

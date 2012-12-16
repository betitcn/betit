/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50051
Source Host           : localhost:3306
Source Database       : betit

Target Server Type    : MYSQL
Target Server Version : 50051
File Encoding         : 65001

Date: 2012-08-06 15:13:39
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `uchome_quizresult`
-- ----------------------------
DROP TABLE IF EXISTS `uchome_quizresult`;
CREATE TABLE `uchome_quizresult` (
  `rid` int(10) NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `username` varchar(15) NOT NULL default '',
  `quizid` mediumint(8) unsigned NOT NULL default '0',
  `dateline` int(10) unsigned NOT NULL default '0',
  `totalcost` int(10) unsigned NOT NULL default '0',
  `totalwin` int(10) unsigned NOT NULL default '0',
  `totallost` int(10) unsigned NOT NULL default '0',
  `winflag` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`rid`,`uid`,`quizid`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uchome_quizresult
-- ----------------------------

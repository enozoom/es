/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50709
Source Host           : localhost:3306
Source Database       : haolaopo

Target Server Type    : MYSQL
Target Server Version : 50709
File Encoding         : 65001

Date: 2016-12-27 16:58:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for es4_category
-- ----------------------------
DROP TABLE IF EXISTS `es4_category`;
CREATE TABLE `es4_category` (
  `category_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `category_title` varchar(255) NOT NULL DEFAULT '',
  `category_pid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `category_sequence` tinyint(255) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of es4_category
-- ----------------------------
INSERT INTO `es4_category` VALUES ('1', '管理员', '0', '1');
INSERT INTO `es4_category` VALUES ('2', '角色', '1', '1');
INSERT INTO `es4_category` VALUES ('3', '等级', '1', '1');
INSERT INTO `es4_category` VALUES ('4', '文章', '0', '1');
INSERT INTO `es4_category` VALUES ('5', '系统', '4', '1');
INSERT INTO `es4_category` VALUES ('6', '普通', '4', '1');
INSERT INTO `es4_category` VALUES ('7', '设置', '5', '1');
INSERT INTO `es4_category` VALUES ('8', '活动', '6', '1');
INSERT INTO `es4_category` VALUES ('9', '公告', '6', '1');
INSERT INTO `es4_category` VALUES ('10', '广告', '0', '1');
INSERT INTO `es4_category` VALUES ('11', '首页', '10', '1');
INSERT INTO `es4_category` VALUES ('12', '会员', '0', '1');
INSERT INTO `es4_category` VALUES ('13', '等级', '12', '1');
INSERT INTO `es4_category` VALUES ('14', '高级', '13', '1');
INSERT INTO `es4_category` VALUES ('15', '普通', '13', '1');
INSERT INTO `es4_category` VALUES ('16', '商品', '0', '1');
INSERT INTO `es4_category` VALUES ('17', '状态', '16', '1');
INSERT INTO `es4_category` VALUES ('18', '分类', '16', '1');
INSERT INTO `es4_category` VALUES ('19', '通用', '0', '1');
INSERT INTO `es4_category` VALUES ('20', '状态', '19', '1');
INSERT INTO `es4_category` VALUES ('21', '正常', '20', '1');
INSERT INTO `es4_category` VALUES ('22', '限制', '20', '1');
INSERT INTO `es4_category` VALUES ('23', '回收', '20', '1');

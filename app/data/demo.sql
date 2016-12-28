SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for es4_ad
-- ----------------------------
DROP TABLE IF EXISTS `es4_ad`;
CREATE TABLE `es4_ad` (
  `ad_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `ad_title` varchar(255) NOT NULL DEFAULT '',
  `ad_target` varchar(255) NOT NULL DEFAULT '',
  `ad_pic` varchar(255) NOT NULL DEFAULT '',
  `category_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ad_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `ad_endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `ad_sequence` tinyint(255) unsigned NOT NULL DEFAULT '0',
  `status_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_id`)
);


-- ----------------------------
-- Table structure for es4_article
-- ----------------------------
DROP TABLE IF EXISTS `es4_article`;
CREATE TABLE `es4_article` (
  `article_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `article_title` varchar(255) NOT NULL DEFAULT '',
  `article_keywords` varchar(255) NOT NULL DEFAULT '',
  `article_description` varchar(255) NOT NULL DEFAULT '',
  `article_pic` varchar(255) NOT NULL DEFAULT '',
  `article_txt` text NOT NULL,
  `category_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `status_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `article_author` varchar(255) NOT NULL DEFAULT '',
  `article_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`article_id`),
  UNIQUE KEY `article_title` (`article_title`),
  KEY `category_id` (`category_id`,`status_id`)
);

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

-- ----------------------------
-- Table structure for es4_user
-- ----------------------------
DROP TABLE IF EXISTS `es4_user`;
CREATE TABLE `es4_user` (
  `user_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `user_password` varchar(255) NOT NULL DEFAULT '',
  `user_level` smallint(6) unsigned NOT NULL DEFAULT '0',
  `user_status` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
);

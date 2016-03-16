# A文章
DROP TABLE IF EXISTS `es_article`;
CREATE TABLE `es_article` (
  `article_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `article_title` varchar(80) NOT NULL DEFAULT '' UNIQUE KEY,
  `article_keywords` varchar(80) NOT NULL DEFAULT '',
  `article_description` varchar(200) NOT NULL DEFAULT '',
  `pic_id` int unsigned NOT NULL DEFAULT 0,
  `article_txt` text,
  `category_id` smallint unsigned NOT NULL DEFAULT 3,
  `status_id` smallint unsigned NOT NULL DEFAULT 411,
  `article_author` varchar(150) NOT NULL DEFAULT '',
  `article_timestamp` int unsigned NOT NULL DEFAULT 0
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `es_article`(`article_title`,`category_id`,`status_id`,`article_author`)
VALUES ('网站标题',31,413,'系统禁止删除');

# B品牌
DROP TABLE IF EXISTS `es_brand`;
CREATE TABLE `es_brand`(
  `brand_id` smallint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `category_id` smallint unsigned NOT NULL DEFAULT 0,
  `article_id` int unsigned NOT NULL DEFAULT 0,
  `ok_ids` varchar(50) NOT NULL DEFAULT '',
  `pic_ids` varchar(50) NOT NULL DEFAULT '',
  `brand_name` varchar(30) NOT NULL DEFAULT '',
  `brand_sequence` tinyint unsigned NOT NULL DEFAULT 0,
  `brand_timestamp` int unsigned NOT NULL DEFAULT 0,
  `status_id` smallint unsigned NOT NULL DEFAULT 411
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

# C分类
DROP TABLE IF EXISTS `es_category`;
CREATE TABLE `es_category` (
  `category_id` smallint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `category_name` varchar(20) NOT NULL DEFAULT '',
  `category_pid` smallint unsigned NOT NULL DEFAULT 0,
  `category_intro` varchar(200) NOT NULL DEFAULT '',
  KEY(`category_pid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `es_category` VALUES
(1,'用户',0,''),
(11,'角色',1,''),
(111,'管理员',11,''),
(112,'内部员工',11,''),
(113,'普通用户',11,''),
(12,'等级',1,''),
(121,'普通',12,''),
(122,'中级',12,''),
(123,'高级',12,''),
(2,'图片',0,''),
(3,'文章',0,''),
(31,'系统设置',3,''),
(32,'普通文章',3,''),
(4,'常用',0,'公共的类别'),
(41,'状态',4,''),
(411,'正常',41,'前台显示'),
(412,'禁用',41,'不显示，限制登录'),
(413,'回收',41,'仅后台显示');


# O拓展参数键
DROP TABLE IF EXISTS `es_option_key`;
CREATE TABLE `es_option_key`(
  `ok_id` smallint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `ok_key` varchar(15) NOT NULL DEFAULT '',
  `ok_intro` varchar(16) NOT NULL DEFAULT '',
  `ok_fktable` varchar(20) NOT NULL DEFAULT '',
  UNIQUE KEY (`ok_key`,`ok_fktable`),
  KEY (`ok_fktable`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


# O拓展参数值
DROP TABLE IF EXISTS `es_option_value`;
CREATE TABLE `es_option_value`(
  `ov_id` int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `fkid_id` int unsigned NOT NULL DEFAULT 0,
  `ok_id` smallint unsigned NOT NULL DEFAULT 0,
  `ov_value` varchar(300) NOT NULL DEFAULT '',
  UNIQUE KEY (`fkid_id`,`ok_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

# P图片
DROP TABLE IF EXISTS `es_pic`;
CREATE TABLE `es_pic` (
  `pic_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `pic_timestamp` int unsigned NOT NULL DEFAULT 0,
  `category_id` smallint unsigned NOT NULL DEFAULT 2,
  `pic_url` varchar(255) NOT NULL DEFAULT '',
  `ok_ids` varchar(50) NOT NULL DEFAULT '',
  KEY(`category_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

# S分享
DROP TABLE IF EXISTS `es_share`;
CREATE TABLE `es_share`(
  `share_id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `fk_id` int unsigned NOT NULL DEFAULT 0,
  `category_id` int unsigned NOT NULL DEFAULT 0,
  `status_id` int unsigned NOT NULL DEFAULT 411,
  `share_timestamp` int unsigned NOT NULL DEFAULT 0,
  `share_read_times` int unsigned NOT NULL DEFAULT 0,
  `ok_ids` varchar(50) NOT NULL  DEFAULT ''
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

# S子类品牌
DROP TABLE IF EXISTS `es_subb`;
CREATE TABLE `es_subb`(
  `subb_id` int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `article_id` int unsigned NOT NULL DEFAULT 0,
  `category_id` smallint unsigned NOT NULL DEFAULT 0,
  `brand_id` smallint unsigned NOT NULL DEFAULT 0,
  `pic_ids` varchar(50) NOT NULL DEFAULT '',
  `ok_ids` varchar(50) NOT NULL DEFAULT '',
  `subb_name` varchar(30)  NOT NULL DEFAULT ''
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

# U用户
DROP TABLE IF EXISTS `es_usr`;
CREATE TABLE `es_usr` (
  `usr_id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `usr_name` varchar(16) NOT NULL DEFAULT '' UNIQUE KEY,
  `usr_pword` char(32) NOT NULL DEFAULT '',
  `usr_regtime` int unsigned NOT NULL DEFAULT 0,
  `pic_id` int unsigned NOT NULL DEFAULT 0,
  `usr_mobile` char(11) NOT NULL DEFAULT '' UNIQUE KEY,
  `usr_lasttime` int unsigned NOT NULL DEFAULT 0,
  `status_id` smallint unsigned NOT NULL DEFAULT 411,
  `level_id` smallint unsigned NOT NULL DEFAULT 121,
  `category_id` smallint unsigned NOT NULL DEFAULT 113,
  `ok_ids` varchar(50) NOT NULL DEFAULT '',
  KEY (`usr_mobile`,`usr_pword`,`category_id`,`status_id`),
  KEY (`usr_name`,`usr_pword`,`category_id`,`status_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `playsms_featureCollect_mc_member`;
CREATE TABLE `playsms_featureCollect_mc_member` (
  `c_timestamp` int(11) NOT NULL DEFAULT '0',
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `collect_id` int(11) NOT NULL DEFAULT '0',
  `collect_msg` varchar(255) NOT NULL,
  `member_number` varchar(20) NOT NULL,
  `member_since` varchar(20) NOT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `playsms_featureCollect_mc`;
CREATE TABLE `playsms_featureCollect_mc` (
  `c_timestamp` int(11) NOT NULL DEFAULT '0',
  `collect_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `collect_keyword` varchar(20) NOT NULL,
  `collect_msg` varchar(200) NOT NULL,
  `collect_fwd_email` varchar(100) NOT NULL,
  `collect_enable` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`collect_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `playsms_gatewayMsgtoolbox`
--

DROP TABLE IF EXISTS `playsms_gatewayMsgtoolbox`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playsms_gatewayMsgtoolbox` (
  `c_timestamp` bigint(20) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `local_smslog_id` int(11) NOT NULL DEFAULT '0',
  `remote_smslog_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playsms_gatewayMsgtoolbox`
--

LOCK TABLES `playsms_gatewayMsgtoolbox` WRITE;
/*!40000 ALTER TABLE `playsms_gatewayMsgtoolbox` DISABLE KEYS */;
/*!40000 ALTER TABLE `playsms_gatewayMsgtoolbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `playsms_gatewayMsgtoolbox_config`
--

DROP TABLE IF EXISTS `playsms_gatewayMsgtoolbox_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playsms_gatewayMsgtoolbox_config` (
  `c_timestamp` bigint(20) NOT NULL DEFAULT '0',
  `cfg_name` varchar(20) NOT NULL DEFAULT 'msgtoolbox',
  `cfg_url` varchar(250) DEFAULT NULL,
  `cfg_route` varchar(5) DEFAULT NULL,
  `cfg_username` varchar(100) DEFAULT NULL,
  `cfg_password` varchar(100) DEFAULT NULL,
  `cfg_module_sender` varchar(20) DEFAULT NULL,
  `cfg_datetime_timezone` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playsms_gatewayMsgtoolbox_config`
--

LOCK TABLES `playsms_gatewayMsgtoolbox_config` WRITE;
/*!40000 ALTER TABLE `playsms_gatewayMsgtoolbox_config` DISABLE KEYS */;
INSERT INTO `playsms_gatewayMsgtoolbox_config` VALUES (0,'msgtoolbox','http://serverX.msgtoolbox.com/api/current/send/message.php','1','playsms','password','playSMS','');
/*!40000 ALTER TABLE `playsms_gatewayMsgtoolbox_config` ENABLE KEYS */;
UNLOCK TABLES;


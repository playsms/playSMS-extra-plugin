--
-- Table structure for table `playsms_gatewayGnokii_config`
--

DROP TABLE IF EXISTS `playsms_gatewayGnokii_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `playsms_gatewayGnokii_config` (
  `c_timestamp` bigint(20) NOT NULL DEFAULT '0',
  `cfg_name` varchar(20) NOT NULL DEFAULT 'gnokii',
  `cfg_path` varchar(250) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `playsms_gatewayGnokii_config`
--

LOCK TABLES `playsms_gatewayGnokii_config` WRITE;
/*!40000 ALTER TABLE `playsms_gatewayGnokii_config` DISABLE KEYS */;
INSERT INTO `playsms_gatewayGnokii_config` VALUES (0,'gnokii','/var/spool/playsms');
/*!40000 ALTER TABLE `playsms_gatewayGnokii_config` ENABLE KEYS */;
UNLOCK TABLES;


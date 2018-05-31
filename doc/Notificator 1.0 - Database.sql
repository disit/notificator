-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: Notificator
-- ------------------------------------------------------
-- Server version	5.7.14

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clientApplications`
--

DROP TABLE IF EXISTS `clientApplications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientApplications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `ldapName` varchar(100) NOT NULL,
  `loginApiUrl` mediumtext NOT NULL,
  `containerTitleLabel` varchar(100) DEFAULT NULL,
  `genTitleLabel` varchar(100) DEFAULT NULL,
  `genTypeLabel` varchar(100) DEFAULT NULL,
  `usrLabel` varchar(100) DEFAULT NULL,
  `genLinkLabel` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientApplications`
--

LOCK TABLES `clientApplications` WRITE;
/*!40000 ALTER TABLE `clientApplications` DISABLE KEYS */;
INSERT INTO `clientApplications` VALUES (1,'Dashboard Manager','Dashboard','http://localhost/temp/api/notificatorLogin.php','Dashboard title','Widget title','Metric type','User','Dashboard link'),(3,'Twitter Vigilance','TwitterVigilance','http://192.168.128.87/addons/firing/lib/scripts/notificatorLogin.php','Metric page','Metric name','Metric type','User','Metric page link');
/*!40000 ALTER TABLE `clientApplications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emailAddrBook`
--

DROP TABLE IF EXISTS `emailAddrBook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailAddrBook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `adr` varchar(100) NOT NULL,
  `fName` varchar(100) DEFAULT NULL,
  `lName` varchar(100) DEFAULT NULL,
  `org` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`,`adr`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emailAddrBook`
--

LOCK TABLES `emailAddrBook` WRITE;
/*!40000 ALTER TABLE `emailAddrBook` DISABLE KEYS */;
INSERT INTO `emailAddrBook` VALUES (67,'cristianogelli@gmail.com','Cristiano','Gelli','Student'),(68,'gianni.pantaleo@unifi.it','Gianni','Pantaleo','DISIT'),(70,'pierfrancesco.bellini@unifi.it',NULL,NULL,NULL),(57,'mino.marazzini@gmail.com','Mino','Marazzini','Personal'),(58,'mino.marazzini@unifi.it','Mino','Marazzini','DISIT'),(69,'paolo.nesi@unifi.it','Paolo','Nesi','DISIT');
/*!40000 ALTER TABLE `emailAddrBook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emailBook`
--

DROP TABLE IF EXISTS `emailBook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailBook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sub` varchar(200) DEFAULT NULL,
  `txt` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emailBook`
--

LOCK TABLES `emailBook` WRITE;
/*!40000 ALTER TABLE `emailBook` DISABLE KEYS */;
INSERT INTO `emailBook` VALUES (63,'aaaaaa','<p>hfghfghfgh</p>\n\n<p>[[EventDetails]]</p>\n'),(60,'CALDO ratio notif','<p>CALDO ratio notif</p>\n'),(61,'TEST 2 notif','<p>TEST 2 notif</p>\n'),(62,'TEST 2 notif','<p>TEST 2 notif</p>\n'),(57,'User metric notif','<p>User metric notif</p>\n'),(58,'CALDO rtw adj notif','<p>CALDO rtw adj notif</p>\n'),(59,'Caldo pos ratio notif','<p>Caldo pos ratio notif</p>\n'),(46,'Test1','<p>Test1</p>\n'),(47,'Test2','<p>Test2</p>\n'),(49,'Test4 subject','<p>Test4 body</p>\n'),(50,'Test5','<p>Test5</p>\n'),(51,'NewMsgOldRec','<p>NewMsgOldRec</p>\n'),(65,'Value > 70 sub','<p>Value &gt; 70 body</p>\n'),(54,'DISIT Notificator','<p>Dear recipient,<br />\nthe following event has occurred:</p>\n\n<p>[[EventDetails]]</p>\n\n<p>Regards.</p>\n');
/*!40000 ALTER TABLE `emailBook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emailNotificationRecipientsRelation`
--

DROP TABLE IF EXISTS `emailNotificationRecipientsRelation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailNotificationRecipientsRelation` (
  `notId` int(11) NOT NULL,
  `recId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Relations between notification and receipts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emailNotificationRecipientsRelation`
--

LOCK TABLES `emailNotificationRecipientsRelation` WRITE;
/*!40000 ALTER TABLE `emailNotificationRecipientsRelation` DISABLE KEYS */;
INSERT INTO `emailNotificationRecipientsRelation` VALUES (78,57),(79,57),(77,57),(79,57),(80,57),(80,67),(82,67),(82,57),(84,67),(84,57),(83,67),(83,57),(83,68),(81,67),(81,68),(81,57),(85,67),(85,57),(85,58);
/*!40000 ALTER TABLE `emailNotificationRecipientsRelation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emailNotifications`
--

DROP TABLE IF EXISTS `emailNotifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailNotifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `genId` int(11) NOT NULL,
  `eventId` int(11) NOT NULL,
  `msgId` int(11) NOT NULL DEFAULT '-1',
  `val` int(11) DEFAULT NULL,
  `valStart` datetime DEFAULT NULL,
  `valEnd` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=latin1 COMMENT='This table holds notifications data';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emailNotifications`
--

LOCK TABLES `emailNotifications` WRITE;
/*!40000 ALTER TABLE `emailNotifications` DISABLE KEYS */;
INSERT INTO `emailNotifications` VALUES (77,'Engagement sent 1',16,15,54,1,'2017-07-01 11:15:00','2017-07-31 11:15:00'),(78,'Engagement sent 2',16,34,47,1,'2017-07-01 12:40:00','2017-07-31 12:40:00'),(80,'User metric notif',41,42,57,1,'2017-07-01 15:27:00','2017-07-31 15:27:00'),(81,'CALDO rtw adj notif',33,43,58,1,'2017-07-01 15:29:00','2017-07-31 15:29:00'),(82,'Caldo pos ratio notif',42,44,59,1,'2017-07-01 15:30:00','2017-08-01 15:30:00'),(83,'CALDO ratio notif',31,41,60,1,'2017-07-01 15:30:00','2017-08-01 15:30:00'),(84,'TEST 2 notif',53,45,62,1,'2017-07-01 15:31:00','2017-07-31 15:31:00'),(85,'kkkkk',16,15,63,1,'2017-07-01 17:35:00','2017-07-31 17:35:00');
/*!40000 ALTER TABLE `emailNotifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventGenerators`
--

DROP TABLE IF EXISTS `eventGenerators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventGenerators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appName` varchar(100) CHARACTER SET latin1 NOT NULL,
  `generatorOriginalName` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `generatorOriginalType` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `containerName` varchar(100) DEFAULT NULL,
  `appUsr` varchar(100) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `regTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `val` varchar(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventGenerators`
--

LOCK TABLES `eventGenerators` WRITE;
/*!40000 ALTER TABLE `eventGenerators` DISABLE KEYS */;
INSERT INTO `eventGenerators` VALUES (16,'Dashboard Manager','Engagement sent','Km4City_Query_Day','Km4City','Softec','http://localhost/dashboardsmartcity/view/index.php?iddasboard=OA==','2017-05-10 15:45:33','1'),(17,'Dashboard Manager','City consumption','Wifi_Power_5Ril','Energy','Mino','http://localhost/dashboardsmartcity/view/index.php?iddasboard=MjA=','2017-05-10 15:45:33','1'),(18,'Dashboard Manager','Ataf RT','Ataf_RT','Firenze2','Mino','http://localhost/dashboardsmartcity/view/index.php?iddasboard=Ng==','2017-05-10 15:45:33','1'),(23,'Twitter Vigilance','testMetric1','testMetricType','testPageName','Admin','http://www.google.it','2017-07-07 08:49:30','1'),(24,'Twitter Vigilance','testMetric2','testMetricType','testPageName','Admin','http://www.google.it','2017-07-07 08:58:21','1'),(25,'Twitter Vigilance','testMetric3','testMetricType','testPageName','Admin','http://www.google.it','2017-07-07 09:28:18','1'),(26,'Twitter Vigilance','test not 4','HLM','testPageName','Admin','http://www.google.it','2017-07-07 09:34:56','1'),(27,'Twitter Vigilance','user test 4','HLM','testPageName','utente','http://www.google.it','2017-07-07 09:53:38','1'),(28,'Twitter Vigilance','trend test not1','trend','HLM metrics page','admin','http://www.disit.org/tv','2017-07-07 10:50:12','1'),(29,'Dashboard Manager','DM1','DM1','DM1','Mino','http://localhost/dashboardsmartcity/view/index.php?iddasboard=Ng==','2017-05-10 15:45:33','1'),(30,'Twitter Vigilance','14 luglio 2','HLM','HLM metrics page','admin','http://www.disit.org/tv','2017-07-14 07:23:02','1'),(31,'Twitter Vigilance','CALDO ratio','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:02','1'),(32,'Twitter Vigilance','CALDO rtw','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(33,'Twitter Vigilance','CALDO rtw adj','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(34,'Twitter Vigilance','CALDO tw rtw adj','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(35,'Twitter Vigilance','hashtag firenze metric daily','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(36,'Twitter Vigilance','hashtag test fi','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(37,'Twitter Vigilance','poca acqua','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(38,'Twitter Vigilance','sovrametric','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(39,'Twitter Vigilance','sovrasovrametric','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(40,'Twitter Vigilance','test metric 1','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(41,'Twitter Vigilance','user metric','HLM','HLM metrics page','utente','http://www.google.it','2017-07-14 12:32:36','1'),(42,'Twitter Vigilance','caldo pos ratio','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(43,'Twitter Vigilance','caldo pos ratio 2','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(44,'Twitter Vigilance','metric 14 luglio','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(45,'Twitter Vigilance','mix sa metrics','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(46,'Twitter Vigilance','prova canale','HLM','HLM metrics page','utente','http://www.google.it','2017-07-14 12:32:36','1'),(47,'Twitter Vigilance','score fi 2','HLM','HLM metrics page','utente','http://www.google.it','2017-07-14 12:32:36','1'),(48,'Twitter Vigilance','TEST SA 1','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(49,'Twitter Vigilance','TEST SA 2','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(51,'Twitter Vigilance','CALDO rtw','HLM','HLM metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(52,'Twitter Vigilance','TEST ','trend','Trend metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(53,'Twitter Vigilance','TEST 2','trend','Trend metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(54,'Twitter Vigilance','trend test h','trend','Trend metrics page','admin','http://www.google.it','2017-07-14 12:32:36','1'),(55,'Twitter Vigilance','pippo','trend','HLM metrics page','admin','http://www.disit.org/tv','2017-07-14 13:11:44','1');
/*!40000 ALTER TABLE `eventGenerators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `genId` int(11) NOT NULL,
  `eventType` varchar(100) NOT NULL,
  `thrCnt` int(11) DEFAULT NULL,
  `val` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (15,16,'Value < 4500.0 more than 1 time(s)',1,1),(16,17,'Value > 70.0 more than 1 time(s)',1,1),(17,18,'Value < 60.0 more than 1 time(s)',1,1),(18,19,'Value < 70.0 more than 1 time(s)',1,1),(19,20,'Value < 70.0 more than 1 time(s)',1,1),(20,21,'Value < 70.0 more than 1 time(s)',1,1),(21,22,'Value < 70.0 more than 1 time(s)',1,1),(41,31,'CALDO ratio>0',1,1),(42,41,'user metric >2',1,1),(43,33,'CALDO rtw adj>100',1,1),(44,42,'caldo pos ratio>=2',1,1),(45,53,'TEST 2<1000',1,1);
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventsLog`
--

DROP TABLE IF EXISTS `eventsLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventsLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` varchar(20) NOT NULL,
  `eventTypeId` varchar(200) NOT NULL,
  `value` float NOT NULL,
  `compValue` float NOT NULL,
  `genId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventsLog`
--

LOCK TABLES `eventsLog` WRITE;
/*!40000 ALTER TABLE `eventsLog` DISABLE KEYS */;
INSERT INTO `eventsLog` VALUES (1,'2017-07-17 17:12:06','15',739,4500,16),(2,'2017-07-18 10:13:22','15',255,4500,16);
/*!40000 ALTER TABLE `eventsLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restNotifications`
--

DROP TABLE IF EXISTS `restNotifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `restNotifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restNotifications`
--

LOCK TABLES `restNotifications` WRITE;
/*!40000 ALTER TABLE `restNotifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `restNotifications` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-18 10:30:22

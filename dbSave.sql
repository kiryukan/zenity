-- MySQL dump 10.13  Distrib 5.7.17, for Linux (x86_64)
--
-- Host: localhost    Database: zenityService
-- ------------------------------------------------------
-- Server version	5.7.17-0ubuntu0.16.04.1

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
-- Table structure for table `indicator`
--

DROP TABLE IF EXISTS `indicator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `indicator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note_id` int(11) DEFAULT NULL,
  `advice_id` int(11) DEFAULT NULL,
  `class` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field_exact_value` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `coeff` double DEFAULT NULL,
  `max_value` double DEFAULT NULL,
  `min_value` double DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D1349DB326ED0855` (`note_id`),
  KEY `IDX_D1349DB312998205` (`advice_id`),
  CONSTRAINT `FK_D1349DB312998205` FOREIGN KEY (`advice_id`) REFERENCES `advice` (`id`),
  CONSTRAINT `FK_D1349DB326ED0855` FOREIGN KEY (`note_id`) REFERENCES `note` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indicator`
--

LOCK TABLES `indicator` WRITE;
/*!40000 ALTER TABLE `indicator` DISABLE KEYS */;
INSERT INTO `indicator` VALUES (18,3,NULL,'OSState','busyTime',NULL,1,50,0,NULL),(19,2,NULL,'Events','dbTime',NULL,1,20,0,NULL),(22,2,NULL,'Events','dbTime',NULL,1,20,0,NULL),(23,2,NULL,'Events','dbTime',NULL,1,20,0,NULL),(24,2,NULL,'Events','dbTime',NULL,1,20,0,NULL),(25,2,NULL,'Events','dbTime',NULL,1,20,0,NULL),(31,3,NULL,'OSState','sysTime',NULL,1,50,0,NULL),(32,3,NULL,'OSState','userTime',NULL,1,50,0,NULL),(34,3,NULL,'OSState','load',NULL,1,50,0,NULL),(38,6,NULL,'Events','avgWait',NULL,1,0.1,0,NULL),(39,6,NULL,'Events','avgWait',NULL,1,0.1,0,NULL),(40,6,NULL,'Events','avgWait',NULL,1,0.1,0,NULL),(42,6,NULL,'Events','avgWait',NULL,1,0.1,0,NULL),(43,6,NULL,'Events','avgWait',NULL,1,15,0,NULL),(44,6,NULL,'Events','avgWait',NULL,1,15,0,NULL),(45,6,NULL,'Events','avgWait',NULL,1,1,0,NULL),(46,6,NULL,'Events','avgWait',NULL,1,15,0,NULL),(47,6,NULL,'Events','avgWait',NULL,1,1,0,NULL),(48,6,NULL,'LoadProfile','hardParses',NULL,1,5,0,NULL),(49,6,NULL,'LoadProfile','hardParses',NULL,2,1,0,NULL),(50,8,NULL,'Statistics','PhysicalWritesCache',NULL,1,0.1,0,NULL),(51,8,NULL,'Statistics','PhysicalWritesCache',NULL,1,30,0,NULL),(52,8,NULL,'Statistics','PhysicalWritesCache',NULL,1,0.5,0,NULL),(53,8,NULL,'Statistics','DbBlockGetsCache',NULL,1,1,0.95,NULL),(54,8,NULL,'Statistics','DbBlockGetsCache',NULL,1,1,0.5,NULL),(55,8,NULL,'Statistics','DbBlockChangeOverGets',NULL,1,1,0.95,NULL),(56,8,NULL,'Statistics','DbBlockChangeOverGets',NULL,1,1,0,NULL),(57,8,NULL,'Statistics','PhysicalReadsCache',NULL,1,1,0.7,NULL),(58,8,NULL,'Statistics','PhysicalReadsCache',NULL,1,0.4,0.3,NULL),(59,8,NULL,'Statistics','ConsistentChangeOverGets',NULL,1,0.01,0,NULL),(60,8,NULL,'Statistics','ConsistentChangeOverGets',NULL,1,0.03,0,NULL),(61,8,NULL,'Statistics','ConsistentChangeOverGets',NULL,1,0,0,NULL),(64,4,NULL,'SqlInfo','nbRequestWithRowPerExecUnder500',NULL,1,0,0,NULL),(65,4,NULL,'SqlInfo','nbRequestWithRowPerExecUnder1000',NULL,1,1,0,NULL),(66,4,NULL,'SqlInfo','nbRequestWithTotalParseOver50',NULL,2,1,0,NULL),(67,4,NULL,'SqlInfo','nbRequestWithVersionCountOver50',NULL,1,4,0,NULL),(68,4,NULL,'SqlInfo','nbRequestWithVersionCountOver50',NULL,1,1,0,NULL),(69,4,NULL,'SqlInfo','nbRequestWithTotalCpuOver25',NULL,1,1,0,NULL),(70,4,NULL,'SqlInfo','nbRequestWithTotalCpuOver50',NULL,1,1,0,NULL),(71,4,NULL,'SqlInfo','nbRequestWithTotalElapTimeOver25',NULL,1,1,0,NULL),(72,4,NULL,'SqlInfo','nbRequestWithTotalElapTimeOver50',NULL,1,1,0,NULL),(73,4,NULL,'SqlInfo','nbRequestWithTotalGetsOver25',NULL,5,1,0,NULL),(74,4,NULL,'SqlInfo','nbRequestWithTotalGetsOver50',NULL,1,1,0,NULL),(75,4,NULL,'SqlInfo','nbRequestWithTotalReadsOver25',NULL,1,1,0,NULL),(76,4,NULL,'SqlInfo','nbRequestWithTotalReadsOver50',NULL,1,1,0,NULL),(77,3,NULL,'OSState','busyTime',NULL,1,2,0,NULL),(78,3,NULL,'OSState','sysTime',NULL,1,25,2,NULL),(79,3,NULL,'OSState','userTime',NULL,1,25,NULL,NULL),(80,3,NULL,'OSState','load',NULL,1,25,0,NULL);
/*!40000 ALTER TABLE `indicator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `note`
--

DROP TABLE IF EXISTS `note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sgbd` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `version_pattern` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `note`
--

LOCK TABLES `note` WRITE;
/*!40000 ALTER TABLE `note` DISABLE KEYS */;
INSERT INTO `note` VALUES (2,'Network','oracle','.*'),(3,'OS','oracle','.*'),(4,'SQL','oracle','.*'),(6,'Instance','oracle','.*'),(8,'IO','oracle','.*');
/*!40000 ALTER TABLE `note` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-02-24 16:07:47

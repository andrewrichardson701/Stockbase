-- MySQL dump 10.13  Distrib 8.0.34, for Linux (x86_64)
--
-- Host: localhost    Database: inventory_dev
-- ------------------------------------------------------
-- Server version	8.0.34-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `area` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `site_id` bigint NOT NULL,
  `parent_id` int DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area`
--

LOCK TABLES `area` WRITE;
/*!40000 ALTER TABLE `area` DISABLE KEYS */;
INSERT INTO `area` VALUES (1,'DF3 Store','DF3 Store Room and DF3 Environmental',1,NULL,0),(2,'Store 50','Store Room 50 (Pav\'s Lab)',1,NULL,0),(3,'Store 23','Store Room 23 (Customer Storage)',1,NULL,0),(4,'MMRA Store','Meet Me Room A (MMRA) Store Room',2,NULL,0),(5,'testArea','',4,NULL,0);
/*!40000 ALTER TABLE `area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cable_item`
--

DROP TABLE IF EXISTS `cable_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cable_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `stock_id` int NOT NULL,
  `quantity` int NOT NULL,
  `cost` decimal(10,0) DEFAULT NULL,
  `shelf_id` int NOT NULL DEFAULT '0',
  `type_id` int NOT NULL DEFAULT '1',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cable_item`
--

LOCK TABLES `cable_item` WRITE;
/*!40000 ALTER TABLE `cable_item` DISABLE KEYS */;
INSERT INTO `cable_item` VALUES (1,36,11,5,1,2,0),(2,37,2,4,1,2,0),(3,38,3,5,1,2,0),(4,39,1,5,1,2,0),(5,40,1,5,1,2,0),(6,41,1,5,1,2,0),(7,42,1,5,1,2,0),(8,43,11,5,1,2,0),(9,44,11,5,1,2,0),(10,45,11,5,1,2,0),(11,46,11,5,1,2,0),(12,47,13,5,1,2,0),(13,48,7,0,1,5,0),(14,49,10,0,1,5,0),(15,50,15,0,1,5,0),(16,51,9,0,1,5,0),(17,52,9,0,1,5,0),(18,53,10,0,1,5,0),(19,54,10,0,1,5,0),(20,55,7,0,1,5,0),(21,56,5,0,1,5,0),(22,57,7,0,1,8,0),(23,58,12,0,1,8,0),(24,59,15,0,1,8,0),(25,60,9,0,1,8,0),(26,61,8,0,1,8,0),(27,62,4,0,1,8,0),(28,63,6,0,1,8,0),(29,64,6,0,1,8,0),(30,65,12,0,1,8,0),(31,66,35,0,1,11,0),(32,67,5,0,1,11,0),(33,68,12,0,1,11,0),(34,69,18,0,1,11,0),(35,70,10,0,1,11,0),(36,71,8,0,1,11,0),(37,72,5,0,1,11,0),(38,73,5,0,1,11,0),(39,74,7,0,1,11,0),(40,44,12,5,10,2,0),(41,78,11,5,1,2,0),(42,79,5,0,1,2,0),(43,80,1,0,1,2,0),(44,81,18,0,1,2,0),(45,82,5,0,1,11,0);
/*!40000 ALTER TABLE `cable_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cable_transaction`
--

DROP TABLE IF EXISTS `cable_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cable_transaction` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `stock_id` bigint NOT NULL,
  `item_id` bigint NOT NULL,
  `type` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `reason` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `username` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cable_transaction`
--

LOCK TABLES `cable_transaction` WRITE;
/*!40000 ALTER TABLE `cable_transaction` DISABLE KEYS */;
INSERT INTO `cable_transaction` VALUES (1,44,9,'add',12,'Added via Fixed Cable page','2023-08-07','02:36:52','inventory'),(2,44,9,'remove',11,'Removed via Fixed Cable page','2023-08-07','02:36:53','inventory'),(3,46,11,'add',12,'Added via Fixed Cable page','2023-08-07','02:37:45','inventory'),(4,57,22,'add',8,'Added via Fixed Cable page','2023-08-07','02:48:30','inventory'),(5,57,22,'remove',7,'Removed via Fixed Cable page','2023-08-07','02:48:32','inventory'),(6,62,27,'add',5,'Added via Fixed Cable page','2023-08-07','02:48:33','inventory'),(7,62,27,'remove',4,'Removed via Fixed Cable page','2023-08-07','02:48:34','inventory'),(8,62,27,'remove',3,'Removed via Fixed Cable page','2023-08-07','02:50:08','inventory'),(9,62,27,'remove',2,'Removed via Fixed Cable page','2023-08-07','02:50:09','inventory'),(10,62,27,'remove',1,'Removed via Fixed Cable page','2023-08-07','02:50:10','inventory'),(11,62,27,'remove',0,'Removed via Fixed Cable page','2023-08-07','02:50:11','inventory'),(12,62,27,'add',1,'Added via Fixed Cable page','2023-08-07','02:53:45','inventory'),(13,62,27,'add',2,'Added via Fixed Cable page','2023-08-07','02:53:47','inventory'),(14,62,27,'add',3,'Added via Fixed Cable page','2023-08-07','02:53:48','inventory'),(15,62,27,'add',4,'Added via Fixed Cable page','2023-08-07','02:53:50','inventory'),(16,44,9,'add',12,'Added via Fixed Cable page','2023-08-07','02:54:27','inventory'),(17,44,9,'remove',11,'Removed via Fixed Cable page','2023-08-07','03:11:58','inventory'),(18,44,9,'remove',10,'Removed via Fixed Cable page','2023-08-07','03:12:05','inventory'),(19,44,9,'add',11,'Added via Fixed Cable page','2023-08-07','03:12:08','inventory'),(20,44,9,'add',12,'Added via Fixed Cable page','2023-08-07','03:12:10','inventory'),(21,47,12,'remove',12,'Removed via Fixed Cable page','2023-08-07','03:12:25','inventory'),(22,47,12,'add',13,'Added via Fixed Cable page','2023-08-07','03:12:27','inventory'),(23,37,2,'remove',5,'Removed via Fixed Cable page','2023-08-07','03:37:34','inventory'),(24,37,2,'remove',4,'Removed via Fixed Cable page','2023-08-07','03:38:47','inventory'),(25,37,2,'remove',3,'Removed via Fixed Cable page','2023-08-07','03:39:49','inventory'),(26,37,2,'remove',2,'Removed via Fixed Cable page','2023-08-07','03:40:39','inventory'),(27,38,3,'remove',7,'Removed via Fixed Cable page','2023-08-07','03:40:57','inventory'),(28,38,3,'remove',6,'Removed via Fixed Cable page','2023-08-07','03:45:54','inventory'),(29,38,3,'remove',5,'Removed via Fixed Cable page','2023-08-07','03:52:35','inventory'),(30,38,3,'remove',4,'Removed via Fixed Cable page','2023-08-07','03:52:43','inventory'),(31,38,3,'remove',3,'Removed via Fixed Cable page','2023-08-07','03:53:54','inventory'),(32,67,32,'remove',6,'Removed via Fixed Cable page','2023-08-07','07:24:45','inventory'),(33,67,32,'remove',5,'Removed via Fixed Cable page','2023-08-07','07:24:53','inventory'),(34,44,9,'remove',11,'Removed via Fixed Cable page','2023-08-08','23:05:45','inventory'),(35,40,5,'add',2,'Added via Fixed Cable page','2023-08-14','11:52:41','inventory'),(36,40,5,'remove',1,'Removed via Fixed Cable page','2023-08-14','11:52:43','inventory'),(37,44,40,'add',12,'New Stock and Cable Item added','2023-08-15','03:47:49','inventory'),(38,78,41,'add',11,'New Stock and Cable Item added','2023-08-15','03:49:22','inventory'),(39,79,42,'add',1,'New Stock and Cable Item added','2023-08-15','04:14:58','inventory'),(40,80,43,'add',1,'New Stock and Cable Item added','2023-08-15','04:15:15','inventory'),(41,81,44,'add',18,'New Stock and Cable Item added','2023-08-15','04:16:41','inventory'),(42,42,7,'add',1,'Added via Fixed Cable page','2023-08-15','04:23:18','root'),(43,82,45,'add',5,'New Stock and Cable Item added','2023-08-15','05:52:58','root'),(44,79,42,'add',2,'Added via Fixed Cable page','2023-08-22','19:34:06','inventory'),(45,79,42,'add',3,'Added via Fixed Cable page','2023-08-22','19:34:09','inventory'),(46,79,42,'add',4,'Added via Fixed Cable page','2023-08-22','19:34:11','inventory'),(47,79,42,'remove',3,'Removed via Fixed Cable page','2023-08-22','19:34:13','inventory'),(48,79,42,'add',4,'Added via Fixed Cable page','2023-08-28','14:24:19','root'),(49,79,42,'add',5,'Added via Fixed Cable page','2023-08-28','14:26:48','root'),(50,46,11,'remove',11,'Removed via Fixed Cable page','2023-08-30','16:46:08','inventory'),(51,40,5,'remove',0,'Removed via Fixed Cable page','2023-09-04','23:36:52','inventory'),(52,40,5,'add',1,'Added via Fixed Cable page','2023-09-04','23:37:08','inventory');
/*!40000 ALTER TABLE `cable_transaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cable_types`
--

DROP TABLE IF EXISTS `cable_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cable_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `parent` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cable_types`
--

LOCK TABLES `cable_types` WRITE;
/*!40000 ALTER TABLE `cable_types` DISABLE KEYS */;
INSERT INTO `cable_types` VALUES (1,'Copper','Generic Copper Cable','Copper'),(2,'Cat5e','Cat5e Copper Cable','Copper'),(3,'Cat6','Cat6 Copper Cable','Copper'),(4,'Fibre','Generic Fibre Cable','Fibre'),(5,'SM LC-LC','Single Mode LC to LC Fibre Cable','Fibre'),(6,'SM SC-SC','Single Mode SC to SC Fibre Cable','Fibre'),(7,'SM LC-SC','Single Mode LC to SC Fibre Cable','Fibre'),(8,'MM LC-LC','Multi Mode LC to LC Fibre Cable','Fibre'),(9,'MM SC-SC','Multi Mode SC to SC Fibre Cable','Fibre'),(10,'MM LC-SC','Multi Mode LC to SC Fibre Cable','Fibre'),(11,'Power','Generic Power Cable','Power'),(12,'Other','Other Generic Cable','Other');
/*!40000 ALTER TABLE `cable_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `changelog`
--

DROP TABLE IF EXISTS `changelog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `changelog` (
  `id` int NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `user_id` int NOT NULL,
  `user_username` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `action` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `table_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `record_id` int DEFAULT NULL,
  `field_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `value_old` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `value_new` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `changelog`
--

LOCK TABLES `changelog` WRITE;
/*!40000 ALTER TABLE `changelog` DISABLE KEYS */;
INSERT INTO `changelog` VALUES (1,'2023-08-28 13:49:33',2,'inventory','Remove quantity','item',65,'quantity','95',NULL),(2,'2023-08-28 13:50:44',2,'inventory','Remove quantity','item',65,'quantity','94',NULL),(3,'2023-08-28 13:51:49',2,'inventory','Remove quantity','item',65,'quantity','93',NULL),(4,'2023-08-28 13:56:20',2,'inventory','Remove quantity','item',65,'quantity','92',NULL),(5,'2023-08-28 13:56:20',2,'inventory','Add quantity','item',124,'quantity','3',NULL),(6,'2023-08-28 14:24:20',0,'root','Add quantity','cable_item',42,'quantity','3',NULL),(7,'2023-08-28 14:26:48',0,'root','Add quantity','cable_item',42,'quantity','4','5'),(8,'2023-08-28 17:51:12',0,'root','Update record','users',0,'last_name','roott','root'),(9,'2023-08-30 16:46:08',2,'inventory','Remove Quantity','cable_item',11,'quantity','12','11'),(10,'2023-08-30 17:14:32',2,'inventory','New record','item',125,'stock_id',NULL,'1'),(11,'2023-08-30 17:49:50',2,'inventory','New record','item',126,'stock_id',NULL,'4'),(12,'2023-08-30 17:50:37',2,'inventory','New record','item',127,'stock_id',NULL,'4'),(13,'2023-08-30 18:00:52',2,'inventory','New record','item',128,'stock_id',NULL,'4'),(14,'2023-08-30 18:18:01',2,'inventory','Remove quantity','item',126,'quantity','1','0'),(15,'2023-08-30 18:18:02',2,'inventory','Delete record','item',126,'stock_id','4',NULL),(16,'2023-08-30 18:25:06',2,'inventory','Remove quantity','item',127,'quantity','1','0'),(17,'2023-08-30 18:25:06',2,'inventory','Delete record','item',127,'stock_id','4',NULL),(18,'2023-08-30 18:26:11',2,'inventory','Remove quantity','item',4,'quantity','1','0'),(19,'2023-08-30 18:26:12',2,'inventory','Delete record','item',4,'stock_id','4',NULL),(20,'2023-08-30 18:26:30',2,'inventory','Remove quantity','item',128,'quantity','1','0'),(21,'2023-08-30 18:26:30',2,'inventory','Delete record','item',128,'stock_id','4',NULL),(22,'2023-08-30 18:36:00',2,'inventory','New record','item',129,'stock_id',NULL,'1'),(23,'2023-09-01 13:49:26',2,'inventory','New record','item',130,'stock_id',NULL,'4'),(24,'2023-09-01 13:49:26',2,'inventory','New record','item',131,'stock_id',NULL,'4'),(25,'2023-09-01 13:49:27',2,'inventory','New record','item',132,'stock_id',NULL,'4'),(26,'2023-09-01 13:49:27',2,'inventory','New record','item',133,'stock_id',NULL,'4'),(27,'2023-09-01 13:49:27',2,'inventory','New record','item',134,'stock_id',NULL,'4'),(28,'2023-09-01 14:05:30',2,'inventory','New record','item',135,'stock_id',NULL,'4'),(29,'2023-09-01 14:08:13',2,'inventory','New record','item',136,'stock_id',NULL,'4'),(30,'2023-09-01 14:08:13',2,'inventory','New record','item',137,'stock_id',NULL,'4'),(31,'2023-09-01 14:08:13',2,'inventory','New record','item',138,'stock_id',NULL,'4'),(32,'2023-09-01 14:08:13',2,'inventory','New record','item',139,'stock_id',NULL,'4'),(33,'2023-09-02 18:41:51',2,'inventory','Remove quantity','item',130,'quantity','1','0'),(34,'2023-09-02 18:41:52',2,'inventory','Delete record','item',130,'stock_id','4',NULL),(35,'2023-09-02 18:45:37',2,'inventory','Remove quantity','item',131,'quantity','1','0'),(36,'2023-09-02 18:45:37',2,'inventory','Delete record','item',131,'stock_id','4',NULL),(37,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',1,'quantity','1','0'),(38,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',64,'quantity','1','0'),(39,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',65,'quantity','1','0'),(40,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',67,'quantity','1','0'),(41,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',71,'quantity','1','0'),(42,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',72,'quantity','1','0'),(43,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',73,'quantity','1','0'),(44,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',74,'quantity','1','0'),(45,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',124,'quantity','1','0'),(46,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',125,'quantity','1','0'),(47,'2023-09-02 20:25:16',2,'inventory','Remove quantity','item',129,'quantity','1','0'),(48,'2023-09-02 20:28:11',2,'inventory','Remove quantity','item',208,'quantity','1','0'),(49,'2023-09-02 20:31:05',2,'inventory','Remove quantity','item',270,'quantity','1','0'),(50,'2023-09-02 20:33:41',2,'inventory','Remove quantity','item',271,'quantity','1','0'),(51,'2023-09-02 20:35:53',2,'inventory','Remove quantity','item',272,'quantity','1','0'),(52,'2023-09-02 20:35:53',2,'inventory','Remove quantity','item',273,'quantity','1','0'),(53,'2023-09-02 20:35:53',2,'inventory','Remove quantity','item',274,'quantity','1','0'),(54,'2023-09-02 20:35:53',2,'inventory','Remove quantity','item',275,'quantity','1','0'),(55,'2023-09-02 20:35:53',2,'inventory','Remove quantity','item',276,'quantity','1','0'),(56,'2023-09-02 20:35:53',2,'inventory','Remove quantity','item',277,'quantity','1','0'),(57,'2023-09-02 20:35:53',2,'inventory','Remove quantity','item',278,'quantity','1','0'),(58,'2023-09-02 20:37:13',2,'inventory','Remove quantity','item',279,'quantity','1','0'),(59,'2023-09-02 20:37:13',2,'inventory','Remove quantity','item',280,'quantity','1','0'),(60,'2023-09-02 20:37:13',2,'inventory','Remove quantity','item',281,'quantity','1','0'),(61,'2023-09-02 20:37:13',2,'inventory','Remove quantity','item',282,'quantity','1','0'),(62,'2023-09-02 20:37:13',2,'inventory','Remove quantity','item',283,'quantity','1','0'),(63,'2023-09-02 20:37:13',2,'inventory','Remove quantity','item',284,'quantity','1','0'),(64,'2023-09-02 20:37:13',2,'inventory','Remove quantity','item',285,'quantity','1','0'),(65,'2023-09-04 00:04:17',2,'inventory','Remove quantity','item',132,'quantity','1','0'),(66,'2023-09-04 00:04:17',2,'inventory','Add quantity','item',394,'quantity',NULL,'1'),(67,'2023-09-04 00:04:17',2,'inventory','Remove quantity','item',133,'quantity','1','0'),(68,'2023-09-04 00:04:17',2,'inventory','Add quantity','item',395,'quantity',NULL,'1'),(69,'2023-09-04 00:07:47',2,'inventory','Remove quantity','item',394,'quantity','1','0'),(70,'2023-09-04 00:07:47',2,'inventory','Add quantity','item',396,'quantity',NULL,'1'),(71,'2023-09-04 00:07:47',2,'inventory','Remove quantity','item',395,'quantity','1','0'),(72,'2023-09-04 00:07:47',2,'inventory','Add quantity','item',397,'quantity',NULL,'1'),(73,'2023-09-04 00:07:47',2,'inventory','Remove quantity','item',NULL,'quantity','1','0'),(74,'2023-09-04 00:11:20',2,'inventory','Remove quantity','item',134,'quantity','1','0'),(75,'2023-09-04 00:11:20',2,'inventory','Add quantity','item',398,'quantity',NULL,'1'),(76,'2023-09-04 00:11:20',2,'inventory','Remove quantity','item',138,'quantity','1','0'),(77,'2023-09-04 00:11:20',2,'inventory','Add quantity','item',399,'quantity',NULL,'1'),(78,'2023-09-04 00:11:20',2,'inventory','Remove quantity','item',139,'quantity','1','0'),(79,'2023-09-04 00:11:20',2,'inventory','Add quantity','item',400,'quantity',NULL,'1'),(80,'2023-09-04 00:11:35',2,'inventory','Remove quantity','item',398,'quantity','1','0'),(81,'2023-09-04 00:11:35',2,'inventory','Add quantity','item',401,'quantity',NULL,'1'),(82,'2023-09-04 00:11:35',2,'inventory','Remove quantity','item',399,'quantity','1','0'),(83,'2023-09-04 00:11:35',2,'inventory','Add quantity','item',402,'quantity',NULL,'1'),(84,'2023-09-04 00:11:35',2,'inventory','Remove quantity','item',400,'quantity','1','0'),(85,'2023-09-04 00:11:35',2,'inventory','Add quantity','item',403,'quantity',NULL,'1'),(86,'2023-09-04 00:11:35',2,'inventory','Remove quantity','item',NULL,'quantity','1','0'),(87,'2023-09-04 22:37:10',2,'inventory','New record','stock_img',69,'image',NULL,'C13-C14.jpg'),(88,'2023-09-04 22:45:12',2,'inventory','Delete record','stock_img',69,'stock_id','86',NULL),(89,'2023-09-04 22:48:14',2,'inventory','New record','stock_img',70,'image',NULL,'C13-C14.jpg'),(90,'2023-09-04 22:54:10',2,'inventory','Delete record','stock_img',70,'stock_id','86',NULL),(91,'2023-09-04 23:36:52',2,'inventory','Remove Quantity','cable_item',5,'quantity','1','0'),(92,'2023-09-04 23:37:08',2,'inventory','Add Quantity','cable_item',5,'quantity','0','1'),(93,'2023-09-14 05:45:56',2,'inventory','New record','stock_img',71,'image',NULL,'MM-LCLC.jpg'),(94,'2023-09-14 05:46:00',2,'inventory','Delete record','stock_img',54,'stock_id','65',NULL),(95,'2023-09-16 05:35:29',2,'inventory','Update record','item',391,'upc','','aaa'),(96,'2023-09-16 05:35:29',2,'inventory','Update record','item',391,'serial_number','','bbb'),(97,'2023-09-16 05:35:29',2,'inventory','Update record','item',391,'cost','69','111'),(98,'2023-09-16 05:35:30',2,'inventory','Update record','item',391,'comments','','ccc'),(99,'2023-09-16 05:36:18',2,'inventory','Update record','item',391,'upc','aaa',''),(100,'2023-09-16 05:36:18',2,'inventory','Update record','item',391,'serial_number','bbb',''),(101,'2023-09-16 05:37:31',2,'inventory','Update record','item',391,'cost','111',''),(102,'2023-09-16 05:37:31',2,'inventory','Update record','item',391,'comments','ccc',''),(103,'2023-09-16 05:38:05',2,'inventory','Update record','item',391,'cost','0','69'),(104,'2023-09-16 05:38:15',2,'inventory','Update record','item',391,'upc','','aaa'),(105,'2023-09-16 05:38:16',2,'inventory','Update record','item',391,'serial_number','','bbb'),(106,'2023-09-16 05:38:16',2,'inventory','Update record','item',391,'cost','69','111'),(107,'2023-09-16 05:38:16',2,'inventory','Update record','item',391,'comments','','cccc'),(108,'2023-09-16 05:38:24',2,'inventory','Update record','item',391,'upc','aaa',''),(109,'2023-09-16 05:38:24',2,'inventory','Update record','item',391,'serial_number','bbb',''),(110,'2023-09-16 05:38:24',2,'inventory','Update record','item',391,'cost','111',''),(111,'2023-09-16 05:38:24',2,'inventory','Update record','item',391,'comments','cccc',''),(112,'2023-09-16 05:38:35',2,'inventory','Update record','item',391,'cost','0','69'),(113,'2023-09-16 06:07:05',2,'inventory','New record','shelf',18,'name',NULL,'deleteTest'),(114,'2023-09-16 06:09:41',2,'inventory','Delete record','shelf',18,'deleted','0','1');
/*!40000 ALTER TABLE `changelog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config` (
  `id` int NOT NULL AUTO_INCREMENT,
  `banner_color` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `logo_image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `favicon_image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_enabled` tinyint DEFAULT NULL,
  `ldap_username` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_password` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_domain` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_host` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_port` int DEFAULT NULL,
  `ldap_basedn` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_usergroup` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_userfilter` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `currency` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `sku_prefix` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_host` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_port` int DEFAULT NULL,
  `smtp_encryption` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_password` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_from_email` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_from_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_to_email` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_username` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `system_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_host_secondary` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `base_url` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_enabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES (1,'#6b2460','custom/100423064146-AR_Logo.png','custom/100423064146-AR_Logo.png',1,'ldapauth','RHJvcHNCdWlsZHNTa2lsbDEyISE=','ajrich.co.uk','10.0.2.5',389,'DC=ajrich,DC=co,DC=uk','cn=Users','(objectClass=User)','£','STOCK-','mail.ajrich.co.uk',587,'starttls','RGVtb1Bhc3MxIQ==','inventory@ajrich.co.uk','Inventory System','inventory@ajrich.co.uk','inventory@ajrich.co.uk','Inventory System','10.0.2.6','inventory.ajrich.co.uk',1);
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config_default`
--

DROP TABLE IF EXISTS `config_default`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `config_default` (
  `id` int NOT NULL AUTO_INCREMENT,
  `banner_color` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `logo_image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `favicon_image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ldap_enabled` tinyint DEFAULT NULL,
  `ldap_username` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_password` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_domain` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_host` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_port` int DEFAULT NULL,
  `ldap_basedn` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_usergroup` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_userfilter` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `currency` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `sku_prefix` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_host` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_port` int DEFAULT NULL,
  `smtp_encryption` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_password` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_from_email` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_from_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_to_email` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_username` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `system_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `ldap_host_secondary` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `base_url` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `smtp_enabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config_default`
--

LOCK TABLES `config_default` WRITE;
/*!40000 ALTER TABLE `config_default` DISABLE KEYS */;
INSERT INTO `config_default` VALUES (1,'#E1B12C','default/default-logo.png','default/default-favicon.png',1,'ldapauth','RHJvcHNCdWlsZHNTa2lsbDEyISE=','ajrich.co.uk','10.0.2.2',389,'DC=ajrich,DC=co,DC=uk','cn=Users','(objectClass=User)','£','ITEM-','mail.ajrich.co.uk',587,'starttls','RGVtb1Bhc3MxIQ==','inventory@ajrich.co.uk','Inventory System','inventory@ajrich.co.uk','inventory@ajrich.co.uk','Inventory System','10.0.2.2','inventory.ajrich.co.uk',0);
/*!40000 ALTER TABLE `config_default` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `stock_id` bigint NOT NULL,
  `upc` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `quantity` int NOT NULL DEFAULT '0',
  `cost` decimal(10,0) DEFAULT '0',
  `serial_number` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `comments` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `manufacturer_id` bigint DEFAULT NULL,
  `shelf_id` int NOT NULL DEFAULT '0',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=404 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item`
--

LOCK TABLES `item` WRITE;
/*!40000 ALTER TABLE `item` DISABLE KEYS */;
INSERT INTO `item` VALUES (1,1,'',0,60,'','',1,1,1),(3,3,'',1,70,'','Staff Rack',2,2,0),(8,18,'',1,0,'','',1,1,0),(9,18,'test-upoc-2',1,0,'test-sn','',1,1,0),(10,21,'upc',1,69,'sn','',1,1,0),(11,22,'upc',1,69,'sn','',1,1,0),(13,26,'',1,0,'','',NULL,1,0),(14,27,'',1,0,'','',NULL,2,0),(23,31,'',1,30,'','',5,3,0),(28,2,'',1,0,'','',2,3,0),(64,1,'',0,0,'','',1,9,1),(65,1,'',0,69,'','',1,7,1),(67,1,'',0,0,'','',1,8,1),(68,1,'',1,0,'testing','',1,1,0),(71,1,'',0,1,'','',1,3,1),(72,1,'',0,0,'','',1,3,1),(73,1,'',0,69,'','',1,10,1),(74,1,'',0,69,'','',1,2,1),(85,86,'',1,0,'','',1,1,0),(86,87,'',1,0,'','',1,1,0),(87,88,'',1,0,'','',1,1,0),(88,89,'',1,0,'','',1,1,0),(89,90,'',1,0,'','',1,1,0),(90,5,'',1,0,'','',1,1,0),(91,91,'',1,0,'','',1,17,0),(92,92,'',1,0,'','',1,17,0),(93,93,'',1,0,'','',1,17,0),(94,94,'',1,0,'','',1,17,0),(95,95,'',1,0,'','',1,17,0),(96,96,'',1,0,'','',1,17,0),(97,97,'',1,0,'','',1,17,0),(98,98,'',1,0,'','',1,17,0),(99,99,'',1,0,'','',1,17,0),(100,100,'',1,0,'','',1,17,0),(101,101,'',1,0,'','',1,17,0),(102,102,'',1,0,'','',1,17,0),(103,103,'',1,0,'','',1,17,0),(104,104,'',1,0,'','',1,17,0),(105,105,'',1,0,'','',1,17,0),(106,106,'',1,0,'','',1,17,0),(107,107,'',1,0,'','',1,17,0),(108,108,'',1,0,'','',1,17,0),(109,109,'',1,0,'','',1,17,0),(110,110,'',1,0,'','',1,17,0),(111,111,'',1,0,'','',1,17,0),(112,112,'',1,0,'','',1,17,0),(113,113,'',1,0,'','',1,17,0),(114,114,'',1,0,'','',1,17,0),(115,115,'',1,0,'','',1,17,0),(116,116,'',1,0,'','',1,17,0),(117,117,'',1,0,'','',1,17,0),(118,118,'',1,0,'','',1,17,0),(124,1,'',0,69,'','',1,17,1),(125,1,'',0,0,'','',3,4,1),(129,1,'',0,0,'','',1,11,1),(132,4,'',0,0,'','',2,17,1),(133,4,'',0,0,'','',2,17,1),(134,4,'',0,0,'','',2,17,1),(135,4,'',1,0,'testtest','',2,17,0),(136,4,'',1,0,'tests','',2,17,0),(137,4,'',1,0,'testtests','',2,17,0),(138,4,'',0,0,'','',2,17,1),(139,4,'',0,0,'','',2,17,1),(140,22,'upc',1,69,'','',1,1,0),(141,22,'upc',1,69,'','',1,1,0),(142,22,'upc',1,69,'','',1,1,0),(143,22,'upc',1,69,'','',1,1,0),(144,22,'upc',1,69,'','',1,1,0),(145,22,'upc',1,69,'','',1,1,0),(146,22,'upc',1,69,'','',1,1,0),(147,22,'upc',1,69,'','',1,1,0),(148,22,'upc',1,69,'','',1,1,0),(149,22,'upc',1,69,'','',1,1,0),(150,22,'upc',1,69,'','',1,1,0),(151,22,'upc',1,69,'','',1,1,0),(152,22,'upc',1,69,'','',1,1,0),(153,22,'upc',1,69,'','',1,1,0),(154,22,'upc',1,69,'','',1,1,0),(155,22,'upc',1,69,'','',1,1,0),(156,22,'upc',1,69,'','',1,1,0),(157,22,'upc',1,69,'','',1,1,0),(158,22,'upc',1,69,'','',1,1,0),(159,22,'upc',1,69,'','',1,1,0),(160,22,'upc',1,69,'','',1,1,0),(161,22,'upc',1,69,'','',1,1,0),(162,22,'upc',1,69,'','',1,1,0),(163,22,'upc',1,69,'','',1,1,0),(164,22,'upc',1,69,'','',1,1,0),(165,22,'upc',1,69,'','',1,1,0),(166,22,'upc',1,69,'','',1,1,0),(167,22,'upc',1,69,'','',1,1,0),(168,22,'upc',1,69,'','',1,1,0),(169,22,'upc',1,69,'','',1,1,0),(170,22,'upc',1,69,'','',1,1,0),(171,22,'upc',1,69,'','',1,1,0),(172,22,'upc',1,69,'','',1,1,0),(173,22,'upc',1,69,'','',1,1,0),(174,22,'upc',1,69,'','',1,1,0),(175,22,'upc',1,69,'','',1,1,0),(176,22,'upc',1,69,'','',1,1,0),(177,22,'upc',1,69,'','',1,1,0),(178,22,'upc',1,69,'','',1,1,0),(179,22,'upc',1,69,'','',1,1,0),(180,22,'upc',1,69,'','',1,1,0),(181,22,'upc',1,69,'','',1,1,0),(182,22,'upc',1,69,'','',1,1,0),(183,22,'upc',1,69,'','',1,1,0),(184,22,'upc',1,69,'','',1,1,0),(185,22,'upc',1,69,'','',1,1,0),(186,22,'upc',1,69,'','',1,1,0),(187,22,'upc',1,69,'','',1,1,0),(188,22,'upc',1,69,'','',1,1,0),(189,22,'upc',1,69,'','',1,1,0),(190,22,'upc',1,69,'','',1,1,0),(191,22,'upc',1,69,'','',1,1,0),(192,22,'upc',1,69,'','',1,1,0),(193,22,'upc',1,69,'','',1,1,0),(194,22,'upc',1,69,'','',1,1,0),(195,22,'upc',1,69,'','',1,1,0),(196,22,'upc',1,69,'','',1,1,0),(197,22,'upc',1,69,'','',1,1,0),(198,22,'upc',1,69,'','',1,1,0),(199,22,'upc',1,69,'','',1,1,0),(200,22,'upc',1,69,'','',1,1,0),(201,22,'upc',1,69,'','',1,1,0),(202,22,'upc',1,69,'','',1,1,0),(203,22,'upc',1,69,'','',1,1,0),(204,22,'upc',1,69,'','',1,1,0),(205,22,'upc',1,69,'','',1,1,0),(206,22,'upc',1,69,'','',1,1,0),(207,22,'upc',1,69,'','',1,1,0),(208,1,'',0,0,'','',1,9,1),(209,1,'',1,0,'','',1,9,0),(210,1,'',1,0,'','',1,9,0),(211,1,'',1,0,'','',1,9,0),(212,1,'',1,0,'','',1,9,0),(213,1,'',1,0,'','',1,9,0),(214,1,'',1,0,'','',1,9,0),(215,1,'',1,0,'','',1,9,0),(216,1,'',1,0,'','',1,9,0),(217,1,'',1,0,'','',1,9,0),(218,1,'',1,0,'','',1,9,0),(219,1,'',1,0,'','',1,9,0),(220,1,'',1,0,'','',1,9,0),(221,1,'',1,0,'','',1,9,0),(222,1,'',1,0,'','',1,9,0),(223,1,'',1,0,'','',1,9,0),(224,1,'',1,0,'','',1,9,0),(225,1,'',1,0,'','',1,9,0),(226,1,'',1,0,'','',1,9,0),(227,1,'',1,0,'','',1,9,0),(228,1,'',1,0,'','',1,9,0),(229,1,'',1,0,'','',1,9,0),(230,1,'',1,0,'','',1,9,0),(231,1,'',1,0,'','',1,9,0),(232,1,'',1,0,'','',1,9,0),(233,1,'',1,0,'','',1,9,0),(234,1,'',1,0,'','',1,9,0),(235,1,'',1,0,'','',1,9,0),(236,1,'',1,0,'','',1,9,0),(237,1,'',1,0,'','',1,9,0),(238,1,'',1,0,'','',1,9,0),(239,1,'',1,0,'','',1,9,0),(240,1,'',1,0,'','',1,9,0),(241,1,'',1,0,'','',1,9,0),(242,1,'',1,0,'','',1,9,0),(243,1,'',1,0,'','',1,9,0),(244,1,'',1,0,'','',1,9,0),(245,1,'',1,0,'','',1,9,0),(246,1,'',1,0,'','',1,9,0),(247,1,'',1,0,'','',1,9,0),(248,1,'',1,0,'','',1,9,0),(249,1,'',1,0,'','',1,9,0),(250,1,'',1,0,'','',1,9,0),(251,1,'',1,0,'','',1,9,0),(252,1,'',1,0,'','',1,9,0),(253,1,'',1,0,'','',1,9,0),(254,1,'',1,0,'','',1,9,0),(255,1,'',1,0,'','',1,9,0),(256,1,'',1,0,'','',1,9,0),(257,1,'',1,0,'','',1,9,0),(258,1,'',1,0,'','',1,9,0),(259,1,'',1,0,'','',1,9,0),(260,1,'',1,0,'','',1,9,0),(261,1,'',1,0,'','',1,9,0),(262,1,'',1,0,'','',1,9,0),(263,1,'',1,0,'','',1,9,0),(264,1,'',1,0,'','',1,9,0),(265,1,'',1,0,'','',1,9,0),(266,1,'',1,0,'','',1,9,0),(267,1,'',1,0,'','',1,9,0),(268,1,'',1,0,'','',1,9,0),(269,1,'',1,0,'','',1,9,0),(270,1,'',0,69,'','',1,7,1),(271,1,'',0,69,'','',1,7,1),(272,1,'',0,69,'','',1,7,1),(273,1,'',0,69,'','',1,7,1),(274,1,'',0,69,'','',1,7,1),(275,1,'',0,69,'','',1,7,1),(276,1,'',0,69,'','',1,7,1),(277,1,'',0,69,'','',1,7,1),(278,1,'',0,69,'','',1,7,1),(279,1,'',0,69,'','',1,7,1),(280,1,'',0,69,'','',1,7,1),(281,1,'',0,69,'','',1,7,1),(282,1,'',0,69,'','',1,7,1),(283,1,'',0,69,'','',1,7,1),(284,1,'',0,69,'','',1,7,1),(285,1,'',0,69,'','',1,7,1),(286,1,'',1,69,'','',1,7,0),(287,1,'',1,69,'','',1,7,0),(288,1,'',1,69,'','',1,7,0),(289,1,'',1,69,'','',1,7,0),(290,1,'',1,69,'','',1,7,0),(291,1,'',1,69,'','',1,7,0),(292,1,'',1,69,'','',1,7,0),(293,1,'',1,69,'','',1,7,0),(294,1,'',1,69,'','',1,7,0),(295,1,'',1,69,'','',1,7,0),(296,1,'',1,69,'','',1,7,0),(297,1,'',1,69,'','',1,7,0),(298,1,'',1,69,'','',1,7,0),(299,1,'',1,69,'','',1,7,0),(300,1,'',1,69,'','',1,7,0),(301,1,'',1,69,'','',1,7,0),(302,1,'',1,69,'','',1,7,0),(303,1,'',1,69,'','',1,7,0),(304,1,'',1,69,'','',1,7,0),(305,1,'',1,69,'','',1,7,0),(306,1,'',1,69,'','',1,7,0),(307,1,'',1,69,'','',1,7,0),(308,1,'',1,69,'','',1,7,0),(309,1,'',1,69,'','',1,7,0),(310,1,'',1,69,'','',1,7,0),(311,1,'',1,69,'','',1,7,0),(312,1,'',1,69,'','',1,7,0),(313,1,'',1,69,'','',1,7,0),(314,1,'',1,69,'','',1,7,0),(315,1,'',1,69,'','',1,7,0),(316,1,'',1,69,'','',1,7,0),(317,1,'',1,69,'','',1,7,0),(318,1,'',1,69,'','',1,7,0),(319,1,'',1,69,'','',1,7,0),(320,1,'',1,69,'','',1,7,0),(321,1,'',1,69,'','',1,7,0),(322,1,'',1,69,'','',1,7,0),(323,1,'',1,69,'','',1,7,0),(324,1,'',1,69,'','',1,7,0),(325,1,'',1,69,'','',1,7,0),(326,1,'',1,69,'','',1,7,0),(327,1,'',1,69,'','',1,7,0),(328,1,'',1,69,'','',1,7,0),(329,1,'',1,69,'','',1,7,0),(330,1,'',1,69,'','',1,7,0),(331,1,'',1,69,'','',1,7,0),(332,1,'',1,69,'','',1,7,0),(333,1,'',1,69,'','',1,7,0),(334,1,'',1,69,'','',1,7,0),(335,1,'',1,69,'','',1,7,0),(336,1,'',1,69,'','',1,7,0),(337,1,'',1,69,'','',1,7,0),(338,1,'',1,69,'','',1,7,0),(339,1,'',1,69,'','',1,7,0),(340,1,'',1,69,'','',1,7,0),(341,1,'',1,69,'','',1,7,0),(342,1,'',1,69,'','',1,7,0),(343,1,'',1,69,'','',1,7,0),(344,1,'',1,69,'','',1,7,0),(345,1,'',1,69,'','',1,7,0),(346,1,'',1,69,'','',1,7,0),(347,1,'',1,69,'','',1,7,0),(348,1,'',1,69,'','',1,7,0),(349,1,'',1,69,'','',1,7,0),(350,1,'',1,69,'','',1,7,0),(351,1,'',1,69,'','',1,7,0),(352,1,'',1,69,'','',1,7,0),(353,1,'',1,69,'','',1,7,0),(354,1,'',1,69,'','',1,7,0),(355,1,'',1,69,'','',1,7,0),(356,1,'',1,69,'','',1,7,0),(357,1,'',1,69,'','',1,7,0),(358,1,'',1,69,'','',1,7,0),(359,1,'',1,69,'','',1,7,0),(360,1,'',1,0,'','',1,8,0),(361,1,'',1,0,'','',1,8,0),(362,1,'',1,0,'','',1,8,0),(363,1,'',1,0,'','',1,8,0),(364,1,'',1,0,'','',1,8,0),(365,1,'',1,0,'','',1,8,0),(366,1,'',1,0,'','',1,8,0),(367,1,'',1,0,'','',1,8,0),(368,1,'',1,0,'','',1,8,0),(369,1,'',1,1,'','',1,3,0),(370,1,'',1,1,'','',1,3,0),(371,1,'',1,0,'','',1,3,0),(372,1,'',1,69,'','',1,10,0),(373,1,'',1,69,'','',1,10,0),(374,1,'',1,69,'','',1,10,0),(375,1,'',1,69,'','',1,10,0),(376,1,'',1,69,'','',1,10,0),(377,1,'',1,69,'','',1,10,0),(378,1,'',1,69,'','',1,10,0),(379,1,'',1,69,'','',1,10,0),(380,1,'',1,69,'','',1,10,0),(381,1,'',1,69,'','',1,10,0),(382,1,'',1,69,'','',1,10,0),(383,1,'',1,69,'','',1,10,0),(384,1,'',1,69,'','',1,10,0),(385,1,'',1,69,'','',1,10,0),(386,1,'',1,69,'','',1,10,0),(387,1,'',1,69,'','',1,10,0),(388,1,'',1,69,'','',1,10,0),(389,1,'',1,69,'','',1,10,0),(390,1,'',1,69,'','',1,2,0),(391,1,'',1,69,'','',1,17,0),(392,1,'',1,69,'','',1,17,0),(393,1,'',1,69,'','',1,17,0),(394,4,'',0,0,'','',2,10,1),(395,4,'',0,0,'','',2,10,1),(396,4,'',1,0,'','',2,17,0),(397,4,'',1,0,'','',2,17,0),(398,4,'',0,0,'','',2,4,1),(399,4,'',0,0,'','',2,4,1),(400,4,'',0,0,'','',2,4,1),(401,4,'',1,0,'','',2,17,0),(402,4,'',1,0,'','',2,17,0),(403,4,'',1,0,'','',2,17,0);
/*!40000 ALTER TABLE `item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `label`
--

DROP TABLE IF EXISTS `label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `label` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `label`
--

LOCK TABLES `label` WRITE;
/*!40000 ALTER TABLE `label` DISABLE KEYS */;
INSERT INTO `label` VALUES (1,'switch'),(2,'router'),(3,'cisco'),(4,'Watchguard'),(6,'test'),(7,'Dell'),(8,'HP'),(9,'Apple'),(10,'Samsung'),(11,'Supermicro');
/*!40000 ALTER TABLE `label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manufacturer`
--

DROP TABLE IF EXISTS `manufacturer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturer` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manufacturer`
--

LOCK TABLES `manufacturer` WRITE;
/*!40000 ALTER TABLE `manufacturer` DISABLE KEYS */;
INSERT INTO `manufacturer` VALUES (1,'Cisco'),(2,'Dell'),(3,'HP'),(4,'Cyberoam'),(5,'Watchguard'),(6,'Supermicro');
/*!40000 ALTER TABLE `manufacturer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset`
--

DROP TABLE IF EXISTS `password_reset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reset_user_id` int NOT NULL,
  `reset_selector` text NOT NULL,
  `reset_token` longtext NOT NULL,
  `reset_expires` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset`
--

LOCK TABLES `password_reset` WRITE;
/*!40000 ALTER TABLE `password_reset` DISABLE KEYS */;
INSERT INTO `password_reset` VALUES (24,0,'afe8b1598dac16ce','$2y$10$u81BBCknQLeGOhsMLSquZuF6qbv3IjvwzdJw2.hguPaX6zZkZ3alG','1693231190');
/*!40000 ALTER TABLE `password_reset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shelf`
--

DROP TABLE IF EXISTS `shelf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shelf` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `area_id` bigint NOT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shelf`
--

LOCK TABLES `shelf` WRITE;
/*!40000 ALTER TABLE `shelf` DISABLE KEYS */;
INSERT INTO `shelf` VALUES (1,'Shelf 1',1,0),(2,'Shelf 2',1,0),(3,'Shelf 3',1,0),(4,'Shelf A',2,0),(5,'Shelf B',2,0),(6,'Shelf C',2,0),(7,'Shelf 101',3,0),(8,'Shelf 102',3,0),(9,'Shelf 103',3,0),(10,'Shelf MMRA1',4,0),(11,'Shelf MMRA2',4,0),(12,'Shelf MMRA3',4,0),(17,'testShelf',5,0),(18,'deleteTest',5,1);
/*!40000 ALTER TABLE `shelf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site`
--

DROP TABLE IF EXISTS `site`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site`
--

LOCK TABLES `site` WRITE;
/*!40000 ALTER TABLE `site` DISABLE KEYS */;
INSERT INTO `site` VALUES (1,'CDC ME14','Custodian Data Centers, Maidstone',0),(2,'CDC DA2','Custodian Data Centers, Dartford',0),(4,'TestSite','Test Site for Testing',0);
/*!40000 ALTER TABLE `site` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock`
--

DROP TABLE IF EXISTS `stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `sku` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `min_stock` int DEFAULT '0',
  `is_cable` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock`
--

LOCK TABLES `stock` WRITE;
/*!40000 ALTER TABLE `stock` DISABLE KEYS */;
INSERT INTO `stock` VALUES (1,'Cisco C3650X','Cisco Switch test','STOCK-00001',0,0,0),(2,'Dell r710','Dell r710 Server','STOCK-00002',0,0,0),(3,'Dell r610','Dell r610 Server','STOCK-00003',0,0,0),(4,'Dell r420','Dell r420 Server','DELL-R420-01',0,0,0),(5,'HP GL360p','HP Server GL360p','STOCK-00004',1,0,0),(6,'Cyberoam CR 50ia','Cyberoam CR 50ia Firewall','STOCK-00005',0,0,0),(31,'Watchguard Firebox M200','Watchguard Firebox M200 Firewall','WG-Firebox-01',0,0,0),(36,'Cat5e Red 1m','1m Cat5e Red Copper Cable','CABLE-0001',10,1,0),(37,'Cat5e Red 2m','Red Cat5e Cable 2m Pre-terminated','CABLE-0002',10,1,0),(38,'Cat5e Red 3m','Red Cat5e Cable 3m Pre-terminated','CABLE-0003',10,1,0),(39,'Cat5e Red 5m','Red Cat5e Cable 5m Pre-terminated','CABLE-0004',10,1,0),(40,'Cat5e Green 1m','Green Cat5e Cable 1m Pre-terminated','CABLE-0005',10,1,0),(41,'Cat5e Green 2m','Green Cat5e Cable 2m Pre-terminated','CABLE-0006',10,1,0),(42,'Cat5e Green 3m','Green Cat5e Cable 3m Pre-terminated','CABLE-0007',10,1,0),(43,'Cat5e Green 5m','Green Cat5e Cable 5m Pre-terminated','CABLE-0008',10,1,0),(44,'Cat5e Blue 1m','Blue Cat5e Cable 1m Pre-terminated','CABLE-0009',10,1,0),(45,'Cat5e Blue 2m','Blue Cat5e Cable 2m Pre-terminated','CABLE-0010',10,1,0),(46,'Cat5e Blue 3m','Blue Cat5e Cable 3m Pre-terminated','CABLE-0011',10,1,0),(47,'Cat5e Blue 5m','Blue Cat5e Cable 5m Pre-terminated','CABLE-0012',10,1,0),(48,'SM LC-LC 0.5m','Single Mode LC to LC fibre, 0.5m','CABLE-0013',5,1,0),(49,'SM LC-LC 1m','Single Mode LC to LC fibre, 0.1m','CABLE-0014',5,1,0),(50,'SM LC-LC 2m','Single Mode LC to LC fibre, 2m','CABLE-0015',5,1,0),(51,'SM LC-LC 3m','Single Mode LC to LC fibre, 3m','CABLE-0016',5,1,0),(52,'SM LC-LC 5m','Single Mode LC to LC fibre, 5m','CABLE-0017',5,1,0),(53,'SM LC-LC 10m','Single Mode LC to LC fibre, 10m','CABLE-0018',5,1,0),(54,'SM LC-LC 15m','Single Mode LC to LC fibre, 15m','CABLE-0019',5,1,0),(55,'SM LC-LC 20m','Single Mode LC to LC fibre, 20m','CABLE-0020',5,1,0),(56,'SM LC-LC 25m','Single Mode LC to LC fibre, 25m','CABLE-0021',5,1,0),(57,'MM LC-LC 0.5m','Single Mode LC to LC fibre, 0.5m','CABLE-0022',5,1,0),(58,'MM LC-LC 1m','Single Mode LC to LC fibre, 1m','CABLE-0023',5,1,0),(59,'MM LC-LC 2m','Single Mode LC to LC fibre, 2m','CABLE-0024',5,1,0),(60,'MM LC-LC 3m','Single Mode LC to LC fibre, 3m','CABLE-0025',5,1,0),(61,'MM LC-LC 5m','Single Mode LC to LC fibre, 5m','CABLE-0026',5,1,0),(62,'MM LC-LC 10m','Single Mode LC to LC fibre, 10m','CABLE-0027',5,1,0),(63,'MM LC-LC 15m','Single Mode LC to LC fibre, 15m','CABLE-0028',5,1,0),(64,'MM LC-LC 20m','Single Mode LC to LC fibre, 20m','CABLE-0029',5,1,0),(65,'MM LC-LC 25m','Single Mode LC to LC fibre, 25m','CABLE-0030',5,1,0),(66,'C13 to UK 3-pin 2m','C13 to UK 3-pin plug power cable, 2m','CABLE-0031',5,1,0),(67,'C13 to C14 0.5m','C13 to C14 power cable, 0.5m','CABLE-0032',5,1,0),(68,'C13 to C14 1m','C13 to C14 power cable, 1m','CABLE-0033',5,1,0),(69,'C13 to C14 2m','C13 to C14 power cable, 2m','CABLE-0034',5,1,0),(70,'C13 to C14 3m','C13 to C14 power cable, 3m','CABLE-0035',5,1,0),(71,'C13 to C14 5m','C13 to C14 power cable, 5m','CABLE-0036',5,1,0),(72,'C19 to C20 1m','C19 to C20 power cable, 1m','CABLE-0037',5,1,0),(73,'C19 to C20 2m','C19 to C20 power cable, 2m','CABLE-0038',5,1,0),(74,'C15 to UK 3-pin 2m','C19 to UK 3-pin power cable, 2m','CABLE-0038',5,1,0),(78,'Cat5e Black 1m','Black Cat5e Cable 1m Pre-terminated','CABLE-0039',10,1,0),(79,'Cat5e Black 2m','Black Cat5e Cable 2m Pre-terminated','CABLE-0040',10,1,0),(80,'Cat5e Black 3m','Black Cat5e Cable 3m Pre-terminated','CABLE-0041',10,1,0),(81,'Cat5e Black 5m','Black Cat5e Cable 5m Pre-terminated','CABLE-0042',10,1,0),(82,'C13 to C14 Y-Splitter 1m','C13 to C14 x2 Y-Splitter 1m Power Cable','CABLE-0043',5,1,0),(86,'paginationTest01','','STOCK-00006',0,0,0),(87,'paginationTest02','','STOCK-00007',0,0,0),(88,'paginationTest03','','STOCK-00008',0,0,0),(89,'paginationTest04','','STOCK-00009',0,0,0),(90,'paginationTest05','','STOCK-00010',0,0,0),(118,'SKU Test','used to test SKU increase','STOCK-00011',0,0,0);
/*!40000 ALTER TABLE `stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_img`
--

DROP TABLE IF EXISTS `stock_img`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_img` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `stock_id` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_img`
--

LOCK TABLES `stock_img` WRITE;
/*!40000 ALTER TABLE `stock_img` DISABLE KEYS */;
INSERT INTO `stock_img` VALUES (1,'1','stock-1-202304080200.jpg'),(2,'1','stock-1-202304080202.jpg'),(14,'2','stock-2-202304200141.jpg'),(15,'3','stock-3-202304200140.png'),(16,'4','stock-4-202304200139.jpg'),(17,'6','stock-6-202304200138.jpg'),(20,'31','stock-31-202305120648.jpeg'),(24,'31','stock-31-img-160523122013.jpg'),(25,'36','cat5e-red.jpg'),(26,'37','cat5e-red.jpg'),(27,'38','cat5e-red.jpg'),(28,'39','cat5e-red.jpg'),(29,'40','cat5e-green.jpg'),(30,'41','cat5e-green.jpg'),(31,'42','cat5e-green.jpg'),(32,'43','cat5e-green.jpg'),(33,'44','cat5e-blue.jpg'),(34,'45','cat5e-blue.jpg'),(35,'46','cat5e-blue.jpg'),(36,'47','cat5e-blue.jpg'),(37,'48','SM-LCLC.jpg'),(38,'49','SM-LCLC.jpg'),(39,'50','SM-LCLC.jpg'),(40,'51','SM-LCLC.jpg'),(41,'52','SM-LCLC.jpg'),(42,'53','SM-LCLC.jpg'),(43,'54','SM-LCLC.jpg'),(44,'55','SM-LCLC.jpg'),(45,'56','SM-LCLC.jpg'),(46,'57','MM-LCLC.jpg'),(47,'58','MM-LCLC.jpg'),(48,'59','MM-LCLC.jpg'),(49,'60','MM-LCLC.jpg'),(50,'61','MM-LCLC.jpg'),(51,'62','MM-LCLC.jpg'),(52,'63','MM-LCLC.jpg'),(53,'64','MM-LCLC.jpg'),(55,'66','C13-UK3pin.jpg'),(56,'67','C13-C14.jpg'),(57,'68','C13-C14.jpg'),(58,'69','C13-C14.jpg'),(59,'70','C13-C14.jpg'),(60,'71','C13-C14.jpg'),(61,'72','C19-C20.jpg'),(62,'73','C19-C20.jpg'),(63,'74','C15-UK3pin.jpg'),(64,'78','cat5e-black.jpg'),(65,'79','cat5e-black.jpg'),(66,'80','cat5e-black.jpg'),(67,'81','cat5e-black.jpg'),(68,'82','stock-82-img-150823055258.jpg'),(71,'65','MM-LCLC.jpg');
/*!40000 ALTER TABLE `stock_img` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_label`
--

DROP TABLE IF EXISTS `stock_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_label` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `stock_id` bigint NOT NULL,
  `label_id` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_label`
--

LOCK TABLES `stock_label` WRITE;
/*!40000 ALTER TABLE `stock_label` DISABLE KEYS */;
INSERT INTO `stock_label` VALUES (86,31,2),(87,31,4),(108,32,6),(109,33,6),(110,34,6),(111,35,2),(130,1,1),(131,1,3),(132,86,6),(133,87,NULL),(134,88,NULL),(135,89,NULL),(136,90,NULL),(137,91,6),(138,92,6),(139,93,6),(140,94,6),(141,95,6),(142,96,6),(143,97,6),(144,98,6),(145,99,6),(146,100,6),(147,101,6),(148,102,6),(149,103,6),(150,104,6),(151,105,6),(152,106,6),(153,107,6),(154,108,6),(155,109,6),(156,110,6),(157,111,6),(158,112,6),(159,113,6),(160,114,6),(161,115,6),(162,116,6),(163,117,6),(164,118,6);
/*!40000 ALTER TABLE `stock_label` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaction` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `stock_id` bigint NOT NULL,
  `item_id` bigint NOT NULL,
  `type` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  `serial_number` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `reason` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `comments` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `username` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `shelf_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=372 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction`
--

LOCK TABLES `transaction` WRITE;
/*!40000 ALTER TABLE `transaction` DISABLE KEYS */;
INSERT INTO `transaction` VALUES (1,1,7,'add',100,NULL,'','New stock','First transaction','2023-04-09','05:56:00','inventory',0),(6,1,12,'add',7,69,'','New Stock',NULL,'2023-04-16','16:59:53','inventory',0),(9,1,15,'add',1,0,'','New Stock',NULL,'2023-05-01','13:31:38','inventory',0),(10,28,16,'add',1,69,'q','New Stock',NULL,'2023-05-01','13:32:43','inventory',0),(11,29,17,'add',1,0,'test','New Stock',NULL,'2023-05-01','15:07:51','inventory',0),(12,29,18,'add',1,0,'','New Stock',NULL,'2023-05-01','15:24:20','inventory',0),(13,29,0,'delete',0,0,'','No Stock Remaining, Deleted.',NULL,'2023-05-01','15:28:11','inventory',0),(14,30,19,'add',1,69,'test','New Stock',NULL,'2023-05-01','15:28:55','inventory',0),(15,30,19,'remove',1,NULL,'test','test',NULL,'2023-05-01','15:29:07','inventory',0),(16,30,20,'add',1,0,'1','New Stock',NULL,'2023-05-01','15:34:50','inventory',0),(17,30,21,'add',1,0,'','New Stock',NULL,'2023-05-01','15:35:27','inventory',0),(18,30,20,'remove',4,NULL,'','1',NULL,'2023-05-01','15:38:06','inventory',0),(19,30,0,'delete',-2,0,'','No Stock Remaining, Deleted.',NULL,'2023-05-01','15:38:50','inventory',0),(20,1,22,'add',1,0,'test','New Stock',NULL,'2023-05-01','17:55:51','inventory',0),(21,1,22,'remove',1,NULL,'test','test sn removal',NULL,'2023-05-01','17:58:13','inventory',0),(22,31,23,'add',1,30,'','New Stock',NULL,'2023-05-12','06:47:39','inventory',0),(23,6,5,'remove',1,NULL,'','test 0 stock',NULL,'2023-05-16','14:11:57','inventory',0),(24,6,24,'add',1,0,'','New Stock',NULL,'2023-05-16','14:14:29','inventory',0),(25,6,24,'remove',1,NULL,'','test',NULL,'2023-05-16','14:20:51','inventory',0),(26,6,25,'add',1,0,'','New Stock',NULL,'2023-05-16','14:21:45','inventory',0),(27,5,26,'add',1,0,'','New Stock',NULL,'2023-05-16','14:23:07','inventory',0),(28,6,25,'remove',1,NULL,'','test',NULL,'2023-05-16','14:23:23','inventory',0),(29,5,26,'remove',1,NULL,'','1',NULL,'2023-05-16','14:33:10','inventory',0),(30,2,2,'remove',1,NULL,'','test neg q',NULL,'2023-05-16','14:40:07','inventory',0),(31,2,27,'add',1,0,'','New Stock',NULL,'2023-05-16','14:40:51','inventory',0),(32,2,27,'remove',1,NULL,'','1',NULL,'2023-05-16','14:44:25','inventory',0),(33,2,28,'add',1,0,'','New Stock',NULL,'2023-05-16','14:46:21','inventory',0),(34,1,29,'add',12,12,'78156','New Stock',NULL,'2023-05-16','21:50:35','inventory',0),(35,31,30,'add',1,0,'','New Stock',NULL,'2023-05-16','21:50:53','inventory',0),(36,31,31,'add',1,0,'','New Stock',NULL,'2023-05-16','21:51:02','inventory',0),(37,1,32,'add',2,0,'','New Stock',NULL,'2023-05-17','08:10:09','inventory',0),(38,1,33,'add',5,0,'','New Stock',NULL,'2023-05-18','14:13:44','inventory',0),(39,1,1,'remove',-1,NULL,'','test neg quantity transaction',NULL,'2023-05-18','16:42:48','inventory',0),(40,1,33,'remove',-1,NULL,'','111',NULL,'2023-06-20','13:20:22','inventory',0),(41,1,33,'remove',-1,NULL,'','1 test 20-06-23 13:21',NULL,'2023-06-20','13:21:56','inventory',0),(42,1,33,'remove',-1,NULL,'','1 test 20-06-23 13:21',NULL,'2023-06-20','13:25:55','inventory',0),(43,1,33,'remove',-1,NULL,'','1',NULL,'2023-06-20','13:26:09','inventory',0),(44,1,33,'remove',-1,NULL,'','1',NULL,'2023-06-20','13:26:50','inventory',0),(45,1,12,'remove',-1,NULL,'','1',NULL,'2023-06-20','13:34:36','inventory',0),(46,1,12,'remove',-1,NULL,'','1',NULL,'2023-06-20','13:35:07','inventory',0),(47,1,12,'remove',-1,NULL,'','1',NULL,'2023-06-20','13:35:15','inventory',0),(48,1,12,'remove',-1,NULL,'','1',NULL,'2023-06-20','13:37:49','inventory',0),(49,1,12,'remove',-1,NULL,'','1',NULL,'2023-06-20','13:38:56','inventory',0),(50,1,12,'remove',-1,NULL,'','1',NULL,'2023-06-20','13:40:04','inventory',0),(51,1,7,'remove',-1,NULL,'','1',NULL,'2023-06-20','13:43:10','inventory',0),(52,1,34,'add',1,0,'example','New Stock',NULL,'2023-06-20','19:32:57','inventory',0),(53,1,35,'add',2,0,'example2 example3','New Stock',NULL,'2023-06-20','19:33:33','inventory',0),(54,1,36,'add',2,0,'example4, example5','New Stock',NULL,'2023-06-20','19:34:00','inventory',0),(55,1,7,'remove',-1,0,'','Move Stock',NULL,'2023-06-22','18:24:21','inventory',0),(56,1,1,'add',1,0,'','Move Stock',NULL,'2023-06-22','18:24:21','inventory',0),(57,1,7,'remove',-1,0,'','Move Stock',NULL,'2023-06-22','18:28:21','inventory',0),(58,1,1,'add',1,0,'','Move Stock',NULL,'2023-06-22','18:28:21','inventory',0),(59,1,7,'remove',-1,0,'','Move Stock',NULL,'2023-06-22','18:29:26','inventory',0),(60,1,1,'add',1,0,'','Move Stock',NULL,'2023-06-22','18:29:26','inventory',0),(61,1,7,'remove',-1,0,'','Move Stock',NULL,'2023-06-22','18:31:08','inventory',0),(62,1,1,'add',1,0,'','Move Stock',NULL,'2023-06-22','18:31:08','inventory',0),(63,1,7,'remove',-1,0,'','Move Stock',NULL,'2023-06-22','18:33:44','inventory',0),(64,1,1,'add',1,0,'','Move Stock',NULL,'2023-06-22','18:33:44','inventory',0),(65,1,7,'remove',-1,0,'','Move Stock',NULL,'2023-06-22','18:34:16','inventory',0),(66,1,1,'add',1,0,'','Move Stock',NULL,'2023-06-22','18:34:16','inventory',0),(67,1,7,'remove',-1,0,'','Move Stock',NULL,'2023-06-22','18:35:51','inventory',0),(68,1,1,'add',1,0,'','Move Stock',NULL,'2023-06-22','18:35:51','inventory',0),(69,1,7,'remove',-1,0,'','Move Stock',NULL,'2023-06-22','18:37:00','inventory',0),(70,1,1,'add',1,0,'','Move Stock',NULL,'2023-06-22','18:37:00','inventory',0),(71,1,7,'remove',-1,0,'','Move Stock',NULL,'2023-06-22','18:39:12','inventory',0),(72,1,1,'add',1,0,'','Move Stock',NULL,'2023-06-22','18:39:12','inventory',0),(73,1,7,'remove',-1,0,'','Move Stock',NULL,'2023-06-22','18:41:23','inventory',0),(74,1,1,'add',1,0,'','Move Stock',NULL,'2023-06-22','18:41:23','inventory',0),(75,1,7,'move',-1,0,'','Move Stock',NULL,'2023-06-22','18:48:09','inventory',0),(76,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-22','18:48:09','inventory',0),(77,1,12,'move',-1,0,'','Move Stock',NULL,'2023-06-22','18:52:16','inventory',0),(78,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-22','18:52:16','inventory',0),(79,1,6,'move',-10,0,'','Move Stock',NULL,'2023-06-22','18:52:27','inventory',0),(80,1,1,'move',10,0,'','Move Stock',NULL,'2023-06-22','18:52:27','inventory',0),(81,1,36,'move',-1,0,'example4, example5','Move Stock',NULL,'2023-06-23','19:39:15','inventory',0),(82,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-23','19:39:15','inventory',0),(83,1,35,'move',-1,0,'example2 example3','Move Stock',NULL,'2023-06-23','19:55:44','inventory',0),(84,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-23','19:55:44','inventory',0),(85,1,7,'move',-1,0,'','Move Stock',NULL,'2023-06-23','19:56:54','inventory',0),(86,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-23','19:56:54','inventory',0),(87,1,7,'move',-1,0,'','Move Stock',NULL,'2023-06-23','19:57:03','inventory',0),(88,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-23','19:57:03','inventory',0),(89,1,7,'move',-1,0,'','Move Stock',NULL,'2023-06-23','19:57:11','inventory',0),(90,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-23','19:57:11','inventory',0),(91,1,37,'move',-1,0,'example2 example3','Move Stock',NULL,'2023-06-23','19:57:25','inventory',0),(92,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-23','19:57:25','inventory',0),(93,1,34,'move',-1,0,'example','Move Stock',NULL,'2023-06-23','19:59:56','inventory',0),(94,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-23','19:59:56','inventory',0),(95,1,38,'add',1,0,'exampleSN','New Stock',NULL,'2023-06-23','20:01:38','inventory',0),(96,1,39,'add',1,0,'exampleSN','Move Stock',NULL,'2023-06-23','20:03:35','inventory',0),(97,1,38,'move',-1,0,NULL,'Move Stock',NULL,'2023-06-23','20:03:35','inventory',0),(98,1,40,'add',1,0,'78156','Move Stock',NULL,'2023-06-23','20:03:47','inventory',0),(99,1,29,'move',-1,0,NULL,'Move Stock',NULL,'2023-06-23','20:03:47','inventory',0),(100,1,41,'add',12,0,'testtest','New Stock',NULL,'2023-06-23','20:05:41','inventory',0),(101,1,42,'add',1,0,'testtest','Move Stock',NULL,'2023-06-23','20:05:52','inventory',0),(102,1,41,'move',-1,0,'testtest','Move Stock',NULL,'2023-06-23','20:05:52','inventory',0),(103,1,43,'add',12,0,'2q3erfsdx','New Stock',NULL,'2023-06-23','20:10:17','inventory',0),(104,1,44,'add',1,0,'2q3erfsdx','Move Stock',NULL,'2023-06-23','20:10:25','inventory',0),(105,1,43,'move',-1,0,'2q3erfsdx','Move Stock',NULL,'2023-06-23','20:10:25','inventory',0),(106,1,45,'add',1,0,'123123123123123','New Stock',NULL,'2023-06-23','20:32:12','inventory',0),(107,1,46,'move',1,0,'123123123123123','Move Stock',NULL,'2023-06-23','20:32:20','inventory',0),(108,1,45,'move',-1,0,'123123123123123','Move Stock',NULL,'2023-06-23','20:32:20','inventory',0),(109,1,47,'add',12,0,'24564567','New Stock',NULL,'2023-06-23','20:32:42','inventory',0),(110,1,48,'move',1,0,'24564567','Move Stock',NULL,'2023-06-23','20:32:50','inventory',0),(111,1,47,'move',-1,0,'24564567','Move Stock',NULL,'2023-06-23','20:32:50','inventory',0),(112,1,49,'add',12,0,'opopopop','New Stock',NULL,'2023-06-23','20:33:54','inventory',0),(113,1,50,'move',1,0,'opopopop','Move Stock',NULL,'2023-06-23','20:34:01','inventory',0),(114,1,49,'move',-1,0,'opopopop','Move Stock',NULL,'2023-06-23','20:34:01','inventory',0),(115,1,51,'add',12,0,'tp','New Stock',NULL,'2023-06-23','20:36:34','inventory',0),(116,1,52,'move',1,0,'tp','Move Stock',NULL,'2023-06-23','20:36:42','inventory',0),(117,1,51,'move',-1,0,'tp','Move Stock',NULL,'2023-06-23','20:36:42','inventory',0),(118,1,53,'add',100,0,'nhnhnhnh','New Stock',NULL,'2023-06-23','20:41:32','inventory',0),(119,1,54,'move',1,0,'nhnhnhnh','Move Stock',NULL,'2023-06-23','20:41:41','inventory',0),(120,1,53,'move',-1,0,'nhnhnhnh','Move Stock',NULL,'2023-06-23','20:41:41','inventory',0),(121,1,55,'add',111,0,'deeznuts','New Stock',NULL,'2023-06-23','20:42:52','inventory',0),(122,1,56,'move',1,0,'deeznuts','Move Stock',NULL,'2023-06-23','20:43:31','inventory',0),(123,1,55,'move',-1,0,'deeznuts','Move Stock',NULL,'2023-06-23','20:43:31','inventory',0),(124,1,57,'add',999,0,'candice','New Stock',NULL,'2023-06-23','20:47:50','inventory',0),(125,1,58,'move',1,0,'candice','Move Stock',NULL,'2023-06-23','20:48:02','inventory',0),(126,1,57,'move',-1,0,'candice','Move Stock',NULL,'2023-06-23','20:48:02','inventory',0),(127,1,59,'add',111,0,'banana','New Stock',NULL,'2023-06-23','23:06:15','inventory',0),(128,1,60,'move',1,0,'banana','Move Stock',NULL,'2023-06-23','23:07:22','inventory',0),(129,1,59,'move',-1,0,'banana','Move Stock',NULL,'2023-06-23','23:07:22','inventory',0),(130,1,61,'add',121,0,'int','New Stock',NULL,'2023-06-23','23:15:10','inventory',0),(131,1,62,'move',1,0,'int','Move Stock',NULL,'2023-06-23','23:15:18','inventory',0),(132,1,61,'move',-1,0,'int','Move Stock',NULL,'2023-06-23','23:15:18','inventory',0),(133,1,63,'move',1,0,'','Move Stock',NULL,'2023-06-23','23:18:12','inventory',0),(134,1,7,'move',-1,0,'','Move Stock',NULL,'2023-06-23','23:18:12','inventory',0),(135,1,64,'add',69,0,'','New Stock',NULL,'2023-06-23','23:19:33','inventory',0),(136,1,64,'move',-1,0,'','Move Stock',NULL,'2023-06-23','23:19:44','inventory',0),(137,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-23','23:19:44','inventory',0),(138,1,64,'move',-1,0,'','Move Stock',NULL,'2023-06-23','23:22:31','inventory',0),(139,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-23','23:22:31','inventory',0),(140,1,65,'add',131,69,'DNJM','New Stock',NULL,'2023-06-23','23:23:06','inventory',0),(141,1,66,'move',1,0,'DNJM','Move Stock',NULL,'2023-06-23','23:23:19','inventory',0),(142,1,65,'move',-1,0,'DNJM','Move Stock',NULL,'2023-06-23','23:23:19','inventory',0),(143,1,67,'add',10,0,'testing','New Stock',NULL,'2023-06-24','11:24:39','inventory',0),(144,1,68,'move',1,0,'testing','Move Stock',NULL,'2023-06-25','10:50:55','inventory',0),(145,1,67,'move',-1,0,'testing','Move Stock',NULL,'2023-06-25','10:50:55','inventory',0),(146,1,65,'move',-1,0,'','Move Stock',NULL,'2023-06-25','17:03:14','inventory',0),(147,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-25','17:03:14','inventory',0),(148,1,65,'move',-1,0,'','Move Stock',NULL,'2023-06-25','17:11:31','inventory',0),(149,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-25','17:11:31','inventory',0),(150,1,65,'move',-1,0,'','Move Stock',NULL,'2023-06-25','17:11:41','inventory',0),(151,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-25','17:11:41','inventory',0),(152,1,69,'move',1,0,'','Move Stock',NULL,'2023-06-25','17:33:17','inventory',0),(153,1,7,'move',-1,0,'','Move Stock',NULL,'2023-06-25','17:33:17','inventory',0),(154,1,70,'move',1,0,'','Move Stock',NULL,'2023-06-25','17:35:12','inventory',0),(155,1,69,'move',-1,0,'','Move Stock',NULL,'2023-06-25','17:35:12','inventory',0),(156,1,65,'move',7,0,'','Move Stock',NULL,'2023-06-25','18:43:21','inventory',-1),(157,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-25','18:43:21','inventory',1),(158,1,65,'move',7,0,'','Move Stock',NULL,'2023-06-25','18:43:31','inventory',-1),(159,1,70,'move',3,0,'','Move Stock',NULL,'2023-06-25','18:43:31','inventory',1),(160,1,65,'move',7,0,'','Move Stock',NULL,'2023-06-25','18:43:36','inventory',-1),(161,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-25','18:43:36','inventory',1),(162,1,65,'move',7,0,'','Move Stock',NULL,'2023-06-25','18:45:17','inventory',-1),(163,1,70,'move',3,0,'','Move Stock',NULL,'2023-06-25','18:45:17','inventory',1),(164,1,65,'move',-1,0,'','Move Stock',NULL,'2023-06-25','18:46:23','inventory',7),(165,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-25','18:46:23','inventory',1),(166,1,65,'move',-1,0,'','Move Stock',NULL,'2023-06-25','18:46:36','inventory',7),(167,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-25','18:46:36','inventory',1),(168,1,64,'move',-1,0,'','Move Stock',NULL,'2023-06-25','18:50:23','inventory',9),(169,1,70,'move',1,0,'','Move Stock',NULL,'2023-06-25','18:50:23','inventory',3),(170,1,70,'move',-1,0,'','Move Stock',NULL,'2023-06-25','18:50:57','inventory',3),(171,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-25','18:50:57','inventory',1),(172,1,64,'move',-1,0,'','Move Stock',NULL,'2023-06-25','18:52:07','inventory',9),(173,1,1,'move',1,0,'','Move Stock',NULL,'2023-06-25','18:52:07','inventory',1),(174,1,71,'add',1,1,'','New Stock',NULL,'2023-06-27','16:21:26','inventory',NULL),(175,1,72,'add',1,0,'','New Stock',NULL,'2023-06-27','16:26:14','inventory',NULL),(176,1,65,'move',-19,0,'','Move Stock',NULL,'2023-06-27','16:28:03','inventory',7),(177,1,73,'move',19,0,'','Move Stock',NULL,'2023-06-27','16:28:03','inventory',10),(178,1,72,'add',1,0,'','New Stock',NULL,'2023-06-27','16:51:39','inventory',3),(179,1,72,'add',1,0,'','New Stock',NULL,'2023-06-27','16:52:19','inventory',3),(180,1,72,'add',1,0,'','New Stock',NULL,'2023-06-27','16:53:06','inventory',3),(181,1,70,'remove',-1,NULL,'','Sale Test',NULL,'2023-06-27','16:56:16','inventory',3),(182,1,70,'remove',-1,68,'','testing 69 cost',NULL,'2023-06-27','16:58:45','inventory',3),(183,1,64,'move',-1,0,'','Move Stock',NULL,'2023-07-07','05:04:11','inventory',9),(184,1,67,'move',1,0,'','Move Stock',NULL,'2023-07-07','05:04:11','inventory',8),(185,1,67,'move',-1,0,'','Move Stock',NULL,'2023-07-07','05:07:16','inventory',8),(186,1,64,'move',1,0,'','Move Stock',NULL,'2023-07-07','05:07:16','inventory',9),(187,1,64,'move',-1,0,'','Move Stock',NULL,'2023-07-07','05:12:01','inventory',9),(188,1,67,'move',1,0,'','Move Stock',NULL,'2023-07-07','05:12:01','inventory',8),(189,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-07','05:14:20','inventory',7),(190,1,71,'move',1,0,'','Move Stock',NULL,'2023-07-07','05:14:20','inventory',3),(191,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-07','05:18:40','inventory',7),(192,1,71,'move',1,0,'','Move Stock',NULL,'2023-07-07','05:18:40','inventory',3),(193,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-07','05:19:04','inventory',7),(194,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-07','05:19:04','inventory',1),(195,1,64,'move',-1,0,'','Move Stock',NULL,'2023-07-07','05:21:57','inventory',9),(196,1,65,'move',1,0,'','Move Stock',NULL,'2023-07-07','05:21:57','inventory',7),(197,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:30:02','inventory',7),(198,1,71,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:30:02','inventory',3),(199,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:30:58','inventory',7),(200,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:30:58','inventory',1),(201,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:33:29','inventory',7),(202,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:33:29','inventory',1),(203,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:34:47','inventory',7),(204,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:34:47','inventory',1),(205,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:36:32','inventory',7),(206,1,74,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:36:32','inventory',2),(207,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:39:54','inventory',7),(208,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:39:54','inventory',1),(209,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:41:17','inventory',7),(210,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:41:17','inventory',1),(211,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:42:00','inventory',7),(212,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:42:00','inventory',1),(213,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:42:08','inventory',7),(214,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:42:08','inventory',1),(215,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:43:02','inventory',7),(216,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:43:02','inventory',1),(217,1,1,'move',-12,0,'','Move Stock',NULL,'2023-07-10','02:43:14','inventory',1),(218,1,65,'move',12,0,'','Move Stock',NULL,'2023-07-10','02:43:14','inventory',7),(219,1,1,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:44:56','inventory',1),(220,1,65,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:44:56','inventory',7),(221,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:46:17','inventory',7),(222,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:46:17','inventory',1),(223,1,1,'move',-1,0,'','Move Stock',NULL,'2023-07-10','02:46:43','inventory',1),(224,1,65,'move',1,0,'','Move Stock',NULL,'2023-07-10','02:46:43','inventory',7),(225,1,65,'move',-1,0,'','Move Stock',NULL,'2023-07-10','22:05:21','inventory',7),(226,1,1,'move',1,0,'','Move Stock',NULL,'2023-07-10','22:05:21','inventory',1),(227,32,75,'add',12,60,'','Test item stock add',NULL,'2023-07-11','01:07:35','inventory',NULL),(228,33,76,'add',12,0,'','test',NULL,'2023-07-11','01:13:01','inventory',NULL),(229,34,77,'add',12,1,'','New Stock',NULL,'2023-07-11','01:17:20','inventory',NULL),(230,35,78,'add',1,0,'','New Stock',NULL,'2023-07-11','01:23:36','inventory',NULL),(231,75,79,'add',1,0,'','New Stock',NULL,'2023-08-14','07:41:11','inventory',NULL),(232,76,80,'add',1,0,'','New Stock',NULL,'2023-08-14','07:45:51','inventory',NULL),(233,77,81,'add',1,0,'','New Stock',NULL,'2023-08-14','07:46:49','inventory',NULL),(234,77,81,'remove',-1,0,'','123',NULL,'2023-08-14','07:47:08','inventory',11),(235,76,80,'remove',-1,0,'','123',NULL,'2023-08-14','07:47:28','inventory',10),(236,75,79,'remove',-1,0,'','12',NULL,'2023-08-14','07:48:22','inventory',11),(237,83,82,'add',1,0,'','New Stock',NULL,'2023-08-16','04:30:29','root',NULL),(238,83,82,'remove',-1,-1,'','test',NULL,'2023-08-16','04:30:46','root',12),(239,83,0,'delete',0,0,'','No Stock Remaining, Deleted.',NULL,'2023-08-16','04:31:53','root',NULL),(240,84,83,'add',1,0,'','New Stock',NULL,'2023-08-18','20:11:31','inventory',NULL),(241,84,83,'add',1,0,'','New Stock',NULL,'2023-08-18','20:14:03','inventory',2),(242,84,83,'remove',-1,0,'','modify testing',NULL,'2023-08-18','20:15:31','inventory',2),(243,84,83,'remove',-1,0,'','testing ready for delete',NULL,'2023-08-18','20:15:55','inventory',2),(244,84,0,'delete',0,0,'','No Stock Remaining, Deleted.',NULL,'2023-08-18','20:15:59','inventory',NULL),(245,85,84,'add',1,0,'','New Stock',NULL,'2023-08-18','20:24:06','inventory',NULL),(246,85,84,'remove',-1,0,'','123',NULL,'2023-08-18','20:24:13','inventory',1),(247,85,0,'delete',0,0,'','No Stock Remaining, Deleted.',NULL,'2023-08-18','20:24:17','inventory',NULL),(248,1,65,'move',-1,0,'','Move Stock',NULL,'2023-08-18','20:32:38','inventory',7),(249,1,1,'move',1,0,'','Move Stock',NULL,'2023-08-18','20:32:38','inventory',1),(250,1,1,'move',-1,0,'','Move Stock',NULL,'2023-08-18','20:33:06','inventory',1),(251,1,74,'move',1,0,'','Move Stock',NULL,'2023-08-18','20:33:06','inventory',2),(252,86,85,'add',1,0,'','New Stock',NULL,'2023-08-18','20:42:27','inventory',NULL),(253,87,86,'add',1,0,'','New Stock',NULL,'2023-08-18','20:42:42','inventory',NULL),(254,88,87,'add',1,0,'','New Stock',NULL,'2023-08-18','20:42:55','inventory',NULL),(255,89,88,'add',1,0,'','New Stock',NULL,'2023-08-18','20:43:16','inventory',NULL),(256,90,89,'add',1,0,'','New Stock',NULL,'2023-08-18','20:43:44','inventory',NULL),(257,5,90,'add',1,0,'','New Stock',NULL,'2023-08-19','12:18:05','inventory',NULL),(258,91,91,'add',1,0,'','New Stock',NULL,'2023-08-20','20:15:02','inventory',NULL),(259,92,92,'add',1,0,'','New Stock',NULL,'2023-08-20','20:16:07','inventory',NULL),(260,93,93,'add',1,0,'','New Stock',NULL,'2023-08-20','20:17:25','inventory',NULL),(261,94,94,'add',1,0,'','New Stock',NULL,'2023-08-20','20:17:43','inventory',NULL),(262,95,95,'add',1,0,'','New Stock',NULL,'2023-08-20','20:18:02','inventory',NULL),(263,96,96,'add',1,0,'','New Stock',NULL,'2023-08-20','20:18:15','inventory',NULL),(264,97,97,'add',1,0,'','New Stock',NULL,'2023-08-20','20:18:23','inventory',NULL),(265,98,98,'add',1,0,'','New Stock',NULL,'2023-08-20','20:19:08','inventory',NULL),(266,99,99,'add',1,0,'','New Stock',NULL,'2023-08-20','20:22:01','inventory',NULL),(267,100,100,'add',1,0,'','New Stock',NULL,'2023-08-20','20:22:31','inventory',NULL),(268,101,101,'add',1,0,'','New Stock',NULL,'2023-08-20','20:23:16','inventory',NULL),(269,102,102,'add',1,0,'','New Stock',NULL,'2023-08-20','20:25:27','inventory',NULL),(270,103,103,'add',1,0,'','New Stock',NULL,'2023-08-20','20:26:01','inventory',NULL),(271,104,104,'add',1,0,'','New Stock',NULL,'2023-08-20','20:26:22','inventory',NULL),(272,105,105,'add',1,0,'','New Stock',NULL,'2023-08-20','20:27:23','inventory',NULL),(273,106,106,'add',1,0,'','New Stock',NULL,'2023-08-20','20:27:34','inventory',NULL),(274,107,107,'add',1,0,'','New Stock',NULL,'2023-08-20','20:27:43','inventory',NULL),(275,108,108,'add',1,0,'','New Stock',NULL,'2023-08-20','20:27:59','inventory',NULL),(276,109,109,'add',1,0,'','New Stock',NULL,'2023-08-20','20:28:42','inventory',NULL),(277,110,110,'add',1,0,'','New Stock',NULL,'2023-08-20','20:28:56','inventory',NULL),(278,111,111,'add',1,0,'','New Stock',NULL,'2023-08-20','20:29:06','inventory',NULL),(279,112,112,'add',1,0,'','New Stock',NULL,'2023-08-20','20:29:20','inventory',NULL),(280,113,113,'add',1,0,'','New Stock',NULL,'2023-08-20','20:29:47','inventory',NULL),(281,114,114,'add',1,0,'','New Stock',NULL,'2023-08-20','20:30:33','inventory',NULL),(282,115,115,'add',1,0,'','New Stock',NULL,'2023-08-20','20:30:51','inventory',NULL),(283,116,116,'add',1,0,'','New Stock',NULL,'2023-08-20','20:31:47','inventory',NULL),(284,117,117,'add',1,0,'','New Stock',NULL,'2023-08-20','20:32:43','inventory',NULL),(285,118,118,'add',1,0,'','New Stock',NULL,'2023-08-20','20:37:19','inventory',NULL),(286,120,119,'add',1,0,'','New Stock',NULL,'2023-08-20','21:42:44','inventory',NULL),(287,119,0,'delete',0,0,'','No Stock Remaining, Deleted.',NULL,'2023-08-20','21:43:13','inventory',NULL),(288,120,119,'remove',-1,0,'','123',NULL,'2023-08-20','21:45:52','inventory',17),(289,120,0,'delete',0,0,'','No Stock Remaining, Deleted.',NULL,'2023-08-20','21:45:55','inventory',NULL),(290,121,120,'add',1,0,'','New Stock',NULL,'2023-08-20','21:47:53','inventory',NULL),(291,121,120,'remove',-1,0,'','123',NULL,'2023-08-20','21:48:01','inventory',17),(292,121,0,'delete',0,0,'','No Stock Remaining, Deleted.',NULL,'2023-08-20','21:48:04','inventory',NULL),(293,122,121,'add',1,0,'','New Stock',NULL,'2023-08-20','21:52:14','inventory',NULL),(294,122,121,'remove',-1,0,'','123',NULL,'2023-08-20','21:52:23','inventory',17),(295,122,122,'add',1,0,'','New Stock',NULL,'2023-08-20','21:54:11','inventory',NULL),(296,122,122,'remove',-1,0,'','123',NULL,'2023-08-20','21:54:18','inventory',17),(297,122,0,'delete',0,0,'','No Stock Remaining, Deleted.',NULL,'2023-08-20','21:54:26','inventory',NULL),(298,123,123,'add',1,0,'','New Stock',NULL,'2023-08-20','21:55:24','inventory',NULL),(299,123,123,'remove',-1,0,'','123',NULL,'2023-08-20','21:55:30','inventory',17),(300,123,0,'delete',0,0,'','No Stock Remaining, Deleted.',NULL,'2023-08-20','21:55:34','inventory',NULL),(301,1,65,'move',-1,0,'','Move Stock',NULL,'2023-08-28','13:50:44','inventory',7),(302,1,124,'move',1,0,'','Move Stock',NULL,'2023-08-28','13:50:44','inventory',17),(303,1,65,'move',-1,0,'','Move Stock',NULL,'2023-08-28','13:51:49','inventory',7),(304,1,124,'move',1,0,'','Move Stock',NULL,'2023-08-28','13:51:49','inventory',17),(305,1,65,'move',-1,0,'','Move Stock',NULL,'2023-08-28','13:56:20','inventory',7),(306,1,124,'move',1,0,'','Move Stock',NULL,'2023-08-28','13:56:20','inventory',17),(307,1,125,'add',1,0,'','New Stock',NULL,'2023-08-30','17:14:32','inventory',NULL),(308,4,126,'add',1,0,'sn-test-02','New Stock',NULL,'2023-08-30','17:49:49','inventory',NULL),(309,4,127,'add',1,0,'sn-test-023','New Stock',NULL,'2023-08-30','17:50:37','inventory',NULL),(310,4,128,'add',1,0,'TEST-SN65','New Stock',NULL,'2023-08-30','18:00:52','inventory',NULL),(311,4,126,'remove',-1,0,'sn-test-02','test',NULL,'2023-08-30','18:18:01','inventory',4),(312,4,127,'remove',-1,0,'sn-test-023','test',NULL,'2023-08-30','18:25:06','inventory',4),(313,4,4,'remove',-1,0,'','Remove last',NULL,'2023-08-30','18:26:11','inventory',4),(314,4,128,'remove',-1,0,'','Remove last',NULL,'2023-08-30','18:26:30','inventory',10),(315,1,129,'add',1,0,'','New Stock',NULL,'2023-08-30','18:36:00','inventory',NULL),(316,4,134,'add',5,0,'','New Stock',NULL,'2023-09-01','13:49:26','inventory',NULL),(317,4,135,'add',1,0,'testtest','New Stock',NULL,'2023-09-01','14:05:30','inventory',NULL),(318,4,136,'add',1,0,'tests','New Stock',NULL,'2023-09-01','14:08:13','inventory',NULL),(319,4,137,'add',1,0,'testtests','New Stock',NULL,'2023-09-01','14:08:13','inventory',NULL),(320,4,138,'add',1,0,'','New Stock',NULL,'2023-09-01','14:08:13','inventory',NULL),(321,4,139,'add',1,0,'','New Stock',NULL,'2023-09-01','14:08:13','inventory',NULL),(322,4,130,'remove',-1,0,'0','as',NULL,'2023-09-02','18:41:51','inventory',17),(323,4,131,'remove',-1,0,'0','123',NULL,'2023-09-02','18:45:37','inventory',17),(324,1,1,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(325,1,64,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(326,1,65,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(327,1,67,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(328,1,71,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(329,1,72,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(330,1,73,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(331,1,74,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(332,1,124,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(333,1,125,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(334,1,129,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:25:16','inventory',7),(335,1,208,'remove',-1,0,'','10 test',NULL,'2023-09-02','20:28:11','inventory',7),(336,1,270,'remove',-1,0,'','test12',NULL,'2023-09-02','20:31:05','inventory',7),(337,1,271,'remove',-1,0,'','test5',NULL,'2023-09-02','20:33:41','inventory',7),(338,1,272,'remove',-1,0,'','test6',NULL,'2023-09-02','20:35:53','inventory',7),(339,1,273,'remove',-1,0,'','test6',NULL,'2023-09-02','20:35:53','inventory',7),(340,1,274,'remove',-1,0,'','test6',NULL,'2023-09-02','20:35:53','inventory',7),(341,1,275,'remove',-1,0,'','test6',NULL,'2023-09-02','20:35:53','inventory',7),(342,1,276,'remove',-1,0,'','test6',NULL,'2023-09-02','20:35:53','inventory',7),(343,1,277,'remove',-1,0,'','test6',NULL,'2023-09-02','20:35:53','inventory',7),(344,1,278,'remove',-1,0,'','test6',NULL,'2023-09-02','20:35:53','inventory',7),(345,1,279,'remove',-1,0,'','test7',NULL,'2023-09-02','20:37:13','inventory',7),(346,1,280,'remove',-1,0,'','test7',NULL,'2023-09-02','20:37:13','inventory',7),(347,1,281,'remove',-1,0,'','test7',NULL,'2023-09-02','20:37:13','inventory',7),(348,1,282,'remove',-1,0,'','test7',NULL,'2023-09-02','20:37:13','inventory',7),(349,1,283,'remove',-1,0,'','test7',NULL,'2023-09-02','20:37:13','inventory',7),(350,1,284,'remove',-1,0,'','test7',NULL,'2023-09-02','20:37:13','inventory',7),(351,1,285,'remove',-1,0,'','test7',NULL,'2023-09-02','20:37:13','inventory',7),(352,4,132,'move',-1,0,'','Move Stock',NULL,'2023-09-04','00:04:17','inventory',17),(353,4,394,'move',1,0,'','Move Stock',NULL,'2023-09-04','00:04:17','inventory',10),(354,4,133,'move',-1,0,'','Move Stock',NULL,'2023-09-04','00:04:17','inventory',17),(355,4,395,'move',1,0,'','Move Stock',NULL,'2023-09-04','00:04:17','inventory',10),(356,4,394,'move',-1,0,'','Move Stock',NULL,'2023-09-04','00:07:47','inventory',10),(357,4,396,'move',1,0,'','Move Stock',NULL,'2023-09-04','00:07:47','inventory',17),(358,4,395,'move',-1,0,'','Move Stock',NULL,'2023-09-04','00:07:47','inventory',10),(359,4,397,'move',1,0,'','Move Stock',NULL,'2023-09-04','00:07:47','inventory',17),(360,4,134,'move',-1,0,'','Move Stock',NULL,'2023-09-04','00:11:20','inventory',17),(361,4,398,'move',1,0,'','Move Stock',NULL,'2023-09-04','00:11:20','inventory',4),(362,4,138,'move',-1,0,'','Move Stock',NULL,'2023-09-04','00:11:20','inventory',17),(363,4,399,'move',1,0,'','Move Stock',NULL,'2023-09-04','00:11:20','inventory',4),(364,4,139,'move',-1,0,'','Move Stock',NULL,'2023-09-04','00:11:20','inventory',17),(365,4,400,'move',1,0,'','Move Stock',NULL,'2023-09-04','00:11:20','inventory',4),(366,4,398,'move',-1,0,'','Move Stock',NULL,'2023-09-04','00:11:35','inventory',4),(367,4,401,'move',1,0,'','Move Stock',NULL,'2023-09-04','00:11:35','inventory',17),(368,4,399,'move',-1,0,'','Move Stock',NULL,'2023-09-04','00:11:35','inventory',4),(369,4,402,'move',1,0,'','Move Stock',NULL,'2023-09-04','00:11:35','inventory',17),(370,4,400,'move',-1,0,'','Move Stock',NULL,'2023-09-04','00:11:35','inventory',4),(371,4,403,'move',1,0,'','Move Stock',NULL,'2023-09-04','00:11:35','inventory',17);
/*!40000 ALTER TABLE `transaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `first_name` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `last_name` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `auth` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `password` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `role_id` int DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `password_expired` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (0,'root','root','root','root@inventory.local','local','$2y$10$AH3rVs9iStQICTei0peKhO.BHIkS8FuqjcaWxcupiyIUJWgRSqIa.',0,1,0),(1,'andrew1','Andrew','Richardson','andrew@ajrich.co.uk','ldap',NULL,1,1,0),(2,'inventory','Inventory','User','inventory@ajrich.co.uk','ldap',NULL,2,1,0),(3,'Spam','Spam','Account','Spam@ajrich.co.uk','ldap',NULL,1,1,0),(4,'testlocal','test','local','spam@ajrich.co.uk','local','$2y$10$E8Agy4bN/CwTNC1w/j1xcuQSNBMWRvCLLRkScrHt3zSdBTmlYTBgO',1,1,0),(5,'example','example','user','example@example.com','local','$2y$10$HZBchNa34DE6T/cLsfko2eow.55AWDvf5ojUswPPBvOrx8E..ONne',1,0,1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_roles`
--

DROP TABLE IF EXISTS `users_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_root` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_roles`
--

LOCK TABLES `users_roles` WRITE;
/*!40000 ALTER TABLE `users_roles` DISABLE KEYS */;
INSERT INTO `users_roles` VALUES (0,'Root','Root role for the default Root user ONLY.',1,1),(1,'User','Default group for normal Users.',0,0),(2,'Admin','Administrator role for any Administrator users.',1,0);
/*!40000 ALTER TABLE `users_roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-09-16 20:51:41

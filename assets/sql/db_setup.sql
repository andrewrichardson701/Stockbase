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
-- Current Database: `inventory`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `inventory` /*!40100 DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `inventory`;

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
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=505 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `default_theme_id` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `default_theme_id` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=465 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `title` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb3_unicode_ci,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `theme`
--

DROP TABLE IF EXISTS `theme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `file_name` text COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB AUTO_INCREMENT=481 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
  `theme_id` int DEFAULT '0',
  `card_primary` int DEFAULT NULL,
  `card_secondary` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-10-09  6:58:25

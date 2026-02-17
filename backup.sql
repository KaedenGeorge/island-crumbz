-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: cake_site
-- ------------------------------------------------------
-- Server version       8.0.45-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Cakes'),(2,'Ponche Crème');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price_each` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,2,1,75.00),(2,2,3,1,40.00),(3,3,3,1,40.00),(4,4,1,1,80.00),(5,5,1,1,80.00),(6,6,1,1,80.00),(7,7,1,2,80.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(40) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `delivery_method` enum('pickup','delivery') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_method` enum('cash','card') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,NULL,NULL,NULL,NULL,NULL,NULL,75.00,'cancelled','2025-11-26 11:13:14'),(2,2,NULL,NULL,NULL,NULL,NULL,NULL,40.00,'cancelled','2025-11-26 11:14:21'),(3,2,'Kaeden George','kaedengeorge1324@gmail.com','14734235104','pickup','card','',40.00,'cancelled','2025-11-26 16:40:20'),(4,2,'Kaeden George','kaedengeorge1324@gmail.com','89765467','pickup','card','',80.00,'cancelled','2025-11-26 18:45:25'),(5,2,'Kaeden A George','kaedengeorge1324@gmail.com','4734235104','delivery','cash','Schenectady Avenue, BROOKLYN, Grenada, No',80.00,'cancelled','2025-12-05 23:45:58'),(6,7,'Kaeden A George','kaedeng709@gmail.com','4734235104','pickup','cash','',80.00,'pending','2025-12-08 03:22:32'),(7,2,'Kaeden A George','kaedengeorge1324@gmail.com','4734235104','pickup','cash','',160.00,'pending','2026-01-13 19:53:49');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Chocolate Fudge Cake','Rich chocolate sponge with dark ganache.',80.00,NULL,1,'2025-11-25 14:59:09'),(2,'Vanilla Bean Cake','Classic vanilla cake with buttercream topping.',75.00,NULL,1,'2025-11-25 14:59:09'),(3,'Ponche Crème 750ml','Traditional creamy drink with nutmeg & rum.',40.00,NULL,2,'2025-11-25 14:59:09'),(4,'Ponche Crème Mini Bottles 6-pack','Great for gifts and parties.',25.00,NULL,2,'2025-11-25 14:59:09'),(5,'Black Cake','An alcohol filled fruity cake',35.00,'uploads/1764861792_WhatsApp Image 2025-11-12 at 19.50.28_e430b511.jpg',1,'2025-11-27 02:15:00'),(6,'Rum Cupcakes','Cupcakes that have alcohol within the batter',15.00,'uploads/1764874875_Screenshot 2025-11-26 211941.png',1,'2025-12-04 19:01:15');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `profile_photo` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('user','admin') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address_line` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parish` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'','','user@example.com',NULL,'fcf730b6d95236ecd3c9fc2d92d7b6b2bb061514961aec041d6c7a7192f592e4','2025-11-25 14:59:09','user',NULL,NULL,NULL,NULL),(2,'Kaeden','George','kaedengeorge1324@gmail.com','https://lh3.googleusercontent.com/a/ACg8ocLp87Hwu3JSlpOrTCIuyFTVqeI3fGvbEeI-OWbGPozXPWaxnEWdCw=s96-c','aff652b3cb05c11e9971233f19356690e115c3bdbf95d3e81e0883b310279d2e','2025-11-25 17:49:35','admin','1 (473) 423-5104','St. Pauls','St. George',''),(4,'Raffiela','Bonadie','braffeila@gmail.com',NULL,'3b83c23fc986449b8b1ebc52c60f0197b4ca5d877147eb3b62bbfbf0690c52c1','2025-11-25 17:53:58','user',NULL,NULL,NULL,NULL),(5,'Kaeden','George','kaedeng707@gmail.com','https://lh3.googleusercontent.com/a/ACg8ocJKZKeFtfsbRYMjfPtUYgd4Y1ABcpfhTesG9lOCNGiN3SzjKg=s96-c','a55958b40d7a83062c5efc1ee5dc06b0618490120b7b8c5addb9b2265403f4c5','2025-11-25 20:26:39','admin',NULL,NULL,NULL,NULL),(6,'Josh','Roberts','joshwilc.23@gmail.com',NULL,'2204f2f125c2a5e5eb4329051de5546c9533beec192d8ba5269b01b78df7f47f','2025-12-05 13:19:50','user',NULL,NULL,NULL,NULL),(7,'Kaeden','A George','kaedeng709@gmail.com',NULL,'aff652b3cb05c11e9971233f19356690e115c3bdbf95d3e81e0883b310279d2e','2025-12-08 03:22:01','user',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
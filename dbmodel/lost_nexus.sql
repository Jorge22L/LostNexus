CREATE DATABASE  IF NOT EXISTS `lost_nexus` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `lost_nexus`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: lost_nexus
-- ------------------------------------------------------
-- Server version	8.4.6

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
-- Table structure for table `tbl_categoria`
--

DROP TABLE IF EXISTS `tbl_categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_categoria`
--

LOCK TABLES `tbl_categoria` WRITE;
/*!40000 ALTER TABLE `tbl_categoria` DISABLE KEYS */;
INSERT INTO `tbl_categoria` VALUES (1,'Billetera'),(2,'Teléfono'),(3,'Llaves'),(4,'Otros');
/*!40000 ALTER TABLE `tbl_categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_punto_recepcion`
--

DROP TABLE IF EXISTS `tbl_punto_recepcion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_punto_recepcion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `ubicacion` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_punto_recepcion`
--

LOCK TABLES `tbl_punto_recepcion` WRITE;
/*!40000 ALTER TABLE `tbl_punto_recepcion` DISABLE KEYS */;
INSERT INTO `tbl_punto_recepcion` VALUES (1,'Sala de docentes','Edificio P'),(2,'Recepcion','Edificio A');
/*!40000 ALTER TABLE `tbl_punto_recepcion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_reclamacion`
--

DROP TABLE IF EXISTS `tbl_reclamacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_reclamacion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idobjeto` int NOT NULL,
  `idreclamante` int NOT NULL,
  `fecha_reclamacion` datetime NOT NULL,
  `observaciones` varchar(200) DEFAULT NULL,
  `usuario_atiende` int NOT NULL,
  `evidencia` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idobjeto_idx` (`idobjeto`),
  KEY `reclamante_idx` (`idreclamante`),
  KEY `usuario_atiende_idx` (`usuario_atiende`),
  CONSTRAINT `idobjeto` FOREIGN KEY (`idobjeto`) REFERENCES `tblobjeto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reclamante` FOREIGN KEY (`idreclamante`) REFERENCES `tbl_reclamante` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usuario_atiende` FOREIGN KEY (`usuario_atiende`) REFERENCES `tbl_usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_reclamacion`
--

LOCK TABLES `tbl_reclamacion` WRITE;
/*!40000 ALTER TABLE `tbl_reclamacion` DISABLE KEYS */;
INSERT INTO `tbl_reclamacion` VALUES (1,1,3,'2025-06-16 21:40:08','La persona muestra una foto en la que se aprecia una seña en la billetera',1,'30ee5f91eef42952d1b15b49cba05980.jpg'),(2,2,4,'2025-06-23 15:03:14','Dueño muestra una foto en el que posee el objeto puesto',1,'74dab2db499dac737372d4dd14236514.jpg'),(3,4,5,'2025-06-24 20:17:26','Reclamante identifica seña en la tapa de la botella',1,'645c78711aeec4fdd4f4ea2be326e24b.jpg'),(4,5,6,'2025-06-25 15:30:52','Estudiante se presenta a recepción a reclamar el objeto identificando los auriculares como propios',1,'6610550138c8ca671d408675f0c30071.jpg');
/*!40000 ALTER TABLE `tbl_reclamacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_reclamante`
--

DROP TABLE IF EXISTS `tbl_reclamante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_reclamante` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `carnet_estudiante` varchar(20) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_reclamante`
--

LOCK TABLES `tbl_reclamante` WRITE;
/*!40000 ALTER TABLE `tbl_reclamante` DISABLE KEYS */;
INSERT INTO `tbl_reclamante` VALUES (1,'Jorge','Morales','0013110920024B','','jorgeluismreyes@gmail.com','2025-06-16 21:13:00'),(2,'Jorge','Morales','0013110920024B','','jorgeluismreyes@gmail.com','2025-06-16 21:18:38'),(3,'Jorge','Morales','0013110920024B','','jorgeluismreyes@gmail.com','2025-06-16 21:40:08'),(4,'Jorge','Morales','0013110920024B','','jorgeluismreyes@gmail.com','2025-06-23 15:03:14'),(5,'Odalys','Icaza','0011608000024B','','jorgeluismreyes@gmail.com','2025-06-24 20:17:26'),(6,'José','Palacios','0012505990025G','','jorgeluismreyes@gmail.com','2025-06-25 15:30:52');
/*!40000 ALTER TABLE `tbl_reclamante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_rol`
--

DROP TABLE IF EXISTS `tbl_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_rol` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_rol`
--

LOCK TABLES `tbl_rol` WRITE;
/*!40000 ALTER TABLE `tbl_rol` DISABLE KEYS */;
INSERT INTO `tbl_rol` VALUES (1,'Administrador'),(2,'Estudiante'),(3,'Docente'),(4,'Personal Administrativo'),(5,'Docente');
/*!40000 ALTER TABLE `tbl_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_solicitud_revision_camaras`
--

DROP TABLE IF EXISTS `tbl_solicitud_revision_camaras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_solicitud_revision_camaras` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha_solicitud` datetime NOT NULL,
  `descripcion_objeto` varchar(155) NOT NULL,
  `estado` enum('pendiente','en_proceso','cerrada') NOT NULL DEFAULT 'pendiente',
  `notas` varchar(155) NOT NULL,
  `nombre_solicitante` varchar(45) NOT NULL,
  `apellido_solicitante` varchar(45) NOT NULL,
  `telefono_solicitante` varchar(45) NOT NULL,
  `email_solicitante` varchar(45) NOT NULL,
  `cif_solicitante` varchar(20) DEFAULT NULL,
  `usuario_solicita` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `revision_camara_vs_usuario_idx` (`usuario_solicita`),
  CONSTRAINT `revision_camara_vs_usuario` FOREIGN KEY (`usuario_solicita`) REFERENCES `tbl_usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_solicitud_revision_camaras`
--

LOCK TABLES `tbl_solicitud_revision_camaras` WRITE;
/*!40000 ALTER TABLE `tbl_solicitud_revision_camaras` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_solicitud_revision_camaras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_usuario`
--

DROP TABLE IF EXISTS `tbl_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) NOT NULL,
  `apellido` varchar(30) NOT NULL,
  `nombre_usuario` varchar(25) NOT NULL,
  `pwd` varchar(60) NOT NULL,
  `token` varchar(32) DEFAULT NULL,
  `id_rol` int NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  KEY `fk_usuario_rol` (`id_rol`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `tbl_rol` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_usuario`
--

LOCK TABLES `tbl_usuario` WRITE;
/*!40000 ALTER TABLE `tbl_usuario` DISABLE KEYS */;
INSERT INTO `tbl_usuario` VALUES (1,'Admin','Prueba','admin','$2y$12$WFKMk6j4lDd./P/SxZDh0uZrjaR8EITP6qYjmXe2KH22LzwESf4JG',NULL,1),(6,'Luis','Orozco','lorozco','$2y$12$BQ6BP/syZ0Y3ZIiMb.SOue44S8RjeH96FmQAI/hnUS3IU7OwU0EF2','691fe1b2c2fa0',2),(8,'Farit','Mendieta','flamer','$2y$12$HN39nX3BRqgp27tm3QppIuZt8V2ggm12Oy/rsWQnkLxTT3ysBZNjq','691fec7036704',3);
/* USUARIO admin/admin123*/ /* USUARIO lorozco/memo123*/ /*flamer/flamer123*/ 
/*!40000 ALTER TABLE `tbl_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tblobjeto`
--

DROP TABLE IF EXISTS `tblobjeto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tblobjeto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `fecha_reporte` datetime NOT NULL,
  `idpunto_recepcion` int NOT NULL,
  `foto` varchar(150) NOT NULL,
  `idcategoria` int NOT NULL,
  `estado` varchar(15) NOT NULL,
  `observaciones` varchar(150) NOT NULL,
  `usuario_guarda` int DEFAULT NULL,
  `usuario_devuelve` int DEFAULT NULL,
  `fecha_devolucion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `objeto_punto_recepcion_idx` (`idpunto_recepcion`),
  KEY `objeto_categoria_idx` (`idcategoria`),
  KEY `usuario_guarda_objeto_idx` (`usuario_guarda`),
  KEY `usuario_devuelve_objeto_idx` (`usuario_devuelve`),
  CONSTRAINT `objeto_categoria` FOREIGN KEY (`idcategoria`) REFERENCES `tbl_categoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `objeto_punto_recepcion` FOREIGN KEY (`idpunto_recepcion`) REFERENCES `tbl_punto_recepcion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usuario_devuelve_objeto` FOREIGN KEY (`usuario_devuelve`) REFERENCES `tbl_usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usuario_guarda_objeto` FOREIGN KEY (`usuario_guarda`) REFERENCES `tbl_usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tblobjeto`
--

LOCK TABLES `tblobjeto` WRITE;
/*!40000 ALTER TABLE `tblobjeto` DISABLE KEYS */;
INSERT INTO `tblobjeto` VALUES (1,'Billetera','Billetera de cuero','2025-06-09 16:11:58',1,'ba0f9ec31bd699930298c0644c775a58.jpg',1,'Perdido','Billetera de cuero',1,NULL,'2025-06-16 21:40:08'),(2,'Reloj','Reloj de pulsera','2025-06-23 15:00:55',1,'dd565613f40a3f45c87d4204db746ca1.jpg',4,'Devuelto','Reloj de pulsera color plateado',NULL,1,'2025-06-23 15:03:15'),(3,'Libro','Libro nuevo encontrado en comedor de colaboradores','2025-06-24 17:53:08',2,'8dae1dc5f5f8869f594e44b8ddd490e2.jpg',4,'Perdido','Libro nuevo encontrado en comedor',1,NULL,NULL),(4,'Botella','Botella transparente de plástico','2025-06-24 20:11:12',1,'a9663841172310eebb02b39682a1a198.jpg',4,'Devuelto','Botella encontrada en mesas',1,1,'2025-06-24 20:17:26'),(5,'Auriculares','Auriculares inalámbricos','2025-06-25 15:09:31',2,'ae11f17f84c1c5e32046254c67efd9b5.jpg',4,'Devuelto','Auriculares inalámbricos color blanco encontrados con su cable de carga en mesas de DIEM',1,1,'2025-06-25 15:30:52');
/*!40000 ALTER TABLE `tblobjeto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'lost_nexus'
--

--
-- Dumping routines for database 'lost_nexus'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-20 22:40:54

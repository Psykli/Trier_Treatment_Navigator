-- MariaDB dump 10.17  Distrib 10.4.6-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: portal
-- ------------------------------------------------------
-- Server version	10.4.6-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `abschluss_probatorik_persönlichkeitsstörung`
--

DROP TABLE IF EXISTS `abschluss_probatorik_persönlichkeitsstörung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `abschluss_probatorik_persönlichkeitsstörung` (
  `CODE` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `INSTANCE` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `PPSDAT` datetime DEFAULT NULL,
  `PKS001` double DEFAULT -9999,
  `PKS002` double DEFAULT -9999,
  `PKS003` double DEFAULT -9999,
  `PKS004` double DEFAULT -9999,
  `PKS005` double DEFAULT -9999,
  `PKS006` double DEFAULT -9999,
  `PKS007` double DEFAULT -9999,
  `PKS008` double DEFAULT -9999,
  `PKS009` double DEFAULT -9999,
  `PKS010` double DEFAULT -9999,
  `PKS011` double DEFAULT -9999,
  `PKS012` double DEFAULT -9999,
  `PKS013` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `PKS014` double DEFAULT -9999,
  `PKS015` double DEFAULT -9999,
  `PKS016` double DEFAULT -9999,
  `PKS017` double DEFAULT -9999,
  `PKS018` double DEFAULT -9999,
  `PKS019` double DEFAULT -9999,
  `PKS020` double DEFAULT -9999,
  `PKS021` double DEFAULT -9999,
  `PKS022` double DEFAULT -9999,
  `PKS023` double DEFAULT -9999,
  `PKS024` double DEFAULT -9999,
  `PKS025` double DEFAULT -9999,
  `PKS026` double DEFAULT -9999,
  `PKS027` double DEFAULT -9999,
  `PKS028` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `PKS029` double DEFAULT -9999,
  `PPS001` double DEFAULT NULL,
  `PPS002` double DEFAULT NULL,
  `PPS003` double DEFAULT NULL,
  `PPS004` double DEFAULT NULL,
  `PPS005` double DEFAULT NULL,
  `PPS006` double DEFAULT NULL,
  `PPS007` double DEFAULT NULL,
  `PPS008` double DEFAULT NULL,
  `PPS009` double DEFAULT NULL,
  `PPS010` double DEFAULT NULL,
  `PPS011` double DEFAULT NULL,
  `PPS012` double DEFAULT NULL,
  `PPS013` double DEFAULT NULL,
  `PPS014` double DEFAULT NULL,
  `PPS015` double DEFAULT NULL,
  `PPS016` double DEFAULT NULL,
  `PPS017` double DEFAULT NULL,
  `PPS018` double DEFAULT NULL,
  `PPS019` double DEFAULT NULL,
  `PPS020` double DEFAULT NULL,
  `PPS021` double DEFAULT NULL,
  `PPS022` double DEFAULT NULL,
  `PPS023` double DEFAULT NULL,
  `PPS024` double DEFAULT NULL,
  `PPS025` double DEFAULT NULL,
  `PPS026` double DEFAULT NULL,
  `PPS027` double DEFAULT NULL,
  `PPS028` double DEFAULT NULL,
  `PPS029` double DEFAULT NULL,
  `PPS030` double DEFAULT NULL,
  `PPS031` double DEFAULT NULL,
  `PPS032` double DEFAULT NULL,
  `PPS033` double DEFAULT NULL,
  `PPS034` double DEFAULT NULL,
  `PPS035` double DEFAULT NULL,
  `PPS036` double DEFAULT NULL,
  `PPS037` double DEFAULT NULL,
  `PPS038` double DEFAULT NULL,
  `PPS039` double DEFAULT NULL,
  `PPS040` double DEFAULT NULL,
  `PPS041` double DEFAULT NULL,
  `PPS042` double DEFAULT NULL,
  `PPS043` double DEFAULT NULL,
  `PPS044` double DEFAULT NULL,
  `PPS045` double DEFAULT NULL,
  `PPS046` double DEFAULT NULL,
  `PPS047` double DEFAULT NULL,
  `PPS048` double DEFAULT NULL,
  `PPS049` double DEFAULT NULL,
  `PPS050` double DEFAULT NULL,
  `PPS051` double DEFAULT NULL,
  `PPS052` double DEFAULT NULL,
  `PPS053` double DEFAULT NULL,
  `PPS054` double DEFAULT NULL,
  `PPS055` double DEFAULT NULL,
  `PPS056` double DEFAULT NULL,
  `PPS057` double DEFAULT NULL,
  `PPS058` double DEFAULT NULL,
  `PPS059` double DEFAULT NULL,
  `PPS060` double DEFAULT NULL,
  `PPS061` double DEFAULT NULL,
  `PPS062` double DEFAULT NULL,
  `PPS063` double DEFAULT NULL,
  `PPS064` double DEFAULT NULL,
  `PPS065` double DEFAULT NULL,
  `PPS066` double DEFAULT NULL,
  `PPS067` double DEFAULT NULL,
  `PPS068` double DEFAULT NULL,
  `PPS069` double DEFAULT NULL,
  `PPS070` double DEFAULT NULL,
  `PPS071` double DEFAULT NULL,
  `PPS072` double DEFAULT NULL,
  `PPS073` double DEFAULT NULL,
  `PPS075` double DEFAULT NULL,
  `PPS076` double DEFAULT NULL,
  `PPS077` double DEFAULT NULL,
  `PPS078` double DEFAULT NULL,
  `PPS079` double DEFAULT NULL,
  `PPS080` double DEFAULT NULL,
  `PPS081` double DEFAULT NULL,
  `PPS082` double DEFAULT NULL,
  `PPS083` double DEFAULT NULL,
  `PPS084` double DEFAULT NULL,
  `PPS085` double DEFAULT NULL,
  `PPS086` double DEFAULT NULL,
  `PPS087` double DEFAULT NULL,
  `PPS088` double DEFAULT NULL,
  `PPS074` double DEFAULT NULL,
  `PPS089` double DEFAULT NULL,
  `PPS090` double DEFAULT NULL,
  `PPS091` double DEFAULT NULL,
  `PPS092` double DEFAULT NULL,
  `PPS093` double DEFAULT NULL,
  `PPS094` double DEFAULT NULL,
  `PPS095` double DEFAULT NULL,
  `PPS096` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `PPS097` double DEFAULT NULL,
  `PPS098` double DEFAULT NULL,
  `PPS099` double DEFAULT NULL,
  `PPS100` double DEFAULT NULL,
  `PPS101` double DEFAULT NULL,
  `PPS102` double DEFAULT NULL,
  `PPS103` double DEFAULT NULL,
  `PPS104` double DEFAULT NULL,
  `PPS105` double DEFAULT NULL,
  `PPS106` double DEFAULT NULL,
  `PPS107` double DEFAULT NULL,
  `PPS108` double DEFAULT NULL,
  `PPS109` double DEFAULT NULL,
  `PPS110` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `PPS111` double DEFAULT NULL,
  KEY `INSTIndex` (`INSTANCE`),
  KEY `CODEIndex` (`CODE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_mail_messages`
--

DROP TABLE IF EXISTS `admin_mail_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_mail_messages` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `allow_sb`
--

DROP TABLE IF EXISTS `allow_sb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `allow_sb` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `patientcode` varchar(20) NOT NULL,
  `date` datetime NOT NULL,
  `current_instance` int(15) NOT NULL,
  `allowed_until_instance` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `antrag`
--

DROP TABLE IF EXISTS `antrag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `antrag` (
  `CODE` varchar(255) DEFAULT NULL,
  `INSTANCE` varchar(255) DEFAULT NULL,
  `ANTDAT` datetime DEFAULT NULL,
  `ANT001` datetime DEFAULT NULL,
  `ANT002` double DEFAULT NULL,
  `ANT003` double DEFAULT NULL,
  `ANT004` datetime DEFAULT NULL,
  `ANT005` double DEFAULT NULL,
  `ANT006` varchar(255) DEFAULT NULL,
  `ANT007` double DEFAULT NULL,
  `ANT008` varchar(255) DEFAULT NULL,
  `ANT009` double DEFAULT -9999,
  `ANT010` datetime DEFAULT NULL,
  KEY `INSTIndex` (`INSTANCE`),
  KEY `CODEIndex` (`CODE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asq`
--

DROP TABLE IF EXISTS `asq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asq` (
  `CODE` varchar(255) DEFAULT NULL,
  `INSTANCE` varchar(255) DEFAULT NULL,
  `ASQDAT` datetime DEFAULT NULL,
  `ASQ001` double DEFAULT NULL,
  `ASQ002` double DEFAULT NULL,
  `ASQ003` double DEFAULT NULL,
  `ASQ004` double DEFAULT NULL,
  `ASQ005` double DEFAULT NULL,
  `ASQ006` double DEFAULT NULL,
  `ASQ007` double DEFAULT NULL,
  `ASQ008` double DEFAULT NULL,
  `ASQ009` double DEFAULT NULL,
  `ASQ010` double DEFAULT NULL,
  `ASQ011` double DEFAULT NULL,
  `ASQ012` double DEFAULT NULL,
  `ASQ013` double DEFAULT NULL,
  `ASQ014` double DEFAULT NULL,
  `ASQ015` double DEFAULT NULL,
  `ASQ016` double DEFAULT NULL,
  `ASQ017` double DEFAULT NULL,
  `ASQ018` double DEFAULT NULL,
  `ASQ019` double DEFAULT NULL,
  `ASQ020` double DEFAULT NULL,
  `ASQ021` varchar(255) DEFAULT NULL,
  KEY `INSTIndex` (`INSTANCE`),
  KEY `CODEIndex` (`CODE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ceq_einschaetzungen_zur_behandlung`
--

DROP TABLE IF EXISTS `ceq_einschaetzungen_zur_behandlung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ceq_einschaetzungen_zur_behandlung` (
  `CODE` varchar(255) NOT NULL DEFAULT '',
  `INSTANCE` varchar(255) NOT NULL DEFAULT '',
  `CEQDAT` datetime DEFAULT NULL,
  `CEQ001` double DEFAULT NULL,
  `CEQ002` double DEFAULT NULL,
  `CEQ003` double DEFAULT NULL,
  `CEQ004` double DEFAULT NULL,
  `CEQ005` double DEFAULT NULL,
  KEY `INSTIndex` (`INSTANCE`),
  KEY `CODEIndex` (`CODE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dokumentation`
--

DROP TABLE IF EXISTS `dokumentation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dokumentation` (
  `CODE` varchar(255) NOT NULL,
  `INSTANCE` varchar(255) NOT NULL,
  `DOKDAT` datetime DEFAULT NULL,
  `DOK001` datetime DEFAULT NULL,
  `DOK002` double DEFAULT NULL,
  `DOK003` datetime DEFAULT NULL,
  `DOK004` datetime DEFAULT NULL,
  `DOK005` double DEFAULT NULL,
  `DOK006` double DEFAULT NULL,
  `DOK007` double DEFAULT NULL,
  `DOK008` double DEFAULT NULL,
  `DOK009` double DEFAULT NULL,
  `DOK010` varchar(255) DEFAULT NULL,
  `DOK011` varchar(255) DEFAULT NULL,
  `DOK012` double DEFAULT NULL,
  `DOK013` double DEFAULT NULL,
  `DOK014` double DEFAULT NULL,
  `DOK015` double DEFAULT NULL,
  `DOK016` double DEFAULT NULL,
  `DOK017` double DEFAULT NULL,
  `DOK018` double DEFAULT NULL,
  `DOK019` varchar(255) DEFAULT NULL,
  `DOK020` varchar(255) DEFAULT NULL,
  `DOK021` varchar(255) DEFAULT NULL,
  `DOK022` varchar(255) DEFAULT NULL,
  `DOK023` varchar(255) DEFAULT NULL,
  `DOK024` double DEFAULT NULL,
  `DOK025` double DEFAULT NULL,
  `DOK026` double DEFAULT NULL,
  `DOK027` double DEFAULT NULL,
  `DOK028` double DEFAULT NULL,
  `DOK029` double DEFAULT NULL,
  `DOK030` double DEFAULT NULL,
  `DOK031` varchar(255) DEFAULT NULL,
  `DOK032` varchar(255) DEFAULT NULL,
  `DOK033` varchar(255) DEFAULT NULL,
  `DOK034` varchar(255) DEFAULT NULL,
  `DOK035` varchar(255) DEFAULT NULL,
  `DOK036` double DEFAULT NULL,
  `DOK037` datetime DEFAULT NULL,
  `DOK038` double DEFAULT NULL,
  `DOK039` datetime DEFAULT NULL,
  `DOK040` datetime DEFAULT NULL,
  `DOK041` double DEFAULT NULL,
  `DOK042` varchar(255) DEFAULT NULL,
  `DOK043` datetime DEFAULT NULL,
  `DOK044` datetime DEFAULT NULL,
  `DOK045` double DEFAULT NULL,
  `DOK046` varchar(255) DEFAULT NULL,
  `DOK047` varchar(255) DEFAULT NULL,
  `DOK048` double DEFAULT NULL,
  `DOK049` varchar(255) DEFAULT NULL,
  `DOK050` varchar(255) DEFAULT NULL,
  `DOK051` varchar(255) DEFAULT NULL,
  `DOK052` datetime DEFAULT NULL,
  `DOK053` varchar(255) DEFAULT NULL,
  `DOK054` double DEFAULT NULL,
  `DOK055` datetime DEFAULT NULL,
  `DOK056` varchar(255) DEFAULT NULL,
  `DOK057` double DEFAULT NULL,
  `DOK058` datetime DEFAULT NULL,
  `DOK059` varchar(255) DEFAULT NULL,
  `DOK060` double DEFAULT NULL,
  `DOK061` datetime DEFAULT NULL,
  `DOK062` varchar(255) DEFAULT NULL,
  `DOK063` double DEFAULT NULL,
  `DOK064` datetime DEFAULT NULL,
  `DOK065` varchar(255) DEFAULT NULL,
  `DOK066` datetime DEFAULT NULL,
  `DOK067` varchar(255) DEFAULT NULL,
  `DOK068` datetime DEFAULT NULL,
  `DOK069` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`CODE`(7),`INSTANCE`(3)),
  KEY `INSTIndex` (`INSTANCE`),
  KEY `CODEIndex` (`CODE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_confirmation_codes`
--

DROP TABLE IF EXISTS `email_confirmation_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_confirmation_codes` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `confirmation_code` char(32) CHARACTER SET ascii NOT NULL,
  `old_email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `new_email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_id_email_confirmation_codes_constr` FOREIGN KEY (`user_id`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entscheidungsregeln_hscl`
--

DROP TABLE IF EXISTS `entscheidungsregeln_hscl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entscheidungsregeln_hscl` (
  `ID` int(15) NOT NULL AUTO_INCREMENT,
  `CODE` varchar(8) NOT NULL,
  `INSTANCE` double NOT NULL,
  `HSCL_MEAN` double DEFAULT NULL,
  `BOUNDARY_NEXT` double NOT NULL,
  `BOUNDARY_UEBERSCHRITTEN` int(11) NOT NULL,
  `DATE` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=47277 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `entscheidungsregeln_hscl2`
--

DROP TABLE IF EXISTS `entscheidungsregeln_hscl2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entscheidungsregeln_hscl2` (
  `ID` int(15) NOT NULL AUTO_INCREMENT,
  `DATE` datetime NOT NULL DEFAULT current_timestamp(),
  `CODE` varchar(255) NOT NULL,
  `EXPECTED_VALUE1` double NOT NULL,
  `EXPECTED_VALUE2` double NOT NULL,
  `EXPECTED_VALUE3` double NOT NULL,
  `EXPECTED_VALUE4` double NOT NULL,
  `EXPECTED_VALUE5` double NOT NULL,
  `EXPECTED_VALUE6` double NOT NULL,
  `EXPECTED_VALUE7` double NOT NULL,
  `EXPECTED_VALUE8` double NOT NULL,
  `EXPECTED_VALUE9` double NOT NULL,
  `EXPECTED_VALUE10` double NOT NULL,
  `EXPECTED_VALUE11` double NOT NULL,
  `EXPECTED_VALUE12` double NOT NULL,
  `EXPECTED_VALUE13` double NOT NULL,
  `EXPECTED_VALUE14` double NOT NULL,
  `EXPECTED_VALUE15` double NOT NULL,
  `EXPECTED_VALUE16` double NOT NULL,
  `EXPECTED_VALUE17` double NOT NULL,
  `EXPECTED_VALUE18` double NOT NULL,
  `EXPECTED_VALUE19` double NOT NULL,
  `EXPECTED_VALUE20` double NOT NULL,
  `EXPECTED_VALUE21` double NOT NULL,
  `EXPECTED_VALUE22` double NOT NULL,
  `EXPECTED_VALUE23` double NOT NULL,
  `EXPECTED_VALUE24` double NOT NULL,
  `EXPECTED_VALUE25` double NOT NULL,
  `EXPECTED_VALUE26` double NOT NULL,
  `EXPECTED_VALUE27` double NOT NULL,
  `EXPECTED_VALUE28` double NOT NULL,
  `EXPECTED_VALUE29` double NOT NULL,
  `EXPECTED_VALUE30` double NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2285 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ex_nachrichten`
--

DROP TABLE IF EXISTS `ex_nachrichten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ex_nachrichten` (
  `id` mediumint(15) unsigned NOT NULL AUTO_INCREMENT,
  `sender` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `receiver` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `datum` datetime NOT NULL DEFAULT current_timestamp(),
  `betreff` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `nachricht` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `cipher` varchar(25) CHARACTER SET ascii DEFAULT NULL,
  `iv` varbinary(12) DEFAULT NULL,
  `tagSubject` varbinary(16) DEFAULT NULL,
  `tagMessage` varbinary(16) DEFAULT NULL,
  `randomKeyBytes` varbinary(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `feedback_recommendation`
--

DROP TABLE IF EXISTS `feedback_recommendation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback_recommendation` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `patientcode` varchar(15) NOT NULL,
  `therapeut` varchar(15) NOT NULL,
  `date` datetime NOT NULL,
  `status` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=371 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fep-2`
--

DROP TABLE IF EXISTS `fep-2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fep-2` (
  `ID` int(15) NOT NULL AUTO_INCREMENT,
  `CODE` varchar(255) DEFAULT NULL,
  `INSTANCE` varchar(255) DEFAULT NULL,
  `FEPDAT` datetime DEFAULT NULL,
  `FEP001` double DEFAULT NULL,
  `FEP002` double DEFAULT NULL,
  `FEP003` double DEFAULT NULL,
  `FEP004` double DEFAULT NULL,
  `FEP005` double DEFAULT NULL,
  `FEP006` double DEFAULT NULL,
  `FEP007` double DEFAULT NULL,
  `FEP008` double DEFAULT NULL,
  `FEP009` double DEFAULT NULL,
  `FEP010` double DEFAULT NULL,
  `FEP011` double DEFAULT NULL,
  `FEP012` double DEFAULT NULL,
  `FEP013` double DEFAULT NULL,
  `FEP014` double DEFAULT NULL,
  `FEP015` double DEFAULT NULL,
  `FEP016` double DEFAULT NULL,
  `FEP017` double DEFAULT NULL,
  `FEP018` double DEFAULT NULL,
  `FEP019` double DEFAULT NULL,
  `FEP020` double DEFAULT NULL,
  `FEP021` double DEFAULT NULL,
  `FEP022` double DEFAULT NULL,
  `FEP023` double DEFAULT NULL,
  `FEP024` double DEFAULT NULL,
  `FEP025` double DEFAULT NULL,
  `FEP026` double DEFAULT NULL,
  `FEP027` double DEFAULT NULL,
  `FEP028` double DEFAULT NULL,
  `FEP029` double DEFAULT NULL,
  `FEP030` double DEFAULT NULL,
  `FEP031` double DEFAULT NULL,
  `FEP032` double DEFAULT NULL,
  `FEP033` double DEFAULT NULL,
  `FEP034` double DEFAULT NULL,
  `FEP035` double DEFAULT NULL,
  `FEP036` double DEFAULT NULL,
  `FEP037` double DEFAULT NULL,
  `FEP038` double DEFAULT NULL,
  `FEP039` double DEFAULT NULL,
  `FEP040` double DEFAULT NULL,
  `FEP041` double DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `INSTIndex` (`INSTANCE`),
  KEY `CODEIndex` (`CODE`)
) ENGINE=InnoDB AUTO_INCREMENT=22529 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gas`
--

DROP TABLE IF EXISTS `gas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gas` (
  `CODE` varchar(255) NOT NULL,
  `INSTANCE` varchar(255) DEFAULT NULL,
  `GASDAT` datetime DEFAULT NULL,
  `GAS001` double DEFAULT NULL,
  `GAS002` double DEFAULT NULL,
  `GAS003` double DEFAULT NULL,
  `GAS004` double DEFAULT NULL,
  `GAS005` double DEFAULT NULL,
  `GAS006` double DEFAULT NULL,
  `GAS007` double DEFAULT NULL,
  `GAS008` double DEFAULT NULL,
  `GAS009` double DEFAULT NULL,
  `GAS010` double DEFAULT NULL,
  `GAS011` double DEFAULT NULL,
  `GAS012` text DEFAULT NULL,
  `GAS013` text DEFAULT NULL,
  `GAS014` text DEFAULT NULL,
  `GAS015` text DEFAULT NULL,
  `GAS016` text DEFAULT NULL,
  `GAS017` text DEFAULT NULL,
  `GAS018` text DEFAULT NULL,
  `GAS019` text DEFAULT NULL,
  `GAS020` text DEFAULT NULL,
  `GAS021` text DEFAULT NULL,
  `GAS022` text DEFAULT NULL,
  `GAS023` text DEFAULT NULL,
  `GAS024` text DEFAULT NULL,
  `GAS025` text DEFAULT NULL,
  `GAS026` text DEFAULT NULL,
  `GAS027` text DEFAULT NULL,
  `GAS028` text DEFAULT NULL,
  `GAS029` text DEFAULT NULL,
  `GAS030` text DEFAULT NULL,
  `GAS031` text DEFAULT NULL,
  `GAS032` text DEFAULT NULL,
  `GAS033` text DEFAULT NULL,
  `GAS034` text DEFAULT NULL,
  `GAS035` text DEFAULT NULL,
  `GAS036` text DEFAULT NULL,
  `GAS037` text DEFAULT NULL,
  `GAS038` text DEFAULT NULL,
  `GAS039` text DEFAULT NULL,
  `GAS040` text DEFAULT NULL,
  `GAS041` text DEFAULT NULL,
  `GAS042` text DEFAULT NULL,
  `GAS043` text DEFAULT NULL,
  `GAS044` text DEFAULT NULL,
  `GAS045` text DEFAULT NULL,
  `GAS046` text DEFAULT NULL,
  `GAS047` text DEFAULT NULL,
  `GAS048` text DEFAULT NULL,
  `GAS049` text DEFAULT NULL,
  `GAS050` text DEFAULT NULL,
  `GAS051` text DEFAULT NULL,
  `GAS052` text DEFAULT NULL,
  `GAS053` text DEFAULT NULL,
  `GAS054` text DEFAULT NULL,
  `GAS055` text DEFAULT NULL,
  `GAS056` text DEFAULT NULL,
  `GAS057` text DEFAULT NULL,
  `GAS058` text DEFAULT NULL,
  `GAS059` text DEFAULT NULL,
  `GAS060` text DEFAULT NULL,
  `GAS061` text DEFAULT NULL,
  `GAS062` text DEFAULT NULL,
  `GAS063` text DEFAULT NULL,
  `GAS064` text DEFAULT NULL,
  `GAS065` text DEFAULT NULL,
  `GAS066` text DEFAULT NULL,
  `GAS067` text DEFAULT NULL,
  `GAS068` text DEFAULT NULL,
  `GAS069` text DEFAULT NULL,
  `GAS070` text DEFAULT NULL,
  `GAS071` text DEFAULT NULL,
  `GAS072` text DEFAULT NULL,
  `GAS073` text DEFAULT NULL,
  `GAS074` text DEFAULT NULL,
  `GAS075` text DEFAULT NULL,
  `GAS076` text DEFAULT NULL,
  `GAS077` text DEFAULT NULL,
  `GAS078` text DEFAULT NULL,
  `GAS079` text DEFAULT NULL,
  `GAS080` text DEFAULT NULL,
  `GAS081` text DEFAULT NULL,
  `GAS082` text DEFAULT NULL,
  `GAS083` text DEFAULT NULL,
  `GAS084` text DEFAULT NULL,
  `GAS085` text DEFAULT NULL,
  `GAS086` text DEFAULT NULL,
  `GAS087` text DEFAULT NULL,
  `GAS088` text DEFAULT NULL,
  `GAS089` text DEFAULT NULL,
  `GAS090` text DEFAULT NULL,
  `GAS091` text DEFAULT NULL,
  `IMMUTABLE` tinyint(1) NOT NULL DEFAULT 0,
  KEY `INSTIndex` (`INSTANCE`),
  KEY `CODEIndex` (`CODE`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `haq-s`
--

DROP TABLE IF EXISTS `haq-s`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `haq-s` (
  `CODE` varchar(255) DEFAULT NULL,
  `INSTANCE` varchar(255) DEFAULT NULL,
  `HQSDAT` datetime DEFAULT NULL,
  `HQS001` double DEFAULT NULL,
  `HQS002` double DEFAULT NULL,
  `HQS003` double DEFAULT NULL,
  `HQS004` double DEFAULT NULL,
  `HQS005` double DEFAULT NULL,
  `HQS006` double DEFAULT NULL,
  `HQS007` double DEFAULT NULL,
  `HQS008` double DEFAULT NULL,
  `HQS009` double DEFAULT NULL,
  `HQS010` double DEFAULT NULL,
  `HQS011` double DEFAULT NULL,
  `HQS012` varchar(255) DEFAULT NULL,
  `HQS013` varchar(255) DEFAULT NULL,
  `HQS014` varchar(255) DEFAULT NULL,
  `HQS015` varchar(255) DEFAULT NULL,
  `HQS016` varchar(255) DEFAULT NULL,
  `HQS017` varchar(255) DEFAULT NULL,
  `HQS018` double DEFAULT NULL,
  `HQS019` double DEFAULT NULL,
  `HQS020` double DEFAULT NULL,
  KEY `INSTIndex` (`INSTANCE`),
  KEY `CODEIndex` (`CODE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hscl-11`
--

DROP TABLE IF EXISTS `hscl-11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hscl-11` (
  `ID` int(15) NOT NULL AUTO_INCREMENT,
  `CODE` varchar(255) DEFAULT NULL,
  `INSTANCE` varchar(255) DEFAULT NULL,
  `HSCDAT` datetime DEFAULT NULL,
  `HSC001` double DEFAULT NULL,
  `HSC002` double DEFAULT NULL,
  `HSC003` double DEFAULT NULL,
  `HSC004` double DEFAULT NULL,
  `HSC005` double DEFAULT NULL,
  `HSC006` double DEFAULT NULL,
  `HSC007` double DEFAULT NULL,
  `HSC008` double DEFAULT NULL,
  `HSC009` double DEFAULT NULL,
  `HSC010` double DEFAULT NULL,
  `HSC011` double DEFAULT NULL,
  `temp9999` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `INSTIndex` (`INSTANCE`),
  KEY `CODEIndex` (`CODE`)
) ENGINE=InnoDB AUTO_INCREMENT=76759 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_confirmation_codes`
--

DROP TABLE IF EXISTS `password_confirmation_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_confirmation_codes` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `confirmation_code` char(32) CHARACTER SET ascii NOT NULL,
  `new_password` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  CONSTRAINT `user_id_password_confirmation_codes_constr` FOREIGN KEY (`user_id`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patient_feedback`
--

DROP TABLE IF EXISTS `patient_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patient_feedback` (
  `code` varchar(255) DEFAULT NULL,
  `instance` varchar(255) DEFAULT NULL,
  `farbe_oq` varchar(255) DEFAULT NULL,
  `farbe_asc_alliance` varchar(255) DEFAULT NULL,
  `farbe_asc_lifeevents` varchar(255) DEFAULT NULL,
  `farbe_asc_motivation` varchar(255) DEFAULT NULL,
  `farbe_asc_socsup` varchar(255) DEFAULT NULL,
  `farbe_asq_emotion` varchar(255) DEFAULT NULL,
  `farbe_risk` varchar(255) NOT NULL,
  `farbe_konkruenz` varchar(255) NOT NULL,
  `farbe_risk_suicide` varchar(255) NOT NULL,
  `farbe_asq_emotion_adapt` varchar(255) NOT NULL,
  `farbe_asq_emotion_oppress` varchar(255) NOT NULL,
  `farbe_asq_emotion_accept` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire`
--

DROP TABLE IF EXISTS `questionnaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire` (
  `table` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `descPatient` text DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `columnIdent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_batterie`
--

DROP TABLE IF EXISTS `questionnaire_batterie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_batterie` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `gas_section` int(11) NOT NULL DEFAULT -1,
  `sections` int(15) NOT NULL DEFAULT 1,
  `section_names` text NOT NULL,
  `is_standard` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_batterie_feedback`
--

DROP TABLE IF EXISTS `questionnaire_batterie_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_batterie_feedback` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `bid` int(15) NOT NULL,
  `type` text NOT NULL,
  `data` text NOT NULL,
  `feedback_order` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_batterie_hat`
--

DROP TABLE IF EXISTS `questionnaire_batterie_hat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_batterie_hat` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `bid` int(11) NOT NULL,
  `qid` int(11) NOT NULL,
  `section` int(15) NOT NULL,
  `section_order` int(15) NOT NULL,
  `is_Z` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_batterie_patient`
--

DROP TABLE IF EXISTS `questionnaire_batterie_patient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_batterie_patient` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `patientcode` varchar(20) NOT NULL,
  `bid` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_item_infos`
--

DROP TABLE IF EXISTS `questionnaire_item_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_item_infos` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(120) NOT NULL,
  `item_names` longtext NOT NULL,
  `item_texts` longtext NOT NULL,
  `language` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_items`
--

DROP TABLE IF EXISTS `questionnaire_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_items` (
  `columnName` text DEFAULT NULL,
  `text` text DEFAULT NULL,
  `invert` int(11) DEFAULT NULL,
  `qid` int(11) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `high_1` int(11) DEFAULT NULL,
  `high_2` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_list`
--

DROP TABLE IF EXISTS `questionnaire_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tablename` varchar(50) NOT NULL,
  `filename` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_list_names`
--

DROP TABLE IF EXISTS `questionnaire_list_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_list_names` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `qid` int(15) NOT NULL,
  `language` text NOT NULL,
  `header_name` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `questionnaire_list_id_constr` (`qid`),
  CONSTRAINT `questionnaire_list_id_constr` FOREIGN KEY (`qid`) REFERENCES `questionnaire_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_process_scales`
--

DROP TABLE IF EXISTS `questionnaire_process_scales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_process_scales` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `items` longtext NOT NULL,
  `item_invert` longtext NOT NULL,
  `title` varchar(25) NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_released`
--

DROP TABLE IF EXISTS `questionnaire_released`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_released` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `therapist` varchar(20) NOT NULL,
  `patientcode` varchar(20) NOT NULL,
  `qid` int(11) NOT NULL,
  `datum` date NOT NULL,
  `finished` tinyint(1) NOT NULL,
  `instance` varchar(100) NOT NULL,
  `activation` date NOT NULL,
  `daysInterval` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2248 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_scales`
--

DROP TABLE IF EXISTS `questionnaire_scales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_scales` (
  `skala` text DEFAULT NULL,
  `mean` double DEFAULT NULL,
  `sd` double DEFAULT NULL,
  `cutOff1` double DEFAULT NULL,
  `cutOff2` double DEFAULT NULL,
  `cutOff3` double DEFAULT NULL,
  `qid` int(11) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `invert` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_scales_hat`
--

DROP TABLE IF EXISTS `questionnaire_scales_hat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_scales_hat` (
  `scaleId` int(11) DEFAULT NULL,
  `columnName` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `questionnaire_status_scales`
--

DROP TABLE IF EXISTS `questionnaire_status_scales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questionnaire_status_scales` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `items` longtext NOT NULL,
  `item_invert` longtext NOT NULL,
  `title` varchar(25) NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  `mean` float NOT NULL,
  `sd` float NOT NULL,
  `low` float NOT NULL,
  `mid` float NOT NULL,
  `high` float NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reminds_deleted`
--

DROP TABLE IF EXISTS `reminds_deleted`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reminds_deleted` (
  `id` int(11) DEFAULT NULL,
  `therapist` text DEFAULT NULL,
  `code` text DEFAULT NULL,
  `instance` text DEFAULT NULL,
  `type` text DEFAULT NULL,
  `inactive_questionnaire` text DEFAULT NULL,
  `date` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sb_start`
--

DROP TABLE IF EXISTS `sb_start`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sb_start` (
  `ID` int(15) NOT NULL AUTO_INCREMENT,
  `sb_id` char(7) NOT NULL,
  `sb_start_01` char(7) NOT NULL,
  `sb_start_02` int(3) NOT NULL,
  `sb_start_03` char(5) DEFAULT NULL,
  `sb_version` char(7) DEFAULT NULL,
  `sb_start_date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `sb_id` (`sb_id`),
  KEY `sb_start_date` (`sb_start_date`)
) ENGINE=InnoDB AUTO_INCREMENT=29655 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `CODE` varchar(255) NOT NULL DEFAULT '',
  `GENDER` double DEFAULT NULL,
  `SCHOOL` double DEFAULT NULL,
  `THERAPIST` varchar(255) DEFAULT NULL,
  `SUPERVIS` varchar(255) DEFAULT NULL,
  `THERAPI2` double DEFAULT NULL,
  `NAWECHSL` double DEFAULT NULL,
  `ALTCODE` varchar(255) DEFAULT NULL,
  `EPROJECT` double DEFAULT NULL,
  `ERSTSICH` datetime DEFAULT NULL,
  `Projekt` int(11) DEFAULT NULL,
  `Reference` int(11) DEFAULT NULL,
  `LANGUAGE` int(11) DEFAULT NULL,
  `BIRTHDAY` datetime DEFAULT NULL,
  `ENDINSTA` varchar(64) DEFAULT NULL,
  `COMMENT` varchar(20) DEFAULT NULL,
  `CURINSTA` varchar(20) DEFAULT NULL,
  `ZUSTAND` int(11) DEFAULT NULL,
  `COMMENT2` varchar(100) DEFAULT NULL,
  `LOCATION` smallint(6) DEFAULT NULL,
  `LATEST` date DEFAULT NULL,
  `Studie` smallint(6) DEFAULT NULL,
  `COMPLETE` tinyint(4) DEFAULT NULL,
  `SOURCE` tinyint(4) DEFAULT NULL,
  `CATALOG` int(11) DEFAULT NULL,
  `GROUPS` double DEFAULT NULL,
  `CATALOGS` varchar(250) DEFAULT NULL,
  `DIAGN1` varchar(200) DEFAULT NULL,
  `DIAGN2` varchar(200) DEFAULT NULL,
  `DIAGN3` varchar(200) DEFAULT NULL,
  `DIAGN4` varchar(200) DEFAULT NULL,
  `DIAGN5` varchar(200) DEFAULT NULL,
  `ALIAS` varchar(200) DEFAULT NULL,
  `view_status` tinyint(2) NOT NULL,
  PRIMARY KEY (`CODE`),
  KEY `EndInstaIndex` (`ENDINSTA`),
  KEY `BirthdayIndex` (`BIRTHDAY`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `ID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `FIRST_NAME` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `LAST_NAME` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `INITIALS` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `PASSWORD` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ROLE` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kohorte` int(11) NOT NULL,
  `rechte_wb` tinyint(1) NOT NULL,
  `rechte_feedback` tinyint(1) NOT NULL,
  `rechte_entscheidung` tinyint(1) NOT NULL,
  `rechte_nn` tinyint(1) NOT NULL,
  `rechte_uebungen` tinyint(1) NOT NULL,
  `change_password` tinyint(2) NOT NULL,
  `rechte_zuweisung` tinyint(2) NOT NULL,
  `rechte_wb_questionnaire` tinyint(2) NOT NULL,
  `rechte_verlauf_normal` tinyint(1) NOT NULL DEFAULT 1,
  `rechte_verlauf_gruppe` tinyint(1) NOT NULL,
  `rechte_verlauf_online` tinyint(1) NOT NULL,
  `rechte_verlauf_seminare` tinyint(1) NOT NULL,
  `rechte_zw` tinyint(2) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `INITIALS` (`INITIALS`)
) ENGINE=InnoDB AUTO_INCREMENT=474 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wohlbefinden_patient`
--

DROP TABLE IF EXISTS `wohlbefinden_patient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wohlbefinden_patient` (
  `CODE` varchar(255) DEFAULT NULL,
  `INSTANCE` varchar(255) DEFAULT NULL,
  `WBPDAT` datetime DEFAULT NULL,
  `WBP001` double DEFAULT NULL,
  `WBP002` double DEFAULT NULL,
  `WBP003` double DEFAULT NULL,
  `WBP004` double DEFAULT NULL,
  `WBP005` double DEFAULT NULL,
  `WBP006` double DEFAULT NULL,
  KEY `INSTIndex` (`INSTANCE`),
  KEY `CODEIndex` (`CODE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-11-04 14:54:47

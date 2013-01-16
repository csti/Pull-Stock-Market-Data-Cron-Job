

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table ss_data_public
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ss_data_public`;

CREATE TABLE `ss_data_public` (
  `id` int(12) unsigned NOT NULL auto_increment,
  `symbol` varchar(6) default '',
  `date` date NOT NULL,
  `time` time NOT NULL,
  `high` float(7,3) unsigned NOT NULL,
  `low` float(7,3) unsigned NOT NULL,
  `open` float(7,3) unsigned NOT NULL,
  `close` float(7,3) unsigned NOT NULL,
  `volume` int(9) unsigned NOT NULL,
  `pe_ratio` varchar(100) NOT NULL default '',
  `change_amount` varchar(100) NOT NULL default '',
  `change_percent` varchar(100) NOT NULL default '',
  `sequence_id` int(12) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `stock per day` (`date`,`symbol`),
  KEY `INDEX` (`symbol`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ss_data_sequences
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ss_data_sequences`;

CREATE TABLE `ss_data_sequences` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `completed` tinyint(1) unsigned NOT NULL default '0',
  `completed_on` timestamp NULL default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ss_stocks_public
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ss_stocks_public`;

CREATE TABLE `ss_stocks_public` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `symbol` varchar(6) default '',
  `company` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `exchange` varchar(10) NOT NULL,
  `sector` varchar(100) NOT NULL default '',
  `industry` varchar(100) NOT NULL default '',
  `employees` int(11) unsigned NOT NULL,
  `oneyeartarget` float(7,3) unsigned NOT NULL,
  `yearlow` float(7,3) unsigned NOT NULL,
  `yearhigh` float(7,3) unsigned NOT NULL,
  `date_updated` timestamp NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `symbol` (`symbol`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

# noinspection SqlNoDataSourceInspectionForFile

CREATE DATABASE IF NOT EXISTS `bpositive` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `bpositive`;

GRANT ALL PRIVILEGES ON `bpositive`.* TO `bpositive`@localhost IDENTIFIED BY 'bpositivepass';

CREATE TABLE `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `deleted` int(11) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `transcription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `description` text NOT NULL,
  `linkZip` varchar(100) NOT NULL,
  `linkPdf` varchar(100) NOT NULL,
  `deleted` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `analyzed` int(11) NOT NULL,
  `positivelySelected` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

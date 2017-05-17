CREATE DATABASE tereplaces;

use tereplaces;

DROP TABLE IF EXISTS `searches_count`;

CREATE TABLE `searches_count` (
  `num_searches` int(11) DEFAULT NULL,
  `day` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
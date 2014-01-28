DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `address_id` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `name`, `address_id`)
VALUES
	(1,'User 1', 1),
	(2,'User 2', NULL),
	(3,'User 3', NULL),
	(4,'User 4', NULL);

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `zip_code` varchar(100) NOT NULL,
  `locatoion` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `address` (`id`, `zip_code`, `locatoion`)
VALUES
	(1,'1000', 'Belvedere');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NULL,
  `password` varchar(100) NULL,
  `address_id` int(11) UNSIGNED NULL,
  `parent_id` int(11) UNSIGNED NULL,
  `deleted_at` TIMESTAMP NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `zip_code` varchar(100) NULL,
  `locatoion` varchar(100) NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NULL,
  `body` varchar(100) NULL,
  `user_id` int(11) UNSIGNED NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `name`, `address_id`)
VALUES
  (1,'User 1', 1),
  (2,'User 2', NULL),
  (3,'User 3', NULL),
  (4,'User 4', 1);

INSERT INTO `address` (`id`, `zip_code`, `locatoion`)
VALUES
  (1,'1000', 'Belvedere');

INSERT INTO `post` (`id`, `title`, `body`, `user_id`)
VALUES
  (1,'News', 'Big news on the ship', 1),
  (2,'New President', 'We will have a new president soon', 4),
  (3,'Oil Spill', 'BP did it again', 5);

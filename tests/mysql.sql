DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NULL,
  `password` varchar(100) NULL,
  `addressId` int(11) UNSIGNED NULL,
  `parentId` int(11) UNSIGNED NULL,
  `isBlocked` int(1) UNSIGNED NULL,
  `deletedAt` TIMESTAMP NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `zipCode` varchar(100) NULL,
  `location` varchar(100) NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NULL,
  `body` varchar(100) NULL,
  `userId` int(11) UNSIGNED NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `name`, `addressId`, `isBlocked`)
VALUES
  (1,'User 1', 1, 0),
  (2,'User 2', NULL, 1),
  (3,'User 3', NULL, 1),
  (4,'User 4', 1, NULL);

INSERT INTO `address` (`id`, `zipCode`, `location`)
VALUES
  (1,'1000', 'Belvedere');

INSERT INTO `post` (`id`, `title`, `body`, `userId`)
VALUES
  (1,'News', 'Big news on the ship', 1),
  (2,'New President', 'We will have a new president soon', 4),
  (3,'Oil Spill', 'BP did it again', 5);

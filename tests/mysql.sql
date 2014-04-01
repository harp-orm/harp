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
  `body` MEDIUMTEXT NULL,
  `price` DECIMAL(10, 2) NULL,
  `tags` varchar(255) NULL,
  `createdAt` TIMESTAMP,
  `updatedAt` TIMESTAMP,
  `publishedAt` DATETIME,
  `userId` int(11) UNSIGNED NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstName` varchar(100) NULL,
  `lastName` varchar(100) NULL,
  `userId` int(11) UNSIGNED NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `name`, `addressId`, `isBlocked`)
VALUES
  (1,'User 1', 1, 0),
  (2,'User 2', NULL, 1),
  (3,'User 3', NULL, 1),
  (4,'User 4', 1, NULL);

INSERT INTO `profile` (`id`, `firstName`, `lastName`, `userId`)
VALUES
  (1,'John', 'Doe', 1),
  (2,'Foo', 'Bar', 2);

INSERT INTO `address` (`id`, `zipCode`, `location`)
VALUES
  (1,'1000', 'Belvedere');

INSERT INTO `post` (`id`, `title`, `body`,`price`,`tags`, `createdAt`, `updatedAt`, `publishedAt`, `userId`)
VALUES
  (1,'News', 'Big news on the ship', 10.20, 'big,small,medium', '2014-02-10 12:00:00', '2014-02-20 12:00:00', '2014-03-01 12:00:00', 1),
  (2,'New President', 'We will have a new president soon', 10.20, 'medium', '2014-01-10 12:00:00', '2014-01-20 12:00:00', '2014-03-02 12:00:00', 4),
  (3,'Oil Spill', 'BP did it again', 10.20, 'big,medium', '2014-02-20 12:20:00', '2014-02-23 12:00:00', '2014-3-03 12:00:00', 5);

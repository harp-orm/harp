DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NULL,
  `password` varchar(100) NULL,
  `addressId` int(11) UNSIGNED NULL,
  `parentId` int(11) UNSIGNED NULL,
  `isBlocked` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `locationId` int(1) UNSIGNED NULL,
  `locationClass` varchar(100) NULL,
  `object` varchar(255) NULL,
  `deletedAt` TIMESTAMP NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `City`;
CREATE TABLE `City` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NULL,
  `countryId` int(11) UNSIGNED NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Country`;
CREATE TABLE `Country` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Address`;
CREATE TABLE `Address` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `zipCode` varchar(100) NULL,
  `location` varchar(100) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Post`;
CREATE TABLE `Post` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NULL,
  `body` MEDIUMTEXT NULL,
  `price` DECIMAL(10, 2) NULL,
  `tags` varchar(255) NULL,
  `createdAt` TIMESTAMP,
  `updatedAt` TIMESTAMP,
  `publishedAt` DATETIME,
  `userId` int(11) UNSIGNED NULL,
  `class` varchar(255) NULL,
  `isPublished` int(1) UNSIGNED NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Profile`;
CREATE TABLE `Profile` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstName` varchar(100) NULL,
  `lastName` varchar(100) NULL,
  `userId` int(11) UNSIGNED NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Tag`;
CREATE TABLE `Tag` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `PostTag`;
CREATE TABLE `PostTag` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `postId` int(11) UNSIGNED NULL,
  `tagId` int(11) UNSIGNED NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `User` (`id`, `name`, `addressId`, `isBlocked`, `locationId`, `locationClass`, `object`, `deletedAt`)
VALUES
  (1, 'User 1', 1   , 0   , 1, 'Harp\\Harp\\Test\\TestModel\\City', 'C:41:"Harp\\Harp\\Test\\Integration\\SaveableObject":22:{a:1:{i:0;s:5:"test1";}}', NULL),
  (2, 'User 2', NULL, 1   , 2, 'Harp\\Harp\\Test\\TestModel\\City', NULL, NULL),
  (3, 'User 3', NULL, 1   , 1, 'Harp\\Harp\\Test\\TestModel\\Country', NULL, NULL),
  (4, 'User 4', 2   , NULL, 2, 'Harp\\Harp\\Test\\TestModel\\Country', NULL, NULL),
  (5, 'deleted', NULL   , NULL, NULL, NULL, NULL, '2014-02-10 12:00:00');

INSERT INTO `Profile` (`id`, `firstName`, `lastName`, `userId`)
VALUES
  (1,'John', 'Doe', 1),
  (2,'Foo', 'Bar', 2);

INSERT INTO `Address` (`id`, `zipCode`, `location`)
VALUES
  (1,'1000', 'Belvedere'),
  (2,'1000', 'Tsarigradsko Shose 1');

INSERT INTO `Post` (`id`, `title`, `body`,`price`,`tags`, `createdAt`, `updatedAt`, `publishedAt`, `userId`, `class`, `isPublished`)
VALUES
  (1,'News', 'Big news on the ship', 10.20, 'big,small,medium', '2014-02-10 12:00:00', '2014-02-20 12:00:00', '2014-03-01 12:00:00', 1, 'Harp\\Harp\\Test\\TestModel\\Post', NULL),
  (2,'New President', 'We will have a new president soon', 10.20, 'medium', '2014-01-10 12:00:00', '2014-01-20 12:00:00', '2014-03-02 12:00:00', 4, 'Harp\\Harp\\Test\\TestModel\\Post', NULL),
  (3,'Oil Spill', 'BP did it again', 10.20, 'big,medium', '2014-02-20 12:20:00', '2014-02-23 12:00:00', '2014-3-03 12:00:00', 4, 'Harp\\Harp\\Test\\TestModel\\Post', NULL),
  (4,'Blog News', 'DHH Does not like TDD', 1.20, 'small', '2014-04-20 12:20:00', '2014-07-23 12:00:00', '2014-3-04 12:00:00', 3, 'Harp\\Harp\\Test\\TestModel\\BlogPost', 1);

INSERT INTO `Tag` (`id`, `name`)
VALUES
  (1, 'buzzword'),
  (2, 'green');

INSERT INTO `PostTag` (`id`, `postId`, `tagId`)
VALUES
  (1, 1, 1),
  (2, 1, 2),
  (3, 3, 2);

INSERT INTO `Country` (`id`, `name`)
VALUES
  (1, 'Bulgaria'),
  (2, 'United Kingdom');

INSERT INTO `City` (`id`, `name`, `countryId`)
VALUES
  (1, 'Sofia', 1),
  (2, 'Pernik', 1),
  (3, 'London', 2),
  (4, 'Manchester', 2);

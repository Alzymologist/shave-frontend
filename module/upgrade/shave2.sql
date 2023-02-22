----------------------------------------------------------
--
-- Структура таблицы `shave_test`
--
-- CREATE TABLE IF NOT EXISTS `shave_test` (
--   `id` tinyint(10) NOT NULL auto_increment COMMENT 'id',
--   `val` tinyint(10) NOT NULL COMMENT 'val',
--   PRIMARY KEY (`id`),
--   UNIQUE KEY (`val`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=1;
--
----------------------------------------------------------
--
-- Структура таблицы `shave_login`
--
CREATE TABLE IF NOT EXISTS `shave_login` (
  `uid` int(10) unsigned NOT NULL auto_increment COMMENT 'Autoincrement uid',
  `login` varchar(255) NOT NULL COMMENT 'Login',
  `password` char(32) NOT NULL COMMENT 'blake3(Password,16)',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `login` (`login`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SHAVE Logins' AUTO_INCREMENT=1;

----------------------------------------------------------
--
-- Структура таблицы `shave_friends`
--
CREATE TABLE IF NOT EXISTS `shave_friends` (
  `uid` int(10) unsigned NOT NULL default '0' COMMENT 'uid login',
  `friend` int(10) unsigned NOT NULL default '0' COMMENT 'uid friend',
  PRIMARY KEY (`uid`,`friend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

----------------------------------------------------------
--
-- Структура таблицы `shave-items`
--
CREATE TABLE IF NOT EXISTS `shave-items` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'Autoincrement ID',
  `blake3DOI` char(32) NOT NULL COMMENT 'blake3(DOI)',
  `uid` int(10) unsigned NOT NULL default '0' COMMENT 'ID of Author or 0',
  `DOI` LONGTEXT NOT NULL COMMENT 'DOI',
  `Date` int(10) unsigned NOT NULL default '0' COMMENT 'Date of isseu',
  `Date_created` int(10) unsigned NOT NULL default '0' COMMENT 'Added to database',
  `author` LONGTEXT NOT NULL COMMENT 'Authors or Login',
  `about` LONGTEXT NOT NULL COMMENT 'About publication',
  `raw` LONGTEXT NOT NULL COMMENT 'Raw Data',
--    `Like` smallint(5) unsigned NOT NULL default '0' COMMENT 'Count of Likes',
--    `Dislike` smallint(5) unsigned NOT NULL default '0' COMMENT 'Count of Dislikes',
--    `commFROM` smallint(5) unsigned NOT NULL default '0' COMMENT 'Count of Comments',
--    `commTO` smallint(5) unsigned NOT NULL default '0' COMMENT 'Count of Comments',
  PRIMARY KEY (`id`),
  UNIQUE KEY `blake3DOI` (`blake3DOI`(32)),
  KEY `uid` (`uid`),
  KEY `Date` (`Date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='SHAVE Data' AUTO_INCREMENT=1;


----------------------------------------------------------
--
-- Структура таблицы `shave-like`
--
CREATE TABLE IF NOT EXISTS `shave-like` (
  `uid` int(10) unsigned NOT NULL default '0' COMMENT 'login uid',
  `id` int(10) unsigned NOT NULL default '0' COMMENT 'item id',
  `Date` int(10) unsigned NOT NULL default '0' COMMENT 'Date of like',
  `like` enum('like','dislike') NOT NULL default 'like',
  PRIMARY KEY (`id`,`uid`),
  KEY `like` (`like`),
  KEY `Date` (`Date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='like-dislike';

----------------------------------------------------------
--
-- Структура таблицы `shave-tags`
--
CREATE TABLE IF NOT EXISTS `shave-tags` (
  `id` int(10) unsigned NOT NULL default '0' COMMENT 'answer id',
  `toid` int(10) unsigned NOT NULL default '0' COMMENT 'answer TO id',
  `uid` int(10) unsigned NOT NULL default '0' COMMENT 'uid login',
  PRIMARY KEY (`id`,`toid`),
  KEY `uid` (`uid`),
  KEY `toid` (`toid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

----------------------------------------------------------
--
-- Структура таблицы `shave-extsearch`
--
CREATE TABLE IF NOT EXISTS `shave-extsearch` (
  `blake3search` char(32) NOT NULL COMMENT 'blake3(search text)',
  `total` int(10) NOT NULL default '0' COMMENT 'Total',
  `Date` int(10) unsigned NOT NULL default '0' COMMENT 'Date of isseu',
  PRIMARY KEY (`blake3search`),
  KEY `total` (`total`),
  KEY `Date` (`Date`)
) ENGINE=InnoDB COMMENT='SHAVE external search';


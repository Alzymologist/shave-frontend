-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
-- База данных: `lleoblog`
-- ОСНОВНОЙ МИНИМУМ

-- --------------------------------------------------------
--
-- Структура таблицы `mailbox`
--
CREATE TABLE IF NOT EXISTS `mailbox` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `answerid` int(10) unsigned NOT NULL default '0' COMMENT 'ответ на',
  `unicfrom` int(10) unsigned NOT NULL default '0' COMMENT 'id отправителя',
  `unicto` int(10) unsigned NOT NULL default '0' COMMENT 'id получателя',
  `timecreate` int(11) unsigned NOT NULL default '0' COMMENT 'Время создания',
  `timeview` int(11) unsigned NOT NULL default '0' COMMENT 'Время первого прочтения',
  `timeread` int(11) unsigned NOT NULL default '0' COMMENT 'Время подтверждения прочтения',
  `text` text NOT NULL COMMENT 'Текст письма',
  `IPN` int(10) unsigned NOT NULL default '0' COMMENT 'IP в цифре',
  `BRO` varchar(1024) NOT NULL COMMENT 'Браузер все-таки запишем?',
  `whois` varchar(128) NOT NULL  COMMENT 'Определялка страны',
  PRIMARY KEY  (`id`),
  KEY `new` (`timeread`,`unicto`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='Почта посетителей' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `geoip`
--
CREATE TABLE IF NOT EXISTS `geoip` (
    `from` VARBINARY(16) NOT NULL COMMENT 'Начало IP-диапазона',
    `to` VARBINARY(16) NOT NULL COMMENT 'Конец IP-диапазона',
    `i` int(10) unsigned NOT NULL COMMENT 'Идентификатор результата в geoipd',
  PRIMARY KEY (`from`),
  KEY `to` (`to`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='база IP-адресов мира';

----------------------------------------------------------
--
-- Структура таблицы `geoipd`
--
CREATE TABLE IF NOT EXISTS `geoipd` (
    `i` int(10) unsigned NOT NULL auto_increment COMMENT 'Номер записи',
    `country` char(5) NOT NULL COMMENT 'Код страны',
    `city` varchar(160) NOT NULL COMMENT 'Город',
    PRIMARY KEY (`i`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='результаты для geoip' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `userdata`
--
CREATE TABLE IF NOT EXISTS `userdata` (
  `acn` int(10) unsigned NOT NULL auto_increment COMMENT 'Номер журнала',
  `basa` varchar(32) NOT NULL COMMENT 'база',
  `name` varchar(32) NOT NULL COMMENT 'имя',
  `data` text NOT NULL COMMENT 'данные',
  `dostup` enum('all','adm') NOT NULL default 'adm' COMMENT 'Доступ: админу или всем',
   PRIMARY KEY (`acn`),
   KEY `ibu` (`name`(32),`basa`(32),`acn`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Пользовательские данные' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `unic`
--
--    `teddyid` int(11) NOT NULL default '0' COMMENT 'мобильный логин https://teddyid.com',
--   `aboutme` varchar(2048) NOT NULL COMMENT 'личное: О себе',

CREATE TABLE IF NOT EXISTS `unic` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT 'Личный номер из куки',
  `realname` varchar(64) NOT NULL COMMENT 'имя/ник (предпочтительно имя-фамилия)',
  `openid` varchar(128) NOT NULL COMMENT 'inf-url',
  `login` varchar(32) NOT NULL COMMENT 'логин на сайте',
  `password` varchar(32) NOT NULL COMMENT 'пароль',
  `mail` varchar(64) NOT NULL COMMENT 'mail при регистрации - нельзя сменить никогда',
  `mailw` varchar(64) NOT NULL COMMENT 'действующий mail (изначально совпадает)',
  `tel` varchar(16) NOT NULL COMMENT 'мобильник при регистрации - нельзя сменить никогда',
  `telw` varchar(16) NOT NULL COMMENT 'действующий мобильник (изначально совпадает)',
  `img` varchar(180) NOT NULL COMMENT 'ссылка на фотку.jpg',
  `mail_comment` enum('1','0') NOT NULL default '1' COMMENT 'личное: отправлять ли комментарии на email?',
  `site` varchar(128) NOT NULL COMMENT 'пользователь указал личный сайт',
  `birth` date NOT NULL default '0000-00-00' COMMENT 'пользователь указал дату рождения',
  `admin` enum('user','podzamok') NOT NULL default 'user' COMMENT 'подзамочный доступ',
  `ipn` int(10) unsigned NOT NULL default '0' COMMENT 'ip при последнем редактировании личной карточки',
  `telegram` int(10) unsigned NOT NULL default '0' COMMENT 'telegram id',
  `time_reg` int(11) NOT NULL default '0' COMMENT 'время регистрации',
  `timelast` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'время последнего обновления личной карточки',
  `capcha` enum('yes','no') NOT NULL default 'no',
  `capchakarma` tinyint(3) unsigned NOT NULL default '0' COMMENT 'Капча-карма нового формата',
  `opt` text NOT NULL,
  `NFC` varchar(32) NOT NULL COMMENT 'hash NFC',
  PRIMARY KEY  (`id`),
  KEY `login` (`login`(32))
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='Логины посетителей' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `dnevnik_link`
--
CREATE TABLE IF NOT EXISTS `dnevnik_link` (
  `n` bigint(20) NOT NULL auto_increment,
  `link` varchar(2048) NOT NULL,
  `count` int(10) NOT NULL default '0',
  `datetime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `DateID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`n`),
  KEY `DateID` (`DateID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='заходы по ссылкам' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `dnevnik_posetil`
--
CREATE TABLE IF NOT EXISTS `dnevnik_posetil` (
  `unic` int(10) unsigned NOT NULL default '0',
  `url` int(10) unsigned NOT NULL default '0',
  `date` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`url`,`unic`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='посещения заметок';

----------------------------------------------------------
--
-- Структура таблицы `dnevnik_search`
--
CREATE TABLE IF NOT EXISTS `dnevnik_search` (
  `n` bigint(20) NOT NULL auto_increment,
  `poiskovik` varchar(32) NOT NULL,
  `link` varchar(2048) NOT NULL,
  `search` varchar(2048) NOT NULL,
  `count` int(10) NOT NULL default '0',
  `datetime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `DateID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`n`),
  KEY `link` (`link`(1000))
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='поисковые заходы' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `dnevnik_zapisi`
--
CREATE TABLE IF NOT EXISTS `dnevnik_zapisi` (
  `num` int(10) unsigned NOT NULL auto_increment,
  `Date` varchar(128) NOT NULL,
  `Header` varchar(255) NOT NULL,
  `Body` mediumtext NOT NULL,
  `Access` enum('all','podzamok','admin') NOT NULL default 'admin',
  `visible` enum('1','0') NOT NULL default '1',
  `DateUpdate` int(10) unsigned NOT NULL default '0',
  `view_counter` int(10) unsigned NOT NULL default '0',
  `DateDatetime` int(11) NOT NULL default '0',
  `DateDate` int(11) NOT NULL default '0',
  `opt` text NOT NULL,
  `acn` int(10) unsigned NOT NULL default '0' COMMENT 'Номер журнала',
  UNIQUE KEY `num` (`num`),
  KEY `acn` (`acn`),
  KEY `Date` (`Date`(128)),
  KEY `Access` (`Access`),
  KEY `visible` (`visible`),
  KEY `DateDatetime` (`DateDatetime`),
  KEY `DateDate` (`DateDate`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='Заметки блога' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `site`
--
CREATE TABLE IF NOT EXISTS `site` (
  `name` varchar(128) NOT NULL,
  `text` text NOT NULL,
  `acn` int(10) unsigned NOT NULL default '0' COMMENT 'Номер журнала',
  PRIMARY KEY (`name`(128),`acn`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='Полезные переменные пользователей' AUTO_INCREMENT=0;

-- --------------------------------------------------------
--
-- Структура таблицы `unictemp`
--
CREATE TABLE IF NOT EXISTS `unictemp` (
  `unic` int(10) unsigned NOT NULL default '0',
  `text` text NOT NULL,
  `timelast` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`unic`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Временные данные пользователя';

----------------------------------------------------------
--
-- Структура таблицы `dnevnik_tags`
--
CREATE TABLE IF NOT EXISTS `dnevnik_tags` (
  `num` int(10) unsigned NOT NULL default '0' COMMENT 'id заметки',
  `tag` varchar(128) NOT NULL COMMENT 'имя тэга',
  `acn` int(10) unsigned NOT NULL default '0' COMMENT 'Номер журнала',
  PRIMARY KEY (`num`,`acn`,`tag`(128)),
  KEY `acn` (`acn`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

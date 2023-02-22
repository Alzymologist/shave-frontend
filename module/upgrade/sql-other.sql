----------------------------------------------------------
--
-- Структура таблицы `socialmedias`
--
CREATE TABLE IF NOT EXISTS `socialmedias` (
  `i` int(10) unsigned NOT NULL auto_increment COMMENT 'Номер записи',
  `acn` int(10) unsigned NOT NULL default '0' COMMENT 'Номер журнала',
  `num` int(10) unsigned NOT NULL default '0' COMMENT 'Номер заметки',
  `comm` int(11) unsigned NOT NULL default '0' COMMENT 'Номер комментария, если постится комментарий',
  `net` varchar(64) NOT NULL default '' COMMENT 'СОЦСЕТЬ:ЮЗЕР',
  `url` varchar(128) NOT NULL default '' COMMENT 'url объекта, относящегося к заметке - url фотки, имя альбома, data заметки',
  `cap_sha1` varchar(40) NOT NULL default '' COMMENT 'sha1-хэш объекта для отслеживания изменений',
  `id` varchar(256) NOT NULL default '' COMMENT 'уникальный id',
  `type` enum('post','vk_album','vk_foto','vk_note','fb_album','fb_foto','ya_album','ya_foto','instagramm_foto','telegraph_file') NOT NULL default 'post' COMMENT 'вид материала',
  PRIMARY KEY (`i`),
    KEY `newi` (`num`,`comm`,`net`(64),`acn`),
    KEY `url` (`url`),
    KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='база объектов внешних постингов' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `golosovalka`
--
CREATE TABLE IF NOT EXISTS `golosovalka` (
  `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id голосующего',
  `acn` int(10) unsigned NOT NULL default '0' COMMENT 'id журнала',
  `gid` int(10) unsigned NOT NULL default '0' COMMENT 'id опроса',
  `vid` tinyint(3) unsigned NOT NULL default '0' COMMENT 'номер вопроса',
  `vad` tinyint(3) unsigned NOT NULL default '0' COMMENT 'вариант ответа',
  PRIMARY KEY  (`unic`,`vid`,`gid`,`acn`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Голосовалка';

----------------------------------------------------------
--   `unic` int(10) unsigned NOT NULL default '0' COMMENT 'Владелец',
--
-- Структура таблицы `jur`
--
CREATE TABLE IF NOT EXISTS `jur` (
  `acn` int(10) unsigned NOT NULL auto_increment COMMENT 'Номер журнала',
  `acc` varchar(32) NOT NULL default '' COMMENT 'Имя журнала',
  `unic` text NOT NULL default '' COMMENT 'Владельцы через запятую',
  `domain` varchar(32) NOT NULL default '' COMMENT 'Личный домен если ходит с него',
  `time` timestamp DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT 'время последнего обновления админов',
   PRIMARY KEY (`acn`),
   KEY `domain` (`domain`(32)),
   KEY `acc` (`acc`(32))
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='База журналов' AUTO_INCREMENT=0;

-- --------------------------------------------------------
--
-- Структура таблицы `dnevnik_comm`
--
CREATE TABLE IF NOT EXISTS `dnevnik_comm` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id автора',
  `DateID` int(10) NOT NULL default '0' COMMENT 'К какой заметке относится',
  `Name` varchar(128) NOT NULL default '' COMMENT 'Автор',
  `Mail` varchar(128) NOT NULL default '' COMMENT 'email',
  `Text` text NOT NULL default '' COMMENT 'Текст комментария',
  `Parent` int(10) unsigned NOT NULL default '0' COMMENT 'На что ответ',
  `Time` int(11) unsigned NOT NULL default '0' COMMENT 'Время комментария',
  `IPN` int(10) unsigned NOT NULL default '0' COMMENT 'IP в цифре',
  `BRO` varchar(1024) NOT NULL default '' COMMENT 'Браузер все-таки запишем?',
  `whois` varchar(128) NOT NULL default '' COMMENT 'Определялка страны',
  `scr` enum('1','0') NOT NULL default '0' COMMENT 'Открытый, скрытый',
  `rul` enum('1','0') NOT NULL default '0' COMMENT 'Особый',
  `ans` enum('1','0','u') NOT NULL default 'u' COMMENT 'Разрешено ли принимать комментарии к нему?',
  `group` tinyint(3) unsigned NOT NULL default '0' COMMENT 'Группа для выделения разным цветом. 0 - все, 1 - админ, 2... ну, допустим, Topbot',
  `golos_plu` int(10) unsigned NOT NULL default '0' COMMENT 'Голосование плюсики',
  `golos_min` int(10) unsigned NOT NULL default '0' COMMENT 'Голосование минусики',
  PRIMARY KEY  (`id`),
  KEY `Parent` (`Parent`),
  KEY `Time` (`Time`),
  KEY `scr` (`scr`),
  KEY `DateID` (`DateID`),
  KEY `poset` (`unic`,`scr`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='Комментарии посетителей' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `dnevnik_plusiki`
--
CREATE TABLE IF NOT EXISTS `dnevnik_plusiki` (
  `unic` int(10) unsigned NOT NULL default '0',
  `commentID` int(10) unsigned NOT NULL default '0',
  `var` enum('plus','minus') NOT NULL default 'plus',
  PRIMARY KEY  (`unic`,`commentID`),
  KEY `commentID` (`commentID`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='плюсики к комментариям';

----------------------------------------------------------
--
-- Структура таблицы `dnevnik_autopost`
--
CREATE TABLE IF NOT EXISTS `dnevnik_autopost` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `Header` varchar(255) NOT NULL default '',
  `Body` mediumtext NOT NULL default '',
  `tag` varchar(64) NOT NULL default '',
  `postmode` enum('is_date','silent','silent_priority','day','tag_interval') NOT NULL default 'is_date',
  `randmode` enum('num','random') NOT NULL default 'num',
  `dat` int(11) NOT NULL default '0',
  `opt` text NOT NULL default '',
  PRIMARY KEY (`id`),
  KEY `dat` (`dat`),
  KEY `postmode` (`postmode`),
  KEY `randmode` (`randmode`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='Для автопостинга' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `pravki`
--
CREATE TABLE IF NOT EXISTS `pravki` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `Date` varchar(255) NOT NULL default '',
  `DateTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `acn` int(10) unsigned NOT NULL default '0' COMMENT 'Номер журнала',
  `unic` int(10) unsigned NOT NULL default '0',
  `text` text NOT NULL default '',
  `textnew` text NOT NULL default '',
  `stdprav` text NOT NULL default '',
  `Answer` text NOT NULL default '',
  `metka` enum('new','submit','discard') NOT NULL default 'new',
  PRIMARY KEY  (`id`),
  KEY `Date` (`Date`(255)),
  KEY `metka` (`metka`),
  KEY `unic` (`unic`),
  KEY `acn` (`acn`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='Правки блога' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `golosovanie_golosa` ??????
--
CREATE TABLE IF NOT EXISTS `golosovanie_golosa` (
  `golosid` int(10) unsigned NOT NULL default '0' COMMENT 'id голосования',
  `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id голосующего',
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `value` text NOT NULL default '',
  PRIMARY KEY  (`golosid`,`unic`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Голосования: голоса';

----------------------------------------------------------
-- unique
-- Структура таблицы `golosovanie_result`
--
CREATE TABLE IF NOT EXISTS `golosovanie_result` (
  `golosid` int(10) unsigned NOT NULL auto_increment COMMENT 'id голосования',
  `golosname` varchar(32) NOT NULL default '' COMMENT 'имя голосования',
  `n` int(10) NOT NULL default '0',
  `text` text NOT NULL default '',
  PRIMARY KEY (`golosid`),
  KEY `golosname` (`golosname`(32))
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Голосования: результаты' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- Структура таблицы `lastcomm`
--
CREATE TABLE IF NOT EXISTS `lastcomm` (
  `unic` int(10) unsigned NOT NULL default '0',
  `acn` int(10) unsigned NOT NULL default '0' COMMENT 'Номер журнала',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY (`unic`,`acn`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Дата последних прочитанных комментов';

------------------------------------------------------------

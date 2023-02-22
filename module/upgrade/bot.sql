

CREATE TABLE IF NOT EXISTS `bot_visit` (
  `unit` char(17) NOT NULL,
  `time` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`unit`),
  KEY `time` (`time`)
) ENGINE=MEMORY default CHARSET=cp1251 COMMENT='онлайн-девайсы' ;

-- --------------------------------------------------------
--
-- Структура таблицы `bot_tg_subscribe`
--     `i` int(10) unsigned NOT NULL auto_increment COMMENT 'просто номер записи',

CREATE TABLE IF NOT EXISTS `bot_tg_subscribe` (
    `time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'время',
    `unit` varchar(32) NOT NULL COMMENT 'уникальный id устройства, на который оформить подписку',
    `type` varchar(32) NOT NULL COMMENT 'тип сообщений подписки',
    `telegram` int(10) unsigned NOT NULL default '0' COMMENT 'telegram id',
    PRIMARY KEY (`unit`(32),`telegram`,`type`(32))
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='база сообщений от девайсов';

-- --------------------------------------------------------
--
-- Структура таблицы `bot_event`
--

CREATE TABLE IF NOT EXISTS `bot_event` (
    `i` int(10) unsigned NOT NULL auto_increment COMMENT 'просто номер записи',
    `time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'время',
    `unit` varchar(32) NOT NULL COMMENT 'уникальный id устройства, от которого пришло',
    `message` varchar(256) NOT NULL COMMENT 'присланное сообщение',
    PRIMARY KEY (`i`),
    KEY `unit` (`unit`),
    KEY `time` (`time`)
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='база сообщений от девайсов';


-- --------------------------------------------------------
--
-- Структура таблицы `bot_in`
--

CREATE TABLE IF NOT EXISTS `bot_in` (
    `i` int(10) unsigned NOT NULL auto_increment COMMENT 'просто номер записи',
    `time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'время',
    `unit` varchar(32) NOT NULL COMMENT 'уникальный id устройства, от которого пришло',
    `message` varchar(256) NOT NULL COMMENT 'присланное сообщение',
    PRIMARY KEY (`i`),
    KEY `unit` (`unit`(32)),
    KEY `time` (`time`)
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='база сообщений от девайсов';


-- --------------------------------------------------------
--
-- Структура таблицы `bot_out`
--
-- timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'время последнего обновления (massage или answer)',

CREATE TABLE IF NOT EXISTS `bot_out` (
    `i` int(10) unsigned NOT NULL auto_increment COMMENT 'просто номер записи',
    `time` int(11) NOT NULL default '0' COMMENT 'время последнего обновления (massage или answer)',
    `unit` varchar(32) NOT NULL COMMENT 'уникальный id устройства, которому адресовано',
    `message` text NOT NULL COMMENT 'передаваемое сообщение',
    `answer` text NOT NULL COMMENT 'возможный ответ на сообщение',
    `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id отправителя',
    `status` enum('new','read','answer') NOT NULL default 'new' COMMENT 'этапы обработки сообщений',
    PRIMARY KEY  (`i`),
    KEY `status` (`status`),
    KEY `unit` (`unit`(32)),
    KEY `time` (`time`)
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='база сообщений для девайсов';

-- --------------------------------------------------------
--
-- Структура таблицы `bot_device`
--

CREATE TABLE IF NOT EXISTS `bot_device` (
    `unit_id` int(10) unsigned NOT NULL auto_increment COMMENT 'просто номер записи',
    `time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'время регистрации',
    `unit` varchar(32) NOT NULL COMMENT 'уникальный id устройства',
    `name` varchar(32) NOT NULL default '' COMMENT 'данное устройству имя',
    `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id владельца',
    PRIMARY KEY (`unit_id`),
    KEY `unit` (`unit`(32)),
    KEY `nameunic` (`unic`,`name`(32))
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='зарегистрированные устройства';


-- --------------------------------------------------------
--
-- Структура таблицы `bot_device_info`
--
CREATE TABLE IF NOT EXISTS `bot_device_info` (
    `unit` varchar(32) NOT NULL COMMENT 'уникальный id устройства',
    `time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT 'время последнего обновления данных',
    `Settings` text  NOT NULL default '' COMMENT 'сообщенные данные IP и Settings.txt',
    `message` text NOT NULL default '' COMMENT 'передаваемое сообщение',
    `answer` text NOT NULL default '' COMMENT 'возможный ответ на сообщение',
    `status` enum('no','new','read','answer') NOT NULL default 'no' COMMENT 'этапы обработки сообщений',
    PRIMARY KEY (`unit`(32))
) ENGINE=InnoDB default CHARSET=UTF8 COMMENT='информация об устройстве';

-- --------------------------------------------------------
--
-- Структура таблицы `bot_script`
--

CREATE TABLE IF NOT EXISTS `bot_script` (
    `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id владельца',
    `text` text NOT NULL COMMENT 'текст скрипта',
    PRIMARY KEY (`unic`)
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='пользовательский скрипт';

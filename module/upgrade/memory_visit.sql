
CREATE TABLE IF NOT EXISTS `memory_visit` (
  `unic` int(10) unsigned NOT NULL,
  `time` int(11) unsigned NOT NULL default '0',
  `user` varchar(256) NOT NULL
---  PRIMARY KEY  (`unic`),
---  KEY `time` (`time`)
) ENGINE=MEMORY default CHARSET=cp1251 COMMENT='онлайн-визиты' ;

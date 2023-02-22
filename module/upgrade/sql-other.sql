----------------------------------------------------------
--
-- ��������� ������� `socialmedias`
--
CREATE TABLE IF NOT EXISTS `socialmedias` (
  `i` int(10) unsigned NOT NULL auto_increment COMMENT '����� ������',
  `acn` int(10) unsigned NOT NULL default '0' COMMENT '����� �������',
  `num` int(10) unsigned NOT NULL default '0' COMMENT '����� �������',
  `comm` int(11) unsigned NOT NULL default '0' COMMENT '����� �����������, ���� �������� �����������',
  `net` varchar(64) NOT NULL default '' COMMENT '�������:����',
  `url` varchar(128) NOT NULL default '' COMMENT 'url �������, ������������ � ������� - url �����, ��� �������, data �������',
  `cap_sha1` varchar(40) NOT NULL default '' COMMENT 'sha1-��� ������� ��� ������������ ���������',
  `id` varchar(256) NOT NULL default '' COMMENT '���������� id',
  `type` enum('post','vk_album','vk_foto','vk_note','fb_album','fb_foto','ya_album','ya_foto','instagramm_foto','telegraph_file') NOT NULL default 'post' COMMENT '��� ���������',
  PRIMARY KEY (`i`),
    KEY `newi` (`num`,`comm`,`net`(64),`acn`),
    KEY `url` (`url`),
    KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='���� �������� ������� ���������' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `golosovalka`
--
CREATE TABLE IF NOT EXISTS `golosovalka` (
  `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id �����������',
  `acn` int(10) unsigned NOT NULL default '0' COMMENT 'id �������',
  `gid` int(10) unsigned NOT NULL default '0' COMMENT 'id ������',
  `vid` tinyint(3) unsigned NOT NULL default '0' COMMENT '����� �������',
  `vad` tinyint(3) unsigned NOT NULL default '0' COMMENT '������� ������',
  PRIMARY KEY  (`unic`,`vid`,`gid`,`acn`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='�����������';

----------------------------------------------------------
--   `unic` int(10) unsigned NOT NULL default '0' COMMENT '��������',
--
-- ��������� ������� `jur`
--
CREATE TABLE IF NOT EXISTS `jur` (
  `acn` int(10) unsigned NOT NULL auto_increment COMMENT '����� �������',
  `acc` varchar(32) NOT NULL default '' COMMENT '��� �������',
  `unic` text NOT NULL default '' COMMENT '��������� ����� �������',
  `domain` varchar(32) NOT NULL default '' COMMENT '������ ����� ���� ����� � ����',
  `time` timestamp DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '����� ���������� ���������� �������',
   PRIMARY KEY (`acn`),
   KEY `domain` (`domain`(32)),
   KEY `acc` (`acc`(32))
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='���� ��������' AUTO_INCREMENT=0;

-- --------------------------------------------------------
--
-- ��������� ������� `dnevnik_comm`
--
CREATE TABLE IF NOT EXISTS `dnevnik_comm` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id ������',
  `DateID` int(10) NOT NULL default '0' COMMENT '� ����� ������� ���������',
  `Name` varchar(128) NOT NULL default '' COMMENT '�����',
  `Mail` varchar(128) NOT NULL default '' COMMENT 'email',
  `Text` text NOT NULL default '' COMMENT '����� �����������',
  `Parent` int(10) unsigned NOT NULL default '0' COMMENT '�� ��� �����',
  `Time` int(11) unsigned NOT NULL default '0' COMMENT '����� �����������',
  `IPN` int(10) unsigned NOT NULL default '0' COMMENT 'IP � �����',
  `BRO` varchar(1024) NOT NULL default '' COMMENT '������� ���-���� �������?',
  `whois` varchar(128) NOT NULL default '' COMMENT '����������� ������',
  `scr` enum('1','0') NOT NULL default '0' COMMENT '��������, �������',
  `rul` enum('1','0') NOT NULL default '0' COMMENT '������',
  `ans` enum('1','0','u') NOT NULL default 'u' COMMENT '��������� �� ��������� ����������� � ����?',
  `group` tinyint(3) unsigned NOT NULL default '0' COMMENT '������ ��� ��������� ������ ������. 0 - ���, 1 - �����, 2... ��, ��������, Topbot',
  `golos_plu` int(10) unsigned NOT NULL default '0' COMMENT '����������� �������',
  `golos_min` int(10) unsigned NOT NULL default '0' COMMENT '����������� ��������',
  PRIMARY KEY  (`id`),
  KEY `Parent` (`Parent`),
  KEY `Time` (`Time`),
  KEY `scr` (`scr`),
  KEY `DateID` (`DateID`),
  KEY `poset` (`unic`,`scr`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='����������� �����������' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `dnevnik_plusiki`
--
CREATE TABLE IF NOT EXISTS `dnevnik_plusiki` (
  `unic` int(10) unsigned NOT NULL default '0',
  `commentID` int(10) unsigned NOT NULL default '0',
  `var` enum('plus','minus') NOT NULL default 'plus',
  PRIMARY KEY  (`unic`,`commentID`),
  KEY `commentID` (`commentID`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='������� � ������������';

----------------------------------------------------------
--
-- ��������� ������� `dnevnik_autopost`
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
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='��� ������������' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `pravki`
--
CREATE TABLE IF NOT EXISTS `pravki` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `Date` varchar(255) NOT NULL default '',
  `DateTime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `acn` int(10) unsigned NOT NULL default '0' COMMENT '����� �������',
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
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='������ �����' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `golosovanie_golosa` ??????
--
CREATE TABLE IF NOT EXISTS `golosovanie_golosa` (
  `golosid` int(10) unsigned NOT NULL default '0' COMMENT 'id �����������',
  `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id �����������',
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `value` text NOT NULL default '',
  PRIMARY KEY  (`golosid`,`unic`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='�����������: ������';

----------------------------------------------------------
-- unique
-- ��������� ������� `golosovanie_result`
--
CREATE TABLE IF NOT EXISTS `golosovanie_result` (
  `golosid` int(10) unsigned NOT NULL auto_increment COMMENT 'id �����������',
  `golosname` varchar(32) NOT NULL default '' COMMENT '��� �����������',
  `n` int(10) NOT NULL default '0',
  `text` text NOT NULL default '',
  PRIMARY KEY (`golosid`),
  KEY `golosname` (`golosname`(32))
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='�����������: ����������' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `lastcomm`
--
CREATE TABLE IF NOT EXISTS `lastcomm` (
  `unic` int(10) unsigned NOT NULL default '0',
  `acn` int(10) unsigned NOT NULL default '0' COMMENT '����� �������',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY (`unic`,`acn`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='���� ��������� ����������� ���������';

------------------------------------------------------------

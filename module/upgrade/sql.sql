-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
-- ���� ������: `lleoblog`
-- �������� �������

-- --------------------------------------------------------
--
-- ��������� ������� `mailbox`
--
CREATE TABLE IF NOT EXISTS `mailbox` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `answerid` int(10) unsigned NOT NULL default '0' COMMENT '����� ��',
  `unicfrom` int(10) unsigned NOT NULL default '0' COMMENT 'id �����������',
  `unicto` int(10) unsigned NOT NULL default '0' COMMENT 'id ����������',
  `timecreate` int(11) unsigned NOT NULL default '0' COMMENT '����� ��������',
  `timeview` int(11) unsigned NOT NULL default '0' COMMENT '����� ������� ���������',
  `timeread` int(11) unsigned NOT NULL default '0' COMMENT '����� ������������� ���������',
  `text` text NOT NULL COMMENT '����� ������',
  `IPN` int(10) unsigned NOT NULL default '0' COMMENT 'IP � �����',
  `BRO` varchar(1024) NOT NULL COMMENT '������� ���-���� �������?',
  `whois` varchar(128) NOT NULL  COMMENT '����������� ������',
  PRIMARY KEY  (`id`),
  KEY `new` (`timeread`,`unicto`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='����� �����������' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `geoip`
--
CREATE TABLE IF NOT EXISTS `geoip` (
    `from` VARBINARY(16) NOT NULL COMMENT '������ IP-���������',
    `to` VARBINARY(16) NOT NULL COMMENT '����� IP-���������',
    `i` int(10) unsigned NOT NULL COMMENT '������������� ���������� � geoipd',
  PRIMARY KEY (`from`),
  KEY `to` (`to`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='���� IP-������� ����';

----------------------------------------------------------
--
-- ��������� ������� `geoipd`
--
CREATE TABLE IF NOT EXISTS `geoipd` (
    `i` int(10) unsigned NOT NULL auto_increment COMMENT '����� ������',
    `country` char(5) NOT NULL COMMENT '��� ������',
    `city` varchar(160) NOT NULL COMMENT '�����',
    PRIMARY KEY (`i`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='���������� ��� geoip' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `userdata`
--
CREATE TABLE IF NOT EXISTS `userdata` (
  `acn` int(10) unsigned NOT NULL auto_increment COMMENT '����� �������',
  `basa` varchar(32) NOT NULL COMMENT '����',
  `name` varchar(32) NOT NULL COMMENT '���',
  `data` text NOT NULL COMMENT '������',
  `dostup` enum('all','adm') NOT NULL default 'adm' COMMENT '������: ������ ��� ����',
   PRIMARY KEY (`acn`),
   KEY `ibu` (`name`(32),`basa`(32),`acn`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='���������������� ������' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `unic`
--
--    `teddyid` int(11) NOT NULL default '0' COMMENT '��������� ����� https://teddyid.com',
--   `aboutme` varchar(2048) NOT NULL COMMENT '������: � ����',

CREATE TABLE IF NOT EXISTS `unic` (
  `id` int(10) unsigned NOT NULL auto_increment COMMENT '������ ����� �� ����',
  `realname` varchar(64) NOT NULL COMMENT '���/��� (��������������� ���-�������)',
  `openid` varchar(128) NOT NULL COMMENT 'inf-url',
  `login` varchar(32) NOT NULL COMMENT '����� �� �����',
  `password` varchar(32) NOT NULL COMMENT '������',
  `mail` varchar(64) NOT NULL COMMENT 'mail ��� ����������� - ������ ������� �������',
  `mailw` varchar(64) NOT NULL COMMENT '����������� mail (���������� ���������)',
  `tel` varchar(16) NOT NULL COMMENT '��������� ��� ����������� - ������ ������� �������',
  `telw` varchar(16) NOT NULL COMMENT '����������� ��������� (���������� ���������)',
  `img` varchar(180) NOT NULL COMMENT '������ �� �����.jpg',
  `mail_comment` enum('1','0') NOT NULL default '1' COMMENT '������: ���������� �� ����������� �� email?',
  `site` varchar(128) NOT NULL COMMENT '������������ ������ ������ ����',
  `birth` date NOT NULL default '0000-00-00' COMMENT '������������ ������ ���� ��������',
  `admin` enum('user','podzamok') NOT NULL default 'user' COMMENT '����������� ������',
  `ipn` int(10) unsigned NOT NULL default '0' COMMENT 'ip ��� ��������� �������������� ������ ��������',
  `telegram` int(10) unsigned NOT NULL default '0' COMMENT 'telegram id',
  `time_reg` int(11) NOT NULL default '0' COMMENT '����� �����������',
  `timelast` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '����� ���������� ���������� ������ ��������',
  `capcha` enum('yes','no') NOT NULL default 'no',
  `capchakarma` tinyint(3) unsigned NOT NULL default '0' COMMENT '�����-����� ������ �������',
  `opt` text NOT NULL,
  `NFC` varchar(32) NOT NULL COMMENT 'hash NFC',
  PRIMARY KEY  (`id`),
  KEY `login` (`login`(32))
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='������ �����������' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `dnevnik_link`
--
CREATE TABLE IF NOT EXISTS `dnevnik_link` (
  `n` bigint(20) NOT NULL auto_increment,
  `link` varchar(2048) NOT NULL,
  `count` int(10) NOT NULL default '0',
  `datetime` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `DateID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`n`),
  KEY `DateID` (`DateID`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='������ �� �������' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `dnevnik_posetil`
--
CREATE TABLE IF NOT EXISTS `dnevnik_posetil` (
  `unic` int(10) unsigned NOT NULL default '0',
  `url` int(10) unsigned NOT NULL default '0',
  `date` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`url`,`unic`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='��������� �������';

----------------------------------------------------------
--
-- ��������� ������� `dnevnik_search`
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
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='��������� ������' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `dnevnik_zapisi`
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
  `acn` int(10) unsigned NOT NULL default '0' COMMENT '����� �������',
  UNIQUE KEY `num` (`num`),
  KEY `acn` (`acn`),
  KEY `Date` (`Date`(128)),
  KEY `Access` (`Access`),
  KEY `visible` (`visible`),
  KEY `DateDatetime` (`DateDatetime`),
  KEY `DateDate` (`DateDate`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='������� �����' AUTO_INCREMENT=0;

----------------------------------------------------------
--
-- ��������� ������� `site`
--
CREATE TABLE IF NOT EXISTS `site` (
  `name` varchar(128) NOT NULL,
  `text` text NOT NULL,
  `acn` int(10) unsigned NOT NULL default '0' COMMENT '����� �������',
  PRIMARY KEY (`name`(128),`acn`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 COMMENT='�������� ���������� �������������' AUTO_INCREMENT=0;

-- --------------------------------------------------------
--
-- ��������� ������� `unictemp`
--
CREATE TABLE IF NOT EXISTS `unictemp` (
  `unic` int(10) unsigned NOT NULL default '0',
  `text` text NOT NULL,
  `timelast` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (`unic`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='��������� ������ ������������';

----------------------------------------------------------
--
-- ��������� ������� `dnevnik_tags`
--
CREATE TABLE IF NOT EXISTS `dnevnik_tags` (
  `num` int(10) unsigned NOT NULL default '0' COMMENT 'id �������',
  `tag` varchar(128) NOT NULL COMMENT '��� ����',
  `acn` int(10) unsigned NOT NULL default '0' COMMENT '����� �������',
  PRIMARY KEY (`num`,`acn`,`tag`(128)),
  KEY `acn` (`acn`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;



CREATE TABLE IF NOT EXISTS `bot_visit` (
  `unit` char(17) NOT NULL,
  `time` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`unit`),
  KEY `time` (`time`)
) ENGINE=MEMORY default CHARSET=cp1251 COMMENT='������-�������' ;

-- --------------------------------------------------------
--
-- ��������� ������� `bot_tg_subscribe`
--     `i` int(10) unsigned NOT NULL auto_increment COMMENT '������ ����� ������',

CREATE TABLE IF NOT EXISTS `bot_tg_subscribe` (
    `time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT '�����',
    `unit` varchar(32) NOT NULL COMMENT '���������� id ����������, �� ������� �������� ��������',
    `type` varchar(32) NOT NULL COMMENT '��� ��������� ��������',
    `telegram` int(10) unsigned NOT NULL default '0' COMMENT 'telegram id',
    PRIMARY KEY (`unit`(32),`telegram`,`type`(32))
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='���� ��������� �� ��������';

-- --------------------------------------------------------
--
-- ��������� ������� `bot_event`
--

CREATE TABLE IF NOT EXISTS `bot_event` (
    `i` int(10) unsigned NOT NULL auto_increment COMMENT '������ ����� ������',
    `time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT '�����',
    `unit` varchar(32) NOT NULL COMMENT '���������� id ����������, �� �������� ������',
    `message` varchar(256) NOT NULL COMMENT '���������� ���������',
    PRIMARY KEY (`i`),
    KEY `unit` (`unit`),
    KEY `time` (`time`)
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='���� ��������� �� ��������';


-- --------------------------------------------------------
--
-- ��������� ������� `bot_in`
--

CREATE TABLE IF NOT EXISTS `bot_in` (
    `i` int(10) unsigned NOT NULL auto_increment COMMENT '������ ����� ������',
    `time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT '�����',
    `unit` varchar(32) NOT NULL COMMENT '���������� id ����������, �� �������� ������',
    `message` varchar(256) NOT NULL COMMENT '���������� ���������',
    PRIMARY KEY (`i`),
    KEY `unit` (`unit`(32)),
    KEY `time` (`time`)
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='���� ��������� �� ��������';


-- --------------------------------------------------------
--
-- ��������� ������� `bot_out`
--
-- timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT '����� ���������� ���������� (massage ��� answer)',

CREATE TABLE IF NOT EXISTS `bot_out` (
    `i` int(10) unsigned NOT NULL auto_increment COMMENT '������ ����� ������',
    `time` int(11) NOT NULL default '0' COMMENT '����� ���������� ���������� (massage ��� answer)',
    `unit` varchar(32) NOT NULL COMMENT '���������� id ����������, �������� ����������',
    `message` text NOT NULL COMMENT '������������ ���������',
    `answer` text NOT NULL COMMENT '��������� ����� �� ���������',
    `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id �����������',
    `status` enum('new','read','answer') NOT NULL default 'new' COMMENT '����� ��������� ���������',
    PRIMARY KEY  (`i`),
    KEY `status` (`status`),
    KEY `unit` (`unit`(32)),
    KEY `time` (`time`)
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='���� ��������� ��� ��������';

-- --------------------------------------------------------
--
-- ��������� ������� `bot_device`
--

CREATE TABLE IF NOT EXISTS `bot_device` (
    `unit_id` int(10) unsigned NOT NULL auto_increment COMMENT '������ ����� ������',
    `time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT '����� �����������',
    `unit` varchar(32) NOT NULL COMMENT '���������� id ����������',
    `name` varchar(32) NOT NULL default '' COMMENT '������ ���������� ���',
    `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id ���������',
    PRIMARY KEY (`unit_id`),
    KEY `unit` (`unit`(32)),
    KEY `nameunic` (`unic`,`name`(32))
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='������������������ ����������';


-- --------------------------------------------------------
--
-- ��������� ������� `bot_device_info`
--
CREATE TABLE IF NOT EXISTS `bot_device_info` (
    `unit` varchar(32) NOT NULL COMMENT '���������� id ����������',
    `time` timestamp NOT NULL default CURRENT_TIMESTAMP COMMENT '����� ���������� ���������� ������',
    `Settings` text  NOT NULL default '' COMMENT '���������� ������ IP � Settings.txt',
    `message` text NOT NULL default '' COMMENT '������������ ���������',
    `answer` text NOT NULL default '' COMMENT '��������� ����� �� ���������',
    `status` enum('no','new','read','answer') NOT NULL default 'no' COMMENT '����� ��������� ���������',
    PRIMARY KEY (`unit`(32))
) ENGINE=InnoDB default CHARSET=UTF8 COMMENT='���������� �� ����������';

-- --------------------------------------------------------
--
-- ��������� ������� `bot_script`
--

CREATE TABLE IF NOT EXISTS `bot_script` (
    `unic` int(10) unsigned NOT NULL default '0' COMMENT 'id ���������',
    `text` text NOT NULL COMMENT '����� �������',
    PRIMARY KEY (`unic`)
) ENGINE=InnoDB default CHARSET=cp1251 COMMENT='���������������� ������';

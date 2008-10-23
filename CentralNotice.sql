CREATE TABLE `central_notice_campaign` (
  `notice_name` varchar(255) NOT NULL default '',
  `notice_start_date` char(14) NOT NULL,
  `notice_end_date` char(14) NOT NULL,
  `notice_enabled` varchar(1) NOT NULL,
  `notice_id` int(11) NOT NULL auto_increment,
  `notice_locked` char(1) NOT NULL default 'N',
  `notice_language` char(2) NOT NULL default '',
  `notice_project` varchar(255) NOT NULL,
  PRIMARY KEY  (`notice_id`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=latin1;

CREATE TABLE `central_notice_template_assignments` (
  `template_assignment_id` int(11) NOT NULL auto_increment,
  `template_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `template_weight` int(3) NOT NULL,
  PRIMARY KEY  (`template_assignment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;

CREATE TABLE `central_notice_templates` (
  `template_id` int(11) NOT NULL auto_increment,
  `template_name` varchar(255) default NULL,
  PRIMARY KEY  (`template_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

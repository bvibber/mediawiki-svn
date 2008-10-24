CREATE TABLE `central_notice_campaign` (
  `notice_id` int NOT NULL auto_increment,
  `notice_name` varchar(255) NOT NULL,
  `notice_start_date` char(14) NOT NULL,
  `notice_end_date` char(14) NOT NULL,
  `notice_enabled` bool NOT NULL default '0',
  `notice_locked` bool NOT NULL default '0',
  `notice_language` char(2) NOT NULL,
  `notice_project` varchar(255) NOT NULL,
  PRIMARY KEY  (`notice_id`)
) /*$wgDBTableOptions*/;

CREATE TABLE `central_notice_template_assignments` (
  `template_assignment_id` int NOT NULL auto_increment,
  `template_id` int NOT NULL,
  `campaign_id` int NOT NULL,
  `template_weight` int NOT NULL,
  PRIMARY KEY  (`template_assignment_id`)
) /*$wgDBTableOptions*/;

CREATE TABLE `central_notice_templates` (
  `template_id` int NOT NULL auto_increment,
  `template_name` varchar(255) default NULL,
  PRIMARY KEY  (`template_id`)
) /*$wgDBTableOptions*/;

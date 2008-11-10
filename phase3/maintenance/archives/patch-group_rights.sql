--- Add table for group rights assignable on-wiki 

CREATE TABLE /*$wgDBprefix*/group_rights (
  gr_group varbinary(16) NOT NULL default '',
  gr_right varbinary(16) NOT NULL default '',
  gr_enabled tinyint(1) NOT NULL default 1,
  PRIMARY KEY  (gr_group,gr_right),
  KEY gr_right (gr_right)
) /*$wgDBoptions*/;
--- Add table for group rights assignable on-wiki 

CREATE TABLE /*$wgDBprefix*/changeable_groups (
  cg_changer varbinary(16) NOT NULL,
  cg_group varbinary(16) NOT NULL,
  cg_action varbinary(16) default NULL,
  UNIQUE KEY cg_changer (cg_changer,cg_group,cg_action),
  KEY cg_group (cg_group)
) /*$wgDBoptions*/;
--- Add table for group rights assignable on-wiki 
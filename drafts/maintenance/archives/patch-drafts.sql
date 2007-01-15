-- Stores drafts/autosaves
-- (Rob Church, Jan 2007)

CREATE TABLE /*wgDBprefix*/drafts (

  -- Unique identifier
  dr_id int(11) NOT NULL auto_increment,
  
  -- Owner/creator of the draft
  dr_user int(11) NOT NULL,
  
  -- Page (and optional section) the draft corresponds to
  dr_page int(11) NOT NULL,
  dr_section varbinary(40) NULL,
  
  -- Time the draft was created/updated
  dr_timestamp varbinary(14) NOT NULL,
  
  -- Text of the draft
  dr_text blob NOT NULL,
  
  -- Can other users see this draft?
  dr_shared tinyint(1) NOT NULL,
  
  PRIMARY KEY  (dr_id),
  KEY user_page (dr_user,dr_page,dr_section),
  KEY user_shared (dr_user,dr_shared)
  
) TYPE=InnoDB;
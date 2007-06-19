CREATE TABLE /*$wgDBprefix*/thread (
  thread_id int(8) unsigned NOT NULL auto_increment,
  thread_root int(8) unsigned NOT NULL,
  thread_article int(8) unsigned NOT NULL,
  thread_path text NOT NULL,
  thread_summary_page int(8) unsigned NULL,
  thread_touched char(14) binary NOT NULL default '',

  PRIMARY KEY thread_id (thread_id),
  UNIQUE INDEX thread_id (thread_id),
  INDEX( thread_path(255) ),
  INDEX thread_touched (thread_touched)
) TYPE=InnoDB;

/*
	old_superthread and old_article are mutually exclusive.
	New position is recorded either in the text movement or in the
	thread's current information.
*/
CREATE TABLE /*$wgDBprefix*/lqt_movement (
  movement_id int(8) unsigned NOT NULL auto_increment,
  
  movement_thread int(8) unsigned NOT NULL,

  movement_old_superthread int(8) unsigned NULL,
  movement_old_article int(8) unsigned NULL,

  movement_timestamp char(14) binary NOT NULL default '',

  PRIMARY KEY movement_id (movement_id)
  /* TODO we will need an index to look up my article and timestamp. */

) TYPE=InnoDB;


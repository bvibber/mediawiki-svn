CREATE TABLE /*$wgDBprefix*/lqt_thread (
  thread_id int(8) unsigned NOT NULL auto_increment,
  
  thread_root_post int(8) unsigned NOT NULL,

  -- Article that this thread belongs to.
  thread_article int(8) unsigned NOT NULL,

  -- Thread this is a subthread (reply) of. NULL if top-level to article.
  thread_subthread_of int(8) unsigned NULL,

  -- Summary post:
  thread_summary_page int(8) unsigned NULL,

  -- Subject line string:
  thread_subject varchar(255) binary NULL,

  -- Timestamp
  thread_touched char(14) binary NOT NULL default '',

  PRIMARY KEY thread_id (thread_id),
  UNIQUE INDEX thread_id (thread_id),
  INDEX thread_subthread_of (thread_subthread_of),
  INDEX thread_article_touched (thread_article, thread_touched),
  INDEX thread_article (thread_article),
  INDEX thread_root_post (thread_root_post)

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


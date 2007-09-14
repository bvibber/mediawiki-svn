CREATE TABLE /*$wgDBprefix*/thread (
  thread_id int(8) unsigned NOT NULL auto_increment,
  thread_root int(8) unsigned UNIQUE NOT NULL,
  thread_article int(8) unsigned NOT NULL default 0,
  thread_path text NOT NULL,
  thread_summary_page int(8) unsigned NULL,
  thread_timestamp char(14) binary NOT NULL default '',
  thread_revision int(8) unsigned NOT NULL default 1,

  -- The following are used only for non-existant article where
  -- thread_article = 0. They should be ignored if thread_article != 0.
  thread_article_namespace int NULL,
  thread_article_title varchar(255) binary NULL,

  -- Special thread types such as schrodinger's thread:
  thread_type int(4) unsigned NOT NULL default 0,

  thread_change_type int(4) unsigned NOT NULL,
  thread_change_object int(8) unsigned NULL,
  thread_change_comment tinyblob NOT NULL,
  thread_change_user int unsigned NOT NULL default '0',
  thread_change_user_text varchar(255) binary NOT NULL default '',

  PRIMARY KEY thread_id (thread_id),
  UNIQUE INDEX thread_id (thread_id),
  INDEX thread_article (thread_article),
  INDEX thread_article_title (thread_article_namespace, thread_article_title),
  INDEX( thread_path(255) ),
  INDEX thread_timestamp (thread_timestamp)
) TYPE=InnoDB;

CREATE TABLE /*$wgDBprefix*/historical_thread (
  -- Note that many hthreads can share an id, which is the same as the id
  -- of the live thread. It is only the id/revision combo which must be unique.
  hthread_id int(8) unsigned NOT NULL,
  hthread_revision int(8) unsigned NOT NULL,
  hthread_contents BLOB NOT NULL,
  hthread_change_type int(4) unsigned NOT NULL,
  hthread_change_object int(8) unsigned NULL,
  PRIMARY KEY hthread_id_revision (hthread_id, hthread_revision)
) TYPE=InnoDB;

CREATE TABLE /*$wgDBprefix*/user_message_state (
  ums_user int unsigned NOT NULL,
  ums_thread int(8) unsigned NOT NULL,
  ums_read_timestamp varbinary(14),
  
  PRIMARY KEY (ums_user, ums_thread)

) TYPE=InnoDB;

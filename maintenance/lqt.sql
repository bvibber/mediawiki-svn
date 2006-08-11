-- One-to-one relationship between this table and the 'page' table.
-- We have this in a separate table because we don't want to change
-- the 'page' schema for now. This is the extra info we need for
-- Liquid Threads. We will be adding columns in the future: summary,
-- maybe original author, ...

CREATE TABLE /*$wgDBprefix*/lqt_post (
  lqt_post_id int(8) unsigned NOT NULL auto_increment,

  -- key to the page that this is an extension of.
  lqt_post_this int(8) unsigned NOT NULL,

  -- This points to that post's next sibling. NULL if this is the last
  -- post on this level.
  lqt_post_next int(8) unsigned NULL,

  -- If this post has any replies, this points to the first one.
  lqt_post_first_reply int(8) unsigned NULL,

  -- lqt_thread to which this post belongs.
  lqt_post_thread int(8) unsigned NOT NULL,

  -- Rudementary deletion.
  lqt_post_is_deleted boolean NOT NULL,

  -- Who deleted the post? == user_text.
  lqt_post_deleted_by varchar(255) binary NULL,

  -- header line.
  lqt_post_subject varchar(255) binary NULL,

  PRIMARY KEY this_lqt_post_id (lqt_post_this, lqt_post_id),
  UNIQUE INDEX lqt_post_id (lqt_post_id),
  UNIQUE INDEX lqt_post_this (lqt_post_this),
  INDEX lqt_post_thread (lqt_post_thread)
         
) TYPE=InnoDB;

CREATE TABLE /*$wgDBprefix*/lqt_thread (
       lqt_thread_id int(8) unsigned NOT NULL auto_increment,
       
       -- the page that this thread is posted to.
       lqt_thread_page int(8) unsigned NOT NULL,

       -- the first top-level post (page), from which the linked list hangs.
       lqt_thread_first_post int(8) unsigned NOT NULL,

       -- special summary post (page).
       lqt_thread_summary_post int(8) unsigned NULL,

       -- anytime a post in the thread is inserted, updated, deleted, etc., 
       -- this timestamp is updated to the current time.
       lqt_thread_touched char(14) binary NOT NULL default '',

       PRIMARY KEY lqt_thread_page_id (lqt_thread_page, lqt_thread_id),
       UNIQUE INDEX lqt_thread_id (lqt_thread_id),
       INDEX lqt_thread_page_touched (lqt_thread_page, lqt_thread_touched),
       INDEX lqt_thread_touched (lqt_thread_touched)
) TYPE=InnoDB;

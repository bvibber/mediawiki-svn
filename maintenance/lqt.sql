-- One-to-one relationship between this table and the 'page' table.
-- We have this in a separate table because we don't want to change
-- the 'page' schema for now. This is the extra info we need for
-- Liquid Threads. We will be adding columns in the future: summary,
-- maybe original author, ...

CREATE TABLE /*$wgDBprefix*/lqt (
  lqt_id int(8) unsigned NOT NULL auto_increment,

  -- key to the page that this is an extension of.
  lqt_this int(8) unsigned NOT NULL,

  -- If the page is an article, this points to the first top-level
  -- post of that article's unarchived talk page. If this is a post,
  -- it points to that post's next sibling. NULL if the article has an
  -- empty talk page, or if this is the last post on this level.
  lqt_next int(8) unsigned NULL,

  -- If this post has any replies, this points to the first one.
  lqt_first_reply int(8) unsigned NULL,

  -- lqt_thread to which this post belongs.
  lqt_thread int(8) unsigned NOT NULL,

  PRIMARY KEY this_lqt_id (lqt_this, lqt_id),
  UNIQUE INDEX lqt_id (lqt_id),
  UNIQUE INDEX lqt_this (lqt_this),
  INDEX lqt_thread (lqt_thread)
         
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

       -- header line.
       lqt_thread_subject varchar(255) binary NULL,

       PRIMARY KEY lqt_thread_page_id (lqt_thread_page, lqt_thread_id),
       UNIQUE INDEX lqt_thread_id (lqt_thread_id),
       INDEX lqt_thread_page_touched (lqt_thread_page, lqt_thread_touched),
       INDEX lqt_thread_touched (lqt_thread_touched)
) TYPE=InnoDB;

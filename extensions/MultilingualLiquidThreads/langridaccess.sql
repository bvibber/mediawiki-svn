CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/thread_language (
  thread_language_id int(11) NOT NULL auto_increment,
  thread_id int(11) NOT NULL,
  `language` varbinary(10) NOT NULL,
  PRIMARY KEY  (thread_language_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/translated_thread (
  tt_id int(10) unsigned NOT NULL auto_increment,
  tt_original int(11) unsigned NOT NULL,
  tt_lang varbinary(10) NOT NULL,
  tt_root int(8) unsigned NOT NULL,
  tt_subject varbinary(255) NOT NULL,
  PRIMARY KEY  (tt_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/translated_subject (
  translated_subject_id int(10) unsigned NOT NULL auto_increment,
  thread_id int(11) unsigned NOT NULL,
  target_lang varbinary(10) NOT NULL,
  translated_subject_root int(8) unsigned NOT NULL,
  PRIMARY KEY  (translated_subject_id)
) /*$wgDBTableOptions*/;
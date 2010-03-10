CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/thread_language (
  thread_language_id int(11) NOT NULL auto_increment,
  thread_id int(11) NOT NULL,
  `language` varbinary(10) NOT NULL,
  PRIMARY KEY  (thread_language_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/translated_body (
  translated_body_id int(10) unsigned NOT NULL auto_increment,
  thread_id int(11) unsigned NOT NULL,
  body text NOT NULL,
  target_lang varbinary(10) NOT NULL,
  PRIMARY KEY  (translated_body_id)
) /*$wgDBTableOptions*/;

CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/translated_subject (
  translated_subject_id int(10) unsigned NOT NULL auto_increment,
  thread_id int(11) unsigned NOT NULL,
  `subject` varbinary(255) NOT NULL,
  target_lang varbinary(10) NOT NULL,
  PRIMARY KEY  (translated_subject_id)
) /*$wgDBTableOptions*/;
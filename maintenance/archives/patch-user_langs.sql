--
-- User language preferences
--
-- This requires the langtags table
--

CREATE TABLE /*$wgDBprefix*/user_langs (
  user_id integer unsigned NOT NULL,
  language_id integer unsigned NOT NULL,
  attribute varchar(15),
  attribute_level integer
) /*$wgDBTableOptions*/;
CREATE UNIQUE INDEX user_id
  ON /*$wgDBprefix*/user_langs (user_id,language_id);
CREATE INDEX language_id
  ON  /*$wgDBprefix*/user_langs (language_id,attribute,attribute_level);


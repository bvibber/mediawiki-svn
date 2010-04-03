-- MySQL version of the database schema for the Storyboard extension.

CREATE TABLE /*$wgDBprefix*/storyboard (
  story_id                 INT(8) unsigned   NOT NULL auto_increment PRIMARY KEY,
  story_lang_code          VARCHAR(6)        NOT NULL,
  story_author_id          INT unsigned          NULL,
  story_author_name        VARCHAR(255)      NOT NULL,
  story_author_location    VARCHAR(255)      NOT NULL,
  story_author_occupation  VARCHAR(255)      NOT NULL,
  story_author_image       VARCHAR(255)          NULL,  -- TODO: find out if this is an acceptible way to refer to an image
  story_author_contact     VARCHAR(255)      NOT NULL,  -- TODO: confirm with erik this is a mandatory field
  story_hit_count          INT(8) unsigned   NOT NULL default '0',
  story_title              VARCHAR(255)      NOT NULL,
  story_text               MEDIUMBLOB        NOT NULL,
  story_modified           CHAR(14) binary   NOT NULL default '',
  story_created            CHAR(14) binary   NOT NULL default '',
  story_is_published       TINYINT           NOT NULL default '0',
  story_is_hidden          TINYINT           NOT NULL default '0',
  story_image_hidden       TINYINT           NOT NULL default '0'
) /*$wgDBTableOptions*/;

CREATE INDEX story_published_modified ON /*$wgDBprefix*/storyboard (story_is_published, story_modified);
CREATE INDEX story_modified_id ON /*$wgDBprefix*/storyboard (story_modified, story_id);
CREATE INDEX story_title ON /*$wgDBprefix*/storyboard (story_title);
ALTER TABLE /*$wgDBprefix*/storyboard ADD UNIQUE (story_title) 
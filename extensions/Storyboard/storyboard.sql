-- MySQL version of the database schema for the Storyboard extension.

CREATE TABLE /*$wgDBprefix*/storyboard (
  story_id                 INT(8) unsigned   NOT NULL auto_increment PRIMARY KEY,
  story_author_id          INT unsigned          NULL,
  story_author_name        VARCHAR(100)      NOT NULL,
  story_author_location    VARCHAR(150)      NOT NULL,
  story_author_occupation  VARCHAR(100)      NOT NULL,
  story_author_image       VARCHAR(50)           NULL,  -- TODO: find out if this is an acceptible way to refer to an image
  story_hit_count          INT(8) unsigned   NOT NULL,
  story_title              VARCHAR(255)      NOT NULL,
  story_text               MEDIUMBLOB        NOT NULL,
  story_modified           CHAR(14) binary   NOT NULL default '',
  story_created            CHAR(14) binary   NOT NULL default '',
  story_is_published       TINYINT           NOT NULL default '0',
  story_is_hidden          TINYINT           NOT NULL default '0'
) /*$wgDBTableOptions*/;

CREATE INDEX story_published_modified ON /*$wgDBprefix*/storyboard (story_is_published, story_modified);

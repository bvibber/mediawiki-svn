-- Store mapping of i18n key of "rating" to an ID
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/article_assessment_ratings {
  aam_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  aam_rating varchar(255) binary NOT NULL
} /*$wgDBTableOptions*/;

-- INSERT INTO /*$wgDBprefix*/article_assessment_ratings(aam_rating) VALUES('blah');

-- Store article assessments
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/article_assessment (
  -- Foreign key to page.page_id
  aa_page_id integer unsigned NOT NULL,
  aa_user_id integer NOT NULL,
  -- unique user identifier
  aa_user_text varchar(255) binary NOT NULL,
  -- Foreign key to revision.rev_id
  aa_revision integer unsigned NOT NULL,
  -- MW Timestamp
  aa_timestamp binary(14) NOT NULL default '',
  -- Rating info
  aa_rating_id int unsigned NOT NULL,
  aa_rating_value int unsigned NOT NULL,
  -- 1 vote per user per revision
  PRIMARY KEY (aa_revision, aa_user_text)
) /*$wgDBTableOptions*/;

-- Store article assessments
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/article_assessment_pages (
  -- Foreign key to page.page_id
  aap_page_id integer unsigned NOT NULL,
  -- Which "rating"
  aap_rating_id integer unsigned NOT NULL,
  -- Sum (total) of all the ratings for this article revision
  aap_total integer unsigned NOT NULL,
  -- Number of ratings
  aap_count integer unsigned NOT NULL,
  PRIMARY KEY (aap_page_id, aap_rating_id)
) /*$wgDBTableOptions*/;
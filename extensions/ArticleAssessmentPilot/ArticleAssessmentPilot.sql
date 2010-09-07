-- Store mapping of i18n key of "rating" to an ID
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/article_assessment_ratings (
  --Rating Id
  aar_id int unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
  --Text (i18n key) for rating description
  aar_rating varchar(255) binary NOT NULL
) /*$wgDBTableOptions*/;

--Default article assessment ratings for the pilot
INSERT INTO /*$wgDBprefix*/article_assessment_ratings (aar_rating) VALUES
('articleassessment-rating-wellsourced'), ('articleassessment-rating-neutrality'),
('articleassessment-rating-completeness'), ('articleassessment-rating-readability');

-- Store article assessments (user rating per revision)
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/article_assessment (
  -- Foreign key to page.page_id
  aa_page_id integer unsigned NOT NULL,
  -- User Id (0 if anon)
  aa_user_id integer NOT NULL,
  -- Username or IP address
  aa_user_text varchar(255) binary NOT NULL,
  -- Unique token for anonymous users (to facilitate ratings from multiple users on the same IP)
  aa_user_anon_token binary(32) DEFAULT '',
  -- Foreign key to revision.rev_id
  aa_revision integer unsigned NOT NULL,
  -- MW Timestamp
  aa_timestamp binary(14) NOT NULL DEFAULT '',
  -- Foreign key to article_assessment_ratings.aar_rating
  aa_rating_id int unsigned NOT NULL,
  -- Value of the rating (0 is "unrated", else 1-5)
  aa_rating_value int unsigned NOT NULL,
  -- 1 vote per user per revision
  PRIMARY KEY (aa_revision, aa_user_text, aa_rating_id, aa_user_anon_token)
) /*$wgDBTableOptions*/;
CREATE INDEX /*i*/aa_user_page_revision ON /*_*/article_assessment (aa_user_id, aa_page_id, aa_revision);

-- Aggregate rating table for a page
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/article_assessment_pages (
  -- Foreign key to page.page_id
  aap_page_id integer unsigned NOT NULL,
  -- Foreign key to article_assessment_ratings.aar_rating
  aap_rating_id integer unsigned NOT NULL,
  -- Sum (total) of all the ratings for this article revision
  aap_total integer unsigned NOT NULL,
  -- Number of ratings
  aap_count integer unsigned NOT NULL,
  -- One rating row per page
  PRIMARY KEY (aap_page_id, aap_rating_id)
) /*$wgDBTableOptions*/;
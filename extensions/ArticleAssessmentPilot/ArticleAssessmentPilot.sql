
-- Store article assessments
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/article_assessment (
  -- Foreign key to page.page_id
  aa_page_id integer unsigned NOT NULL,
  -- unique user identifier
  aa_user_text varchar(255),
  -- Foreign key to revision.rev_id
  aa_revision integer unsigned NOT NULL,
  -- MW Timestamp
  aa_timestamp binary(14) NOT NULL default '',
  -- Vote info
  aa_m1 integer unsigned,
  aa_m2 integer unsigned,
  aa_m3 integer unsigned,
  aa_m4 integer unsigned,
  -- 1 vote per user per revision
  PRIMARY KEY (aa_revision, aa_user_text)
) /*$wgDBTableOptions*/;

-- Store article assessments
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/article_assessment_pages (
  -- Foreign key to page.page_id
  aap_page_id integer unsigned NOT NULL,
  -- Foreign key to revision.rev_id
  aap_revision integer unsigned NOT NULL,
  aap_total integer unsigned NOT NULL,
  aap_count integer unsigned NOT NULL,
  aap_dimension integer unsigned NOT NULL,
  PRIMARY KEY (aap_page_id, aap_revision, aap_dimension)
) /*$wgDBTableOptions*/;

CREATE INDEX /*i*/revision ON /*_*/article_assessment_pages (aap_revision, aap_page_id, aap_dimension);
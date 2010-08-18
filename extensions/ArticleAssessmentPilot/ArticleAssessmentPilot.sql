
-- Store article assessments
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/article_assessment (
  -- Foreign key to page.page_id
  aa_page_id integer unsigned NOT NULL,
  -- unique user identifier
  aa_user varchar(255),
  -- Foreign key to revision.rev_id
  aa_revision integer unsigned NOT NULL,
  -- MW Timestamp
  aa_timestamp char(14) NOT NULL default '',
  -- Vote info
  aa_m1 integer unsigned,
  aa_m2 integer unsigned,
  aa_m3 integer unsigned,
  aa_m4 integer unsigned,
  -- 1 vote per user per revision
  PRIMARY KEY (aa_revision,aa_user)
) /*$wgDBTableOptions*/;

-- Store article assessments
CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/article_assessment_pages (
  -- Foreign key to page.page_id
  aa_page_id integer unsigned NOT NULL,
  -- Foreign key to revision.rev_id
  aa_revision integer unsigned NOT NULL,
  aa_total integer unsigned NOT NULL,
  aa_count integer unsigned NOT NULL,
  aa_dimension integer unsigned NOT NULL,
  PRIMARY KEY (aa_page_id, aa_revision)
) /*$wgDBTableOptions*/;
CREATE TABLE /*_*/code_signoffs (
  -- Repository ID and revision ID
  cs_repo_id int not null,
  cs_rev_id int not null,

  -- User that signed off
  cs_user_text varchar(255) not null,

  -- Type of signoff. Current values: 'inspected', 'tested'
  -- See CodeRevision::getPossibleFlags() (in backend/CodeRevision.php) for most up to date list
  cs_flag varchar(25) not null,
  
  -- Timestamp of the sign-off
  cs_timestamp binary(14) not null default ''
) /*$wgDBTableOptions*/;
CREATE UNIQUE INDEX /*i*/cs_repo_rev_user_flag ON /*_*/code_signoffs (cs_repo_id, cs_rev_id, cs_user_text, cs_flag);
CREATE INDEX /*i*/cs_repo_repo_rev_timestamp ON /*_*/code_signoffs (cs_repo_id, cs_rev_id, cs_timestamp);

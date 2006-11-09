-- Tables for the CheckUser extension
-- vim: autoindent syn=mysql sts=2 sw=2

-- Private data about all changes being made on the wiki
CREATE TABLE /*$wgDBprefix*/cu_changes (
  -- Primary key
  cuc_id INTEGER NOT NULL AUTO_INCREMENT,

  -- Link to revision.rev_id
  cuc_rev_id INTEGER NOT NULL DEFAULT 0,

  -- Link to logging.log_id
  -- The logging.log_id column might not exist, in which case this field will 
  -- be -1. The field is zero if the row does not refer to a log event.
  cuc_log_id INTEGER NOT NULL DEFAULT 0,

  -- Event timestamp
  cuc_timestamp CHAR(14) NOT NULL DEFAULT '',

  -- IP address, in hexadecimal to allow range queries
  -- Same format as ipblocks.ip_range_start
  cuc_ip TINYBLOB NOT NULL DEFAULT '',

  -- user.user_id
  cuc_user INTEGER NOT NULL DEFAULT 0,

  cuc_user_text VARCHAR(255) NOT NULL DEFAULT '',

  -- XFF header
  cuc_xff VARCHAR(255) BINARY NOT NULL DEFAULT '',
  
  PRIMARY KEY (cuc_id),
  INDEX (cuc_ip),
  INDEX (cuc_user_text),
  INDEX cuc_log_rev (cuc_log_id, cuc_rev_id),
  INDEX (cuc_timestamp)
) TYPE=InnoDB;


-- A log of Special:CheckUser queries
CREATE TABLE /*$wgDBprefix*/cu_log (
  -- Primary key
  cul_id INTEGER NOT NULL AUTO_INCREMENT,

  -- Event timestamp
  cul_timestamp CHAR(14) NOT NULL DEFAULT '',

  -- The user who did the lookup (user.user_id)
  cuc_user INTEGER NOT NULL DEFAULT 0,

  -- The username who did the lookup
  cuc_user_text VARCHAR(255) BINARY NOT NULL DEFAULT '',

  -- The target user ID, or zero for IP
  cuc_target_user INTEGER NOT NULL DEFAULT 0,

  -- The target text
  cuc_target_text VARCHAR(255) BINARY NOT NULL DEFAULT '',

  PRIMARY KEY (cul_id),
  INDEX (cul_timestamp),
  INDEX (cul_user)
) TYPE=InnoDB;

-- Saved result sets
CREATE TABLE /*$wgDBprefix*/cu_resultsets (
  -- Link to the cul_id, identifies the result set
  curs_log_id INTEGER NOT NULL,

  -- Link to the cuc_id, identifies the change
  curs_change_id INTEGER NOT NULL, 

  -- Offset within the result set, for paging
  curs_offset INTEGER NOT NULL,

  INDEX log_offset (curs_log_id, curs_offset)
) TYPE=InnoDB;


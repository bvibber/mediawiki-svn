-- Stores an md5sum of each revision to facilitate finding reversions.
CREATE TABLE hashs (
  hs_nstitle VARCHAR(48) BINARY,
  hs_timestamp VARCHAR(14) BINARY,
  hs_old_id INT(8) UNSIGNED,
  hs_hash VARCHAR(48) BINARY,
  hs_user_text VARCHAR(255) BINARY,
  KEY hs_nstitle (hs_nstitle, hs_timestamp, hs_hash),
  KEY hs_timestamp (hs_timestamp)
);

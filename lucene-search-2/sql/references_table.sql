--
-- Table with cached information about references to a page
--
CREATE TABLE /*DBprefix*/references (
  -- key in form <ns>:<title>
  rf_key varchar(255) binary NOT NULL,

  -- number of page links to this page
  rf_references int(10) unsigned NOT NULL,
  
  -- 
  PRIMARY KEY rf_key(rf_key)
  
) TYPE=InnoDB;


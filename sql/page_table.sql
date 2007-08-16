--
-- Table with cached information about pages and references to id
--
CREATE TABLE /*DBprefix*/page (
  page_id int unsigned auto_increment NOT NULL,
  
  -- key in form <ns>:<title>
  page_key varchar(255) binary NOT NULL,

  -- number of page links to this page
  page_references int unsigned NOT NULL,
  
  -- 
  PRIMARY KEY page_id(page_id),
  UNIQUE (page_key)
  
) TYPE=InnoDB;
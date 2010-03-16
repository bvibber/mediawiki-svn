--
-- Table with a mapping of related articles
--
CREATE TABLE /*DBprefix*/related (
  -- key of article (fk to page_id)
  rel_to int unsigned NOT NULL,

  -- article which links to rel_id (fk to page_id)
  rel_related int unsigned NOT NULL,
  
  -- the strength of relatedness, positive
  rel_score float(23) NOT NULL,
  
  -- 
  PRIMARY KEY re_relates(rel_to,rel_related),
  INDEX (rel_to)
  
) TYPE=InnoDB;
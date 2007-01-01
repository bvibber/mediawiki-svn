-- Add cl_dispname column to categorylinks table
-- (Rob Church, Jan 2007)
ALTER TABLE /*wgDBprefix*/categorylinks
ADD cl_dispname VARCHAR( 255 ) NOT NULL AFTER cl_sortkey ;
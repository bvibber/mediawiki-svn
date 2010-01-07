-- media type columns, added for 1.16
-- this alters the scheme for 1.16, img_type is no longer used.

ALTER TABLE /*$wgDBprefix*/image ADD (
  -- File extension, appended to the on-disk file in cases where the 
  -- extension derived from img_name doesn't match the media type of 
  -- the file. See bug #4421.
  img_file_ext varchar(32) binary NOT NULL default ''
);

ALTER TABLE /*$wgDBprefix*/oldimage ADD (
  -- oldimage table counterpart of img_file_ext
  oi_file_ext varchar(32) binary NOT NULL default ''
);

ALTER TABLE /*$wgDBprefix*/filearchive ADD (
  -- filearchive table counterpart of img_file_ext
  fa_file_ext varchar(32) binary NOT NULL default ''
);


-- rev_id of the verified revision. This is needed
-- for the 'verify' feature.
-- Added 2005-07-23

ALTER TABLE /*$wgDBprefix*/page
  ADD page_verified_rev int(8) unsigned NOT NULL;

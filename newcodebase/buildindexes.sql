# MySQL script for creating non-unique indexes on
# wikipedia database tables

ALTER TABLE user
  ADD INDEX user_name (user_name(10));

ALTER TABLE cur
  ADD INDEX cur_namespace (cur_namespace),
  ADD INDEX cur_title (cur_title(20)),
  ADD INDEX cur_timestamp (cur_timestamp),
  ADD FULLTEXT cur_ind_title (cur_ind_title),
  ADD FULLTEXT cur_ind_text (cur_ind_text);

ALTER TABLE old
  ADD INDEX old_title (old_title(20)),
  ADD INDEX old_timestamp (old_timestamp);

ALTER TABLE links
  ADD INDEX l_from (l_from (10)),
  ADD INDEX l_to (l_to);

ALTER TABLE brokenlinks
  ADD INDEX bl_from (bl_from),
  ADD INDEX bl_to (bl_to(10));

ALTER TABLE imagelinks
  ADD INDEX il_from (il_from(10)),
  ADD INDEX il_to (il_to(10));

ALTER TABLE ipblocks
  ADD INDEX ipb_address (ipb_address),
  ADD INDEX ipb_user (ipb_user);

ALTER TABLE image
  ADD INDEX img_name (img_name(10)),
  ADD INDEX img_size (img_size),
  ADD INDEX img_timestamp (img_timestamp);

ALTER TABLE oldimage
  ADD INDEX oi_name (oi_name(10));


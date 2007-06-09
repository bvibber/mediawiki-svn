-- Alters columns in `user_newtalk` to be nullable and
-- drops existing indices in favour of a single UNIQUE
-- index
ALTER TABLE user_newtalk DROP INDEX user_newtalk_id_idx,
DROP INDEX user_newtalk_ip_idx, ALTER user_id DROP NOT NULL,
ALTER user_ip DROP NOT NULL;

CREATE UNIQUE INDEX user_newtalk_unique ON user_newtalk ( user_id, user_ip );
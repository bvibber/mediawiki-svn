# This code adapts older versions of the database.
# It adapts the table definitions adds indexes and, if necessary,
# fills new columns with appropriate values.

# adding the indexes on the timestamps

ALTER TABLE cur ADD INDEX timeind (cur_timestamp);
ALTER TABLE old ADD INDEX timeind (old_timestamp);

# adding index on old_title

ALTER TABLE old MODIFY old_title VARCHAR(255) BINARY NOT NULL DEFAULT '';
ALTER TABLE old ADD INDEX old_title (old_title);

# dropping the superfluous indexes on cur_id and old_id

ALTER TABLE cur DROP key cur_id_2;
ALTER TABLE old DROP key old_id_2;

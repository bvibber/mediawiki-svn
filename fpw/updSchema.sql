# This code adapts older versions of the database.
# It adapts the table definitions adds indexes and, if necessary,
# fills new columns with appropriate values. Uncomment
# the command that you still need to run to bring
# your database scheme up-to-date.

# adding the indexes on the timestamps

# ALTER TABLE cur ADD INDEX timeind (cur_timestamp);
# ALTER TABLE old ADD INDEX timeind (old_timestamp);

# adding index on old_title

# ALTER TABLE old MODIFY old_title VARCHAR(255) BINARY NOT NULL DEFAULT '';
# ALTER TABLE old ADD INDEX old_title (old_title);

# dropping the superfluous indexes

# ALTER TABLE cur DROP KEY cur_id_2;
# ALTER TABLE old DROP KEY old_id_2;
# ALTER TABLE old DROP PRIMARY KEY;
# ALTER TABLE user DROP PRIMARY KEY;
# ALTER TABLE user DROP KEY user_id_2;

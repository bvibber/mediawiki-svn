# This code adapts older versions of the database.
# It adapts the table definitions adds indexes and, if necessary,
# fills new columns with appropriate values.

ALTER TABLE cur ADD INDEX timeind (cur_timestamp);
ALTER TABLE old ADD INDEX timeind (old_timestamp);

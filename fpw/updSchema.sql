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

# ----- new instructions per 2002 February 13 ----

# We change the table types from ISAM to MyISAM
# which allows the fulltext indexes.

# ALTER TABLE cur TYPE = MyISAM;
# ALTER TABLE old TYPE = MyISAM;
# ALTER TABLE user TYPE = MyISAM;

# We add a duplicate column for cur_title
# because the original column is binary
# which does not allow the fulltext index.

# ALTER TABLE cur ADD COLUMN cur_ind_title VARCHAR(255);

# We copy cur_title to cur_ind_title.

# UPDATE cur SET cur_ind_title = cur_title;

# The grand moment: we add the fulltext index.
# NOTE: This may take a while, on my 126 MB
#       x86 Fam. 6 Model 8 it took about 1 hr. 36 min.

# ALTER TABLE cur ADD FULLTEXT ( cur_ind_title );
# ALTER TABLE cur ADD FULLTEXT ( cur_text );

# ----- new instructions per 2002 February 17 ----

# this replaces '_'with ' ' for better search results

# UPDATE cur SET cur_ind_title = REPLACE ( cur_title, '_', ' ' );

# ----- new instructions per 2002 February 19 ----

# New tables that are going to be used by the MostWanted pages and others
# there is a PHP script to bring these tables up-to-date: updLinks.php
# Read this script for further instructions. It is advised that you 
# first create the tables, then run the script, and afterwards
# add the indexes. This is because the script has to do a lot of
# inserts (about 620.000) which take longer if an index is defined.
# Of course the total amount of time it takes is the same, but
# the ALTER TABLE statements can be run without manual interaction.

# CREATE TABLE linked (
#   linked_to varchar(255) binary NOT NULL default '',
#   linked_from varchar(255) binary NOT NULL default ''
# ) TYPE=MyISAM;

# CREATE TABLE unlinked (
#   unlinked_from varchar(255) binary NOT NULL default '',
#   unlinked_to varchar(255) binary NOT NULL default ''
# ) TYPE=MyISAM;

# now run the script "updLinks.php".

# ALTER TABLE linked ADD INDEX (linked_from);
# ALTER TABLE linked ADD INDEX (linked_to)
# ALTER TABLE unlinked ADD INDEX (unlinked_from),
# ALTER TABLE unlinked ADD INDEX (unlinked_to)

# ----- new instructions per 2002 February 24 ----

# The cur_linked_links and cur_unlinked_links are now officially
# dropped

# ALTER TABLE cur DROP COLUMN cur_linked_links ;
# ALTER TABLE cur DROP COLUMN cur_unlinked_links ;

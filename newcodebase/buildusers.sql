# MySQL script to create required database users
# with proper access rights.  Must be run as root!
#

GRANT ALL ON wikidb.* TO wikiadmin@'%' IDENTIFIED BY 'admin7399';
GRANT ALL ON wikidb.* TO wikiadmin@localhost IDENTIFIED BY 'admin7399';
GRANT ALL ON wikidb.* TO wikiadmin@localhost.localdomain IDENTIFIED BY 'admin7399';
GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.* TO wikiuser@'%' IDENTIFIED BY 'user2682';
GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.* TO wikiuser@localhost IDENTIFIED BY 'user2682';
GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.* TO wikiuser@localhost.localdomain IDENTIFIED BY 'user2682';

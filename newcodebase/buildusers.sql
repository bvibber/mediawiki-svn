# MySQL script to create required database users
# with proper access rights.  Must be run as root!
#

GRANT ALL ON wikidb.* TO wikiadmin@localhost IDENTIFIED BY 'adminpwd';
GRANT ALL ON wikidb.* TO wikiadmin@'%' IDENTIFIED BY 'adminpwd';
GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.* TO wikiuser@localhost IDENTIFIED BY 'userpwd';
GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.* TO wikiuser@'%' IDENTIFIED BY 'userpwd';

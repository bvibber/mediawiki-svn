# MySQL script to create required database users
# with proper access rights.  Must be run as root!
#

GRANT ALL ON wikidb.* TO wikiadmin@'%' IDENTIFIED BY 'adminpass';
GRANT ALL ON wikidb.* TO wikiadmin@localhost IDENTIFIED BY 'adminpass';
GRANT ALL ON wikidb.* TO wikiadmin@localhost.localdomain IDENTIFIED BY 'adminpass';
GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.* TO wikiuser@'%' IDENTIFIED BY 'userpass';
GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.* TO wikiuser@localhost IDENTIFIED BY 'userpass';
GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.* TO wikiuser@localhost.localdomain IDENTIFIED BY 'userpass';

# MySQL script to create required database users
# with proper access rights.  Must be run as root!
# Replace "wikidb", "adminpass", "sqlpass", "userpass"
# with your local settings.
#
# FIXME: this script should be more automated


GRANT ALL ON wikidb.*
TO wikiadmin@'%' IDENTIFIED BY 'adminpass';
GRANT ALL ON wikidb.*
TO wikiadmin@localhost IDENTIFIED BY 'adminpass';
GRANT ALL ON wikidb.*
TO wikiadmin@localhost.localdomain IDENTIFIED BY 'adminpass';

GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.*
TO wikiuser@'%' IDENTIFIED BY 'userpass';
GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.*
TO wikiuser@localhost IDENTIFIED BY 'userpass';
GRANT DELETE,INSERT,SELECT,UPDATE ON wikidb.*
TO wikiuser@localhost.localdomain IDENTIFIED BY 'userpass';

# wikisql user is for direct sql queries by sysops
# We don't want to give out e-mails or passwords on
# a public site where sysops are only _mostly_ trusted.
GRANT SELECT (user_id,user_name,user_rights,user_options) on wikidb.user
TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.cur TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.old TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.archive TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.links TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.brokenlinks TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.imagelinks TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.site_stats TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.ipblocks TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.image TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.oldimage TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.random TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.recentchanges TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.watchlist TO wikisql@'%' IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.math TO wikisql@'%' IDENTIFIED BY 'sqlpass';

GRANT SELECT (user_id,user_name,user_rights,user_options,user_newtalk) on wikidb.user
TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.cur TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.old TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.archive TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.links TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.brokenlinks TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.imagelinks TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.site_stats TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.ipblocks TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.image TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.oldimage TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.random TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.recentchanges TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.watchlist TO wikisql@localhost IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.math TO wikisql@localhost IDENTIFIED BY 'sqlpass';

GRANT SELECT (user_id,user_name,user_rights,user_options,user_newtalk) on wikidb.user
TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.cur TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.old TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.archive TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.links TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.brokenlinks TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.imagelinks TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.site_stats TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.ipblocks TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.image TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.oldimage TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.random TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.recentchanges TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.watchlist TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';
GRANT SELECT on wikidb.math TO wikisql@localhost.localdomain IDENTIFIED BY 'sqlpass';

##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from pwd import getpwnam
from grp import getgrnam

try:
    from wikitex.config.local import Config
except ImportError:
    from wikitex.config.default import Config
    
Config.uid = getpwnam(Config.user).pw_uid
Config.gid = getgrnam(Config.group).gr_gid
Config.http_uid = getpwnam(Config.http_user).pw_uid
Config.http_gid = getgrnam(Config.http_group).gr_gid

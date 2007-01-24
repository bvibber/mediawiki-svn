##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from syslog import syslog
from os import setregid, setgroups, setreuid, setuid, EX_NOPERM

class RootEscalationError(Exception):
    SAW = 'Process is yet able to become root'

    def __init__(self, saw=SAW):
        Exception.__init__(self, saw)

def droppriv(uid, gid, code=0):
    # Set group must be done before user.
    setregid(gid, gid)
    # Reduce supplementary groups
    setgroups([gid])
    # Finally, set user.
    setreuid(uid, uid)
    # Test that we cannot re-root
    try:
        setuid(0)
    except OSError:
        pass
    else:
        if code:
            print >> stderr, RootEscalationError().message
            exit(code)
        else:
            raise RootEscalationError()

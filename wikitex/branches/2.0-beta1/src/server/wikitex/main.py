##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from __future__ import with_statement
from os import EX_USAGE, EX_NOPERM, kill
from sys import argv, stderr
from glob import glob
from os.path import join
from signal import SIGTERM

from wikitex.server import WikitexServer
from wikitex.actions.math import Math
from wikitex.config import *
from wikitex.droppriv import droppriv
from wikitex.constants import Constants

USAGE = 'Usage: %(file)s start|stop' % {'file': __file__}

def err(error, code=0):
    print >> stderr, '%(file)s: %(error)s' % {'file': __file__,
                                              'error': error}
    if code:
        exit(code)
    
try:
    COMMAND, DIRECTIVE = argv
except ValueError:
    err(USAGE, EX_USAGE)

class Main(object):
    ACTIONS = [Math()]

    def start(self):
        WikitexServer(self.ACTIONS)

    def stop(self):
        WILD_CARD = '*'
        ERR_NO_SUCH_PROCESS = 3

        # Drop to unprivileged user, kill the pids associated with
        # respective wikitex servers. DRAWBACK: leaves pid-file-
        # skeletons behind; on the other hand, will not kill privileged
        # processes if pid files have been tampered with.
        #
        # A file-descriptor is left open in each process so as to maintain
        # a write-lock on the pid-file; worst case: it may be tampered with.

        # Drop privileges per configuration
        droppriv(Config.uid, Config.gid, EX_NOPERM)
        for lockfilename in glob(join(Config.run, Constants.LOCKFILE % \
                                  {'application': Constants.APPLICATION % \
                                   {'action': WILD_CARD}})):
            with open(lockfilename) as lockfile:
                try:
                    pid_string = lockfile.read()
                    pid = int(pid_string)
                    kill(pid, SIGTERM)
                    err('Stopped `%(pid)d\'' % {'pid': pid})
                except ValueError:
                    if pid_string:
                        err('`%(lockfilename)s\' contains an invalid pid' % \
                            {'lockfilename': lockfilename})
                except OSError, (errno, strerror):
                    if errno != ERR_NO_SUCH_PROCESS:
                        err(('pid `%(pid)d\' from `%(lockfilename)s\' ' +
                             'cannot be killed: %(strerror)s') % \
                            {'pid': pid,
                             'strerror': strerror,
                             'lockfilename': lockfilename})

driver = Main()

try:
    {'start': driver.start, 'stop': driver.stop}[DIRECTIVE]()
except KeyError:
    err(USAGE, EX_USAGE)

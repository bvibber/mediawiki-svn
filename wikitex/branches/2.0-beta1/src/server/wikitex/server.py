##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from SimpleXMLRPCServer import SimpleXMLRPCServer, SimpleXMLRPCRequestHandler
from SocketServer import ForkingMixIn
from codecs import lookup
from os.path import join, isfile
from os import chroot, chdir, setreuid, setgroups, setregid, setuid, close, \
     fork, umask, setsid, open, dup, O_RDWR, getpid, _exit, EX_OSERR, \
     EX_OK, EX_NOPERM, O_RDWR, O_CREAT, SEEK_SET, ftruncate, write, unlink, \
     chown
from resource import setrlimit, getrlimit, RLIMIT_NOFILE, RLIM_INFINITY
# stdout, stderr, stdin demandeth naked importation
import sys
from sys import exit
from signal import signal, SIG_IGN, SIGHUP, SIGTERM, SIGINT
from syslog import syslog, openlog, LOG_CONS, LOG_DAEMON, LOG_ERR
from stat import S_IRUSR, S_IWUSR, S_IRGRP, S_IROTH
from errno import EAGAIN, EACCES
from fcntl import LOCK_EX, LOCK_NB, lockf
from atexit import register

from wikitex.config import *
from wikitex.actions.math import Math
from wikitex.facility import Facility
from wikitex.constants import Constants
from wikitex.droppriv import droppriv
from wikitex.logger import Logger

class WikitexActions(object):
    action_to_method = {}

    def __init__(self, actions):
        for action in actions:
            self.action_to_method[action.name] = action.method

    def _dispatch(self, action, params):
        return self.action_to_method[action.lower()](*params)
    
class WikitexForkingMixIn(ForkingMixIn):
    max_children = Config.max_children

class WikitexRequestHandler(SimpleXMLRPCRequestHandler):
    def setrlimits(self):
        for resource, limit in Config.limits.iteritems():
            try: 
                setrlimit(resource, limit)
            except Exception, e:
                syslog('Failed to set resource limit %(resource)d ' %
                       {'resource': resource} + 'to %(limit)s: %(message)s' %
                       {'limit': limit,
                        'message': e.message});

    def setup(self):
        SimpleXMLRPCRequestHandler.setup(self)
        self.setrlimits()

class WikitexServer(WikitexForkingMixIn, SimpleXMLRPCServer):
    class IrreducibleFacilityError(Exception):
        SAW = 'The facilities of this action-set are irreducible'

        def __init__(self, saw=SAW):
            Exception.__init__(self, saw)
    
    def __init__(self, actions):
        # facility = reduce(lambda a, b: a, [action.facility for action
        # in actions])
        facility = reduce(self.reduce_facilities,
                          [action.facility for action in actions])
        # Detach from the parent
        self.daemonize(Constants.APPLICATION % {'action': facility.root})
        # Store encodings in cache before chroot
        for encoding in Constants.ENCODINGS:
            lookup(encoding)
        # Changing root does not automatically change dir.
        root = join(Config.root, facility.root)
        chdir(root)
        chroot(root)
        # Reduce to unprivileged user
        droppriv(Config.uid, Config.gid, EX_NOPERM)

        SimpleXMLRPCServer.__init__(self, (facility.host, facility.port),
                                    requestHandler=WikitexRequestHandler)
        self.register_instance(WikitexActions(actions))
        self.serve_forever()
        # self.handle_request()
        
    def reduce_facilities(self, superducendum, subducendum):
        if superducendum != subducendum:
            raise self.IrreducibleFacilityError()
        return superducendum

    def daemonize(self, application):
        IDENTITY = application + '[%(pid)d]'
        DESCRIPTORS = 1024
        HARD_LIMIT = 1
        STDIN = 0
        STDOUT = 1
        STDERR = 2
        UMASK = 0022
        FLAGS = O_RDWR | O_CREAT
        LOCKMODE = S_IRUSR | S_IWUSR | S_IRGRP | S_IROTH
        LENGTH = START = 0

        umask(UMASK)
        descriptors = getrlimit(RLIMIT_NOFILE)[HARD_LIMIT]
        pid = fork()
        if pid:
            _exit(EX_OK)
        setsid()
        signal(SIGHUP, SIG_IGN)
        pid = fork()
        if pid:
            _exit(EX_OK)
        # chdir
        # Obtain lock on pid-file
        lockfile = join(Config.run, Constants.LOCKFILE %
                        {'application': application})
        lockfile_descriptor = open(lockfile, FLAGS, LOCKMODE)
        self.lockfile_descriptor = lockfile_descriptor
        try:
            lockf(lockfile_descriptor, LOCK_EX | LOCK_NB, LENGTH, START,
                  SEEK_SET)
        except IOError, e:
            if e.errno == EAGAIN or e.errno == EACCES:
                exit("Can't obtain lock on %s; another process is using it." %
                     lockfile)
            else:
                exit("Can't obtain lock on %s." % lockfile)
        # Reduce lockfile to nought
        ftruncate(lockfile_descriptor, 0)
        pid = getpid()
        # Record pid in lockfile
        write(lockfile_descriptor, str(pid))

        # Allow us to handle cleanup on terminate and interrupt
        signal(SIGTERM, self.terminate)
        signal(SIGINT, self.terminate)
        # Register lockfile's release on exit
        register(self.release_lockfile, lockfile_descriptor)

        # Close remaining open descriptors
        if descriptors == RLIM_INFINITY:
            descriptors = DESCRIPTORS
        for descriptor in range(0, descriptors - 1):
            try:
                if descriptor != lockfile_descriptor:
                    close(descriptor)
            except OSError:
                pass
        # Point stdin to '/dev/null'
        # Stevens' protocoll dictates closing the following; it shuts
        # off my access to syslog, though!
        # stdin = open(join(Constants.DEV, Constants.NULL), O_RDWR)
        # stdout = dup(stdin)
        # stderr = dup(stdin)
        identity = IDENTITY % {'pid': pid}
        openlog(identity, LOG_CONS, LOG_DAEMON)
        sys.stdout = sys.stderr =  Logger
        # Prime the syslog-redirect
        syslog('Process %s is ready, sir.' % getpid())

        # Correllary to Stevens' protocoll above
        # if stdin != STDIN or stdout != STDOUT or stderr != STDERR:
        #     syslog(LOG_ERR, 'unexpected file descriptors %d %d %d' %
        #            (stdin, stdout, stderr))
        #     exit(EX_OSERR)

    def release_lockfile(self, lockfile_descriptor):
        # Strictly speaking, superfluous; in addition:
        # can't unlink the lockfile itself, since it
        # existeth outside our jail.
        # TODO: move lockfiles inside jail like Apache?
        # To unlink it inside the jail, however, it must
        # be owned or writable by the process: which leaves
        # the lockfile open to mischief.
        close(lockfile_descriptor)
            
    def terminate(self, signal, frame):
        PREFIX = 2**7

        syslog('Terminating')
        # Allow cleanup functions to be called
        exit(PREFIX + signal)

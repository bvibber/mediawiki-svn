##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from os import EX_SOFTWARE, kill, WIFEXITED, WIFSIGNALED, WIFSTOPPED, \
     WTERMSIG, WSTOPSIG, WEXITSTATUS, EX_OK
from syslog import syslog
from popen2 import Popen4
from threading import Timer
from os.path import basename
from signal import SIGKILL

from wikitex.constants import Constants

class ExecutionError(Exception):
    def __init__(self, errno, strerr):
        Exception.__init__(self, errno, strerr)

class Execution(object):
    def __init__(self, facility):
        self.facility = facility

    def kill(self, pid, signal):
        try:
            kill(pid, signal)
        except OSError:
            # Whoops: done-in, I believe, by a race condition
            # (e.g., the process finished while we were iterating
            # through timers).
            syslog('`%s\' met with a stale timer' % self.facility.path)
        
    def execute(self, *args):
        process = Popen4([self.facility.path] + self.facility.args + list(args))
        timers = [Timer(time, self.kill, [process.pid, signal]) for time, signal in
                  self.facility.wait.iteritems()]
        for timer in timers:
            timer.start()
        status = process.wait()
        for timer in timers:
            # No penalty, btw, for cancelling a dead timer
            if timer.isAlive():
                timer.cancel()
        process.tochild.close()
        if __debug__:
            while True:
                line = process.fromchild.readline()
                if line:
                    syslog(line)
                else:
                    break
        process.fromchild.close()
        command = basename(self.facility.path)
        if WIFEXITED(status):
            exit_status = WEXITSTATUS(status)
            if exit_status != EX_OK:
                raise ExecutionError(EX_SOFTWARE, '`%(command)s\' exited with \
                error-code %(exit_status)d' % {'command': command,
                                               'exit_status': exit_status})
        elif WIFSIGNALED(status):
            raise ExecutionError(EX_SOFTWARE, '`%(command)s\' terminated \
            with signal %(signal)d' % {'command': command,
                                       'signal': WTERMSIG(status)})
        elif WIFSTOPPED(status):
            raise ExecutionError(EX_SOFTWARE, '`%(command)s\' stopped with \
            signal %(signal)d' % {'command': command,
                                  'signal': WSTOPSIG(status)})
        else:
            # Failsafe: timers should have killed the process by this point, or
            # it should have ended naturally.
            kill(process.pid, SIGKILL)
            raise ExecutionError(EX_SOFTWARE, 'Failed timer on `%(command)s\'; \
            terminating the process extraordinarily.' % {'command': command})

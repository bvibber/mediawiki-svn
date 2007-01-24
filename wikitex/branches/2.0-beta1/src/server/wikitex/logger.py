##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from syslog import syslog

class Logger(object):
    @staticmethod
    def write(scribendum):
        syslog(scribendum)

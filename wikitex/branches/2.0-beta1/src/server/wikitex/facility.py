##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
class Facility(object):
    def __eq__(self, other):
        return (self.root, self.host, other.port) == \
               (other.root, other.host, other.port)

    def __ne__(self, other):
        return not self.__eq__(other)

    def __init__(self, root, host, port, path, wait, args):
        self.root = root
        self.host = host
        self.port = port
        self.path = path
        self.wait = wait
        self.args = args

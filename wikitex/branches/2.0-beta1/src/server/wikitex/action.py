##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from __future__ import with_statement
from os.path import join, dirname
from xmlrpclib import Binary
from tempfile import mkdtemp
from os import rmdir, chdir
from hashlib import md5

from wikitex.config import *
from wikitex.constants import Constants
from wikitex.cleanup import Cleanup

class Action(object):
    LIBRARY_PATH = join(dirname(__file__), 'templates')

    def __init__(self, name, method, template_file, facility):
        super(Action, self).__init__(self, name, method, template_file, facility)
        self.name = name
        self.method = method
        self.template_file = template_file
        # Reads the template here, before chroot renders it
        # unavailable.
        with open(join(self.LIBRARY_PATH, template_file)) as template:
            self.template = template.read()
        self.facility = facility

    ##
    # Render the content.
    # 
    # @param reddendum A dictionary of template substitutions.
    def render(self, reddendum):
        self.populate_tmpdir(reddendum)

    ##
    # Create and populate the work directory.
    # 
    # Create the temp directory; chdir thither; and register the
    # directory to be cleaned up, unless __debug__ is active (i.e.,
    # Python was started without -O.
    # 
    # @param populandum a dictionary of template substitutions.
    # @return Reference to the cleanup mechanism that should
    # persist as long as needed
    def populate_tmpdir(self, populandum):
        temp_dir = mkdtemp(Constants.TEMP)
        chdir(temp_dir)
        with open(self.template_file, 'w') as template:
            template.write(self.template % populandum)
        # Register deletion (unless __debug__) of temp_dir
        return Cleanup(temp_dir)

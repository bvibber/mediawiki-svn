##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from __future__ import with_statement
from xmlrpclib import Binary, Fault
from glob import glob

from wikitex.cleanup import Cleanup
from wikitex.constants import Constants
from wikitex.action import Action
from wikitex.config import *
from wikitex.execution import Execution, ExecutionError

class Latex(Action):
    NAME = 'latex'
    TEMPLATE_FILE = Constants.TEMPLATE % {'file': NAME,
                                          'suffix': Constants.MIMES['latex'][1]}
    FACILITY = Config.facilities[NAME]

    def __init__(self, name=NAME, method=None, template_file=TEMPLATE_FILE,
                 facility=FACILITY):
        if not method:
            method = self.render
        super(Latex, self).__init__(name, method, template_file, facility)

    ##
    # @param reddendum neuter singular gerund of reddere, &ldquo;to give back.&rdquo;
    # @param self reference
    # @return a dictionary comprising resultate image and source;
    # {'image/png': &ldquo;result&rdquo;, 'application/x-latex': &ldquo;source&rdquo;}.
    def render(self, reddendum):
        # Take only the first image produced by dvipng;
        # TODO: glob and deliver n images.
        SOURCE = '%(source)s.%(suffix)s'
        IMAGE = '%(image)s1.%(suffix)s'
        IMG_MIME, IMG_EXT = Constants.MIMES['png']
        SRC_MIME, SRC_EXT = Constants.MIMES['latex']

        # Should persist through execution, and be deleted during garbage-
        # collection; taking with it the temporary directory.
        cleanup = super(Latex, self).populate_tmpdir({self.name: reddendum})
        try:
            Execution(self.facility).execute(self.name)
            Execution(Config.facilities[Constants.DVIPNG]).execute(self.name)
        except ExecutionError, (errno, strerr):
            raise Fault(errno, strerr)
        sourcefilename = SOURCE % {'source': self.name, 'suffix': SRC_EXT}
        with open(sourcefilename) as sourcefile:
            source = sourcefile.read()
        imagefilename = IMAGE % {'image': self.name, 'suffix': IMG_EXT}
        with open(imagefilename) as imagefile:
            image = imagefile.read()
        response = {IMG_MIME: Binary(image), SRC_MIME: Binary(source)}
        return response

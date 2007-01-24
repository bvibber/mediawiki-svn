##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from wikitex.actions.latex import Latex
from wikitex.constants import Constants
from wikitex.config import *

class Math(Latex):
    NAME = 'math'
    TEMPLATE_FILE = Constants.TEMPLATE % {'file': NAME,
                                          'suffix': Constants.MIMES['latex'][1]}
    FACILITY = Config.facilities['latex']

    def __init__(self, name=NAME, method=None, template_file=TEMPLATE_FILE, facility=FACILITY):
        if not method:
            method = self.render
        super(Math, self).__init__(name, method, template_file, facility)

    def render(self, reddendum):
        return Latex.render(self, reddendum)

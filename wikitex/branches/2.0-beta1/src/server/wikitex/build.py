##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from __future__ import with_statement
from distutils.command.build import build
from distutils.core import Command
from os.path import join
from subprocess import call
from glob import glob
from sys import executable
from os import chmod

class build_wikitex(build):
    # Don't build docs by default (already in source-dist).
    # build.sub_commands.append(('build_docs', lambda self:True))
    build.sub_commands.append(('build_scripts', lambda self:True))

class build_scripts(Command):
    description = 'build scripts'
    user_options = []

    def initialize_options(self):
        pass

    def finalize_options(self):
        pass

    def run(self):
        SCRIPTS = './bin/wikitex*.in'
        MOD = 0755
        for scriptfilename in glob(SCRIPTS):
            with open(scriptfilename) as scriptfile:
                script = scriptfile.read() % {'python': executable}
            scriptfilename = scriptfilename[:scriptfilename.rfind('.')]
            with open(scriptfilename, 'w') as scriptfile:
                scriptfile.write(script)
            chmod(scriptfilename, MOD)
        

class build_docs(Command):
    description = 'build documentation'
    user_options = []

    def initialize_options(self):
        pass

    def finalize_options(self):
        pass

    def run(self):
        DOCS = './doc/src'
        MAKEFILE = 'Makefile.in'
        VERSION = self.distribution.get_version()
        makefilename = join(DOCS, MAKEFILE)
        with open(makefilename) as makefile:
            make = makefile.read() % {'version': VERSION}
        with open(makefilename[:makefilename.rfind('.')], 'w') as makefile:
            makefile.write(make)
        call(['make', '-C', DOCS])


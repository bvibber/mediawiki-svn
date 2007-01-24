##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from distutils.core import setup, Extension
from sys import path

path.insert(0, './src/server')

from wikitex.config import *
from wikitex.install import install_wikitex
from wikitex.install import install_root
from wikitex.install import install_client
from wikitex.install import install_handler
from wikitex.install import install_scripts
from wikitex.install import install_docs
from wikitex.build import build_wikitex
from wikitex.build import build_docs
from wikitex.build import build_scripts

setup(name='wikitex',
      version='2.0-beta',
      description='Expansible LaTeX module for MediaWiki',
      long_description=('Server-component of the WikiTeX module, complementary ' +
                        'to the integrated PHP client for MediaWiki.'),
      platforms='Linux',
      license='GPLv2',
      author='Peter Danenberg',
      author_email='<pcd at wikitex dot org>',
      url='http://wikitex.org',
      package_dir={'wikitex': 'src/server/wikitex'},
      packages=['wikitex', 'wikitex.actions', 'wikitex.config'],
      package_data={'wikitex': ['templates/*']},
      data_files=[(Config.web, ['src/handler/.htaccess',
                               'src/handler/traderjoe.py'])],
      cmdclass={'build': build_wikitex,
                'build_docs': build_docs,
                'build_scripts': build_scripts,
                'install': install_wikitex,
                'install_root': install_root,
                # We don't do client by default
                'install_client': install_client,
                'install_handler': install_handler,
                'install_scripts': install_scripts,
                'install_docs': install_docs,
                }
      )

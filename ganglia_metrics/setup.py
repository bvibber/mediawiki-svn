#!/usr/bin/env python

from distutils.core import setup

setup(name='ganglia_metrics',
      version='1.3',
      description='Ganglia metric daemon',
      author='Tim Starling',
      author_email='tstarling@wikimedia.org',
      url='http://svn.wikimedia.org/viewvc/mediawiki/trunk/ganglia_metrics/',
      packages=['ganglia_metrics'],
      package_dir={'ganglia_metrics': ''},
      scripts=['gmetricd'],
      data_files=[('/etc/init.d', ['init.d/gmetricd'])]
     )



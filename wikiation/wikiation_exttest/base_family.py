# -*- coding: utf-8  -*-              # REQUIRED
# This software, copyright (C) 2008-2009 by Wikiation.
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.


import sys, settings
if settings.pywikipedia_path not in sys.path:
	sys.path.append(settings.pywikipedia_path)

import config, family, urllib    # REQUIRED
class Family(family.Family):          # REQUIRED
	"""friendlier version of tghe pywikipedia family class.
	We can use this in conjunction with none-pywikipedia
	config files."""
	def __init__(self,
		name='test',
		protocol='http',
		server='6.wikiation.nl',
		scriptpath='/revisions/REL1.13.2',
		version='1.13.2',
		lang='nl',
		encoding='utf-8',
		api_supported=False,	
		RversionTab=None	# very rare beast, you probably won't need it.
		):              

		family.Family.__init__(self) 
		self.name = name        # REQUIRED; replace with actual name

		self.langs = {                # REQUIRED
		    lang: server,  # Include one line for each wiki in family
		}
		self._protocol=protocol
		self._scriptpath=scriptpath
		self._version=version
		self._encoding=encoding
		# may as well add these here, so we can have a 1 stop shop
		self._lang=lang
		self._server=server
		self._api_supported=api_supported
		self._RversionTab=RversionTab

	def protocol(self, code):
		"""
		Can be overridden to return 'https'. Other protocols are not supported.
		"""
		return self._protocol

	def scriptpath(self, code):
		"""The prefix used to locate scripts on this wiki.

		This is the value displayed when you enter {{SCRIPTPATH}} on a
		wiki page (often displayed at [[Help:Variables]] if the wiki has
		copied the master help page correctly).

		The default value is the one used on Wikimedia Foundation wikis,
		but needs to be overridden in the family file for any wiki that
		uses a different value.

		"""
		return self._scriptpath

	# IMPORTANT: if your wiki does not support the api.php interface,
	# you must uncomment the second line of this method:
	def apipath(self, code):
		if self._api_supported:
			return '%s/api.php' % self.scriptpath(code)
		else:
			raise NotImplementedError, "%s wiki family does not support api.php" % self.name

    # Which version of MediaWiki is used?
	def version(self, code):
		# Replace with the actual version being run on your wiki
		return self._version

	def code2encoding(self, code):
		"""Return the encoding for a specific language wiki"""
		# Most wikis nowadays use UTF-8, but change this if yours uses
		# a different encoding
		return self._encoding

	def RversionTab(self, code):
		return self._RversionTab

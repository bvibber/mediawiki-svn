# This software, copyright (C) 2008-2009 by Wikiation.
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license

import sys, os
import settings
if settings.pywikipedia_path not in sys.path:
	sys.path.append(settings.pywikipedia_path)

cwd=os.getcwd()
os.chdir(settings.pywikipedia_path)
import wikipedia, login
os.chdir(cwd)
import base_family

class LoginData:

	def __init__(
		self,
		name='MY_NAME_FOR_THIS_SERVER',
		protocol='http',
		server='www.my_server.com',
		scriptpath='/my/script/path/',
		version='1.13.2',
		lang='en',
		encoding='utf-8',
		user='MY_BOT_USER',
		password='MY_SECRET_PASSWORD',
		RversionTab=None,
		api_supported=False	
		):
		self.lang=lang
		self.user=user
		self.password=password
		self.family=base_family.Family(
			name=name, 
			protocol=protocol,
			server=server,
			scriptpath=scriptpath,
			version=version,
			lang=lang,
			encoding=encoding,
			RversionTab=RversionTab,
			api_supported=api_supported)
		self.site=None
	
	def login(self):
		self.site=wikipedia.Site(
			code=self.lang,
			fam=self.family,
			user=self.user
			)
		loginManager=login.LoginManager(
			password=self.password,
			site=self.site,
			username=self.user
			)
		loginManager.login()
		return self.site

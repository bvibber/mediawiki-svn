#!/usr/bin/python

#This software, copyright (C) 2008-2009 by Wikiation.
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

# Logintest: a quick test of logindata

import logindata

l=logindata.LoginData(
		name='6Basic',
		protocol='http',
		server='6.wikiation.nl',
		scriptpath='/revisions/REL1_13_2',
		version='1.13.2',
		lang='en',
		user='------',
		password='-------'
		)

site=l.login()
print site
page=logindata.wikipedia.Page(site,'Hello World')

page.put("hello world\n");

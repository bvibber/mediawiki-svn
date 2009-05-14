#!/usr/bin/python
# originally adapted from the util/wiki_works script
# (C) 2009 Kim Bruning
# Distributed under the MIT License

# Quick&dirty test to see if a wiki is up and running,
# by asking pywikipedia to attempt to 
# log in and read the version page
# assumes default password settings.

# can either be run as an independant program
# 	./wiki_works.py <instance_name>
#
# returns either "wiki works" or "wiki doesn't work"

# or be used as a module:
# 	from wiki_works import wiki_works
# 	wiki_works("instance_name")
#
# returns True or False


import sys, os.path
sys.path.append("..")

import settings_handler as installersettings
import exttest.settings	#for some reason
from exttest.logindata import LoginData

def do_test(login):
	"""given (hopefully) valid login data,
	attempt to read the version.
	If we get something that looks like a valid version array,
	we assume that the wiki is working fine, and return True.
	If something goes wrong, we return False."""
	try:
		site=login.login()
		ver=site.live_version()
		if isinstance(ver,list):
			return True
		else:
			return False
	except Exception, e:
		return False
	# or, if in doubt
	return False # anyway 

def make_login(target):
	"""Put together some valid login data"""
	login=LoginData(
		name="Does it blend",
		protocol='http',
		server='localhost',
		scriptpath=installersettings.base_scriptpath+target,
		lang='en',
		encoding='utf-8',
		user=installersettings.adminuser_name,
		password=installersettings.adminuser_password
	)
	return login


def wiki_works(target):
		"""test if the wiki works.
		target is a valid installed mediawiki instance
		returns True if wiki appears to be up, False if not."""
		login=make_login(target)
		result=do_test(login)
		return result

def main():
	"""run as an independent program"""
	if len(sys.argv)<2:
		print "syntax:    wiki_works.py instance_name"
		sys.exit(1)
	target=sys.argv[1]
	
	if wiki_works(target):
		print "wiki works"
		sys.exit(0)
	else:
		print "wiki doesn't work"
		sys.exit(1)

if __name__=="__main__":
	main()

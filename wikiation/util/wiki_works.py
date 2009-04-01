#!/usr/bin/python
import sys, os.path
sys.path.append("..")

import installer.settings as installersettings
import wikiation_exttest.settings	#for some reason
from wikiation_exttest.logindata import LoginData



def do_test(login):
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
	login=LoginData(
		name="Does it blend",
		protocol='http',
		server='localhost',
		scriptpath=installersettings.base_scriptpath+target,
		lang='en',
		encoding='utf-8',
		user='admin',
		password='admin1234'
	)
	return login


def wiki_works(target):
		login=make_login(target)
		result=do_test(login)
		return result

def main():
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

#!/usr/bin/env python
import sys
import os, os.path
sys.path.append("..")
sys.path.append("../..")
from naive_installer import Naive_Installer


from wikiation_exttest import settings

# settings
target_wiki='test'
resultfile=file("naive_results","w")

def do_test():
	try:
		site=settings.target_login.login()
		ver=site.live_version()
		print isinstance(ver,list)
		if isinstance(ver,list):
			return True
		else:
			return False
	except Exception, e:
		return False
	# or, if in doubt
	return False # anyway 

def test_extension(extension_name):
	installer=Naive_Installer()
	installer.set_instance(target_wiki)
	installer.install(extension_name)
	result=do_test()
	print "result=",result
	installer.uninstall(extension_name)

	return result

if __name__=="__main__":
	installer=Naive_Installer()
	# NOTE TO SELF set_revision is a bad name for this...
	# needs refactoring
	installer.set_instance(target_wiki)
	naive_extensions=installer.get_installers()
	for extension_name in naive_extensions:
		print extension_name,
		result=test_extension(extension_name)
		print result
		r="Unknown"
		if result:
			r="NAIVE_INSTALL_SEEMS_OK"
		else:
			r="NAIVE_INSTALL_BREAKS"
		resultfile.write("* "+extension_name+" "+r+"\n")
		resultfile.flush()	
	resultfile.close()

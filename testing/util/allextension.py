#!/usr/bin/env python
import sys
import os, os.path
sys.path.append("..")


import wiki_works
from installer.extension_installer2 import extension_installer2
# settings

target_wiki='test'
resultfile=file("extension_results","w")


def test_extension(extension_name):
	result=None
	try:
		installer=extension_installer2()
		installer.set_instance(target_wiki)
		installer.install(extension_name)
		result=wiki_works.wiki_works(target_wiki)
		print "result=",result
		installer.uninstall(extension_name)
	except Exception,e:
		print e

	return result

if __name__=="__main__":
	installer=extension_installer2()
	installer.set_instance(target_wiki)
	extensions=installer.get_installers()
	for extension_name in extensions:
		print extension_name,
		result=test_extension(extension_name)
		print result
		r="Unknown"
		if result:
			r="EXTENSION_INSTALL_SEEMS_OK"
		else:
			r="EXTENSION_INSTALL_BREAKS"
		resultfile.write("* "+extension_name+" "+r+"\n")
		resultfile.flush()	
	resultfile.close()

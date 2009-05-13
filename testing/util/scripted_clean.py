#!/usr/bin/env python
import sys
import os, os.path
sys.path.append("..")


import wiki_works
from installer.scripted_installer import Scripted_Installer
from installer.mediawiki_installer import Mediawiki_Installer
from installer.isolation import check_isolation,difftests
# settings

target_wiki='test'
target_wiki2='test2'
resultfile=file("scripted_results","w")
mw_revision="REL1_14_0"

def test_extension(extension_name):
	result=None
	try:
		installer=Scripted_Installer()
		installer.set_instance(target_wiki)
		installer.install(extension_name)
		result=wiki_works.wiki_works(target_wiki)
		print "result=",result
		installer.uninstall(extension_name)

	except Exception,e:
		print e

	return result

def newenvironment():
	"""setup a base environment, with a backup wiki in case an extension actually fails"""
	mwinstaller=Mediawiki_Installer()
	
	#remove old stuff
	try:
		mwinstaller.uninstall(target_wiki)
	except Exception,e: #?
		raise e

	try:
		mwinstaller.uninstall(target_wiki2)
	except Exception,e: #?
		raise e
	mwinstaller.install(installer_name=mw_revision, as_alias=target_wiki)	
	difftests(target_wiki)
	mwinstaller.duplicate(target_wiki, target_wiki2)


def clear():
	"""When an extension fails to uninstall cleanly, uninstall the entire wiki, and 
	copy the backup back in its place"""
	mwinstaller=Mediawiki_Installer()
	mwinstaller.uninstall(target_wiki)
	mwinstaller.duplicate(target_wiki2, target_wiki)


def is_clean():
	return check_isolation(["check_isolation",target_wiki])	



if __name__=="__main__":
	newenvironment()
	installer=Scripted_Installer()
	installer.set_instance(target_wiki)
	scripted_extensions=installer.get_installers()
	for extension_name in scripted_extensions:
		print extension_name,
		result=test_extension(extension_name)
		print result
		r="Unknown"
		if result:
			r="SCRIPTED_INSTALL_SEEMS_OK"
		else:
			r="SCRIPTED_INSTALL_BREAKS"

		clean=is_clean()
		if clean:
			r+="	UNINSTALL_CLEAN"
		else:
			r+="	UNINSTALL_DIRTY"
			clear()	


		resultfile.write("* "+extension_name+" "+r+"\n")
		resultfile.flush()	
	resultfile.close()

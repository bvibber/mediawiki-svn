# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

import settings_handler as settings
import os, os.path, shutil
import subprocess

from mediawiki_installer import dbname
from extension_installer import Extension_Installer, Extension_Installer_Exception

class Scripted_Installer_Exception (Extension_Installer_Exception):
	pass
	

class Scripted_Installer(Extension_Installer):

	def __init__(self,instance=None):
		Extension_Installer.__init__(self,instance)
		self.subsystemdir=os.path.join(settings.installfiles,"extensions")

	system_name="scripted"	
	def install_settings(self, installer_name):
		installdir=self.installdir_name(installer_name)
		settingsdir=os.path.join(self.destination_dir,"../LocalSettings")
		dirlist=os.listdir(installdir)
		for filename in dirlist:
			filepath=os.path.join(installdir,filename)
			if filename.endswith('.settings.php') and os.access(filepath, os.R_OK):
 				shutil.copy(filepath,settingsdir)

	def uninstall_settings(self,installer_name):
		settingsdir=os.path.join(self.destination_dir,"..","LocalSettings")
		installdir=self.installdir_name(installer_name)
		dirlist=os.listdir(installdir)
		#compare which files were originally installed, then remove those
		for filename in dirlist:
			filepath=os.path.join(installdir,filename)
			if filename.endswith('.settings.php') and os.access(filepath, os.R_OK):
				destpath=os.path.join(settingsdir,filename)
				if os.access(destpath, os.R_OK):
					os.unlink(destpath)

	def exec_task(self,installer_name,task,env=None):
		if env==None:
			env={}

		env=dict(env)
		env["EXTENSIONS_SVN"]=self.get_extensionsdir()
		env["DATABASE_NAME"]=dbname(self.instance)
		env["IN_INSTANCE"]=self.instance

		return Extension_Installer.exec_task(self, installer_name,task,env)
	

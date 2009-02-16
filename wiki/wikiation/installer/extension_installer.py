import settings
import os, os.path, shutil
import subprocess

from installation_system import Installation_System, Installer_Exception
	
class Extension_Installer_Exception(Installer_Exception):
	pass

class Extension_Installer(Installation_System):
	system_name='extensions'
	destination_dir=None

#	def __init__(self, revision):
#		Installation_System.__init__(self, revision)

	def set_revision(self,revision):
		self.destination_dir=os.path.join(settings.revisionsdir,revision,"extensions")
		self.revision=revision	
	

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


	def is_installed(self, installer_name):
		if self.revision==None:
			raise Extension_Installer_Exception("no revision specified ... did you try doing   ...  in <Revision> ?")
		return Installation_System.is_installed(self, installer_name)

	def exec_task(self,installer_name,task,env=None):
		if env==None:
			env={}

		env=dict(env)
		env["EXTENSIONS_SVN"]=settings.extensionsdir

		return Installation_System.exec_task(self, installer_name,task,env)
	

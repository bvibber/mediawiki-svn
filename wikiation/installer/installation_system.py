# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

import settings
import os, os.path, shutil
import subprocess


class Installer_Exception (Exception):
	pass

class Installation_System:
	system_name=None
	destination_dir=None
	revision=None
	as_alias=None

	def __init__(self,revision=None):
		self.subsystemdir=os.path.join(settings.installfiles, self.system_name)

		if revision:
			self.set_revision(revision)

	def set_revision(self,revision):
		self.revision=revision	

	def get_installers(self):
		installers=os.listdir(self.subsystemdir)
		installers2=[]
		for line in installers:
			if line.endswith(".install"):
				installers2.append(line.replace(".install",""))

		return installers2

	def exists(self,installer_name):
		return installer_name in self.get_installers()

	def installdir_name(self, installer_name):
		return os.path.join(self.subsystemdir, installer_name+".install")
	

	def exec_task(self, installer_name, task, env=None):
		"""attempt to execute a file starting with the name of task[o] (ie. task.sh, task.py, task.pl, etc)
		   * task is name of task. If task requires args, provide a list, task[0] is name of task, task[1:] is args
		   * if no such file exists or file fails to run, return an exception.
		   * if more than one match exists, one match is picked
		        Exactly Which match is picked is not defined
			(so Don't Do That)
		   * destination_dir is passed as a parameter 
			returns 
			* stdout returned by task command if successful
			* None if task not available
			
			raises  exception if problems occur
			"""
		
		installdir=self.installdir_name(installer_name)
		dirlist=os.listdir(installdir)

		if env==None:
			env={}

		env=dict(env)

		env["INSTALL_DIR"]=installdir
		env["DESTINATION_DIR"]=self.destination_dir
		env["NAME"]=installer_name
		env["REVISION"]=""	#reserved for future expansion

		if isinstance(task,str):
			task2=[task]
		else:
			task2=list(task)

		for filename in dirlist:
			if filename.startswith(task2[0]):
				task2[0]=os.path.join(installdir,filename)
				try:
					#print task2,env	#useful for debugging. Might want to make a flag! :-)
					process=subprocess.Popen(task2 ,stdout=subprocess.PIPE, env=env)
					stdout,stderr=process.communicate()
				except Exception, e:
					#reraise with task and env info included, to give us a clue where we went wrong
					raise Exception((e,task2,env))
				return stdout
		return None

	def can_exec(self, installer_name, task):
		installdir=self.installdir_name(installer_name)
		dirlist=os.listdir(installdir)
		for filename in dirlist:
			if filename.startswith(task) and os.access(os.path.join(installdir,filename), os.X_OK):
				return True
		
		return False
	
	
	def get_installed(self):

		installed=[]
		for installer_name in self.get_installers():
			if self.is_installed(installer_name):
				installed.append(installer_name)

		return installed
	
	def is_installed(self,installer_name):
		if not self.destination_dir:
			raise Installer_Exception("Installation_system: Internal Error: No destination_dir provided")

		if self.can_exec(installer_name,"is_installed"):
			rv=self.exec_task(installer_name,"is_installed")
			if rv==None:
				print "Warning: "+installer_name+" does not support 'is_installed'."
			elif "true" in rv:
				return True
			elif "false" in rv:
				return False
			else:
				print "Warning: "+installer_name+" 'is_installed' provides unexpected output "
		else:
			print "Warning: "+installer_name+" has some problems with 'is_installed'."

		return None


	def get_info(self,installer_name):
		"""print out information about the target from the info file"""
		info_filename=os.path.join(self.installdir_name(installer_name),"info")
		if not self.exists(installer_name):
			raise Installer_Exception("Can't find installer "+installer_name)

		if os.path.exists(info_filename):
			print file(info_filename).read()
		else:
			print "This installer provides no information."

	def install (self, installer_name):
		"""use the installation dir to actually install the program
			returns True if installation successful, false if not, and None if unknown"""
		if not self.exists(installer_name):
			raise Installer_Exception("Can't find installer "+installer_name)

		if self.is_installed(installer_name):
			print installer_name,"already installed."
			return

		self.download(installer_name)
		self.install_settings(installer_name)
		# ...
		# ...

		#query the installer to see if ot thinks the component is properly installed
		# any flaws at this point are the fault of the installer :-P
		return self.is_installed(installer_name)
	
	def download (self, installer_name, destination_dir=None):
		"""perform actions needed to download all the files we need"""

		destination_dir=destination_dir or self.destination_dir
		if not destination_dir:
			raise Exception("Installation_system: Internal Error: No  destination_dir provided")

		if not self.exists(installer_name):
			raise Installer_Exception("Can't find installer "+installer_name)
			
		# if a particular step in the install procedure is not provided
		# we simply skip it
		if not self.can_exec(installer_name,"download"):
			print "notice: cannot execute download script for "+installer_name
			return

		self.exec_task(installer_name,"download")

	def install_settings(self,installer_name):
		"""do setup for settings files, etc... override to do something useful"""
		pass

	def uninstall_settings(self,installer_name):
		"""remove settings files etc... override to do something useful"""
		pass

	def uninstall (self, installer_name, destination_dir=None):
		"""uninstall the component"""

		destination_dir=destination_dir or self.destination_dir
		if not destination_dir:
			raise Installer_Exception("Installation_system: Internal Error: No  destination_dir provided")

		if not self.exists(installer_name):
			raise Installer_Exception("Can't find installer "+str(installer_name))
			
		if not self.is_installed(installer_name):
			print installer_name+" does not appear to be installed"
			return

		# if a particular step in the install procedure is not provided
		# we simply skip it
		if not self.can_exec(installer_name,"uninstall"):
			return

		self.exec_task(installer_name,"uninstall")
		
		self.uninstall_settings(installer_name)
		# use is_installed to determine success.
		return not self.is_installed(installer_name) 


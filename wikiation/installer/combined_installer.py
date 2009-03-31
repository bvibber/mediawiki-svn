# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.


from installation_system import Installation_System, Installer_Exception

class Combined_Installer_Exception(Installer_Exception):
	pass

class Combined_Installer(Installation_System):
	"""operate a whole list of installers at once.
	 attempts to perform an action with each installer
	 in list in turn, until one of them works"""
	system_name="combined"
	destination_dir=None
	systemlist=None

	def __init__(self, systemlist):
		"""initialize with a list of pre-initialized
		   installers
		"""
		Installation_System.__init__(self)
		self.systemlist=systemlist

	def set_instance(self,instance):
		"""set instance on all items"""
		for system in self.systemlist:
			system.set_instance(instance)
	
	def get_installers(self):
		installers=set()
		for system in self.systemlist:
			try:
				installers.update(system.get_installers())
			except Exception:
				pass

		installers2=list(installers)
		installers2.sort()
		return installers2

	def get_revisions(self,installer_name):
		revisions=[]
		for system in self.systemlist:
			try:
				revisions=system.get_revisions(installer_name)
			except Exception:
				pass

			if revisions:
				break
	 	
		return revisions

	def get_tags(self, installer_name):
		tags=[]
		for system in self.systemlist:
			try:
				tags=system.get_tags(installer_name)
			except Exception:
				pass

			if tags:
				break
	 	
		return tags

	def get_svnbase(self):
		return None

	# exists: 		ok
	# installdir_name:	unused
	
	def exec_task(self, installer_name, task, env=None):
		"""try systems, if any system can_exec, we exec.
		short circuits: Only first system with capability
		will exec"""
		for system in self.systemlist:
			if system.can_exec(task):
				system.exec_task(installer_name, task, env)
				break
	

	def can_exec(self, installer_name, task):
		"""reply if we can execute a task, (allows nested
		combined_installers, fwiw. Short circuits (stops
		trying once it finds one system that supports the task))"""
		for system in self.systemlist:
			if system.can_exec(self, installer_name, task):
				return True

		return False

	def get_installed(self):
		"""return a list of installed items (items installed by all the installers)"""
		installers=set()
		for system in self.systemlist:
			try:
				installers.update(system.get_installed())
			except Exception:
				pass

		installers2=list(installers)
		installers2.sort()
		return installers2

	def is_installed(self,installer_name):
		"""return true if any of the installers finds that the particular item is already installed. (short circuits)"""
		for system in self.systemlist:
			if system.is_installed(installer_name):
				return True

	def get_info(self, installer_name):
		"""print out information about the target from the info file, short circuits"""
		for system in self.systemlist:
			ret=system.get_info()
			if ret:
				return ret
		return None

	def install(self, installer_name):
		"""actually install something. Short circuits (Will try each installer, until success"""
		messages=[]
		for system in self.systemlist:
			try:
				if system.install(installer_name):
					return True
			except Exception,e:	#TODO sometimes an installer may recognise that something CAN NOT be installed, in that case, we should stop trying.
				messages.append(system.system_name+": "+e.message)
		
		if messages:
			raise Combined_Installer_Exception ("\n".join(messages))
		
		return False

	def setup(self, installer_name, destination_dir=None):
		raise Combined_Installer_Exception("Internal Error:Can't do setup from here")


	def download (self, installer_name, destination_dir=None):
		raise Combined_Installer_Exception("Internal Error:Can't do download from here")


	def install_settings(self,installer_name):
		raise Combined_Installer_Exception("Internal Error:Can't do download from here")


	def uninstall_settings(self,installer_name):
		raise Combined_Installer_Exception("Internal Error:Can't do download from here")

	def uninstall(self, installer_name):
		"""actually uninstall something. Short circuits (Will try each installer, until success)"""

		messages=[]
		for system in self.systemlist:
			try:
				if system.uninstall(installer_name):
					return True
			except: #TODO sometimes an installer may recognise that something CAN NOT be uninstalled, in that case, we should stop trying
				messages.append(system.system_name+": "+e.message)
		
		if messages:
			raise Combined_Installer_Exception ("\n".join(messages))

		return False

	

	def get_extensionsdir(self):
		raise Combined_Installer_Exception("Internal Error:Can't do download from here")


	def __setattr__(self,name,value):
		"""also set any attributes for subsystems
		we also set the same attribute locally.
		Be careful when reading back!"""
		self.__dict__[name]=value
		if self.systemlist:
			for system in self.systemlist:
				system.__setattr__(name,value)


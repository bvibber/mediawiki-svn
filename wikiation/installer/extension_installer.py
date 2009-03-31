import os
import settings_handler as settings
from installation_system import Installation_System, Installer_Exception

class Extension_Installer_Exception(Installer_Exception):
	pass


class Extension_Installer(Installation_System):
	system_name='extensions_generic'
	destination_dir=None
	
	def set_instance(self,instance):
		self.destination_dir=os.path.join(settings.instancesdir,instance,"extensions")
		Installation_System.set_instance(self,instance)
	
	def is_installed(self, installer_name):
		if self.instance==None:
			raise Extension_Installer_Exception("no instance specified ... did you try doing   ...  in <instance> ?")
		return Installation_System.is_installed(self, installer_name)


	def get_svnbase(self):
		return settings.extensionsdir

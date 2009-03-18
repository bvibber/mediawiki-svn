import settings_handler as settings
import os, os.path, shutil
import subprocess

from installation_system import Installation_System

class Toolkit_Installer(Installation_System):
	system_name='wikiation_toolkit'
	def __init__(self,instance=None):
		Installation_System.__init__(self, instance)
		self.destination_dir=settings.toolkit_dir
	

import settings
import os, os.path, shutil
import subprocess

from installation_system import Installation_System


class Toolkit_Installer(Installation_System):
	system_name='wikiation_toolkit'
	destination_dir=settings.toolkit_dir
#	def __init__(self,revision):
#		Installation_System.__init__(self)
		

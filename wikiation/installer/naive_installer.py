# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

import settings_handler as settings
import os, os.path, shutil
import subprocess

from installation_system import Installation_System
from extension_installer import Extension_Installer_Exception
from download_installer import Download_Installer, Download_Installer_Exception

class Naive_Installer_Exception(Download_Installer_Exception):
	pass

class Unsupported_Exception(Exception):
	pass

class Naive_Installer(Download_Installer):
	"""download an extension, and try to naively just require_once() it in
	LocalSettings, this SHOULD work 60-80% of the time. (famous last words)"""
	system_name='naive'
	destination_dir=None

	def install_settings(self,installer_name):

		settings=file(self._settings_filepath(installer_name),"w")
	
		n=installer_name
		settings.write('<?PHP\n')
		settings.write('require_once("$IP/extensions/'+n+'/'+n+'.php");\n');
		settings.write('?>\n')
		settings.close()

	def uninstall_settings(self,installer_name):
		# rm -f (fail silently)
		try:
			os.unlink(self._settings_filepath(installer_name))
		except OSError:
			pass

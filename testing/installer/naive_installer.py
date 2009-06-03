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
import installer_util
import unschema

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

	def _setup(self, installer_name, destination_dir):
		self._setup_schema(installer_name, destination_dir)
		self._setup_update()

	def _setup_schema(self, installer_name, destination_dir):
		schema_path=os.path.join(destination_dir, installer_name, "schema.sql")
		if os.path.exists(schema_path):
			try:
				installer_util.sqldotphp(self.instancedir,schema_path)
			except Exception,e:
				raise Naive_Installer_Exception("_setup_schema: While installing, I found a schema.sql and tried to use it, but there was some issue with it.\n",e)

	def _setup_update(self):
		if not self.instancedir():
			raise Naive_Installer_Exception("_setup_update:Internal Error: Could not determine instancedir")
		command=settings.phpcommand+os.path.join(self.instancedir,maintenance,"update.php")
		rv=os.system(command)>>8
		if rv:
			raise Naive_Installer_Exception("_setup_update:While installing, I tried to run the instance's maintenance/update.php, but some issue occurred.")

	def _uninstall(self, installer_name, destination_dir):
		"""perform uninstallation of things that need uninstalling, in the case of the naive installer, we uninstall"""
		schemafilename=os.path.join(destination_dir, installer_name, "schema.sql")
		if os.path.exists(schemafilename):
			unschema.unschema(self.instancedir(), schemafilename)
		super(Naive_Installer,self)._uninstall(installer_name, destination_dir)

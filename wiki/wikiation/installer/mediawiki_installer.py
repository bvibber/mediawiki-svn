import settings
import os, os.path, shutil
import subprocess

from installation_system import Installation_System

from installer_util import *
from isolation import *

#this still uses some legacy structured code, wrapped in a class so it can't do too much harm outside this file. Will refactor later when I have more time.

# already partially refactored while creating class.


#constants

#Things that are not mediawiki revisions (in the svn tags directory on mediawiki svn)
filter_available=['extensions/']


class Mediawiki_Installer(Installation_System):
	system_name='mediawiki_installer'
	# TODO: destination_dir isn't quite changable until we have finished refactoring everything (not today)
	destination_dir=settings.revisionsdir
	def __init__(self):
		Installation_System.__init__(self)
	
	def get_installers(self):
		"""list available items"""

		l=list(os.popen('svn ls '+settings.tagsdir))
		stripped=[line.strip() for line in l]
		#filter out things that are not mediawiki revisions
		for item in filter_available:
			if item in stripped:
				stripped.remove(item)
		return stripped
	
	#def exists: same as super

	# Hmm: perhaps these should really belong in a separate mixin?
	#installdir_name: unused, but leave for future expansion
	#exec_task: unused, but leave for future expansion
	#can_exec: unused, but leave for future expansion

	def get_installed(self):
		"""list installed items"""
		return os.listdir(self.destination_dir)

	def is_installed(self, installer_name):
		return installer_name in self.get_installed()

	# super.get_info does something sane here, let's leave it in. 
	# (we can even provide info files for certain releases if we want)

	def install(self, installer_name, as_alias=None):
		name=as_alias or self.as_alias or installer_name

		install(installer_name, name)
		return self.is_installed(name)

	#download is unused, but leave for future expansion
	#install_settings unused
	#uninstall_settings

	def uninstall(self,installer_name):
		name=self.as_alias or installer_name
		if not self.is_installed(name):
			print name+" does not appear to be installed"
			return
	
		uninstall(name)
		return not self.is_installed(name) 



#duplicate of get_installed()
def installed():
	"""list installed items"""
	return os.listdir(settings.revisionsdir)

#duplicate of get_installers()
def available():
	"""list available items"""

	l=list(os.popen('svn ls '+settings.tagsdir))
	stripped=[line.strip() for line in l]
	for item in filter_available:
		if item in stripped:
			stripped.remove(item)
	return stripped



def install(target, option_as):
	"""implement install command. Installs a mediawiki version"""
	target=clean_target(target)
	
	latest=False
	if target=="latest":
		latest=True
	
	if option_as:
		name=clean_target(option_as)
	else:
		name=target

	if name in installed():
		print name+" already installed."
		return
	
	# available targets all end in '/', very consistent, this :-P
	if not latest and target+'/' not in available():
		print target+" is not available or invalid.\n(try:  ls available  )"
		return

	#Everything checks out ok, so let's install.
	os.chdir(settings.revisionsdir)
	print "Checking out code from subversion (please be patient)..."
	if latest:
		checkout_latest(name)
	else:
		checkout(target+"/", name)

	print "Creating LocalSettings.php..."
	localsettings(name)
	print "Copy logo..."
	logo(name)
	print "Setting up database..."
	makedb(name)
	if settings.run_automated_tests:
		print "Storing comparison data for check_isolation"
		difftests(name)
	print "Done."


def uninstall(target):
	"""implements uninstall command: uninstall mediawiki version"""

	target=clean_target(target)
	
	if target not in installed():
		print target+": can't find an installed revision by that name"
		return
	
	#Ok, looks like our arguments are valid.
	os.chdir(settings.revisionsdir)
	print "Dropping database..."
	dropdb(target)
	print "Deleting directory..."
	delete(target)
	print "Done."

def checkout(target, name):
	"""checkout the given target revision"""

	command="svn checkout "+settings.tagsdir+"/"+target+"phase3"
	_checkout(command,name)

def checkout_latest(name):
	"""checkout the latest trunk revision"""

	command="svn checkout "+settings.trunkdir+"/phase3"
	_checkout(command,name)

def _checkout(command, name):
	"""perform the actual check out, and rename our checked out data to something more useful"""

	os.system(command)
	os.rename('phase3',name)


def localsettings(target):
	"""create a localSettings.php file, for target mediawiki instance based
	on the LocalSettings.php.template file
	LocalSettings.php is the main configuration file for mediawiki."""

	template=settings.installerdir+"/LocalSettings.php.template"
	localsettings=settings.revisionsdir+"/"+target+"/LocalSettings.php"
	replacements={'<<TARGET>>':target,"<<BASE_SCRIPTPATH>>":settings.base_scriptpath}
	replace_generic(replacements,template,localsettings)	
	subdir=settings.revisionsdir+"/"+target+"/LocalSettings"
	os.mkdir(subdir)

def logo(target):
	"""copy a nice logo"""

	logo=settings.installerdir+"/Logo.png"
	dest=settings.revisionsdir+"/"+target+"/Logo.png"
	shutil.copy(logo,dest)

def makedb(target):
	"""make a mediawiki database for target mediawiki instance """

	dropdb(target);
	createdb(target);
	make_tables(target)
	make_admin(target)	

def make_tables(target):
	"""use the maintenance/tables.sql file provided by target mediawiki
	instance to generate our tables"""

	target_file=settings.revisionsdir+"/"+target+"/maintenance/tables.sql"
	do_sql(target, target_file)

def make_admin(target):
	"""use our usr.sql file to create an admin user"""
	do_sql(target, settings.installerdir+"/user.sql")

def do_sql(target, infile):
	"""execute an sql file, using mysql"""

	command="< "+infile+" "+settings.mysql_command+"" +target
	os.system(command)

def createdb(target):
	"""create a database using mysql"""

	command="echo 'CREATE DATABASE "+target+";' | "+settings.mysql_command
	os.system(command)

def dropdb(target):
	"""drop a database using mysql"""

	command="echo 'DROP DATABASE IF EXISTS "+target+";' | "+settings.mysql_command
	os.system(command)

def delete(target):
	"""delete target mediawiki installation, assumes we are in the revisions directory"""
	shutil.rmtree(target)


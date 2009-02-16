# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

import settings
import os, os.path, shutil
import subprocess

#from installation_system import Installation_System
from toolkit_installer import Toolkit_Installer
from extension_installer import Extension_Installer
from mediawiki_installer import Mediawiki_Installer
from installation_system import Installer_Exception

class Parse_Exception(Exception):
	pass

def ls(args):
	if len(args)==0:
		print "Internal error: args list too short"
		return
	if len(args)<=1:
		args.append("")

	ppath=None
	try:
		ppath=parse_path(" ".join(args[1:]))
	except Parse_Exception,e:
		print e.message
		return

	if ppath['ai']=='available':
		output=ls_available(ppath)
	elif ppath['ai']=='installed' or ppath['ai']==None: # XXX hardcoded default
		output=ls_installed(ppath)
	
	if output==None:
		return

	for line in output:
		print line

def ls_available(ppath):
	if ppath["system"]==None:
		return ls_systems()
	else:
		return ls_available_in_system(ppath)

def ls_installed(ppath):
	if ppath["system"]==None:
		if ppath["installer"]==None:
			return ls_systems()
		else:
			ppath["system"]="mediawiki"	# XXX hardcoded default

	return ls_installed_in_system(ppath)

def ls_systems():
	return [item+':' for item in systems.keys()]

def ls_available_in_system(ppath):
	system=get_system(ppath["system"])
	if not system:
		return
	return system.get_installers()

def ls_installed_in_system(ppath):
	system=get_system(ppath["system"])
	if not system:
		return

	if ppath["in_installer"]:
		system.set_revision(ppath["in_installer"])
	installed=None
	try:
		installed=system.get_installed()
	except Installer_Exception,e:
		print e.message
		return 

	return installed


def info(args):
	if len(args)<1:
		print "info: Internal error: expected more arguments"
	
	ppath=None
	try:
		ppath=parse_path(" ".join(args[1:]),defaults={'ai':'available'})
	except Parse_Exception,e:
		print e.message
		return

	if not ppath["ai"]:
		ppath["ai"]="available"
	
	system=get_system(ppath["system"])
	system.get_info(ppath["installer"])
	
def install(args):
	if len(args)<1:
		print "install: Internal error: expected more arguments"

	ppath=None
	try:
		ppath=parse_path(" ".join(args[1:]), defaults={'ai':'available','system':'mediawiki'})
	except Parse_Exception,e:
		print e.message
		return

	if ppath["ai"]=="installed":
		print "Did you mean to install from available. ?"
		return
	
	system=get_system(ppath["system"])
	if ppath["in_installer"]:
		system.set_revision(ppath["in_installer"])
	if ppath["as_alias"]:
		system.as_alias=ppath["as_alias"]

	try:
		success=system.install(ppath["installer"])
		if success:
			print "Install successful."
		else:
			print "Install failed."

	except Installer_Exception,e:
		print e.message
		return 


def uninstall(args):
	if len(args)<1:
		print "install: Internal error: expected more arguments"

	ppath=None
	try:
		ppath=parse_path(" ".join(args[1:]),defaults={'ai':'installed','system':'mediawiki'})
	except Parse_Exception,e:
		print e.message
		return

	if ppath["ai"]=="available":
		print "Did you mean to uninstall from installed. ?"
		return

	system=get_system(ppath["system"])
	if ppath["in_installer"]:
		system.set_revision(ppath["in_installer"])
	if ppath["as_alias"]:
		system.as_alias=ppath["as_alias"]
	try:
		success=system.uninstall(ppath["installer"])
	 	if success:
			print "Uninstall successful."
		else:
			print "Uninstall failed."
	except Installer_Exception,e:
		print e.message
		return 

def _ppath_defaults(ppath,defaults):
	for key in ppath.keys():
		if key in defaults:
			ppath[key]=ppath[key] or defaults[key]
		

def parse_path(path,defaults=None):
	ai=None	
	system=None
	installer=None
	in_installer=None
	as_alias=None

	#partial components
	whence=None	# eg. 'available.mediawiki:'
	single_case=None # a single word without context
	inpath=None	# eg. 'ImageMap in REL1_13_2"

	if ":" in path:
		whence, inpath=path.split(':')
	elif "." in path:
		whence=path
	else:
		single_case=path

	if whence:
		if "." in whence:
			ai,system=whence.split('.')
		else:
			single_case=whence

	if single_case:
		if single_case in systems.keys():
			system=single_case
		elif single_case in ["available","installed"]:
			ai=single_case
		elif single_case==path:
			inpath=single_case
		else:
			raise Parse_Exception("I'm not sure what to do with '"+single_case+"' in this context.")

	if inpath:
		l=inpath.split()
		if l[0] not in ["in","as"]:
			installer=l[0]

		if "in" in l:
			i=l.index("in")
			try:
				in_installer=l[i+1]
			except IndexError:
				raise Parse_Exception("Syntax error. Nothing after 'in'.")

		if "as" in l:
			i=l.index("as")
			try:
				as_alias=l[i+1]
			except IndexError:
				raise Parse_Exception("Syntax error. Nothing after 'as'.")
	
	# Ok, we have our basic return value now
	ppath={
		"ai":ai,	#available or installed
		"system":system,
		"installer":installer,
		"in_installer":in_installer,
		"as_alias":as_alias}
	

	# maybe we can assume some useful defaults (saves typing)
	if defaults:
		_ppath_defaults(ppath,defaults)

	# let's check to see if what we get is sane.

	if ppath['ai'] not in ["available","installed",None]:
		raise Parse_Exception("By '"+ppath['ai']+"', did you mean available or did you mean installed?")
		
	if ppath['system'] not in systems.keys() and not ppath['system']=="None":
		system_names=", ".join(ls_systems())
		raise Parse_Exception("Did you mean to specify any of "+system_names)
	
	# we assume that the "in" directive always applies to a mediawiki instance
	if ppath['in_installer']:
		mediawiki=get_system('mediawiki')
		if not mediawiki.is_installed(ppath['in_installer']):
			raise Parse_Exception(ppath['in_installer']+' is not currently installed')
	
	# :-(
	#if installer:
	#	if not system:
	#		system='mediawiki'	# XXX hardcoded default
	#	mysystem=get_system(system)
	#	
	#	if in_installer:
	#		system.set_revision(ppath["in_installer"])
	#	
	#	if system.is_installed(installer)
	
	# we can only check if the installer name itself is valid
	# somewhat later, once we know what context we are in.
	
	return ppath
	


def get_system(system_name):
	if system_name not in systems:
		print "Cannot find '"+system_name+"' in the list of supported installation systems."
		return None
	else:
		system=systems[system_name]

		return systems[system_name]

		

systems={'wikiation_toolkit':Toolkit_Installer(),'extension': Extension_Installer(), 'mediawiki':Mediawiki_Installer()}


if __name__=="__main__":
	print "testing installers.py module"
	print "CTRL-C to abort.   run installer.py to actually use the installer"
	print
#	print "ls"
#	ls2(["ls"])
#	print
#	print "ls available"
#	ls2(["ls","available"])
#	print
#	print "ls available/wikiation_toolkit"
#	ls2(["ls","available/wikiation_toolkit"])
#	print
#	print "ls installed/wikiation_toolkit"
#	ls2(["ls","installed/wikiation_toolkit"])
#	print
#	print "get info"
#	system=get_system("wikiation_toolkit")
#	system.get_info("pywikipedia")
#	
#	print "install"
#	print "pywiki",repr(system.install("pywikipedia"))
#	print "exttest", repr(system.install("wikiation_exttest"))
#	print "isolation", repr(system.install("wikiation_check_isolation"))
#	print "ls", os.listdir('..')
#
#	print "uninstall"
#	print "pywiki", repr(system.uninstall("pywikipedia"))
#	print "exttest", repr(system.uninstall("wikiation_exttest"))
#	print "isolation", repr(system.uninstall("wikiation_check_isolation"))
#
#	print "wikiation_extension (assumes existing wiki)"
#	extension_installer=Extension_Installer("REL1_13_3")
#
#	print " \ uninstall " , repr (extension_installer.uninstall("ImageMap"))
#	print "ImageMap", repr (extension_installer.install("ImageMap"))
#
	#print "mediawiki installer"
	#print "avail"
	#ls2(["ls","available/mediawiki"])
	#print "installed"
	#ls2(["ls","installed/mediawiki"])
#
#	print "try another wiki, say REL1_13_2"
	
#	mediawiki_installer=get_system("mediawiki")
#	
#	print "uninstl mediawiki 1_13_2", repr (mediawiki_installer.uninstall("REL1_13_2"))
#	print "install mediawiki 1_13_2", repr (mediawiki_installer.install("REL1_13_2"))
#
#	print "extension (assumes existing wiki)"
#	extension_installer=Extension_Installer("REL1_13_2")
#
#	print " \ uninstall " , repr (extension_installer.uninstall("ImageMap"))
#	print "ImageMap", repr (extension_installer.install("ImageMap"))
#
#	print "try some ls stuff"
#	def qls(mystr): # q for "quick"
#		print "ls", repr(mystr)
#		ls(['ls',mystr])
#	
#	qls('')
#	qls('available')
#	qls('installed')
#	qls('available:')
#	qls('installed:')
#	qls('murp.morp:')
#	qls('installed.mediawiki:')
#	qls('installed.extension:')
#	qls('installed.toolkit:')
#	qls('installed.mediawiki:in REL1_13_2')
#	qls('available.extension:')
#	qls('installed.extension:in REL1_13_2')
#	qls('installed.extension:in FOO')
#	qls('installed.mediawiki:in FOO')
#	qls('available.wikiation')
#	qls('available.wikiation:in BAR as BAZ')
#	qls('available.wikiation:in BOG as BOT')
#	qls('available.wikiation:in BOG as BOT murp morp bla bla bla')

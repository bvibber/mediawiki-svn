# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.
#
#Back end code to deal with paths and to delegate 
#tasks to relevant installation systems"""

import settings_handler as settings
import os, os.path, shutil
import subprocess
import installer_util

#from installation_system import Installation_System
from toolkit_installer import Toolkit_Installer
from scripted_installer import Scripted_Installer
from mediawiki_installer import Mediawiki_Installer
from naive_installer import Naive_Installer
from installation_system import Installer_Exception
from download_installer import Download_Installer
from extension_installer2 import extension_installer2
from test_system import Test_System,Test_Exception

from tags import Tags, TagsException

class Parse_Exception(Exception):
	"""Exception if something goes wrong while 'parsing' installer pathnames"""
	pass

class Listing_Exception(Exception):
	"""Exception if something goes wrong while doing an ls-like operation"""
	pass

def ls(args):
	"""list things, what exactly gets listed depends on the path"""
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

	try:
		if ppath['ai']=='available':
			output=ls_available(ppath)
		elif ppath['ai']=='revisions':
			output=ls_revisions(ppath)
		elif ppath['ai']=='tags':
			output=ls_tags(ppath)
		elif ppath['ai']=='installed' or ppath['ai']==None: # XXX hardcoded default
			output=ls_installed(ppath)
	except Listing_Exception,e:
		print e.message
		return
	
	if output==None:
		return
	
	if ppath['limit']:
		output=output[0:ppath['limit']]
	print installer_util.pretty_list(output)

def ls_available(ppath):
	"""list things that are available to be installed"""
	if ppath["system"]==None:
		return ls_systems()
	else:
		return ls_available_in_system(ppath)

def ls_installed(ppath):
	"""list things that have already been installed"""
	if ppath["system"]==None:
		if ppath["installer"]==None:
			return ls_systems()
		else:
			ppath["system"]="mediawiki"	# XXX hardcoded default

	return ls_installed_in_system(ppath)

def ls_systems():
	"""list the available installation systems"""
	return [item+':' for item in systems.keys()]


def ls_available_in_system(ppath):
	"""list items that are available in a particular installation system
	for example:  ls available.mediawiki: """
	system=get_system(ppath["system"])
	if not system:
		return
	return system.get_installers()

def ls_installed_in_system(ppath):
	"""list items that are already installed a particular installation system
	for example:  ls installed.mediawiki:  or  ls installed.extension: in my_wiki"""

	system=get_system(ppath["system"])
	if not system:
		return

	if ppath["in_installer"]:
		system.set_instance(ppath["in_installer"])
	installed=None
	try:
		installed=system.get_installed()
	except Installer_Exception,e:
		print e.message
		return 

	return installed

def ls_revisions(ppath):
	"""list revisions of an item that are available in a system
	eg
	ls revisions.mediawiki:
	ls revisions.extension:ImageMap"""
	if not ppath["system"]:
		raise Listing_Exception("What system would you like me to list?")
	
	system=get_system(ppath["system"])

	revisions=None
	try:
		revisions=system.get_revisions(ppath["installer"])
	except Installer_Exception, e:
		raise Listing_Exception(e.message)

	return revisions


def ls_tags(ppath):
	"""list all the svn tags that apply to this item.
	eg
	ls tags.mediawiki:
	ls tags.extension:ImageMap
	be aware that on svn, the underlying algorithm can be very slow,
	and may require caching.
	"""

	system=get_system(ppath["system"])
	try:
		tags=system.get_tags(ppath["installer"])
	except TagsException, e:
		raise Listing_Exception(e.message)
	
	return tags

def info(args):
	"""print information on a particular item"""
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
	

def duplicate(args):
	"""duplicate a mediawiki instance"""
	mw=get_system("mediawiki")
	try:
		mw.duplicate(args[1],args[2])
	except Mediawiki_Installer_Exception,e:
		print e.message


def install(args):
	"""install something. What gets installed depends on the path"""
	if len(args)<2:
		print "install: Internal error: expected more arguments"
		return

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
		system.set_instance(ppath["in_installer"])
	if ppath["as_alias"]:
		system.as_alias=ppath["as_alias"]
	if ppath["revision"]:
		system.revision=ppath["revision"]
	if ppath["tag"]:
		system.tag=ppath["tag"]
	if ppath["language"]:
		system.language=ppath["language"]

	try:
		success=system.install(ppath["installer"])
		if success:
			print "Install successful."
		else:
			print "Install failed."

	except Installer_Exception,e:
		print e.message

def test(args):
	if len(args)<2:
		print "test: Internal error: expected more arguments"
		return
	target=args[1]

	test_system=Test_System()
	wiki_works=test_system.run_wikiworks(target)
	if wiki_works:
		print "* Wiki is up"
	else:
		print "* Wiki is DOWN"
		print "Test discontinued. "
		return

def uninstall(args):
	"""uninstall something. What gets uninstalled depends on the path"""
	if len(args)<2:
		print "uninstall: Internal error: expected more arguments"
		return

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
		system.set_instance(ppath["in_installer"])
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
	"""take a parse path, and fill in empty spots with
	default values.
	see: parse_path"""
	for key in ppath.keys():
		if key in defaults:
			ppath[key]=ppath[key] or defaults[key]
		

def _ppath_find(l,keyword):
	"""
	 refactor of parse_path. Take l=inpath.split(),
	 and see if the keyword exists in that list
	 if exists, return the value following the keyword
	 if not exists, nothing happens
	 if keyword exists but no value is provided, throw exception
	 see: parse_path"""
	if keyword in l:
		i=l.index(keyword)
		try:
			value=l[i+1]
		except IndexError:
			raise Parse_Exception("Syntax error. Nothing after '"+keyword+"'.")
		
		return value

def parse_path(path,defaults=None):
	"""parses a path of the form
		[ai.][system:]installer [in iii] [as aaa] [revision rrr] [tag ttt] [limit n]
		
	where:
	ai:=<available|installed|revisions|tags>
	system:=< (see the systems constant, defined near end of file) >
	installer	is some name of a script to install an item or the item name to be installed (depends on system)
	in 		where to install things *in* (typically used with the extension installer)
	as		give something a name or alias    
				install mediawiki:REL1_13_3 as my_mediawiki_example 
			creates a new mediawiki instance (release 1.13.3)  with the name my_mediawiki_example
	revision	use a particular (svn) revision number
	tag		use a particular (svn) tag
	limit n		limits output to at most n items (analogous to the sql command of same name) (useful with ls)
	"""

	ai=None	# available, installed (and now revisions and tags  and test too)
	system=None	# installation system
	installer=None	# installer from that installation system
	in_installer=None # in which instance should we install?
	as_alias=None	# if installing, as what name?
	revision=None	# revision number, if any
	tag=None	# tag, if any
	limit=None	# limit output from list commands to n lines.
	language=None	# default language code for wikis

	#partial components
	whence=None	# eg. 'available.mediawiki:'
	single_case=None # a single word or element, with insufficient context upfront to figure what it is
	inpath=None	# eg. 'ImageMap in REL1_13_2"

	if ":" in path:
		# installed.extension: in foo
		#|-----whence--------|-inpath-|

		try:
			whence, inpath=path.split(':')
		except ValueError:
			raise Parse_Exception("You're doing something odd with ':'. I don't understand.")

	elif "." in path:
		#installed.extension
		whence=path
	else:
		# ? 
		single_case=path

	# left side (whence)  __________:
	if whence:
		if "." in whence:
			# installed.extension    : ...
			#|---ai----|-system--|
			try:
				ai,system=whence.split('.')
			except ValueError:
				raise Parse_Exception("You're doing something odd with '.'. I don't understand.")
		else:
			# ?    : ...
			single_case=whence
	
	# Hmmm, not a fully-formed path(-section). Perhaps we can still make heads or tails of it?
	if single_case:
		if single_case in systems.keys() or single_case=="hailmary":
			system=single_case
		elif single_case in ["available","installed", "revisions","tags"]:
			ai=single_case
		elif single_case==path:
			inpath=single_case
		else:
			raise Parse_Exception("I'm not sure what to do with '"+single_case+"' in this context.")

	# right side (inpath)  :_______________
	if inpath:
		l=inpath.split()
		if l[0] not in ['in', 'as', 'revision', 'tag','limit','language']:
			installer=l[0]

		in_installer=_ppath_find(l,"in")
		as_alias=_ppath_find(l,"as")
		revision=_ppath_find(l,"revision")
		tag=_ppath_find(l,'tag')	
		try:
			limit=int(_ppath_find(l,'limit'))
		except Exception:
			pass
		language=_ppath_find(l,'language')

	# Ok, we have our basic return value now
	ppath={
		"ai":ai,	#available or installed
		"system":system,
		"installer":installer,
		"in_installer":in_installer,
		"as_alias":as_alias,
		"revision":revision,
		"tag":tag,
		"limit":limit,
		"language":language}

	# maybe we can assume some useful defaults (saves typing)
	if defaults:
		_ppath_defaults(ppath,defaults)

	# let's check to see if what we get is sane.

	if ppath['ai'] not in ["available","installed","revisions","tags",None]:
		raise Parse_Exception("By '"+ppath['ai']+"', did you mean available, installed, revisions, or tags?")
	
	if ppath['system']=="hailmary": # easter egg
		ppath['system']='naive' # the naive installer was originally pitched as a 
					# "hail mary" installer, as that's what it does, after all! ;-)

	if ppath['system'] not in systems.keys() and not ppath['system']=="None":
		system_names=", ".join(ls_systems())
		raise Parse_Exception("Did you mean to specify any of "+system_names)
	
	# we assume that the "in" directive always applies to a mediawiki instance
	# NOTE:possibly this snippet of code should be in mediawiki_installer instead
	if ppath['in_installer']:
		mediawiki=get_system('mediawiki')
		if not mediawiki.is_installed(ppath['in_installer']):
			raise Parse_Exception(ppath['in_installer']+' is not currently installed')

	return ppath



def get_system(system_name):
	"""Factory method. Instantiates and returns the relevant installer for the given system_name"""
	if system_name not in systems:
		print "Cannot find '"+system_name+"' in the list of supported installation systems."
		return None
	else:
		sYstem=systems[system_name]

		return sYstem()

# Constants

systems={'toolkit':Toolkit_Installer,'scripted': Scripted_Installer, 'mediawiki':Mediawiki_Installer,'naive': Naive_Installer, 'download':Download_Installer, 'extension':extension_installer2}


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
#	print "ls available/toolkit"
#	ls2(["ls","available/toolkit"])
#	print
#	print "ls installed/toolkit"
#	ls2(["ls","installed/toolkit"])
#	print
#	print "get info"
#	system=get_system("toolkit")
#	system.get_info("pywikipedia")
#	
#	print "install"
#	print "pywiki",repr(system.install("pywikipedia"))
#	print "exttest", repr(system.install("exttest"))
#	print "isolation", repr(system.install("check_isolation"))
#	print "ls", os.listdir('..')
#
#	print "uninstall"
#	print "pywiki", repr(system.uninstall("pywikipedia"))
#	print "exttest", repr(system.uninstall("exttest"))
#	print "isolation", repr(system.uninstall("check_isolation"))
#
#	print "extension (assumes existing wiki)"
#	extension_installer=Scripted_Installer("REL1_13_3")
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
#	extension_installer=Scripted_Installer("REL1_13_2")
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
#	qls('available.toolkit')
#	qls('available.toolkit:in BAR as BAZ')
#	qls('available.toolkit:in BOG as BOT')
#	qls('available.toolkit:in BOG as BOT murp morp bla bla bla')

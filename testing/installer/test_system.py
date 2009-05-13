# (C) 2009 Kim Bruning.
#
# Distributed under the terms of the MIT license.

import settings_handler as settings
import os, os.path, shutil
import subprocess
from tags import Tags

try:
	import wiki_works
except:
	pass # we'll deal with this later (in __init__).
		# this allows us to import the module, even
		#though the class can't work (yet)


class Test_Exception(Exception):
	pass

class Test_System(object):
	"""An Abstract Test System. Don't instantiate this class directly.
		An installation system understands how to install and uninstall
		'things' (instances). An instance might be a particular wiki
		(in the case of the mediawiki installer) or a particular extension 
		in that wiki (extension installer), or perhaps a tool from the 
		toolkit. 
		Next to installing and uninstalling, an installer can also determine
		the status of an instance (installed or uninstalled), and can provide
		lists of instances that are available or installed"""
	system_name=None
	destination_dir=None

	def __init__(self,target=None):
		if "wiki_works" not in globals():
			raise Test_Exception("You need to install pywikipedia and the extension-tester before you can run tests. You can find these fine tools under toolkit:")
		self.testfilesdir=settings.testfilesdir
		self.destination_dir=None
		self.target=target
		self.as_alias=None
		self.revision=None
		self.tag=None
		if instance:
			self.set_instance(instance)

	def set_instance(self,instance):
		self.instance=instance	

	def get_entities(self):
		"""list the extensions we have tests for"""
		entities=os.listdir(self.testfilesdir)
		entities=[]
		for line in entities:
			if line.endswith(".test"):
				entities2.append(line.replace(".test",""))

		entities2.sort()
		return entities2

	def tests_for_entity(self, entity):
		# TODO we only have one kind of test right right now.
		return ["WETE"]

	def entity_exists(self,entity_name):
		"""checks to see if a particular installer exists"""
		return entity_name in self.get_entities()

	def test_exists(self, entity, test):
		if self.entity_exists(entity):
			return test in self.tests_for_extension(extension)
		else:
			 return false

	def testdir_name(self, entity_name):
		"""returns the location of the .install directory for the given installer_name.
		An installer directory is where we store all the scripts to install one particular
		extension, tool from the toolkit, or etc. """
		return os.path.join(self.testfilesdir, entity_name+".tests")


	
	def test (self, entity, test, target=None):
		if not target:
			target=self.target
		if not target:
			raise Test_Exception("What mediawiki instance would you like to test?")
		if test=="WETE":
			self.run_WETE(entity)
		elif test=="wikiworks":
			self.run_wikiworks()
		else:
			raise Test_Exception("I don't know of a test called '"+str(test)+"'.")

	def run_WETE(self,target,entity):
		pass
	
	def run_wikiworks(self,target):
		return wiki_works.wiki_works(target)

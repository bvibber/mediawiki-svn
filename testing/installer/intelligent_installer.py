# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

from combined_installer import Combined_Installer
from extension_installer2 import extension_installer2
from mediawiki_installer import Mediawiki_Installer

def intelligent_installer():
	"""factory: returns a combined installer that installs extensions from either scripted or naive
	
	technical detail:
	For use with installers.get_system(system_name)
	we exploit the fact that instantiation has the same semantics as a function call"""
	mediawiki=Mediawiki_Installer()
	extension2=extension_installer2()
	combined=Combined_Installer([mediawiki,extension2])
	return combined

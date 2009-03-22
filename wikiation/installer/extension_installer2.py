# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

from combined_installer import Combined_Installer
from scripted_installer import Scripted_Installer
from naive_installer import Naive_Installer


def extension_installer2():
	"""factory: returns a combined installer that installs extensions from either scripted or naive
	
	technical detail:
	For use with installers.get_system(system_name)
	we exploit the fact that instantiation has the same semantics as a function call"""
	scripted=Scripted_Installer()
	naive=Naive_Installer()
	combined=Combined_Installer([scripted,naive])
	return combined

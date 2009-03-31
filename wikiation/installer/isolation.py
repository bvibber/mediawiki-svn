# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

import sys,os, os.path
import settings_handler as settings

#support for wikiation_check_isolation

def _check_isolation(args):
	"""wikiation_check_isolation: check diffs now"""
	difftest=settings.isolation_test
	command=difftest+" "+" ".join(args)
	exit_status=os.system(command)>>8
	return exit_status==0

def check_isolation(args):
	"""implement check_isolation command"""

	if len(args) < 2 :
		print "Must specify a target!\n(try:  ls installed  )"
		return
	target=args[1]
	if target not in installed():
		print target+' is not currently installed'
		return

	return _check_isolation(args[1:])


def difftests(target):
	"""wikiation_check_isolation: store diffs, ready for testing later"""

	difftest=settings.isolation_create
	command=difftest+" "+target
	os.system(command)

def installed():
	"""list installed items"""
	# XXX COPIED from mediawiki_installer.py
	#( Else we'd have a recursive import)
	return os.listdir(settings.instancesdir)



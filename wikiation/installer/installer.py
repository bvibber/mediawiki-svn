#!/usr/bin/python

# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

import sys,os, os.path
import readline
import re
import shutil
import settings

import installers

from installer_util import *
from isolation	import *

def intro():
	"""a nice banner/intro text for interactive mode"""

	print "=== Wikiation installer (v. "+revision()+") ==="
	print
	print "Interactive mode.",
	print "Automated testing is",
	if settings.run_automated_tests:
		print "enabled."
	else:
		print "disabled."
	print
	print "please type a command and hit enter"
	print "help<enter> for help"
	print "^D, or quit<enter> to quit"
	print

def revision():
	"""obtain revision number for wikiation_installer itself"""

	revision_string=None
	os.chdir(settings.installerdir)
	info=os.popen("svn info .")
	for line in info:
		if line.startswith("Revision:"):
			revision_string=line.strip()
			break
	info.close()
	if revision_string==None:
		revision="unknown"
	else:
		revision=revision_string.replace("Revision:","")
	
	return revision

def command():
	"""handle the command line in interactive mode"""

	try:
		command=raw_input('installer > ').strip()
	except EOFError:
		print "quit"
		sys.exit(0)

	if command:
		args=command.split()
		do_command(args)

	return True

def do_command(all_args):
	"""execute a command. Command is expected as a list (like we get from str.split(), or from sys.args). all_args[0] is expected to be the command name.
	"""

	if all_args[0] in commands:
		commands[all_args[0]](all_args)
	else:
		print "I don't know how to '"+all_args[0]+"'."

def ls(args):
	"""implement local ls command, lists things we can install"""
	installers.ls(args)

def info(args):
	"""provide information on modules, where available"""
	installers.info(args)

def install(args):
	"""install something"""
	installers.install(args)


def uninstall(args):
	"""uninstall something"""
	installers.uninstall(args)

def update_self(ignored_args):
	"""update self to newest revision, (and switch to interactive mode)"""

	os.chdir(settings.installerdir)	
	os.system("svn update")
	print "\n\n"
	print "wikiation_installer update attempted/completed. Restarting"
	print "----------------------------------------------------------"
	print "\n\n"
	os.execl("/usr/bin/python","/usr/bin/python",__file__)


def default_revision(args):
	if len(args)>1:
		defualt_revision=args[1]
	
	print "current revision:",default_revision

def main():
	"""main function. start of execution when run from shell"""
	if len(sys.argv)>1:
		args=sys.argv[1:]
		do_command(args)
		exit()
		

	histfile=os.path.join(os.environ["HOME"], ".installerhist")
	try:
	    readline.read_history_file(histfile)
	except IOError:
		pass
	readline.parse_and_bind("tab: complete")

	import atexit
	atexit.register(readline.write_history_file, histfile)

	intro()

	# main loop.
	while(command()):
		pass

# Global!
default_revision=None

#constants

# bind commands to functions
commands={
	"help":help,
	"quit":quit,
	"ls":ls,
	"install":install,
	"uninstall":uninstall,
	"info":info,
	"check_isolation":check_isolation,
	"update_self":update_self,
	"revision":default_revision
}

# additional help texts for some commands.
subhelp={
	"ls":"""ls installed : list currently installed items
ls available : list items available"""

}


# our internal "directory structure"
internal_dirs=os.listdir(settings.installfiles)
internal_dirs.extend(['mediawiki'])

if __name__ == "__main__":
	main()


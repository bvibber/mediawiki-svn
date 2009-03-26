#!/usr/bin/python

# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

# Automated Installation tool for mediawiki and extensions.
# primarily inteded to be run in test environments,
# where you quickly (and automatically) want to set up
# and tear down mediawiki instances several times a minute

# Ways to use:
# * Interactive mode CLI to pick up how the system works
# * Call from bash scripts
# * import the relevant installer(s) directly in python, and use from there

# This file: 
#* Main entry point for Interactive CLI mode, (call with no params)
#* Main entry point for scriptable mode (call with relevant command as parameters) 

import sys,os, os.path
import readline
import re
import shutil
import settings_handler as settings

import installers

from installer_util import *
from isolation	import *
from getch import getch
from tags import Tags


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
	"""execute a command. Command is expected as a list
	(like we get from str.split(), or from sys.args). 
	all_args[0] is expected to be the command name.
	"""

	if all_args[0] in commands:
		commands[all_args[0]](all_args)
	else:
		print "I don't know how to '"+all_args[0]+"'."

def ls(args):
	"""implement local ls command, lists things we can install"""
	if len(args)<=1:
		print \
"""available.mediawiki:		installed.mediawiki:
available.wikiation_toolkit:	installed.wikiation_toolkit:
available.scripted:		installed.scripted:	} 
available.naive:		installed.naive:	} extensions
available.download:		installer.download:	} 
"""
		return
	installers.ls(args)

def info(args):
	"""provide information on modules, where available"""
	installers.info(args)

def install(args):
	"""install something"""
	installers.install(args)

def duplicate(args):
	"""duplicate an instance"""
	if len(args)!=3:
		print "syntax:\n duplicate source_instance destination_instance"
	installers.duplicate(args)

def uninstall(args):
	"""uninstall something"""
	installers.uninstall(args)

def update_self(args):
	"""update self to newest revision, (and switch to interactive mode)"""

	print "This could cause the wikiation_installer to break. Are you sure? Y/N"
	answer=getch()
	if answer.upper()=="Y":
		print "Continuing..."
	else:
		print "You did not answer Y. I won't update."
		return

	os.chdir(settings.installerdir)	
	if len(args)==1:
		os.system("svn update")
	elif len(args)>=2 and isanint(args[1]):
		os.system("svn update -r"+args[1])
	elif len(args)>=2:
		print "I'm not sure what to do with "+ (" ".join(args[1:]))

	print "\n\n"
	print "wikiation_installer update attempted/completed. Restarting"
	print "----------------------------------------------------------"
	print "\n\n"
	os.execl("/usr/bin/python","/usr/bin/python",__file__)

def update_tags(args):
	"""manually force update of the tag cache"""
	Tags.update_cache_file()

def main():
	"""main function. start of execution when run from shell"""

	# if we have command line params, parse them and exit
	if len(sys.argv)>1:
		args=sys.argv[1:]
		do_command(args)
		exit()
		
	#else we'll run our own interactive CLI

	#readline
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
#constants

# bind commands to functions
commands={
	"help":help,
	"quit":quit,
	"exit":quit,
	"ls":ls,
	"install":install,
	"uninstall":uninstall,
	"info":info,
	"check_isolation":check_isolation,
	"update_self":update_self,
	"update_tags":update_tags,
	"duplicate":duplicate
}

# additional help texts for some commands.
subhelp={
	"ls":"""ls installed : list currently installed items
ls available : list items available"""

}

if __name__ == "__main__":
	main()


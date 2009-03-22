# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

import sys, os
import settings_handler as settings

def replace_generic(replacements,infilename,outfilename):
	"""generic replace function, takes a dictionary of search/replace
	strings (replacements), and applies them to the input file 
	specified by infilename, and saves the results to the 
	output file at outfilename"""

	infile=file(infilename)
	outfile=file(outfilename,"w")
	for line in infile:
		for search,replace in replacements.iteritems():
			line=line.replace(search,replace)
		outfile.write(line)
	outfile.close()
	infile.close()
	

def help(args):
	"""implement help command: prints helpful messages"""

	if len(args)>1 and args[1] in subhelp:
		print
		print subhelp[args[1]]
	elif len(args)<=1:
		print
		print "wikiation installer, interactive mode"
		print "help: get help"
		print "quit, exit: quit"
		print "ls <path>: list information on available versions "
		print "install <path>: installs the version specified"
		print "install <path> as <name>: installs the version specified under an alternate name"
		print "uninstall <path>: uninstalls the version specified"
		print "check_isolation <instance name>: shows all changes made to mediawiki or database since it was installed."
		print "update_self: updates the installer and restarts in interactive mode" 
		print "update_tags: manually force an update of the tag cache (do this from time to time, if you're referring to things by tag)"
		print "TODO: Implement help path , for now, see documentation for info on how to specify <path>"
		print
		print "instead of interactive mode, you can also access commands directly from the shell:"
		print "wikiation_installer command [args]..."
	else:
		print 'no detailed help available'



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

def intro():
	"""a nice banner/intro text for interactive mode"""

	print "=== Wikiation installer (v. "+revision()+") ==="
	print
	print "(last known safe version: 48528)"
	print "Interactive mode.",
	print "Automated testing is",
	if settings.run_automated_tests:
		print "enabled."
	else:
		print "disabled."
	print
	print "please type a command and hit enter"
	print "help<enter> for help"
	print "^D, exit<enter> or quit<enter> to quit"
	print

def help_for(something):
	"""If the user types incorrect input, try to gently correct them"""

	print "correct syntax:"
	help(["help",something])

def quit(args):
	"""Quits the program."""
	sys.exit(0)

def clean_target(target):
	"""tidy up a target string, return in canonical form"""

	target=str(target)
	target=target.strip()
	if target.endswith('/'):
		target=target[:-1]

	return target

def pretty_list(mylist,layout_width=None):
	"""format a list into columns that fit the terminal,
	similar to gnu ls output."""

	if not mylist:
		return ""

	if layout_width==None:
		layout_width=getTerminalSize()[0]


	if layout_width:
		#first find the widest item
		max_width=0
		for item in mylist:
			width=len(item)+1
			if width>max_width:
				max_width=width
		
		#now calculate
		columns=max(  layout_width/max_width  ,1)
		column_width=layout_width/columns-1
	
		#and let's go
		text=""
		column=0
		for item in mylist:
			text+=item
			text+=" "*(column_width-len(item)+1)
			column+=1
			if column>=columns:
				text+="\n"
				column=0
	else:
		#naive alternative in case we can't get a clear
		#idea of what terminal we're on.
		text="\n".join(mylist)

	return text


def getTerminalSize():
    """determine the size of the terminal we are running in (where available)"""
    def ioctl_GWINSZ(fd):
        try:
            import fcntl, termios, struct, os
            cr = struct.unpack('hh', fcntl.ioctl(fd, termios.TIOCGWINSZ,
        '1234'))
        except:
            return None
        return cr
    cr = ioctl_GWINSZ(0) or ioctl_GWINSZ(1) or ioctl_GWINSZ(2)
    if not cr:
        try:
            fd = os.open(os.ctermid(), os.O_RDONLY)
            cr = ioctl_GWINSZ(fd)
            os.close(fd)
        except:
            pass
    if not cr:
        try:
            cr = (env['LINES'], env['COLUMNS'])
        except:
            cr = (25, 80)
    return int(cr[1]), int(cr[0])

def isanint(value):
	"""If value can be converted to int, return
	true, else return false"""
	try:
		num=int(value)
	except ValueError:
		return False
	return True

if __name__=="__main__":
	print "some tests for the utils module"
	
	x=range(1000,200000,1512)
	x=[str(i) for i in x]
	print x
	print pretty_list(x)

	print "isanint(0)",isanint(0)
	print "isanint('22')",isanint('22')
	print "isanint('22.foo')",isanint('22.foo')
	print "isanint('foo.22')",isanint('foo.22')
	print "isanint('bar')",isanint('bar')

import sys

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
		print "quit: quit"
		print "ls [item]: list information on available versions "
		print "ls2 [item]: list information on extensions or wikiation components"
		print "install <version>: installs the version specified"
		print "install <version> as <name>: installs the version specified under an alternate name"
		print "install latest ; install latest as <name>: installs the latest version available in svn."
		print "uninstall <version/name>: uninstalls the version specified"
		print "check_isolation <version/name>: shows all changes made to mediawiki or database since it was installed."
		print "update_self: updates the installer and restarts in interactive mode" 
		print "revision [new revision]: query current default revision, or set a new one (will be implemented over time)" 
		print
		print "instead of interactive mode, you can also access commands directly from the shell:"
		print "wikiation_installer command [args]..."
	else:
		print 'no detailed help available'

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



#!/usr/bin/python
import sys
import os.path
import GM_test2

count=0

# Check and name the input parameters
if len(sys.argv) > 5 :
	environment=sys.argv[1]
	systemShort=sys.argv[2]
	tag=sys.argv[3]
	parmRevision=sys.argv[4]
	parmLanguages=sys.argv[5]
else:
	sys.exit("The Environment, the System name, a Revision, a Tag and at least one language code are expected as parameter")

# the installer software and stuff is in this directory
os.chdir ("../../installer")

# read the environment file for the "environment" information
filename=file("../util/environment/Environment") 
for line in filename:
	line=line.strip()
	fileEnvironment,fileTag,fileRevision=line.split()
	if fileEnvironment==environment:
		# Check for default values or existence of the parameters delivered as parameters
		if tag=="default":
			tag=fileTag
		#else:
			#check from installer if tag exists
		if parmRevision=="default":
			revision=fileRevision
		#else:
			#check from installer if parmRevision exists
		# do something when not found

filename.close()

if tag=="-":
	installString="./installer.py install mediawiki revision " + revision + " "
else:
	installString="./installer.py install mediawiki:" + tag + " "

languages = parmLanguages.split()

for item in languages:
	if languages == [ "none" ]:
		system=systemShort
		language="en"
	else:
		language=item
		system=language + "_" + systemShort

	#a system is created for the first language
	count +=1
	if count==1:
		firstSystem=system
		# check if the installation already exists and uninstall it
		if os.path.isdir("../../public_html/revisions/" + system):
			os.system ("./installer.py uninstall " + system)
			print "call function/ program to remove the environment from the installed environments list"
	
		#install MediaWiki
		os.system (installString + " language " + language + " as " + system)
		print "add line to the installed environments list - first iteration"

		# read the environment file for the "Ext_Environment" information
		filename=file("../util/environment/Env_Extensions")
		for line in filename:
			if line[0] != "#":
				line=line.strip()
				if len(line.split()) == 3:
					fileEnvironment,extension,revision=line.split()
					installstring=" install extension:" + extension + " revision " + revision + " in " + system
				else:
					fileEnvironment,extension=line.split()
					installstring=" install extension:" + extension + " in " + system
				if environment == fileEnvironment :
					os.system ("./installer.py" + installstring)
		filename.close()

		print "system_next: " + system
		# run the configuration program for the Envirionment
		if os.path.isdir("../util/environment/" + environment + "/" + environment + ".sh" ):
			os.system ("./../util/environment/" + environment + "/" + environment + ".sh " + system)

		# run the maintenance update (makes sense to run this after an install or update
		os.system ("php ../../public_html/revisions/" + system + "/maintenance/update.php")

		# run the LocalisationUpdate if it is installed
		if os.path.isdir("../util/environment/" + system  + "/extensions/LocalisationUpdate/update.php" ):
			os.system ("php ../../public_html/revisions/" + system  + "/extensions/LocalisationUpdate/update.php")

	# duplicate firstSystem .. this is much quicker
	else:
		os.system ("./installer.py uninstall " + system)
		print "call function/ program to remove the environment from the installed environments list"
		os.system ("./installer.py duplicate " + firstSystem  + " " + system + " language " + language)
		print "add line to the installed environments list"

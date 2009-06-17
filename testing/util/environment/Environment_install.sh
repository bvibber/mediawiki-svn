#!/bin/sh
# (c) Gerard Meijssen - MIT licence

# make sense of the input parameters
Environment=$1
SystemShort=$2
Tag=$3
Revision=$4
Languages=$5

# additional parameters
declare -i Count=0 


# At least five parameters are needed for this program
if [ $# -lt 5 ]
then
	echo The Environment, the System name, a Revision, a Tag and at least one language code are expected as parameter
	exit
fi

#Goto the directory of the installer
cd ../../installer

# retrieve the values in the Environment file
while read line
do
        set -- $line
        if [ $1 == $Environment ]
        then
                FileTag=$2
                FileRevision=$3
        fi

done < ../util/environment/Environment

# replace "default" with values from the Environment file
if [ ${Tag} = "default" ]
then
	Tag=$FileTag
else
	if ./installer.py mediawiki:${Tag} | grep ${Revision} ; then echo "${Tag} found"; else exit; fi
fi
if [ ${Revision} = "default" ]
then
	Revision=${FileRevision}
else
	if ./installer.py revision.mediawiki:${Revision} | grep ${Revision} ; then echo "${Revision} found"; else exit; fi
fi

if [ $Tag == "-" ]
then
	InstallString=" revision ${Revision} "
else
	InstallString="${Tag} "
fi

#When multiple languages have been defined, an installation has to be created for each of them
for Language in $Languages
do
	if [ "$Languages" == "none" ]
	then
		System=${SystemShort}
		Language="en"
	else
		System="${Language}_${SystemShort}"
	fi

	((Count=${Count}+1))

	if [ ${Count} = 1 ]
	then
		System1=${System}

		# uninstall and install the system
		if [ -s ../../public_html/revisions/${System} ]
		then
			./installer.py uninstall mediawiki:${System}
			grep -v ${System} ../util/environment/InstalledEnvSystems > ../util/environment/TMP_InstalledEnvSystems
			mv ../util/environment/TMP_InstalledEnvSystems ../util/environment/InstalledEnvSystems
		fi
		./installer.py install mediawiki:${InstallString} language ${Language} as ${System}
		echo "${Environment} ${System}" >> ../util/environment/InstalledEnvSystems

		# install the extensions mentioned in the Env_Extensions file
		while read line
		do
			set -- $line
			if [ $1 == $Environment ]
			then
			        # check if there are three variables
			        if [ $# -lt 3 ]
		        	then
		                	./installer.py install extension:$2 in $System
		        	else
		                	./installer.py install extension:$2 revision $3 in $System
		        	fi
			fi

		done < ../util/environment/Env_Extensions

		# run the configuration program for the Envirionment
		if [ -s ../util/environment/${Environment}/${Environment}.sh ]
		then
			./../util/environment/${Environment}/${Environment}.sh ${System}
		fi

		# run the maintenance update (makes sense to run this after an install or update
		php ../../public_html/revisions/${System}/maintenance/update.php

		# run the LocalisationUpdate if it is installed
		if [ -s ../../public_html/revisions/${System}/extensions/LocalisationUpdate/update.php ]
		then
			php ../../public_html/revisions/${System}/extensions/LocalisationUpdate/update.php
		fi
	
	else # Count is not 1

		# duplication of a system is much faster
		./installer.py uninstall ${System}
			grep -v ${System} ../util/environment/InstalledEnvSystems > ../util/environment/TMP_InstalledEnvSystems
			mv ../util/environment/TMP_InstalledEnvSystems ../util/environment/InstalledEnvSystems
		./installer.py duplicate ${System1} ${System} language ${Language}
			echo "${Environment} ${System}" >> ../util/environment/InstalledEnvSystems
	fi
done

#!/bin/sh
# (c) Gerard Meijssen - MIT licence

# At least five parameters are needed for this program
if [ $# -lt 1 ]
then
	echo The Environment is a mandatory parameter, the revision is eligible, latest being recognised
exit
fi

Environment=${1}
ParmRevision=${2}

# find the systems that have been installed using an environment
while read line
do
	set -- ${line}
	if [ ${1} == ${Environment} ]
	then
		System=${2}

		# install the extensions mentioned in the Env_Extensions file
		while read EnvExtension
		do
			set -- ${EnvExtension}
			if [ $1 == ${Environment} ]
			then
				Extension=$2
				cd ../../../public_html/revisions/${System}/extensions/${Extension}
				CurrentRevision=`svn info | grep Revision | awk '{print $2}'` 
				# check if there are three variables
					if [ ${#} -lt 3 ] || [ "${ParmRevison}" == "latest" ] 
					then
						svn update
					else
						Revision=$3
						svn update -r${Revision}
					fi

				cd ../../../../../testing/util/environment
			fi

		done < Env_Extensions

	# run the maintenance update (makes sense to run this after an install or update
	cd ../../../public_html/revisions/${System}/maintenance
	php update.php
	cd ../../../../testing/util/environment

	fi
done < InstalledEnvSystems

#!/bin/bash

dir=`dirname "$0"`

if [ -z "$1" ]; then
	echo "USAGE: integrator-shell <collection:language> <args...>"
	exit 1
fi

"$dir/integrator-launch.sh" RunIntegratorScript "$@"
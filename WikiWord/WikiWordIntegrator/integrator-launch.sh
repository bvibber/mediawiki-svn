#!/bin/bash

dir=`dirname "$0"`

if [ -z "$1" ]; then
	echo "USAGE: integrator-launch <class> <collection:language> <args...>"
	exit 1
fi

class="$1"
shift

if [ "${class##de.brightbyte.wikiword.}" == "$class" ]; then
	class="de.brightbyte.wikiword.integrator.$class";
fi

if [ -d "$dir/lib" ]; then
	cp=`find "$dir" -path "$dir/lib/*.jar" | tr '\n' ':'`
elif [ -d "$dir/../WikiWord" ]; then
	cp=`find "$dir/.." -path "$dir/../WikiWord*/target/*.jar" | tr '\n' ':'`
fi

if [ -f "$dir/vm.options" ]; then
	vmopt=`cat "$dir/vm.options"`
fi

wwopt="--config-dir '$dir'"

if [ ! -z "$cp" ]; then
	vmopt="$vmopt -classpath '$cp'"
fi

java=`which java`

echo $java $vmopt $class $wwopt "$@"
$java $vmopt $class $wwopt "$@"
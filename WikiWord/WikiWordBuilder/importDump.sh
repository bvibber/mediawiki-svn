#!/bin/bash

if [ -z "$2" ]; then
		echo "USAGE: "`basename "$0"`" <collection:dataset> <dump-file> [options]"
        exit 1
fi

dir=`dirname "$0"`

if [ -f "$dir/ww.sh" ]; then
	ww="$dir/ww.sh"
elif [ -f "$dir/../WikiWord/ww.sh" ]; then
	ww="$dir/../WikiWord/ww.sh"
elif [ -f "$dir/../WikiWord-0.1/ww.sh" ]; then
	ww="$dir/../WikiWord-0.1/ww.sh"
else
	echo "ww.sh not found!"
	exit 5
fi

name="$1"
shift

f="$1"
shift

if [ -z "${name/:*/}" ]; then
	n=`dirname "$f"`
	n=`basename "$n"`
	name="$n$name"
fi

if [ -z "${name/*:/}" ]; then
	n=`basename "$f"`
	lang=${n/wiki*/}
	name="$name$lang"
fi

echo
echo "=== IMPORTING DUMP $f TO $name ================================================================================="
echo

        echo
        echo "=========================================================================================================="
        echo
        "$ww" builder.ImportConcepts "$name" "$f" "$@" || exit $?

        echo
        echo "=========================================================================================================="
        echo
        "$ww" builder.BuildStatistics  "$name" "$@" || exit $?

        echo
        echo "=========================================================================================================="
        echo
        "$ww" builder.BuildConceptInfo "$name" "$@" || exit $?

echo
echo "=== IMPORT  DONE ============================================================================================="
echo

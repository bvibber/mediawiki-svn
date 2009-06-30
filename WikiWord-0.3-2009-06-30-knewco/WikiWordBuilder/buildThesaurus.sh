#!/bin/bash

if [ -z "$1" ]; then
		echo "USAGE: "`basename "$0"`" <collection:dataset> [options]"
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

echo
echo "=== BUILDING THESAURUS $name =========================================================================="
echo

echo
echo "=========================================================================================================="
echo
"$ww" builder.BuildThesaurus "$name" "$@" || exit $?

echo
echo "=========================================================================================================="
echo
"$ww" builder.BuildStatistics  "$name" "$@" || exit $?

echo
echo "=========================================================================================================="
echo
"$ww" builder.BuildConceptInfo "$name" "$@" || exit $?

echo
echo "=== THESAURUS DONE ============================================================================================="
echo

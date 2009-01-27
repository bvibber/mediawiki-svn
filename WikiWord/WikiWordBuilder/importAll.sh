#!/bin/bash

if [ -z "$1" ]; then
		echo "USAGE: "`basename "$0"`" <collection> <dump-dir> [options]"
        exit 1
fi

dir=`dirname "$0"`

coll="$1"
shift

dumpdir="$1"
shift

if [ ! -z "${coll/*:*/}" ]; then
	coll="$coll:"
fi

if [ -z "${coll/:*/}" ]; then
    n=`basename "$dumpdir"`
	coll="$n:"
fi

echo
echo "=== IMPORTING ALL FROM $dumpdir TO $coll  ======================================================"
echo

for f in "$dumpdir"/*.xml*; do
    "$dir/importDump.sh" "$coll" "$f" "$@"
done

"$dir/buildThesaurus.sh" "$coll""thesaurus" "$@"

echo
echo "=== ALL DONE ============================================================================================="
echo


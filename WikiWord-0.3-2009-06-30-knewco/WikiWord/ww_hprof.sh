#!/bin/sh
dir=`dirname "$0"`

WIKIWORD_JAVA_OPTIONS="-agentlib:hprof=cpu=samples,depth=8" "$dir"/ww.sh "$@"

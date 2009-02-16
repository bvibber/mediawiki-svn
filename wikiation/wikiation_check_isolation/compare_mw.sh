#!/bin/sh
#compare local mediawiki installation with our own copy

# This software, copyright (C) 2008-2009 by Wikiation.
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.



if [[ $0 == '/'* ]]; then 
	ScriptLocation="`dirname $0`" 
else 
	ScriptLocation="`pwd`"/"`dirname $0`" 
fi

source $ScriptLocation/settings
data=$ScriptLocation/data
current=$ScriptLocation/current

mask=$1
detail='-q'

if [[ ( $mask = "--detail" ) || ( $mask = "-d" )  ]]; then
	mask=$2
	detail='-N'
fi

mkdir -p $current

if [[ -n $mask ]] ; then
	diff -r $detail -x $mask $mediawiki_location $data/$mediawiki >$current/mwdiff_report
else
	diff -r $detail $mediawiki_location $data/$mediawiki >$current/mwdiff_report
fi

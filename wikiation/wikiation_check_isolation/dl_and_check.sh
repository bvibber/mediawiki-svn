#!/bin/sh
#load data on a particular revision
# This software, copyright (C) 2008-2009 by Wikiation.
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.



if [[ $0 == '/'* ]]; then 
	ScriptLocation="`dirname $0`" 
else 
	ScriptLocation="`pwd`"/"`dirname $0`" 
fi

target=$1

if test -z $target; then
	echo "syntax: wikiation_check_isolation <revision name>"
	exit 0
fi

target_dir="/var/www/revisions/$target"
if test ! -e $target_dir; then 
	echo "$target not found"
	exit 0
fi


p1=$2
p2=$3

export mediawiki=$target
export mediawiki_location=$target_dir
	$ScriptLocation/load.sh $target &&
	echo "Running checks:" &&
	$ScriptLocation/compare_all.sh $p1 $p2

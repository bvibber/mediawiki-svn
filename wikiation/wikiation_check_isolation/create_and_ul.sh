#!/bin/sh
#save data on a particular revision

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
target_dir="/var/www/revisions/$target";
if test ! -e $target_dir; then 
	echo "$target not found"
	exit 0
fi

echo "- Collecting data."
export mediawiki=$target
export mediawiki_location=$target_dir
	$ScriptLocation/create_data.sh &&
	$ScriptLocation/save.sh
	

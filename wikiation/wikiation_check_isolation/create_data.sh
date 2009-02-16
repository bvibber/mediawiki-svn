#!/bin/sh
# This software, copyright (C) 2008-2009 by Wikiation.
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.



if [[ $0 == '/'* ]]; then 
	ScriptLocation="`dirname $0`" 
else 
	ScriptLocation="`pwd`"/"`dirname $0`" 
fi

data=$ScriptLocation/data
source $ScriptLocation/settings

mkdir -p $data
cp -r $mediawiki_location $data
$ScriptLocation/dump_desc.sh $data/original_db

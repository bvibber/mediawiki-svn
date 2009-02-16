#!/bin/sh
# compare database description directories created by
# dump_desc.sh, and write a report to current/

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
current=$ScriptLocation/current

mkdir -p $current

$ScriptLocation/dump_desc.sh $current/current_db

diff -rN $data/original_db $current/current_db > $current/dbdiff_report

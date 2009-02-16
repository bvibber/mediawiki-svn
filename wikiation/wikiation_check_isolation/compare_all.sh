#!/bin/sh

# This software, copyright (C) 2008-2009 by Wikiation.
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.



RED='\e[0;31m'
GREEN='\e[0;32m'
NC='\e[0m' # No Color

if [[ $0 == '/'* ]]; then 
	ScriptLocation="`dirname $0`" 
else 
	ScriptLocation="`pwd`"/"`dirname $0`" 
fi

data=$ScriptLocation/data
current=$ScriptLocation/current

$ScriptLocation/compare_mw.sh $1 $2
$ScriptLocation/compare_db.sh

[ ! -s $current/mwdiff_report ] &&
	 echo -e "$GREEN * $NC After installation no difference found with the original MediaWiki core."

[ ! -s $current/dbdiff_report ] && 
	echo -e "$GREEN * $NC After installation no difference found with the structure of the original MediaWiki database."

[ -s $current/mwdiff_report ] && { 
	echo
	echo -e "$RED * $NC MediaWiki differences: "
	cat $current/mwdiff_report
}


[ -s $current/dbdiff_report ] && { 
	echo
	echo -e "$RED * $NC MediaWiki Database differences: " 
	cat $current/dbdiff_report
}

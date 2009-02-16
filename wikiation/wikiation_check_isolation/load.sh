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

source $ScriptLocation/settings
revisions=$ScriptLocation/revisions
tarball=${mediawiki}.tar.gz
echo "- Obtaining comparison data"
cp $revisions/$tarball $ScriptLocation
rm -rf $ScriptLocation/data
cd $ScriptLocation
tar xf $ScriptLocation/$tarball

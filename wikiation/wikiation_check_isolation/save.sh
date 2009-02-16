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

source ${ScriptLocation}/settings

echo "-- Storing data."
cd $ScriptLocation
tarball=${mediawiki}.tar.gz
tar czf $tarball data
revisions=$ScriptLocation/revisions

mkdir -p $revisions
cp $tarball $revisions

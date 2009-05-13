#!/bin/sh

#repository="$EXTENSIONS_SVN/$NAME"

cd $DESTINATION_DIR

svn checkout http://mediawiki-google-geocoder.googlecode.com/svn/trunk/ GoogleGeocoder

#if test -n "$REVISION"; then
#	svn checkout -r $REVISION $repository
#else
#	svn checkout $repository
#fi

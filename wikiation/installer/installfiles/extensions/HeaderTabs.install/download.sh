#!/bin/sh

#repository="$EXTENSIONS_SVN/$NAME"

cd $DESTINATION_DIR

svn checkout http://mediawiki-header-tabs.googlecode.com/svn/trunk/ HeaderTabs

#if test -n "$REVISION"; then
#	svn checkout -r $REVISION $repository
#else
#	svn checkout $repository
#fi

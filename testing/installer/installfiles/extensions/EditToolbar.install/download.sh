#!/bin/sh

repository="$EXTENSIONS_SVN/UsabilityInitiative/$NAME"

cd $DESTINATION_DIR/UsabilityInitiative
if test -n "$REVISION"; then
	svn checkout -r $REVISION $repository
else
	svn checkout $repository
fi

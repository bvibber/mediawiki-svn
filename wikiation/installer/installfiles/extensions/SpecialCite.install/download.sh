#!/bin/sh

repository="$EXTENSIONS_SVN/Cite"

cd $DESTINATION_DIR
if test -n "$REVISION"; then
	svn checkout -r $REVISION $repository SpecialCite/
else
	svn checkout $repository SpecialCite/
fi

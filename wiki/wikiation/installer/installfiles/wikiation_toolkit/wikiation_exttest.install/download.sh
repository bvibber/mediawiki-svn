#!/bin/sh

repository="http://6.wikiation.nl/svn/wikiation_exttest"

cd $DESTINATION_DIR
if test -n "$REVISION"; then
	svn checkout -r $REVISION $repository
else
	svn checkout $repository
fi

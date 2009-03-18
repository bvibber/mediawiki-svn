#!/bin/sh

cd $DESTINATION_DIR/$NAME
if test -n "$REVISION"; then
	svn update -r $REVISION
else
	svn update
fi

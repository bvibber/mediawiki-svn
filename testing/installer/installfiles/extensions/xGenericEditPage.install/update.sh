#!/bin/sh

cd $DESTINATION_DIR/uniwiki/GenericEditPage
if test -n "$REVISION"; then
	svn update -r $REVISION
else
	svn update
fi

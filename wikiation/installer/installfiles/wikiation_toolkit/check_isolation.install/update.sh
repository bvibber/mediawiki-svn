#!/bin/sh

cd $DESTINATION_DIR/check_isolation
if test -n "$REVISION"; then
	svn update -r $REVISION
else
	svn update
fi

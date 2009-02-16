#!/bin/sh

cd $DESTINATION_DIR/wikiation_check_isolation
if test -n "$REVISION"; then
	svn update -r $REVISION
else
	svn update
fi

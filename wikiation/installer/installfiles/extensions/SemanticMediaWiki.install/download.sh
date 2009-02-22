#!/bin/sh

repository="$EXTENSIONS_SVN/$NAME"

cd $DESTINATION_DIR
if test -n "$REVISION"; then
	svn checkout -r $REVISION $repository
else
	svn checkout $repository
fi
php $NAME/maintenance/SMW_setup.php

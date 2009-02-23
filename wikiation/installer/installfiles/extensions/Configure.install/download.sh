#!/bin/sh

repository="$EXTENSIONS_SVN/$NAME"

cd $DESTINATION_DIR
if test -n "$REVISION"; then
	svn checkout -r $REVISION $repository
else
	svn checkout $repository
fi

# Make the serialized directory world readable so it can be read by the web server.
chmod 777 $DESTINATION_DIR/../serialized/

#!/bin/sh

repository="$EXTENSIONS_SVN/uniwiki/GenericEditPage"

if [ -d "$DESTINATION_DIR/uniwiki" ]; then
        echo "Directory exists"
else 
        mkdir "$DESTINATION_DIR/uniwiki"
        echo "Directory is created"
fi


cd $DESTINATION_DIR/uniwiki
if test -n "$REVISION"; then
	svn checkout -r $REVISION $repository
else
	svn checkout $repository
fi

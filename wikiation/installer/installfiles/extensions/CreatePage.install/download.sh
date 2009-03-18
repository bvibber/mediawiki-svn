#!/bin/sh

repository="$EXTENSIONS_SVN/uniwiki/$NAME"

if [ -d "$DESTINATION_DIR/uniwiki" ]; then
        echo "the directory exists"
else 
        mkdir "$DESTINATION_DIR/uniwiki"
        echo "the directory is created"
fi


cd $DESTINATION_DIR/uniwiki
if test -n "$REVISION"; then
	svn checkout -r $REVISION $repository
else
	svn checkout $repository
fi

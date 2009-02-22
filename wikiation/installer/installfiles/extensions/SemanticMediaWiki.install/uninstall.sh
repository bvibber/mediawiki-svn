#!/bin/sh

php $DESTINATION_DIR/$NAME/maintenance/SMW_setup.php --delete
cd $DESTINATION_DIR
rm -rf $NAME

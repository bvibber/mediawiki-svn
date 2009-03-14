#!/bin/sh

$MYSQL_COMMAND $DATABASE_NAME < $INSTALL_DIR/lqt-uninstall.sql

cd $DESTINATION_DIR
rm -rf $NAME

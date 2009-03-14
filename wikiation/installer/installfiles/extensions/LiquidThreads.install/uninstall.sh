#!/bin/sh

$MYSQL_COMMAND $DATABASE_NAME < lqt-uninstall.sql

cd $DESTINATION_DIR
rm -rf $NAME

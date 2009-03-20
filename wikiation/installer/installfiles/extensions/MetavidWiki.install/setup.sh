#!/bin/sh

$MYSQL_COMMAND $DATABASE_NAME < $DESTINATION_DIR/$NAME/maintenance/mv_tables.sql


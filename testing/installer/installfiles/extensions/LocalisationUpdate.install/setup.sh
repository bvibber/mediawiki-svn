#!/bin/sh

$MYSQL_COMMAND $DATABASE_NAME < $DESTINATION_DIR/$NAME/schema.sql

php $DESTINATION_DIR/../maintenance/update.php

#!/bin/sh
# create a directory describing wiki database (for easy diff-ing)

# This software, copyright (C) 2008-2009 by Wikiation.
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.



set -u
set -e

target_dir=$1

rm -rf $target_dir
mkdir -p $target_dir

echo "show tables" | mysql $mediawiki | grep -v Tables_in_$mediawiki > $target_dir/tables

exec < $target_dir/tables
while read table
do
	echo "desc $table" | mysql $mediawiki > $target_dir/$table
done

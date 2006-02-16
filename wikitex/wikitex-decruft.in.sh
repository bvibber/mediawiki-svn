#!/usr/bin/env bash
#
# wikitex-decruft.sh: cleans the work folder from files
# which don't appear to be in use (checks them
# against objectcache in the database).
#
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-6  Peter Danenberg
#
#      WikiTeX is licensed under the Artistic License 2.0;  to
# view a copy of this license, see COPYING or visit:
#
#      http://dev.perl.org/perl6/rfc/346.html
#

declare -r DATABASE='%DATABASE%'
declare -r USERNAME='%DBUSER%'
declare -r PASSWORD='%DBPASS%'

declare -r WORK='tmp'
declare username
declare password

cd "$(dirname "${0}")/${WORK}"

declare -ar HASHES=($(find . -maxdepth 1 ! -name '*\.*' -printf '%f '));

[[ -z "${USERNAME}" ]] || username="-u ${USERNAME}"
[[ -z "${PASSWORD}" ]] || password="-p${PASSWORD}"

for (( i = ${#HASHES[*]}; --i >= 0; )); do
    OUT=$(mysql ${username} ${password} -D "${DATABASE}" -e "SELECT DISTINCT NULL FROM \`objectcache\` WHERE \`value\` LIKE '%${HASHES[${i}]}%' LIMIT 1;";)
    [[ ${?} -eq 0 && -z "${OUT}" ]] && rm -fr "${HASHES[${i}]}"*
done

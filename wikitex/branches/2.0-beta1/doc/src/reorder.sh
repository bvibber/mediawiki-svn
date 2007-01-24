#!/usr/bin/env bash
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
#
# Reorders groff output from cover, body, toc to cover, toc, body.
# Stream-based alternative, with separate functions for cover, toc and body
# could be done in one pass with multiple file descriptors.  Also: a slightly
# more intelligent solution using csplit with offsets.
declare -r USAGE="Usage: reorder.sh DOC\nOrder groff-DOC in cover, toc, body."
declare -r DOC="${1}"
declare -ri FAIL=1
# Heuristic: lines-per-page in groff-ms-ascii.
# Dynamic alternative: parse wc output from a blank
# page run in groff.
declare -ri LPP=66

(( ${#} )) || {
    echo -e "${USAGE}" 1>&2
    exit $FAIL
}

# Output title page
head -n $LPP "${DOC}"

# Output contents
tail -n $LPP "${DOC}"

# Output body
tail -n +$LPP "${DOC}" | head -n -$LPP

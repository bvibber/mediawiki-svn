#!/usr/bin/env bash
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-6  Peter Danenberg
#
#      WikiTeX is licensed under the Artistic License 2.0;  to
# view a copy of this license, see COPYING or visit:
#
#      http://dev.perl.org/perl6/rfc/346.html
#
# wikitex.sh: shell interface to wikitex.php
# Assumes that $PWD is the base wiki dir.
# Usage: FILE MODULE OUTPATH

declare -ri ARGS=3
declare -ri E_ARGS=2
declare -ri E_FAIL=1
declare -ri E_SUC=0
declare -r HASH="${1}"
declare -r MOD="${2}"
declare -r OUT="${3}"
declare -r EXT='.png'
declare -r CHE='.cache'
declare -r MID='.midi'
declare -r SUDO='wikitex'
declare -r ERR='<span class="errwikitex">WikiTeX: %s reported a failure, namely:</span><pre>Error code: %d\n%s</pre>\n'
declare -ra LIMIT=(\
    0 \                         # The maximum size of core files created
    unlimited \                 # The maximum size of a process's data segment
    unlimited \                 # The maximum size of files created by the shell
    1024 \                      # Pending signals
    32 \                        # The maximum size that may be locked into memory
    unlimited \                 # The maximum resident set size
    1024 \                      # The maximum number of open file descriptors (most systems
    \                           # do not allow this value to be set)
    8 \                         # The pipe size in 512-byte blocks (this may not be set)
    819200 \                    # POSIX message queues
    10240 \                     # The maximum stack size
    30 \                        # The maximum amount of cpu time in seconds
    8185 \                      # The  maximum  number  of  processes available to a single
    \                           # user
    unlimited \                 # The maximum amount of virtual  memory  available  to  the
    \                           # shell
    unlimited \                 # File locks
)

function wt_error() {
    printf "${ERR}" "${1}" ${?} "${2}"
    exit ${3}
}

# Preliminaries
(( ${#} >= ${ARGS} )) || wt_error 'wikitex.sh' 'usage: wikitex.sh HASH MODULE OUTPATH' $E_ARGS

# $PWD is initially the wiki-base.
cd "$(dirname "${0}")/tmp" || wt_error 'wikitex.sh' 'unable to change to cache directory' $E_FAIL

# Check cache
[ -r "${HASH}${CHE}" ] && { cat "${HASH}${CHE}"; exit $E_SUC; }

# Requirement: scribe tmp, read hash
[ -w '.' ] || wt_error 'wikitex.sh' "Can't scribe <code>${PWD}</code>; ownership and permissions should allow us to do so, precious." $E_FAIL
[ -r "${HASH}" ] || wt_error 'wikitex.sh' "Can't hash, baby; ownership and permissions should allow us to perceive <code>${PWD}</code>." $E_FAIL

# Perform self-limitation
ulimit -c ${LIMIT[0]} -d ${LIMIT[1]} -f ${LIMIT[2]} -n ${LIMIT[6]} -s ${LIMIT[9]} -t ${LIMIT[10]} -v ${LIMIT[12]} || wt_error 'wikitex.sh' 'ulimit failed.' $E_FAIL

# Move into work directory
cd ${HASH} || wt_error 'wikitex.sh' 'unable to change into work directory.' $E_FAIL

# Extract useful files from work directory
function wt_out() {
    wt_exec "mv ${@} ../"
}

# Output image
function wt_img() {
    STR="$STR"$(printf "<img src=\"%s\" alt=\"${MOD}\" />" "${1}")
}

# Output link
function wt_anch() {
    STR=$(printf '<a href="%s">%s</a>' "${OUT}${HASH}" "${STR}")
}

# Convert to final image
function wt_dvipng() {
    wt_sudo "dvipng -gamma 1.5 -T tight ${HASH}"
}    

# Trim whitespace
function wt_trim() {
    wt_sudo "mogrify -trim ${@}"
}

# Generic execution, which allows for error trapping
function wt_exec() {
    PUT=$(${@} 2>&1) || wt_error "${@%%\ *}" "${PUT}" $E_FAIL
}

# Generic sudoing; use exec to protect product from being over-written
# by wt_sudo actions.
function wt_sudo() {
    PUT=$(sudo -u ${SUDO} ${@} 2>&1) || wt_error "${@%%\ *}" "${PUT}" $E_FAIL
}

# Catch-all renderer
function wt_generic() {
    wt_sudo "latex --interaction=nonstopmode ${HASH}"
    wt_dvipng
    for i in ${HASH}*${EXT}; do wt_img "${OUT}${i}"; done
    wt_anch
}

function go() {
    wt_sudo "sgf2dg -twoColumn ${HASH}"
    wt_sudo "tex --interaction=nonstopmode ${HASH}"
    wt_dvipng
    for i in ${HASH}*${EXT}; do wt_sudo "mogrify -crop +0-24! ${i}"; wt_trim "${i}"; wt_img "${OUT}${i}"; done
    wt_anch
}

function graph() {
    wt_sudo "dot -Tpng -o ${HASH}${EXT} ${HASH}"
    wt_img "${OUT}${HASH}${EXT}"
    wt_anch
}

function music() {
    wt_sudo "lilypond -s -f png -b eps ${HASH}"
    for i in ${HASH}*${EXT}; do wt_trim "${i}"; wt_img "${OUT}${i}"; done
    wt_anch

    # Sourced files may not produce midi
    if [ -r "${HASH}${MID}" ]; then
        STR="$STR"$(printf '<a href="%s">[listen]</a>' "${OUT}${HASH}${MID}")
        wt_out "${HASH}${MID}"
    fi
}

function plot() {
    sed -i -e "s/\%OUTPUT\%/${HASH}${EXT}/" "${HASH}"
    wt_sudo "gnuplot ${HASH}"
    wt_trim "${HASH}${EXT}"
    wt_img "${OUT}${HASH}${EXT}"
    wt_anch
}

function svg() {
    wt_sudo "convert -size 640x480 ${HASH} ${HASH}${EXT}"
    wt_trim "${HASH}${EXT}"
    wt_img "${OUT}${HASH}${EXT}"
    wt_anch
}

# Check for module-specific functions; otherwise resort to generic.
if [[ $(type -t "${MOD}") == 'function' ]]; then
    "${MOD}" "${HASH}" "${OUT}"
else
    wt_generic "${HASH}" "${OUT}"
fi

# Extract images (additional files per class supra)
wt_out ${HASH}*${EXT}

# Move into cache directory; remove work files; extract source
{ cd ..; rm -fr ${HASH}; cat > "${HASH}"; } < "${HASH}" || wt_error 'wikitex.sh' 'unable to remove work directory or add cache source' $E_FAIL

# Cache
echo "${STR}" > "${HASH}${CHE}"

# Produce result
echo "${STR}"

# Adieu
exit ${E_SUC}

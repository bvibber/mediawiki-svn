#!/bin/bash
# 
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004  Peter Danenberg
# 
#      WikiTeX  is  free  software;  you  can  redistribute it
# and/or modify it under the terms of the GNU  General  Public
# License as published by the Free Software Foundation; either
# version 2 of the License, or (at your option) any later ver-
# sion.
# 
# wikitex.sh: shell interface to wikitex.php
# Usage: FILE MODULE OUTPATH
#
# FILE corresponds an md5 of the given contents; and MODULE,
# a render strategy.
#

N_ARG=3
E_ALL=1
E_NONE=0
S_ERR='error'
S_EXT='gif'
S_MID='midi'
S_TMP='/tmp'
S_T_CLA='class='
S_T_FIL='src='
S_T_MID='midi='
S_CLA='rend'			# default class
S_AUX=''			# module specific parameters
S_REL=''			# outpath

# Error customization.
E_RENDER='WikiTeX: no output.'
E_MOD='WikiTeX: unknown module.'

if (( $# < $N_ARG )); then
    echo "Usage: `basename $0` FILE MODULE OUTPATH"
    exit $E_ALL
fi

S_REL=$3
cd "`dirname $0`$S_TMP"
strInc="../wikitex_rend.%s.inc.tex"

nCleanUp() {
    find . -type f -name "$1*" ! -regex ".*\(\.$S_EXT\|\.$S_MID\)" -exec rm {} \; # preliminary files
}

nLatex() {
    if [[ ! -s "$1.$S_EXT" ]]; then
	case "$2" in
	    'music' ) { lilypond "$1" && psselect 1 "$1".ps "$1"_1.ps && mv "$1"_1.ps "$1".ps && mogrify -trim "$1".ps && convert -transparent "#ffffff" "$1".ps "$1.$S_EXT"; } &> /dev/null;;
	    'go' ) { sgf2tex "$1" -break 0 && tex "$1" && dvi2bitmap --output-type=gif --magnification=2 --scale=6 --font-mode=nechi --resolution=360 -h 360 -w 360 --process=blur,crop,transparent --output="$1".gif "$1".dvi; } &> /dev/null;;
	    * )	{ latex "$1" --interaction=batchmode && dvi2bitmap --output-type="$S_EXT" --magnification=2 --scale=6 --font-mode=nechi --resolution=360 --process=blur,crop,transparent --output="$1.$S_EXT" "$1".dvi; } &> /dev/null;;
	esac;
    fi

    if [[ $? > 0 ]]; then nErr "$E_RENDER" "$1"; fi

    case "$2" in
	'music' ) S_CLA="music"; S_AUX="$S_T_MID$S_REL$1.$S_MID";;
    esac;

    nCleanUp "$1"
    echo "$S_T_FIL$S_REL$1.$S_EXT $S_T_CLA$S_CLA $S_AUX"
    exit
}

# Usage: ERROR_MESSAGE CLEANUP_HASH
nErr() {
    local strErr=$(< ${strInc/'\%s'/"$S_ERR"})
    strErr=${strErr/'\%value\%'/"$1"}
    local strFile=`echo "$strErr" | openssl md5`
    
    echo "$strErr" >"$strFile"
    nCleanUp "$2"

    { latex "$strFile" --interaction=batchmode && "$D_DVI"dvi2bitmap --output-type="$S_EXT" --magnification=2 --scale=6 --font-mode=nechi --resolution=360 --process=blur,crop,transparent --output="$strFile.$S_EXT" "$strFile".dvi; } &> /dev/null
    nCleanUp "$strFile"
    echo "$S_T_FIL$S_REL$strFile.$S_EXT $S_T_CLA$S_CLA"
    exit
}

nLatex "$1" "$2"

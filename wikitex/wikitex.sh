#!/bin/bash
# Usage: FILE MODULE
#
# File name is the md5 of the given contents; module,
# yet math, chess, music: corresponds to a given
# render strategy.
#
# See wikitex.inc.sh for file locations and error
# messages.

source "`dirname $0`/wikitex.inc.sh"

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
S_CLA='rend'	# default class
S_AUX=''		# module specific parameters
S_REL=''		# outpath

if (( $# < $N_ARG )); then
	echo "Usage: `basename $0` FILE MODULE OUTPATH"
	exit $E_ALL
fi

S_REL=$3
cd "`dirname $0`$S_TMP"
strInc="../wikitex_rend.%s.inc.tex"

nCleanUp() {
	find . -type f -atime +5 -exec rm {} \;										# anything not accessed in last 5 days
	find . -type f -name "$1*" ! -regex ".*\(\.$S_EXT\|\.$S_MID\)" -exec rm {} \;	# preliminary files
}

nLatex() {
	if [[ ! -s "$1.$S_EXT" ]]; then
		case "$2" in
			'error' | 'math' | 'chess' | 'chem' | 'tipa' | 'ling' | 'greek' )	{ "$D_LAT"latex "$1" --interaction=batchmode && "$D_DVI"dvi2bitmap --output-type="$S_EXT" --magnification=2 --scale=6 --font-mode=nechi --resolution=360 --process=blur,crop,transparent --output="$1.$S_EXT" "$1".dvi; } &> /dev/null;;
			'music' )															{ "$D_LIL"lilypond "$1" && "$D_MOG"mogrify -trim "$1".ps && "$D_CON"convert -transparent "#ffffff" "$1".ps "$1.$S_EXT"; } &> /dev/null;;
			* )														nErr "$E_MOD" "$1";;
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

nErr() {
# Usage: ERROR_MESSAGE CLEANUP_HASH
	local strErr=$(< ${strInc/'\%s'/"$S_ERR"})
	strErr=${strErr/'\%value\%'/"$1"}
	local strFile=`echo "$strErr" | openssl md5`
	
	echo "$strErr" >"$strFile"
	nCleanUp "$2"

	{ "$D_LAT"latex "$strFile" --interaction=batchmode && "$D_DVI"dvi2bitmap --output-type="$S_EXT" --magnification=2 --scale=6 --font-mode=nechi --resolution=360 --process=blur,crop,transparent --output="$strFile.$S_EXT" "$strFile".dvi; } &> /dev/null
	nCleanUp "$strFile"
	echo "$S_T_FIL$S_REL$strFile.$S_EXT $S_T_CLA$S_CLA"
	exit
}

nLatex "$1" "$2"
#!/usr/bin/env bash
#
# wikitex-audit.sh: locates infelicities in
# WikiTeX's installation.
#
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-6  Peter Danenberg
#
#      WikiTeX is licensed under the Artistic License 2.0;  to
# view a copy of this license, see COPYING or visit:
#
#      http://dev.perl.org/perl6/rfc/346.html
#

declare -r LATEX=%LATEX%
declare -r TEXMF=$(kpsewhich -expand-var '$TEXMFMAIN')/web2c/texmf.cnf
declare -r DVIPNG=%DVIPNG%
declare -r MOGRIFY=%MOGRIFY%
declare -r CONVERT=%CONVERT%
declare -r LILYPOND=%LILYPOND%
declare -r GNUPLOT=%GNUPLOT%
declare -r DOT=%DOT%
declare -r SGF2DG=%SGF2DG%
declare -r TEX=%TEX%

declare -r PARTITION=%PARTITION%

declare -r DECRUFT=%DECRUFT%
declare -r DATABASE=%DATABASE%
declare -r DBUSER=%DBUSER%
declare -r DBPASS=%DBPASS%
declare -r MAILTO=%MAILTO%

declare -r APACHE=%APACHE%
declare -r WIKITEX=%WIKITEX%
declare -r HOST=%HOST%

declare -ri SUCCESS=0
declare -ri FAIL=1
declare -ri ABORT=2

declare -ri WARN=0
declare -ri FATAL=1

declare -ra OUTCOMES=('success' 'FAILURE')

declare -ra TESTS=('root' 'user' 'nologin' 'usrquota' 'quotaed' 'blocks' 'files' 'crontab' 'decruft' 'latex' 'shell_escape' 'openout_any' 'openin_any' 'latex\ execute' 'latex\ read' 'latex\ write' 'dvipng' 'mogrify' 'convert' 'lilypond' 'gnuplot' 'gnuplot\ execute' 'dot' 'sgf2dg' 'tex')

declare -ra DICTA=(
    "You\ must\ be\ root\ to\ continue."
    "User\ ${WIKITEX}\ existeth\ nought."
    "User\ ${WIKITEX}\ does\ not\ have\ /sbin/nologin\ for\ a\ shell."
    "usrquota\ does\ not\ appear\ in\ /etc/fstab\ for\ ${PARTITION}."
    "User\ ${WIKITEX}\ has\ not\ been\ quotaed."
    "Blocks\ have\ not\ been\ quotaed\ on\ ${PARTITION}\ for\ ${WIKITEX}."
    "Files\ have\ not\ been\ quotaed\ on\ ${PARTITION}\ for\ ${WIKITEX}."
    "There\ is\ no\ crontab\ for\ Apache\ user\ ${APACHE}."
    "The\ crontab\ for\ Apache\ user\ ${APACHE}\ does\ not\ invoke\ ${DECRUFT}."
    "${APACHE}\ cannot\ execute\ latex\ as\ ${WIKITEX}."
    "shell_escape\ must\ be\ set\ to\ f\ in\ texmf.cnf"
    "openout_any\ must\ be\ set\ to\ p\ in\ texmf.cnf"
    "openin_any\ must\ be\ set\ to\ p\ in\ texmf.cnf"
    "LaTeX\ appears\ to\ be\ able\ to\ execute\ scripts."    
    "LaTeX\ appears\ to\ be\ able\ to\ read\ files\ in\ parent\ directory."
    "LaTeX\ appears\ to\ be\ able\ to\ write\ files\ in\ parent\ directory."
    "${APACHE}\ cannot\ execute\ dvipng\ as\ ${WIKITEX}."
    "${APACHE}\ cannot\ execute\ mogrify\ as\ ${WIKITEX}."
    "${APACHE}\ cannot\ execute\ convert\ as\ ${WIKITEX}."
    "${APACHE}\ cannot\ execute\ lilypond\ as\ ${WIKITEX}."
    "${APACHE}\ cannot\ execute\ gnuplot\ as\ ${WIKITEX}."
    "Gnuplot\ appears\ to\ be\ able\ to\ execute\ commands."
    "${APACHE}\ cannot\ execute\ dot\ as\ ${WIKITEX}."
    "${APACHE}\ cannot\ execute\ sgf2dg\ as\ ${WIKITEX}."
    "${APACHE}\ cannot\ execute\ tex\ as\ ${WIKITEX}."
)

declare -ar TERMS=(
    'Finished\ successfully.'
    'Finished\ with\ failures.'
    'Aborted\ on\ fatal\ error.'
)

declare -i status=${SUCCESS}
declare -i test=0

echo -e "outcome\ttest\n-------\t----"

function au_run() {
#    echo ${@}
    "${@}" &> /dev/null || {
        echo -e "${OUTCOMES[FAIL]}\t${TESTS[test]}: ${DICTA[test]}"
        return ${FAIL}
    }
    echo -e "${OUTCOMES[SUCCESS]}\t${TESTS[test]}"
    return ${SUCCESS}
}

abort() {
    echo "${TERMS[${ABORT}]}"
    exit ${ABORT}
}

au_run [ $(id -u) == 0 ] || abort && (( test++ ))

au_run id -u "${WIKITEX}" || abort && (( test++ ))

au_run awk -F : -- "/${WIKITEX}/ { if (\$7 !~ /nologin/) exit ${FAIL}; }" /etc/passwd || abort && (( test++ ))

au_run awk -- "\$1 ~ \"${PARTITION}\" { if (\$4 !~ /usrquota/) exit ${FAIL} }" /etc/fstab || abort && (( test++ ))

au_run quota -qu "${WIKITEX}" || abort && (( test++ ))

quota -vu "${WIKITEX}" | au_run awk -- "\$1 ~ \"${PARTITION}\" { if (\$4 == 0) exit ${FAIL} }" || abort && (( test++ ))

quota -vu "${WIKITEX}" | au_run awk -- "\$1 ~ \"${PARTITION}\" { if (\$7 == 0) exit ${FAIL} }" || abort && (( test++ ))

au_run crontab -lu "${APACHE}" || abort && (( test++ ))

crontab -lu "${APACHE}" | au_run grep "${DECRUFT}" || status=${FAIL} && (( test++ ))

au_run sudo -u "${APACHE}" sudo -u "${WIKITEX}" latex -v || abort && (( test++ ))

au_run grep '^shell_escape.*f$' "${TEXMF}" || abort && (( test++ ))

au_run grep '^openout_any.*p$' "${TEXMF}" || abort && (( test++ ))

au_run grep '^openin_any.*p$' "${TEXMF}" || abort && (( test++ ))

latex --interaction=nonstopmode 'wikitex-audit-shell.tex' | au_run grep -qv 'write18.*enabled' || abort && (( test++ ))

latex --interaction=nonstopmode 'wikitex-audit-read.tex' &> /dev/null
au_run [ ${?} -ne 0 ] || abort && (( test++ ))

latex --interaction=nonstopmode 'wikitex-audit-write.tex' &> /dev/null
au_run [ ${?} -ne 0 ] || abort && (( test++ ))

au_run sudo -u "${APACHE}" sudo -u "${WIKITEX}" dvipng -help || abort && (( test++ ))

au_run sudo -u "${APACHE}" sudo -u "${WIKITEX}" mogrify -help || abort && (( test++ ))

au_run sudo -u "${APACHE}" sudo -u "${WIKITEX}" convert -v || abort && (( test++ ))

au_run sudo -u "${APACHE}" sudo -u "${WIKITEX}" lilypond -v || status=${FAIL} && (( test++ ))

au_run sudo -u "${APACHE}" sudo -u "${WIKITEX}" gnuplot -V || status=${FAIL} && (( test++ )) && {
    echo '!cat' | sudo -u "${APACHE}" sudo -u "${WIKITEX}" gnuplot 2>&1 | au_run grep -q 'Permission denied' || abort && (( test++ ))
}

au_run sudo -u "${APACHE}" sudo -u "${WIKITEX}" dot -V || status=${FAIL} && (( test++ ))

au_run sudo -u "${APACHE}" sudo -u "${WIKITEX}" sgf2dg -v || status=${FAIL} && (( test++ ))

au_run sudo -u "${APACHE}" sudo -u "${WIKITEX}" tex -v || status=${FAIL} && (( test++ ))

echo "${TERMS[${status}]}"
exit ${status}

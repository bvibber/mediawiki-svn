# Makefile responsible for token substitutions,
# and localisation of user-mediated edition.
#
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-6  Peter Danenberg
#
#      WikiTeX is licensed under the Artistic License 2.0;  to
# view a copy of this license, see COPYING or visit:
#
#      http://dev.perl.org/perl6/rfc/346.html
#

# Edit the following parameters:

# Required programs
# Full path to LaTeX:
LATEX = /usr/bin/latex
# Full path to dvipng:
DVIPNG = /usr/local/bin/dvipng
# Full path to ImageMagick's mogrify:
MOGRIFY = /usr/local/bin/mogrify
# Full path to ImageMagick's convert:
CONVERT = /usr/local/bin/convert

# Optional programs
# Full path to LilyPond (music):
LILYPOND = /usr/local/bin/lilypond
# Full path to Gnuplot (graph):
GNUPLOT = /usr/local/bin/gnuplot
# Full path to Graphviz' dot (graph):
DOT = /usr/local/bin/dot
# Full path to Graphviz' neato (graph):
NEATO = /usr/local/bin/neato
# Full path to Graphviz' fdp (graph):
FDP = /usr/local/bin/fdp
# Full path to Graphviz' twopi (graph):
TWOPI = /usr/local/bin/twopi
# Full path to Graphviz' circo (graph):
CIRCO = /usr/local/bin/circo
# Full path to sgf2dg (go):
SGF2DG = /usr/bin/sgf2dg
# Full path to TeX (go):
TEX = /usr/bin/tex
# Full path to MetaPost (feyn):
METAPOST = /usr/bin/mpost

# Quota
# Partition on which disk quota resides
PARTITION = /dev/hda3

# Cron
# Full path to wikitex-decruft.sh:
DECRUFT = /path/to/mediawiki/extensions/wikitex/wikitex-decruft.sh
# Database:
DATABASE = wikidb
# Database user:
DBUSER = wikiuser
# Database password (escape any @'s as \@):
DBPASS = password
# Who should receive cron's results (can be blank)
MAILTO = root

# Sudo
# Web server's user:
APACHE = nobody
# WikiTeX's user:
WIKITEX = wikitex
# WikiTeX's host:
HOST = localhost

# End of editable parameters

VERSION = 1.1 BETA 3
SED = sed -e "s@\%VERSION\%@${VERSION}@g;"
GROFF = copying.inc.ms | groff -t -ms -Tascii - | col -bx >
SUDO = -e "s@\%APACHE\%@${APACHE}@g; s@\%WIKITEX\%@${WIKITEX}@g; s@\%HOST\%@${HOST}@g;s@\%LATEX\%@${LATEX}@g; s@\%DVIPNG\%@${DVIPNG}@g; s@\%MOGRIFY\%@${MOGRIFY}@g; s@\%CONVERT\%@${CONVERT}@g; s@\%LILYPOND\%@${LILYPOND}@g; s@\%GNUPLOT\%@${GNUPLOT}@g; s@\%DOT\%@${DOT}@g; s@\%NEATO\%@${NEATO}@g; s@\%FDP\%@${FDP}@g; s@\%TWOPI\%@${TWOPI}@g; s@\%CIRCO\%@${CIRCO}@g; s@\%SGF2DG\%@${SGF2DG}@g; s@\%TEX\%@${TEX}@g; s@\%METAPOST\%@${METAPOST}@g;"
CRON = -e "s@\%DECRUFT\%@${DECRUFT}@g; s@\%DATABASE\%@${DATABASE}@g; s@\%DBUSER\%@${DBUSER}@g; s@\%DBPASS\%@${DBPASS}@g;"
QUOTA = -e "s@\%PARTITION\%@${PARTITION}@g;"
AUDIT = ${SUDO} ${CRON} ${QUOTA}
EXEC = chmod a+x
DOCS = README NEWS COPYING MANIFEST THANKS Wikitex.php WikitexConstants.php main.php wikitex.ini
PROGS = wikitex.sudoers wikitex.cron wikitex-decruft.sh wikitex-audit.sh

all: ${DOCS} ${PROGS}

README: readme.ms
	${SED} ${?} ${GROFF} ${@}

NEWS: news.ms
	${SED} ${?} ${GROFF} ${@}

COPYING: copying.ms
	${SED} ${?} ${GROFF} ${@}

MANIFEST: manifest.ms
	${SED} ${?} ${GROFF} ${@}

THANKS: thanks.ms
	${SED} ${?} ${GROFF} ${@}

Wikitex.php: Wikitex.in.php
	${SED} ${?} > ${@}

WikitexConstants.php: WikitexConstants.in.php
	${SED} ${?} > ${@}

main.php: main.in.php
	${SED} ${?} > ${@}

wikitex.ini: wikitex.in.ini
	${SED} ${?} > ${@}

docs:
	phpdoc -c wikitex

# Forced to forgo ${?} in the following,
# as a change in Makefile can trigger
# recompilation.

${PROGS}: Makefile

wikitex.sudoers: wikitex.in.sudoers
	${SED} ${SUDO} wikitex.in.sudoers > ${@}

wikitex.cron: wikitex.in.cron
	${SED} ${CRON} wikitex.in.cron > ${@}

wikitex-decruft.sh: wikitex-decruft.in.sh
	${SED} ${CRON} wikitex-decruft.in.sh > ${@}; \
	${EXEC} ${@}

wikitex-audit.sh: wikitex-audit.in.sh
	${SED} ${AUDIT} wikitex-audit.in.sh > ${@}; \
	${EXEC} ${@}

clean-docs:
	rm -frv ${DOCS}

clean-progs:
	rm -frv ${PROGS}

clean: clean-progs

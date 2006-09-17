#! /bin/sh
#
#	Get the latest FAQ from the www.squid-cache.org site using wget.
#

cd doc || exit 1
rm -f FAQ*
wget -r -l 1 -L http://www.squid-cache.org/Doc/FAQ/FAQ.html


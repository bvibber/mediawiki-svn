#!/bin/sh

jar="${1:-mwdumper.jar}"
pom="${3:-mwdumper-pom.xml}"

ver=`awk '/<version>(.*)<\/version>/{ gsub("<version>", "", $1); gsub("</version>", "", $1); print $1; exit; }' < "$pom"`
ver="${2:-$ver}"

mvn install:install-file  -Dfile="$jar" \
                          -DgroupId=org.mediawiki \
                          -DartifactId=mwdumper \
                          -Dpackaging=jar \
                          -Dversion="$ver" \
                          -DpomFile="$pom"

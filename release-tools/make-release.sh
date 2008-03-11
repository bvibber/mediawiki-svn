#!/bin/bash

case "$1" in
--help|"")
	echo "Usage:"
	echo "  ./make-release.sh --snapshot"
	echo "  ./make-release.sh 1.12.1"
	exit 1
	;;

--snapshot)
	ver=snapshot-`date +%Y%m%d`
	dir=snapshots
	branch=trunk
	prev=""
	;;

*)
	# ./make-release.sh 1.11.2
	majorminor=`echo "$1" | perl -pe 's/^(\d+\.\d+).*$/\1/'`
	patchlevel=`echo "$1" | perl -pe 's/^\d+\.\d+\.(\d+).*$/\1/'`
	betalevel=`echo "$1" | perl -pe 's/^\d+\.\d+\.\d+(.*)$/\1/'`
	ver="$majorminor.$patchlevel$betalevel"
	dir="$majorminor"
	branch=`echo "REL${ver//./_}" | tr '[a-z]' '[A-Z]'`
	branch="tags/$branch"
	if [ "$patchlevel" -ge "1" ]; then
		prevVer="$majorminor.$(($patchlevel-1))"
		prev=`echo "REL${prevVer//./_}" | tr '[a-z]' '[A-Z]'`
		prev="tags/$prev"
	else
		prev=""
	fi
esac

package=mediawiki-$ver

svn co http://svn.wikimedia.org/svnroot/mediawiki/$branch/phase3 $package

# Unix package
tar cvf - --exclude .svn --exclude testsuite --exclude mediawiki-large.xcf --exclude mediawiki-largesquare.xcf $package | gzip -9 > $package.tar.gz
outfiles="$package.tar.gz"

# Patch
if [ "$prev" ]; then
	svn diff \
	  http://svn.wikimedia.org/svnroot/mediawiki/$prev/phase3 \
	  http://svn.wikimedia.org/svnroot/mediawiki/$branch/phase3 \
	  > $package.patch
  outfiles="$outfiles $package.patch"
fi

for f in $outfiles; do
  gpg --detach-sign $f
done

echo ""
echo "Full release notes:"
echo "http://svn.wikimedia.org/svnroot/mediawiki/$branch/phase3/RELEASE-NOTES"
echo ""

echo ""
echo "Download:"
for f in $outfiles; do
  echo "http://download.wikimedia.org/mediawiki/$dir/$f"
done
echo ""

echo ""
echo "GPG signatures:"
for f in $outfiles; do
  echo "http://download.wikimedia.org/mediawiki/$dir/$f.sig"
done
echo ""

echo ""
echo "SHA-1 checksums:"
for f in $outfiles; do
  sha1 $f
done
echo ""

echo ""
echo "MD-5 checksums:"
for f in $outfiles; do
  md5 $f
done
echo ""

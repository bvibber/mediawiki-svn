#!/bin/bash

set -e

if [ -z $1 ];then
	echo "Usage: update.sh <package>"
fi

thisdir=`dirname $0`
package=$1

cd $thisdir
svn up $package
cd $package

echo "Building package..."
dpkg-buildpackage -rfakeroot
echo "Publishing..."
cd ..
tar -c $package''_* | ssh -A root@khaldun.wikimedia.org "
	test -e /srv/wikimedia/pool/main/$package || mkdir /srv/wikimedia/pool/main/$package
	tar -C /srv/wikimedia/pool/main/$package -x && \
	/root/update-repository 2>/dev/null && \
	echo Success || \
	echo update-repository failed
	"


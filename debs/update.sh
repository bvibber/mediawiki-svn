#!/bin/bash

set -e

if [ -z $1 ];then
	echo "Usage: update.sh <package>"
fi

thisdir=`dirname $0`
package=$1

cd $thisdir
svn up $package

echo "Building package..."
cd $package
prebuild_date=`TZ=UTC0 date +'%Y-%m-%d %H:%M:%SZ'`
sleep 1
dpkg-buildpackage -rfakeroot

echo
echo "Uploading files..."
cd ..
tar -v -N"$prebuild_date" -c $package''_* | ssh -A root@khaldun.wikimedia.org "
	test -e /srv/wikimedia/pool/main/$package || mkdir /srv/wikimedia/pool/main/$package
	tar -C /srv/wikimedia/pool/main/$package -x && \
	echo && \
	echo Updating the repository && \
	/root/update-repository 2>/dev/null && \
	echo Success || \
	echo update-repository failed
	"


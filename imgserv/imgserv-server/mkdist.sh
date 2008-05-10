#! /bin/ksh

# Build a tar distribution of imgserv

[[ $# = 1 ]] || {
	echo >&2 "usage: $0 <version>"
	exit 1
}

set -x
set -e

vers=$1
#trap "rm -rf imgserv-server-$vers" 0

rm -rf imgserv-server-$vers
mkdir imgserv-server-$vers
cd imgserv-server-$vers
mkdir lib
mkdir bin
cp -r ../dist/lib/* lib/
cp -r ../dist/*.jar .
cp log4j.properties dist/

chmod 644 lib/*
chmod 644 *.jar

cat >bin/start.sh <<__EOF__
#! /bin/sh
here=\`pwd\`
exec java -jar imgserv-server.jar "\$@"
__EOF__

cat >bin/run.sh <<__EOF__
#! /bin/sh
exec nohup bin/start.sh "\$@" &
__EOF__

chmod 755 bin/start.sh
cd ..
tar cf imgserv-server-$vers.tar imgserv-server-$vers
gzip -f imgserv-server-$vers.tar
ls -l imgserv-server-$vers.tar.gz

#rm -rf imgserv-server-$vers

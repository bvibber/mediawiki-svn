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

chmod 644 lib/*
chmod 644 *.jar

cat >bin/start.sh <<__EOF__
#! /bin/sh
here=\`pwd\`
cat >imgserv.policy <<EOF
grant codeBase "file:\${here}/imgserv-server.jar" {
        permission java.security.AllPermission;
};

grant codeBase "file:\${here}/lib/pngds.jar" {
        permission java.security.AllPermission;
};

grant codeBase "file:\${here}/lib/-" {
        permission java.awt.AWTPermission "*";
        permission java.io.SerializablePermission "*";
        permission java.lang.reflect.ReflectPermission "*";
        permission java.lang.RuntimePermission "*";
        permission java.util.PropertyPermission "*", "read,write";
        permission java.util.logging.LoggingPermission "control";
        permission java.io.FilePermission "\${here}/lib/-", "read";
};
EOF
java -Djava.security.manager -Djava.security.policy=imgserv.policy -jar imgserv-server.jar "\$@"
__EOF__

chmod 755 bin/start.sh
cd ..
tar cf imgserv-server-$vers.tar imgserv-server-$vers
gzip -f imgserv-server-$vers.tar
ls -l imgserv-server-$vers.tar.gz

#rm -rf imgserv-server-$vers

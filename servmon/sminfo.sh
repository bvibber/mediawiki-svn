#! /bin/sh
# $Header$

haveident=no
for dir in `echo $PATH | tr : ' '`; do
	if [ -x $dir/ident ]; then
		haveident=yes
	fi
done
if [ $haveident = yes ]; then
	idents=`ident -q \`find . -name \*.\[ch\] -o -name \*.\[ch\]xx\`|egrep '\\$(Head|NetBSD|FreeBSD|Nexadesic)'|(while read foo; do echo "	\"$foo\"",; done)`
else
	idents='"ident not available at compile time",'
fi

cat <<_EOF_
/* this file in generated automatically.  do not edit it. */
#ifndef SM_SMINFO_CXX_
#define SM_SMINFO_CXX_
static char const *sm\$compile_user = "$USER";
static char const *sm\$compile_host = "`hostname`";
static char const *sm\$compile_time = "`date +"%d-%b-%Y %H:%M:%S"`";
static char const *sm\$compile_os = "`uname`";
static char const *sm\$compile_release = "`uname -r`";
static char const *sm\$compile_arch = "`uname -m`";
static char const *sm\$compile_ident[] = { 
$idents
	(char const *)0 
};
#endif
_EOF_

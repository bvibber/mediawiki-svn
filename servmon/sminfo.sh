#! /bin/sh
# $Header$

idents=`ident -q \`find . -name \*.\[ch\]xx\`|grep \\\$Head|(while read foo; do echo "	\"$foo\"",; done)`

cat <<_EOF_
/* this file in generated automatically.  do not edit it. */
#ifndef SM_SMINFO_CXX_
#define SM_SMINFO_CXX_
static char const *sm\$compile_user = "`whoami`";
static char const *sm\$compile_host = "`hostname`";
static char const *sm\$compile_time = "`date +"%b-%m-%Y %H:%M:%S"`";
static char const *sm\$compile_os = "`uname`";
static char const *sm\$compile_release = "`uname -r`";
static char const *sm\$compile_arch = "`uname -m`";
static char const *sm\$compile_ident[] = { 
$idents
	NULL 
};
#endif
_EOF_

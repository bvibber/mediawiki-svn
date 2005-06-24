#! /bin/sh
# $Header$
#
# servmon build env configuration

die() {
	echo $*
	exit 1
}

usage() {
	die "usage: $0 buildfile <debug|prod>"
}

[ -n "$1" ] || usage
[ -n "$2" ] || usage

case $2 in
	debug)
		CFLAGS=-g
		;;
	*)
		CFLAGS="-O -g"
		;;
esac

CXXFLAGS=$CFLAGS

SHELL=${SHELL:-"/bin/sh"}

unset CC CXX PREFIX
unset LDFLAGS

. $1

CC=${CC:-$cc};			export CC
CXX=${CXX:-$CC};		export CXX
CPPFLAGS="${CPPFLAGS:-$CPPFLAGS}";	export CPPFLAGS
CXXFLAGS=${CXXFLAGS};		export CXXFLAGS
CFLAGS=${CFLAGS};		export CFLAGS
LDFLAGS=${LDFLAGS:-$LDFLAGS};	export LDFLAGS
PREFIX=${PREFIX:-"/opt/wmf"};	export PREFIX
export BOOST

CPPFLAGS="$CPPFLAGS -Iinclude -I."

grep=`which grep`
[ -x /usr/xpg4/bin/grep ] && grep=/usr/xpg4/bin/grep
($CXX -V 2>&1 | $grep -q "Sun C++") && CXXFLAGS="$CXXFLAGS -features=iddollar"
($CC -V 2>&1 | $grep -q "Sun C") && CFLAGS="$CFLAGS -features=iddollar"

echo "Using CC :    $CC"
echo "      CXX:    $CXX"
echo " CPPFLAGS:    $CPPFLAGS"
echo " CXXFLAGS:    $CXXFLAGS"
echo "   CFLAGS:    $CFLAGS"
echo "  LDFLAGS:    $LDFLAGS"
echo "Boost suffix: $BOOST"
echo "Installation prefix: $PREFIX"
echo
echo Starting new shell.
echo

exec $SHELL

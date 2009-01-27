#!/bin/bash

function getconf {
	f="$1"
	d="${2:- }"
	v="$3"
	
	if [ -f "$f" ]; then
		v="$v$d"`cat "$f"`
	fi
	
	if [ -x "$f.sh" ]; then
		v="$v$d"`"$f.sh"`
	fi
	
	echo "$v"
}

if [ -z "$1" ]; then
		echo "USAGE: "`basename "$0"`" <class-name> <params...>"
        exit 1
fi

cls="$1"
shift

dir=`dirname "$0"`

opt=""
vmopt=""

vmopt="$vmopt"`getconf "$dir/local.vm.options" " "`
vmopt="$vmopt"`getconf /etc/wikiword.vm.options " "`
vmopt="$vmopt"`getconf /etc/wikiword/vm.options " "`
vmopt="$vmopt"`getconf ~/.wikiword.vm.options " "`
vmopt="$vmopt"`getconf ~/.wikiword/vm.options " "`

opt="$opt"`getconf "$dir/local.ww.options" " "`
opt="$opt"`getconf /etc/wikiword.ww.options " "`
opt="$opt"`getconf /etc/wikiword/ww.options " "`
opt="$opt"`getconf ~/.wikiword.ww.options " "`
opt="$opt"`getconf ~/.wikiword/ww.options " "`

cp="$cp"`getconf ~/.wikiword.classpath ":"`
cp="$cp"`getconf ~/.wikiword/classpath ":"`
cp="$cp"`getconf /etc/wikiword.classpath ":"`
cp="$cp"`getconf /etc/wikiword/classpath ":"`
cp="$cp"`getconf "$dir/local.classpath" ":"`

if [ -z "$cp" ]; then
	for j in `find "$dir" -path "$dir/lib/*.jar"`; do
	    cp="$cp:$j"
	done
	
	for j in `find "$dir/.." -path "$dir/../WikiWord*/lib/*.jar"`; do
	    cp="$cp:$j"
	done
fi

if [ ! -z "${cls/de.brightbyte.wikiword.*/}" ]; then
	cls="de.brightbyte.wikiword.$cls"
fi

cmd=`echo java $vmopt $WIKIWORD_JAVA_OPTIONS -cp "$cp" "$cls" $opt "$@"`

echo
echo "CLS: $cls"
echo
echo "OPT: $opt"
echo
echo "VMOPT: $vmopt"
echo
echo "LIB: $cp"
echo
echo "CMD: $cmd"
echo

### DO IT ####################
$cmd
##############################

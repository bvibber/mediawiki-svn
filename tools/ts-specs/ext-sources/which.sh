#! /bin/ksh

[[ $# = 1 ]] || {
	echo >&2 "usage: $0 <program>"
	exit 1
}

for x in $(echo $PATH | tr : ' '); do
	if [[ -x $x/$1 ]]; then
		echo $x/$1
		exit 0
	fi
done

echo "$1 not found in $PATH"
exit 1

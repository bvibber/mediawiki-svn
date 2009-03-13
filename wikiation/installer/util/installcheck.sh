#!/bin/bash


function checkexist {

	aptitude show $1 | grep "State: installed" >/dev/null && { echo "$1 is installed"; return 0; } || { echo "$1 needs to be installed"; return 1; }
}


function pyver {
	pythonver=`python -V 2>&1`
	echo -n "Found python version $pythonver. "
	major=`echo $pythonver | sed "s/^Python //g" | sed "s/\..*$//"`
	if [ $major -ne 2 ]; then
		if [ $major -eq 3 ]; then
			echo "Python 3 is not yet supported"
		else
			echo "Unknown python version. Please ensure you have python > 2.5.2 or later (but not Python 3)"
		fi
		return 1
	fi

	minorrevision=`echo $pythonver | sed "s/^Python 2.//g"`
	minor=`echo $minorrevision | sed "s/\..*$//"`
	revision=`echo $minorrevision | sed "s/^.*\.//"`
	if [ $minor -lt 5 ]; then
		echo "Default python is not version 2.5. Ensure default python version is 2.5 or later."
		return 1
	fi

	if [ $minor -eq 5 -a $revision -lt 2 ]; then
		echo "The wikiation installer is only tested with python version 2.5.2 or better, please ensure you have python version 2.5.2 or better installed."
		return 1
	fi

	echo "Python version seems ok."
	return 0
}

google=`ping -q -c3 -W2 www.google.com`
echo $google | grep "unknown host" > /dev/null && {
	echo "No DNS available. Is the network available at all? Ensure the network becomes available"
	exit 1 
}
echo $google | grep "100% packet loss" > /dev/null && {
	echo "Can't ping www.google.com (100% packet loss). Please check your network settings"
	exit 1
 }
echo $google | grep "0% packet loss" >/dev/null && echo "Network seems up"

uname -a 2>&1 | grep "Linux" >/dev/null 2>&1 && echo "Linux detected." || { 
	echo "This isn't Linux."; exit 1; }

aptitude --version >/dev/null 2>&1 && echo "aptitude found, continuing" || { 
	echo "aptitude fails to respond, please ensure your debian environment is sane."; exit 1; }

echo " --- "

x=0
checkexist mysql-server-5.0
x=$(($?|$x))
checkexist mysql-client-5.0 
x=$(($?|$x))
checkexist python2.5 && pyver
x=$(($?|$x))
checkexist php5-cli
x=$(($?|$x))
checkexist apache2
x=$(($?|$x))
checkexist libapache2-mod-php5
x=$(($?|$x))
checkexist apache2.2-common
x=$(($?|$x))
checkexist subversion
x=$(( $?|$x ))

echo " --- "
if [ $x != 0 ]; then
	echo "One or more issues were discovered. It is unlikely that the installer will work"
else
	echo "It may be possible to install the wikiation installer"
fi


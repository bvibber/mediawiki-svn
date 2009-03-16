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


uname -a 2>&1 | grep "Linux" >/dev/null 2>&1 && echo "Linux detected." || { 
	echo "This isn't Linux."; exit 1; }

google=`ping -q -c3 -W2 www.google.com 2>&1`
echo $google | grep "unknown host" > /dev/null && {
	echo "No DNS available. Is the network available at all? Ensure the network becomes available"
	exit 1 
}
echo $google | grep "100% packet loss" > /dev/null && {
	echo "Can't ping www.google.com (100% packet loss). Please check your network settings"
	exit 1
 }
echo $google | grep "0% packet loss" >/dev/null && echo "Network seems up"

aptitude --version >/dev/null 2>&1 && echo "aptitude found, continuing" || { 
	echo "aptitude fails to respond, please ensure your debian environment is sane. (Try something like apt-get update; apt-get install aptitude) "; exit 1; }

echo " --- "

locale -a 2>&1 | grep "en_US.utf8" >/dev/null && echo "System supports utf8 locale" || echo "locale en_US.utf8 not installed. Ensure debconf is installed, then run   dpkg-reconfigure locales"

echo " --- "
x=0
checkexist debconf
x=$(($?|$x))
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
checkexist php5-mysql
x=$(( $?|$x ))
checkexist php5-imagick
x=$(( $?|$x ))
echo " --- "
if [ $x != 0 ]; then
	echo "One or more issues were discovered. It is unlikely that the installer will work"
else
	echo "It may be possible to install the wikiation installer"
fi

echo
echo "=== Specific things for wikiation environment"
test -d /var/www/revisions && echo "revisionsdir exists" || { echo "you may want to create a directory /var/www/revisions. Don't forget to modify your apache configuration."; exit 1; }

checkexist wget || exit 1

touch /var/www/revisions/installertest
wget 'http://localhost/revisions/installertest' 2>&1 | grep "200 OK" >/dev/null && echo "apache configured well enough." || echo "apache not correctly configured"
rm /var/www/revisions/installertest

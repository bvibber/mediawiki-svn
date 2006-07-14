#!/bin/bash


cd `readlink -f /home/wikipedia/common/php/maintenance`
while [ 1 ];do
	db=
	while [ -z $db ];do
		db=`php -n nextJobDB.php`
		if [ -z $db ];then
			# No jobs to do, wait for a while
			echo "No jobs..."
			sleep 5
		fi
	done
	echo $db
	/usr/local/bin/run-jobs $db | sed -u "s/^/$db:  /" | tee -a /home/wikipedia/logs/jobqueue/$HOSTNAME.$$.log
done


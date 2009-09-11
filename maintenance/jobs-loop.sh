#!/bin/bash

trap 'kill %-; exit' SIGTERM
[ ! -z "$1" ] && {
    echo "starting type-specific job runner: $1"
    type=$1
}

cd `readlink -f /home/wikipedia/common/php/maintenance`
while [ 1 ];do
	db=
	while [ -z $db ];do
                if [ ! -z "$type" ]; then
                    db=`php -n nextJobDB.php --type=$type`
                else
                    db=`php -n nextJobDB.php`
                fi

		if [ -z $db ];then
			# No jobs to do, wait for a while
			echo "No jobs..."
			sleep 5
		fi
	done
	echo $db
        if [ ! -z "$type" ]; then
            nice -n 20 php runJobs.php $db --procs=4 $type &
        else
            nice -n 20 php runJobs.php $db --procs=4 &
        fi
	wait
done


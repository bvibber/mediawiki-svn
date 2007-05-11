#!/bin/bash

[ ! -z "$1" ] && {
    echo "starting type-specific job runner: $1" >> /home/wikipedia/logs/jobqueue/$HOSTNAME.$$.log
    type=$1
}

cd `readlink -f /home/wikipedia/common/php/maintenance`
while [ 1 ];do
	db=
	while [ -z $db ];do
                if [ ! -z "$type" ]; then
                    db=`php -n nextJobDB.php -t $type`
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
            /usr/local/bin/run-jobs $db $type | sed -u "s/^/$db:  /" | tee -a /home/wikipedia/logs/jobqueue/$HOSTNAME.$$.log
        else
            /usr/local/bin/run-jobs $db | sed -u "s/^/$db:  /" | tee -a /home/wikipedia/logs/jobqueue/$HOSTNAME.$$.log
        fi
done


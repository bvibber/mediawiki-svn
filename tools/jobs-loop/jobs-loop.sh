#!/bin/bash


cd `readlink -f /home/wikipedia/common/php/maintenance`

while [ 1 ];do 
	for x in `</h/w/c/pmtpa.dblist`;do 
		/usr/local/bin/run-jobs $x | sed -u "s/^/$x:  /" | tee -a /home/wikipedia/logs/jobqueue/$HOSTNAME.$$.log
		
		#php runJobs.php $x | sed -u "s/^/$x:  /" | tee -a /home/wikipedia/logs/jobqueue/$HOSTNAME.$$.log
		#sleep 1
	done
done


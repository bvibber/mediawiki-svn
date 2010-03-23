#!/bin/bash

for db in `</home/wikipedia/common/all.dblist`;do
#for db in enwiki;do
	echo "
-------------------------------------
$db
-------------------------------------
" | tee -a /home/wikipedia/logs/norotate/rct/stdout.log
	if [ "$db" == enwiki ]; then
		procs=10
	else
		procs=5
	fi
	php recompressTracked.php --wiki=$db --no-count --procs=$procs --info-log=/home/wikipedia/logs/norotate/rct/info.log --critical-log=/home/wikipedia/logs/norotate/rct/critical.log rc1 2>&1 | tee -a /home/wikipedia/logs/norotate/rct/stdout.log
done

#!/bin/bash

for db in `</home/wikipedia/common/all.dblist`;do
	echo "
-------------------------------------
$db
-------------------------------------
" | tee -a /home/wikipedia/logs/norotate/rct/stdout.log
	if [ "$db" == enwiki ]; then
		procs=5
	else
		procs=5
	fi
	php recompressTracked.php --wiki=$db --procs=$procs --info-log=/home/wikipedia/logs/norotate/rct/info.log --critical-log=/home/wikipedia/logs/norotate/rct/critical.log rc1 2>&1 | tee -a /home/wikipedia/logs/norotate/rct/stdout.log
done

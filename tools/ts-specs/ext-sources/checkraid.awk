#! /usr/bin/nawk -f

BEGIN {
	FAILED_DISK=0
	FAILED_VOLUME=0
}

/Controller Status/ {
	STATUS=$4
}

/Defunct disk drive count/ {
	FAILED_DISK=$6
}

/Logical devices\/Failed\/Degraded/ {
	split($4, x, / /)
	FAILED_VOLUME=x[2] + x[3]
}

END {
	print "Controller status: " STATUS
	print "Failed disks: " FAILED_DISK
	print "Failed volumes: " FAILED_VOLUME
}

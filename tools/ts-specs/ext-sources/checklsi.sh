#! /bin/ksh

MAILTO=admins@toolserver.org
VARDIR=/var/opt/ts/checklsi

RAIDCTL=/usr/sbin/raidctl
STATUSFILE=$VARDIR/curr
NEWSTATUS=$VARDIR/new
MAILFILE=$VARDIR/mail

if ! [ -d "$VARDIR" ]; then
	if ! mkdir -p "$VARDIR"; then
		echo >&2 "$0: could not create $VARDIR"
		exit 1
	fi
fi

if ! [ -x "$RAIDCTL" ]; then
	echo >&2 "$0: $RAIDCTL not found; please install the StorMan package."
	exit 1
fi

# Get the list of volumes
volumes=$(
raidctl | sed -n \
'/Volume:/ { 
s/^.*\(c[0-9]*t[0-9]*d[0-9]*\).*$/\1/
p
}'
)

rm -f $NEWSTATUS

for vol in $volumes; do
	if ! $RAIDCTL -l $vol >>$NEWSTATUS; then
		echo >&2 "$0: $RAIDCTL failed; see above messages for details"
		exit 1
	fi
done

if [ ! -f $STATUSFILE ]; then
	if ! mv $NEWSTATUS $STATUSFILE; then
		echo >&2 "$0: couldn't write to the status file $STATUSFILE"
		exit 1
	fi

	exit 0
fi

if cmp -s $NEWSTATUS $STATUSFILE; then
	rm -f $NEWSTATUS
	exit 0
fi

rm -f $MAILFILE
cat >$MAILFILE <<__EOF__
A change has been detected in the RAID controller on $(hostname):

__EOF__
diff -u $STATUSFILE $NEWSTATUS >>$MAILFILE
cat >>$MAILFILE <<__EOF__

The full controller status follows:

__EOF__
cat $NEWSTATUS >>$MAILFILE

rm -f $STATUSFILE
mv $NEWSTATUS $STATUSFILE

if ! mailx -s "RAID status change for $(hostname)" $MAILTO <$MAILFILE; then
	echo >&2 "$0: can't send mail"
	exit 1
fi

rm -f $MAILFILE
exit 0

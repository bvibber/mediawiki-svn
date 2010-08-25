#!/bin/sh -e
### BEGIN INIT INFO
# Provides:          cruisecontrol
# Required-Start:    
# Required-Stop:     
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: Start Cruise Control
### END INIT INFO

PATH="/sbin:/bin:/usr/sbin:/usr/bin"

. /lib/lsb/init-functions

CCDIR=/home/ci/cc
CCPIDFILE=$CCDIR/cc.pid
STARTCC=$CCDIR/cruisecontrol.sh
CCUSER=ci
HOME=/home/ci
CCNAME=CruiseControl

export CCDIR

checkproc() {
    if pidofproc -p $CCPIDFILE > /dev/null; then
        return 0
    else
        return 1
    fi
}


case "$1" in
start)
        log_action_begin_msg "Starting $CCNAME"

        if checkproc; then
            log_failure_msg "$CCNAME already running."
            exit 1
        fi

        if [ ! -f $STARTCC -o ! -x $STARTCC ]; then
            log_failure_msg "Startup script ($STARTCC) can't be used."
            exit 1
        fi

        cd $CCDIR
        if sudo -u $CCUSER env HOME=$HOME $STARTCC >/dev/null 2>/dev/null; then
            log_action_end_msg $?
        else
            log_action_end_msg $?
        fi
	;;

stop)
        log_action_begin_msg "Shutting down $CCNAME"
        if [ ! -f $CCPIDFILE ]; then
            log_failure_msg "PID file not found, can't safely shut down."
            exit 1
        fi

        if ! checkproc; then
            log_action_end_msg 0
            rm $CCPIDFILE
        fi

        kill `cat $CCPIDFILE`
        if checkproc; then
            i=0
            while [ checkproc -a $i -lt 10 ]; do
                i=$(($i+1))
                sleep 1
            done
        fi

        if checkproc; then
            log_action_end_msg 1
        else
            log_action_end_msg 0
            rm $CCPIDFILE
        fi

        ;;
restart)
        $0 stop || true
        $0 start
        ;;
*)
	echo "Usage: /etc/init.d/networking {start|stop|restart}"
	exit 1
	;;
esac

exit 0


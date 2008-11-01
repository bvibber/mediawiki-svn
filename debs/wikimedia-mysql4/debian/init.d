#! /bin/sh
#

DESC="MySQL Four Oh Forever"
NAME=mysql

case "$1" in
  start)
	echo -n "Starting $DESC: "
	/usr/mysql-wikimedia/bin/mysqld_safe &
	;;
  stop)
	echo -n "Stopping $DESC. It may take 10-20 minutes... "
	killall mysqld
	
	;;
  *)
	N=/etc/init.d/$NAME
	# echo "Usage: $N {start|stop|restart|reload|force-reload}" >&2
	echo "Usage: $N {start|stop|restart|force-reload|status|force-stop}" >&2
	exit 1
	;;
esac

exit 0

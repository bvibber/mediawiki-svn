all:

METHODS=\
	method-jira		\
	method-confluence	\
	method-fisheye		\
	method-hq-server	\
	method-memcached	\
	method-msgbot		\
	method-php-fastcgi	\
	method-supybot		\
	method-sshtunnel	\

install:
	/usr/ucb/install -d -m 0755 -o root -g root /opt/wmf
	/usr/ucb/install -d -m 0755 -o root -g root /opt/wmf/smf
	/usr/ucb/install -m 0755 -o root -g bin $(METHODS) /opt/wmf/smf
	/usr/sbin/svccfg import jira.xml
	/usr/sbin/svccfg import confluence.xml
	/usr/sbin/svccfg import fisheye.xml
	/usr/sbin/svccfg import hq-server.xml
	/usr/sbin/svccfg import memcached.xml
	/usr/sbin/svccfg import msgbot.xml
	/usr/sbin/svccfg import php-fastcgi.xml
	/usr/sbin/svccfg import supybot.xml
	/usr/sbin/svccfg import sshtunnel.xml

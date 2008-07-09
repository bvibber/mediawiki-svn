include Makefile.config

CPPFLAGS	=
INCLUDES	= $(EXTRA_CPPFLAGS)
CFLAGS		= $(EXTRA_CFLAGS)
LDFLAGS		= $(EXTRA_LDFLAGS)
LIBS		= -llog4cxx -lboost_system$(BOOSTTAG) -lboost_signals$(BOOSTTAG) $(EXTRA_LIBS)

CONFIG_FLAGS	= \
	-DPREFIX=\"$(PREFIX)\"		\
	-DCONFDIR=\"$(CONFDIR)\"	\
	-DPHP_BIN=\"$(PHP_BIN)\"	\
	-DSB_USER=\"$(SB_USER)\"	\
	-DSB_GROUP=\"$(SB_GROUP)\"	\

SWEXEC_FLAGS	= \
	-DSB_UID_MIN=$(SB_UID_MIN)			\
	-DSB_GID_MIN=$(SB_GID_MIN)			\
	-DSB_USER=\"$(SB_USER)\"			\
	-DSB_GROUP=\"$(SB_GROUP)\"			\
	-DSB_SAFE_PATH=\"$(SB_SAFE_PATH)\"		\
	-DPHP_BIN=\"$(PHP_BIN)\"			\
	-DSB_LOG_EXEC=\"$(SB_LOG_EXEC)\"		\

SWKILL_FLAGS	= \
	-DSB_UID_MIN=$(SB_UID_MIN)		\
	-DSB_GID_MIN=$(SB_GID_MIN)		\
	-DSB_USER=\"$(SB_USER)\"		\
	-DSB_LOG_EXEC=\"$(SB_LOG_EXEC)\"	\

OBJS=	\
	main.o				\
	config.o			\
	fcgi_application.o		\
	fcgi_cgi.o			\
	fcgi_listener.o			\
	fcgi_server_connection.o	\
	process.o			\
	process_factory.o		\
	sbcontext.o			\

PROG=	switchboard
SWEXEC=	swexec
SWKILL=	swkill

all: $(PROG) $(SWEXEC) $(SWKILL)

$(PROG): $(OBJS)
	$(CXX) $(CFLAGS) $(LDFLAGS) $(OBJS) -o $(PROG) $(LIBS)

$(SWEXEC): swexec.o
	$(CC) $(CFLAGS) $(LDFLAGS) swexec.o -o $(SWEXEC)

$(SWKILL): swkill.o
	$(CC) $(CFLAGS) $(LDFLAGS) swkill.o -o $(SWKILL)

.cc.o:
	$(CXX) $(CPPFLAGS) $(CONFIG_FLAGS) $(INCLUDES) $(CFLAGS) -c $<

swexec.o: swexec.c
	$(CC) $(CPPFLAGS) $(SWEXEC_FLAGS) $(INCLUDES) $(CFLAGS) -c $<

swkill.o: swkill.c
	$(CC) $(CPPFLAGS) $(SWKILL_FLAGS) $(INCLUDES) $(CFLAGS) -c $<

install:
	$(INSTALL) -d -m 0755 $(DESTDIR)$(PREFIX)
	$(INSTALL) -d -m 0755 $(DESTDIR)$(PREFIX)/lib
	$(INSTALL) -d -m 0755 $(DESTDIR)$(PREFIX)/lib/switchboard
	$(INSTALL) -d -m 0755 $(DESTDIR)$(PREFIX)/bin
	$(INSTALL) -d -m 0755 $(DESTDIR)$(CONFDIR)
	$(INSTALL) -o root -g $(SB_GROUP) -m 0755 switchboard $(DESTDIR)$(PREFIX)/bin
	$(INSTALL) -o root -g $(SB_GROUP) -m 04710 $(SWEXEC) $(DESTDIR)$(PREFIX)/lib/switchboard
	$(INSTALL) -o root -g $(SB_GROUP) -m 04710 $(SWKILL) $(DESTDIR)$(PREFIX)/lib/switchboard
	$(INSTALL) -o root -g bin -m 0644 log.conf.example $(DESTDIR)$(CONFDIR)
	$(INSTALL) -o root -g bin -m 0644 main.conf.example $(DESTDIR)$(CONFDIR)
	$(INSTALL) -o root -g bin -m 0644 initlog.conf $(DESTDIR)$(CONFDIR)

clean:
	rm -f $(OBJS) $(PROG) $(SWEXEC) $(SWKILL) swexec.o swkill.o

Makefile.config:
	@echo You must create Makefile.config before you can build switchboard
	@exit 1

.SUFFIXES: .c .cc .o
.KEEP_STATE:
.PHONY: clean

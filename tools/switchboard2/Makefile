include Makefile.config

CPPFLAGS	=
INCLUDES	= $(EXTRA_CPPFLAGS)
CFLAGS		= $(EXTRA_CFLAGS)
CXXFLAGS	= $(EXTRA_CXXFLAGS)
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

SWITCHBOARD_FLAGS	= \
	-DPREFIX=\"$(PREFIX)\"

OBJS=	\
	main.o				\
	request_thread.o		\
	fcgi.o				\
	process.o			\
	config.o			\
	process_factory.o		\
	util.o				\

PROG=		switchboard-bin
SWEXEC=		swexec
SWKILL=		swkill
SWITCHBOARD=	switchboard
all: $(PROG) $(SWEXEC) $(SWKILL) $(SWITCHBOARD)

$(PROG): $(OBJS)
	$(CXX) $(CFLAGS) $(CXXFLAGS) $(LDFLAGS) $(OBJS) -o $(PROG) $(LIBS)

$(SWEXEC): swexec.o
	$(CC) $(CFLAGS) $(LDFLAGS) swexec.o -o $(SWEXEC) $(SWEXEC_EXTRA_LIBS)

$(SWKILL): swkill.o
	$(CC) $(CFLAGS) $(LDFLAGS) swkill.o -o $(SWKILL)

$(SWITCHBOARD): switchboard.o
	$(CC) $(CFLAGS) $(LDFLAGS) switchboard.o -o $(SWITCHBOARD)

.cc.o:
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) $(CONFIG_FLAGS) $(INCLUDES) $(CFLAGS) -c $<

swexec.o: swexec.c
	$(CC) $(CPPFLAGS) $(SWEXEC_FLAGS) $(INCLUDES) $(CFLAGS) -c $<

swkill.o: swkill.c
	$(CC) $(CPPFLAGS) $(SWKILL_FLAGS) $(INCLUDES) $(CFLAGS) -c $<

switchboard.o: switchboard.c
	$(CC) $(CPPFLAGS) $(SWITCHBOARD_FLAGS) $(INCLUDES) $(CFLAGS) -c $<

install:
	$(INSTALL) -d -m 0755 $(DESTDIR)$(PREFIX)
	$(INSTALL) -d -m 0755 $(DESTDIR)$(PREFIX)/lib
	$(INSTALL) -d -m 0755 $(DESTDIR)$(PREFIX)/lib/switchboard
	$(INSTALL) -d -m 0755 $(DESTDIR)$(PREFIX)/bin
	$(INSTALL) -d -m 0755 $(DESTDIR)$(CONFDIR)
	$(INSTALL) -o root -g $(SB_GROUP) -m 0755 switchboard $(DESTDIR)$(PREFIX)/bin
	$(INSTALL) -o root -g $(SB_GROUP) -m 0755 switchboard-bin $(DESTDIR)$(PREFIX)/lib/switchboard
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

gccdepend:
	sed '/^# Do not delete this line -- make depend requires it/,$$d' < Makefile > Makefile.new
	echo '# Do not delete this line -- make depend requires it' >> Makefile.new
	$(CXX) $(CPPFLAGS) $(CONFIG_FLAGS) $(INCLUDES) -MM $(OBJS:.o=.cc) >>Makefile.new
	$(CXX) $(CPPFLAGS) $(SWEXEC_FLAGS) $(INCLUDES) -MM swexec.c >>Makefile.new
	$(CXX) $(CPPFLAGS) $(SWKILL_FLAGS) $(INCLUDES) -MM swkill.c >>Makefile.new
	mv -f Makefile.new Makefile

# Do not delete this line -- make depend requires it
main.o: main.cc fcgi.h request_thread.h process.h config.h version.h \
  process_factory.h
request_thread.o: request_thread.cc request_thread.h fcgi.h process.h \
  process_factory.h util.h config.h
fcgi.o: fcgi.cc fcgi.h util.h
process.o: process.cc process.h
config.o: config.cc config.h
process_factory.o: process_factory.cc process_factory.h process.h \
  config.h util.h
util.o: util.cc util.h
swexec.o: swexec.c
swkill.o: swkill.c

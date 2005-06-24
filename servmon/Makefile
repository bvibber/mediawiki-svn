# $Header$

SERVMONOBJS=	csmplex.o servmon.o smalrm.o smauth.o smcfg.o	\
		smirc.o smlog.o smmc.o smmon.o smnet.o smqb.o	\
		smstdrt.o smthr.o smtmr.o smtrm.o smutl.o	\
		l_memcache.o sminfo.o msgtab.o snmp.o mysql.o	\
		server.o

SYSTATDOBJS=	systatd.o
SMLOGMSGOBJS=	smlogmsg.o

all: servmon smlogmsg systatd

servmon: $(SERVMONOBJS) bconf/ldflags
	$(CXX) $(CXXFLAGS) $(SERVMONOBJS) -o $@ $(LDFLAGS) -lboost_thread$(BOOST) \
		-lboost_regex$(BOOST) -lnetsnmp -lmysqlclient -lssl -lcrypto \
		`cat bconf/ldflags`

sminfo.o: sminfo.cxx
sminfo.cxx: sminfo.sh
	sh sminfo.sh >$@
msgtab.hxx msgtab.cxx: mktab messages.tab
	./mktab messages.tab
mktab: mktab.cxx
	$(CXX) $(CPPFLAGS) $(CXXFLAGS) mktab.cxx -o mktab $(LDFLAGS)
smlogmsg: smlogmsg.c
	$(CC) $(CPPFLAGS) $(CFLAGS) smlogmsg.c -o smlogmsg $(LDFLAGS) `cat bconf/ldflags`
systatd: systatd.c
	$(CC) $(CPPFLAGS) $(CFLAGS) systatd.c -o systatd $(LDFLAGS) `cat bconf/ldflags`

.c.o:
	$(CC) $(CPPFLAGS) $(CXXFLAGS) -c $<
.cxx.o:
	$(CXX) $(CPPFLAGS) $(CFLAGS) -c $<
bconf/prelude.h:
	-mkdir bconf
	echo '#define PFX "$(PREFIX)"' >bconf/prelude.h
bconf/ldflags:
	-mkdir bconf
	rm -f $@
	echo "int main() { return 0; }" > test.c
	-($(CC) test.c -lsocket -lnsl -o /dev/null && echo -lsocket -lnsl) >>$@
	-($(CC) test.c -lkstat -o /dev/null && echo -lkstat) >>$@
	-($(CC) test.c -lresolv -o /dev/null && echo -lresolv) >>$@
	rm -f test.c
bconf/headers.h:
	-mkdir bconf
	rm -f $@
	echo '#include <paths.h>' >test.c
	-($(CC) -c -o /dev/null test.c && echo '#define T_PATHS 1') >>bconf/headers.h
	echo '#include <mntent.h>' >test.c
	-($(CC) -c -o /dev/null test.c && echo '#define T_MNTENT 1') >>bconf/headers.h
	echo '#include <stdio.h>' >test.c
	echo '#include <sys/mnttab.h>' >>test.c
	-($(CC) -c -o /dev/null test.c && echo '#define T_SYS_MNTTAB 1') >>bconf/headers.h
	rm -f test.c
.PHONY: install clean
.SUFFIXES: .cxx .c .o
.KEEP_STATE:

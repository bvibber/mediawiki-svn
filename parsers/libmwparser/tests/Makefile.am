
check_PROGRAMS = testtext

testtext_SOURCES = src/testtext.c
testtext_LDADD = $(top_builddir)/.libs/libmwparser.a $(ANTLR3C_LIBS) $(GLIB_LIBS)

test_data_dirs = format nowiki html table hr list link

dist_data_DATA = simpleinput simpleoutput								\
	 $(patsubst $(srcdir)/%, %, $(wildcard $(patsubst %, $(srcdir)/%/*.in,  $(test_data_dirs))))	\
	 $(patsubst $(srcdir)/%, %, $(wildcard $(patsubst %, $(srcdir)/%/*.out, $(test_data_dirs))))

INCLUDES = -I$(top_srcdir)/include -I$(top_builddir)

# The `:;' works around a Bash 3.2 bug when the output is not writeable.
$(srcdir)/package.m4: $(top_srcdir)/configure.ac
	:;{ \
          echo '# Signature of the current package.' && \
          echo 'm4_define([AT_PACKAGE_NAME],' && \
          echo '  [$(PACKAGE_NAME)])' && \
          echo 'm4_define([AT_PACKAGE_TARNAME],' && \
          echo '  [$(PACKAGE_TARNAME)])' && \
          echo 'm4_define([AT_PACKAGE_VERSION],' && \
          echo '  [$(PACKAGE_VERSION)])' && \
          echo 'm4_define([AT_PACKAGE_STRING],' && \
          echo '  [$(PACKAGE_STRING)])' && \
          echo 'm4_define([AT_PACKAGE_BUGREPORT],' && \
          echo '  [$(PACKAGE_BUGREPORT)])'; \
          echo 'm4_define([AT_PACKAGE_URL],' && \
          echo '  [$(PACKAGE_URL)])'; \
        } >'$(srcdir)/package.m4'

EXTRA_DIST = testsuite.at $(srcdir)/package.m4 $(TESTSUITE) atlocal.in
TESTSUITE = $(srcdir)/testsuite

check-local: atconfig atlocal $(TESTSUITE)
	$(SHELL) '$(TESTSUITE)' $(TESTSUITEFLAGS)

installcheck-local: atconfig atlocal $(TESTSUITE)
	$(SHELL) '$(TESTSUITE)' AUTOTEST_PATH='$(bindir)' \
          $(TESTSUITEFLAGS)

clean-local:
	test ! -f '$(TESTSUITE)' || \
         $(SHELL) '$(TESTSUITE)' --clean

AUTOM4TE = $(SHELL) $(top_srcdir)/missing --run autom4te
AUTOTEST = $(AUTOM4TE) --language=autotest
$(TESTSUITE): $(srcdir)/testsuite.at $(srcdir)/package.m4
	$(AUTOTEST) -I '$(srcdir)' -o $@.tmp $@.at
	mv $@.tmp $@


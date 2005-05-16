.PHONY : all clean distclean install

INSTALL_PREFIX=/usr/local
INSTALL_BINDIR=$(INSTALL_PREFIX)/bin
INSTALL_ASSEMBLYDIR=$(INSTALL_PREFIX)/lib/mwsearch

CFLAGS=-codepage:utf8

LIBS=\
  libs/ICSharpCode.SharpZipLib.dll \
  libs/log4net.dll \
  libs/Lucene.Net.dll \
  libs/MySql.Data.dll \
  libs/Nini.dll

ASSEMBLIES=build/MWSearch.dll build/MWPrefix.dll build/MWDaemon.exe build/MWUpdater.exe
SCRIPTS=build/MWDaemon.sh build/MWUpdater.sh




all: $(ASSEMBLIES) $(SCRIPTS)

clean :
	rm -f build/*.dll build/*.exe build/*.sh

distclean : clean

install: all
	install -d $(INSTALL_ASSEMBLYDIR)
	install $(ASSEMBLIES) $(INSTALL_ASSEMBLYDIR)
	install $(LIBS) $(INSTALL_ASSEMBLYDIR)
	install -m 0755 build/MWDaemon.sh $(INSTALL_BINDIR)/MWDaemon
	install -m 0755 build/MWUpdater.sh $(INSTALL_BINDIR)/MWUpdater
	echo "Don't forget to set up /etc/mwsearch.conf"

uninstall :
	rm -f $(INSTALL_BINDIR)/MWDaemon
	rm -f $(INSTALL_BINDIR)/MWUpdater
	rm -f $(INSTALL_ASSEMBLYDIR)/*.dll
	rm -f $(INSTALL_ASSEMBLYDIR)/*.exe
	rmdir $(INSTALL_ASSEMBLYDIR) || true


# MWSearch.dll: core code
SEARCH_SOURCES=\
  Search/Article.cs \
  Search/AssemblyInfo.cs \
  Search/Configuration.cs \
  Search/EnglishAnalyzer.cs \
  Search/EsperantoAnalyzer.cs \
  Search/SearchState.cs

# External libraries used by the core
SEARCH_REFERENCES=\
  -r:libs/log4net.dll \
  -r:libs/Lucene.Net.dll \
  -r:libs/Nini.dll

build/MWSearch.dll : $(SEARCH_SOURCES)
	mcs -target:library -out:build/MWSearch.dll \
	    $(CSFLAGS) \
        $(SEARCH_REFERENCES) \
        $(SEARCH_SOURCES)


# MWPrefix.dll: title prefix matcher. UNFINISHED.
PREFIX_SOURCES=Prefix/PrefixMatcher.cs
build/MWPrefix.dll : $(PREFIX_SOURCES)
	mcs -target:library -out:build/MWPrefix.dll \
	    $(CSFLAGS) \
	    -r:build/MWSearch.dll \
        $(SEARCH_REFERENCES) \
        -r:System.Data \
        -r:Mono.Data.SqliteClient \
        $(PREFIX_SOURCES)

# MWDaemon.exe: search daemon
DAEMON_SOURCES=\
  Daemon/AssemblyInfo.cs \
  Daemon/Daemon.cs \
  Daemon/NamespaceFilter.cs \
  Daemon/QueryStringMap.cs \
  Daemon/Worker.cs

build/MWDaemon.exe : build/MWSearch.dll build/MWPrefix.dll $(DAEMON_SOURCES)
	mcs -target:exe -out:build/MWDaemon.exe \
	    $(CSFLAGS) \
	    -r:build/MWSearch.dll -r:build/MWPrefix.dll \
	    $(SEARCH_REFERENCES) \
	    -r:System.Web \
        $(DAEMON_SOURCES)

build/MWDaemon.sh :
	echo "#!/bin/sh" > build/MWDaemon.sh
	echo "mono $(INSTALL_ASSEMBLYDIR)/MWDaemon.exe \$$@" >> build/MWDaemon.sh


# MWUpdater.exe: database refresher
UPDATER_SOURCES=\
  Updater/ArticleIterator.cs \
  Updater/ArticleList.cs \
  Updater/AssemblyInfo.cs \
  Updater/DatabaseConnection.cs \
  Updater/Updater.cs

build/MWUpdater.exe : build/MWSearch.dll build/MWPrefix.dll $(UPDATER_SOURCES)
	mcs -target:exe -out:build/MWUpdater.exe \
	    $(CSFLAGS) \
	    -r:build/MWSearch.dll -r:build/MWPrefix.dll \
	    $(SEARCH_REFERENCES) \
	    -r:System.Data \
        -r:libs/MySql.Data.dll \
        $(UPDATER_SOURCES)

build/MWUpdater.sh :
	echo "#!/bin/sh" > build/MWUpdater.sh
	echo "mono $(INSTALL_ASSEMBLYDIR)/MWUpdater.exe \$$@" >> build/MWUpdater.sh

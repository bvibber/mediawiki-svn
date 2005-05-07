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

ASSEMBLIES=build/MWSearch.dll build/MWPrefix.dll build/MWUpdater.exe
SCRIPTS=build/MWUpdater.sh




all: $(ASSEMBLIES) $(SCRIPTS)

clean :
	rm -f build/*.dll build/*.exe build/*.sh

distclean : clean

install: all
	install -d $(INSTALL_ASSEMBLYDIR)
	install $(ASSEMBLIES) $(INSTALL_ASSEMBLYDIR)
	install $(LIBS) $(INSTALL_ASSEMBLYDIR)
	install -m 0755 build/MWUpdater.sh $(INSTALL_BINDIR)/MWUpdater
	echo "Don't forget to set up /etc/mwsearch.conf"

# MWSearch.dll: core code
SEARCH_SOURCES=\
Search/Article.cs \
Search/AssemblyInfo.cs \
Search/Configuration.cs \
Search/EnglishAnalyzer.cs \
Search/EsperantoAnalyzer.cs \
Search/SearchState.cs

build/MWSearch.dll : $(SEARCH_SOURCES)
	mcs -target:library -out:build/MWSearch.dll \
	    $(CSFLAGS) \
        -r:libs/log4net.dll -r:libs/Lucene.Net.dll -r:libs/Nini.dll \
        $(SEARCH_SOURCES)


# MWPrefix.dll: title prefix matcher. UNFINISHED.
PREFIX_SOURCES=Prefix/PrefixMatcher.cs
build/MWPrefix.dll : $(PREFIX_SOURCES)
	mcs -target:library -out:build/MWPrefix.dll \
	    $(CSFLAGS) \
	    -r:build/MWSearch.dll \
        -r:libs/log4net.dll -r:libs/Lucene.Net.dll -r:libs/Nini.dll \
        -r:System.Data \
        -r:Mono.Data.SqliteClient \
        $(PREFIX_SOURCES)

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
	    -r:libs/log4net.dll -r:libs/Lucene.Net.dll -r:libs/MySql.Data.dll -r:libs/Nini.dll \
	    -r:System.Data \
        $(UPDATER_SOURCES)

build/MWUpdater.sh :
	echo "#!/bin/sh" > build/MWUpdater.sh
	echo "mono $(INSTALL_ASSEMBLYDIR)/MWUpdater.exe \$$@" >> build/MWUpdater.sh

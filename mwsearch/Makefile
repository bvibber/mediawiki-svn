.PHONY : all clean distclean install

INSTALL_PREFIX=/usr/local
INSTALL_BINDIR=$(INSTALL_PREFIX)/bin
INSTALL_ASSEMBLYDIR=$(INSTALL_PREFIX)/lib/mwsearch

CFLAGS=-codepage:utf8
MCS?=mcs
MONO?=mono

LIBS=\
  build/log4net.dll \
  build/Lucene.Net.dll \
  build/Nini.dll \
  build/CookComputing.XmlRpc.dll \
  build/mwdumper.dll

ASSEMBLIES=\
  build/MWSearch.dll \
  build/MWPrefix.dll \
  build/MWDaemon.exe \
  build/MWBenchmark.exe \
  build/MWUpdateDaemon.exe \
  build/MWSearchTool.exe

SCRIPTS=\
  build/MWDaemon.sh \
  build/MWBenchmark.sh \
  build/MWUpdateDaemon.sh \
  build/MWSearchTool.sh

CONFIG=\
  build/MWUpdateDaemon.exe.config

all: $(ASSEMBLIES) $(SCRIPTS) $(CONFIG)

clean :
	rm -f build/*.dll build/*.exe build/*.sh build/*.config

distclean : clean

install: all
	install -d $(INSTALL_ASSEMBLYDIR)
	install $(ASSEMBLIES) $(CONFIG) $(INSTALL_ASSEMBLYDIR)
	install $(LIBS) $(INSTALL_ASSEMBLYDIR)
	install -m 0755 build/MWDaemon.sh $(INSTALL_BINDIR)/MWBenchmark
	install -m 0755 build/MWDaemon.sh $(INSTALL_BINDIR)/MWDaemon
	install -m 0755 build/MWUpdateDaemon.sh $(INSTALL_BINDIR)/MWUpdateDaemon
	install -m 0755 build/MWSearchTool.sh $(INSTALL_BINDIR)/MWSearchTool
	echo "Don't forget to set up /etc/mwsearch.conf"

uninstall :
	rm -f $(INSTALL_BINDIR)/MWBenchmark
	rm -f $(INSTALL_BINDIR)/MWDaemon
	rm -f $(INSTALL_BINDIR)/MWUpdateDaemon
	rm -f $(INSTALL_BINDIR)/MWSearchTool
	rm -f $(INSTALL_ASSEMBLYDIR)/*.dll
	rm -f $(INSTALL_ASSEMBLYDIR)/*.exe
	rmdir $(INSTALL_ASSEMBLYDIR) || true


# MWBenchmark.exe: quickie search benchmark tool
# Not reliant on any of the other code.
BENCHMARK_SOURCES=\
  Benchmark/AssemblyInfo.cs \
  Benchmark/Benchmark.cs \
  Benchmark/SampleTerms.cs

build/MWBenchmark.exe : $(BENCHMARK_SOURCES)
	mcs -target:exe -out:build/MWBenchmark.exe \
	    $(CSFLAGS) \
	    -r:System.Web \
        $(BENCHMARK_SOURCES)

build/MWBenchmark.sh :
	echo "#!/bin/sh" > build/MWBenchmark.sh
	echo "exec mono $(INSTALL_ASSEMBLYDIR)/MWBenchmark.exe \$$@" >> build/MWBenchmark.sh


# MWSearch.dll: core code
SEARCH_SOURCES=\
  Search/Article.cs \
  Search/AssemblyInfo.cs \
  Search/Configuration.cs \
  Search/EnglishAnalyzer.cs \
  Search/EsperantoAnalyzer.cs \
  Search/SearchPool.cs \
  Search/SearchReader.cs \
  Search/SearchState.cs \
  Search/SearchWriter.cs

# External libraries used by the core
SEARCH_REFERENCES=\
  -r:build/log4net.dll \
  -r:build/Lucene.Net.dll \
  -r:build/Nini.dll

SEARCH_LIBS=\
  build/log4net.dll \
  build/Lucene.Net.dll \
  build/Nini.dll

build/MWSearch.dll : $(SEARCH_SOURCES) $(SEARCH_LIBS)
	mcs -target:library -out:build/MWSearch.dll \
	    $(CSFLAGS) \
        $(SEARCH_REFERENCES) \
        $(SEARCH_SOURCES)


# MWPrefix.dll: title prefix matcher. UNFINISHED.
PREFIX_SOURCES=Prefix/PrefixMatcher.cs
build/MWPrefix.dll : $(PREFIX_SOURCES) build/MWSearch.dll
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
	echo "exec mono $(INSTALL_ASSEMBLYDIR)/MWDaemon.exe \$$@" >> build/MWDaemon.sh


# MWUpdateDaemon.exe: database refresher, daemon version
UPDATEDAEMON_SOURCES=\
  UpdateDaemon/AssemblyInfo.cs \
  UpdateDaemon/Daemon.cs \
  UpdateDaemon/UpdateRecord.cs \
  UpdateDaemon/UpdateThread.cs

UPDATEDAEMON_LIBS=\
  build/MWSearch.dll \
  build/CookComputing.XmlRpc.dll

UPDATEDAEMON_REFERENCES=\
  -r:build/MWSearch.dll \
  -r:build/CookComputing.XmlRpc.dll \
  $(SEARCH_REFERENCES)

build/MWUpdateDaemon.exe : $(UPDATEDAEMON_LIBS) $(UPDATEDAEMON_SOURCES)
	mcs -target:exe -out:build/MWUpdateDaemon.exe \
	    $(CSFLAGS) \
	    $(UPDATEDAEMON_REFERENCES) \
        $(UPDATEDAEMON_SOURCES)

build/MWUpdateDaemon.sh :
	echo "#!/bin/sh" > build/MWUpdateDaemon.sh
	echo "mono $(INSTALL_ASSEMBLYDIR)/MWUpdateDaemon.exe \$$@" >> build/MWUpdateDaemon.sh

build/MWUpdateDaemon.exe.config :
	cp -p UpdateDaemon/app.config build/MWUpdateDaemon.exe.config


# MWSearchTool.exe: command-line tool to optimize indexes and such
SEARCHTOOL_SOURCES=\
  SearchTool/AssemblyInfo.cs \
  SearchTool/SearchTool.cs \
  SearchTool/SearchImporter.cs

SEARCHTOOL_LIBS=\
  build/MWSearch.dll \
  build/mwdumper.dll \
  build/IKVM.GNU.Classpath.dll \
  build/IKVM.Runtime.dll

SEARCHTOOL_REFERENCES=\
  -r:build/MWSearch.dll \
  -r:build/mwdumper.dll \
  -r:build/IKVM.GNU.Classpath.dll \
  -r:build/IKVM.Runtime.dll \
  $(SEARCH_REFERENCES)

build/MWSearchTool.exe : $(SEARCHTOOL_LIBS) $(SEARCHTOOL_SOURCES)
	mcs -target:exe -out:$@ \
	    $(CSFLAGS) \
	    $(SEARCHTOOL_REFERENCES) \
        $(SEARCHTOOL_SOURCES)

build/MWSearchTool.sh :
	echo "#!/bin/sh" > $@
	echo "mono $(INSTALL_ASSEMBLYDIR)/MWSearchTool.exe \$$@" >> $@


build/ICSharpCode.SharpZipLib.dll: libs/ICSharpCode.SharpZipLib.dll
	cp -p libs/ICSharpCode.SharpZipLib.dll build/

build/log4net.dll: libs/log4net.dll
	cp -p libs/log4net.dll build/

build/Lucene.Net.dll: libs/Lucene.Net.dll
	cp -p libs/Lucene.Net.dll build/

build/Nini.dll: libs/Nini.dll
	cp -p libs/Nini.dll build/

build/CookComputing.XmlRpc.dll: libs/CookComputing.XmlRpc.dll
	cp -p libs/CookComputing.XmlRpc.dll build/

build/IKVM.GNU.Classpath.dll : libs/IKVM.GNU.Classpath.dll
	cp -p libs/IKVM.GNU.Classpath.dll build/

build/IKVM.Runtime.dll : libs/IKVM.Runtime.dll
	cp -p libs/IKVM.Runtime.dll build/

build/mwdumper.dll : libs/mwdumper.jar
	$(MONO) libs/ikvmc.exe -out:$@ -target:library -exclude:libs/mwdumper-no-ikvm.txt libs/mwdumper.jar

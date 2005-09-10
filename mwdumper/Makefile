.PHONY : all clean distclean install

INSTALL_PREFIX=/usr/local
INSTALL_BINDIR=$(INSTALL_PREFIX)/bin
INSTALL_ASSEMBLYDIR=$(INSTALL_PREFIX)/lib/mwdumper

MCS?=mcs
CSFLAGS=-codepage:utf8

LIBS=\
  build/ICSharpCode.SharpZipLib.dll

ASSEMBLIES=\
  build/MediaWiki.Import.dll \
  build/mwdumper.exe


SOURCES_IMPORT=\
  mwimport/AssemblyInfo.cs \
  mwimport/Contributor.cs \
  mwimport/ExactListFilter.cs \
  mwimport/IDumpWriter.cs \
  mwimport/LatestFilter.cs \
  mwimport/ListFilter.cs \
  mwimport/MultiWriter.cs \
  mwimport/NamespaceFilter.cs \
  mwimport/NotalkFilter.cs \
  mwimport/Page.cs \
  mwimport/PageFilter.cs \
  mwimport/Revision.cs \
  mwimport/Siteinfo.cs \
  mwimport/SqlWriter14.cs \
  mwimport/SqlWriter15.cs \
  mwimport/SqlWriter.cs \
  mwimport/Title.cs \
  mwimport/TitleMatchFilter.cs \
  mwimport/XmlDumpReader.cs \
  mwimport/XmlDumpWriter.cs

LIBS_IMPORT=\
  build/ICSharpCode.SharpZipLib.dll

REFS_IMPORT=\
  /r:build/ICSharpCode.SharpZipLib.dll


SOURCES_DUMPER=\
  mwdumper/AssemblyInfo.cs \
  mwdumper/Dumper.cs

LIBS_DUMPER=\
  build/MediaWiki.Import.dll

REFS_DUMPER=\
  /r:build/MediaWiki.Import.dll \
  $(REFS_IMPORT)

SCRIPTS=\
  build/mwdumper.sh

all: $(ASSEMBLIES) $(SCRIPTS)

clean:
	rm -f build/*.dll build/*.exe build/*.sh

distclean : clean

install: all
	install -d $(INSTALL_ASSEMBLYDIR)
	install $(ASSEMBLIES) $(INSTALL_ASSEMBLYDIR)
	install $(LIBS) $(INSTALL_ASSEMBLYDIR)
	install -m 0755 build/mwdumper.sh $(INSTALL_BINDIR)/mwdumper

uninstall :
	rm -f $(INSTALL_BINDIR)/mwdumper
	rm -f $(INSTALL_ASSEMBLYDIR)/*.dll
	rm -f $(INSTALL_ASSEMBLYDIR)/*.exe
	rmdir $(INSTALL_ASSEMBLYDIR) || true


build/MediaWiki.Import.dll : $(SOURCES_IMPORT) $(LIBS_IMPORT)
	$(MCS) /target:library \
		/out:$@ \
		$(CSFLAGS) \
		$(REFS_IMPORT) \
		$(SOURCES_IMPORT)

build/mwdumper.exe : $(SOURCES_DUMPER) $(LIBS_DUMPER)
	$(MCS) /target:exe \
		/out:$@ \
		$(CSFLAGS) \
		$(REFS_DUMPER) \
		$(SOURCES_DUMPER)

build/ICSharpCode.SharpZipLib.dll : libs/ICSharpCode.SharpZipLib.dll
	cp -p libs/ICSharpCode.SharpZipLib.dll build/ICSharpCode.SharpZipLib.dll

build/mwdumper.sh :
	echo "#!/bin/sh" > $@
	echo "exec mono $(INSTALL_ASSEMBLYDIR)/mwdumper.exe \$$@" >> $@

.PHONY : all clean dist distclean install uninstall rpm bundle

VERSION=0.0.1

INSTALL_PREFIX?=/usr/local
INSTALL_BINDIR=$(INSTALL_PREFIX)/bin
INSTALL_ASSEMBLYDIR=$(INSTALL_PREFIX)/lib/mwdumper

PACKAGE_PREFIX?=$(INSTALL_PREFIX)
PACKAGE_BINDIR=$(PACKAGE_PREFIX)/bin
PACKAGE_ASSEMBLYDIR=$(PACKAGE_PREFIX)/lib/mwdumper

MCS?=mcs
CSFLAGS=-codepage:utf8

LIBS=\
  build/ICSharpCode.SharpZipLib.dll

ASSEMBLIES=\
  build/mwdumper.exe \
  build/MediaWiki.Import.dll

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

TMPDIST=mwdumper-$(VERSION)
DISTDIRS=build libs mwdumper mwimport
MISCFILES=Makefile mwdumper.spec README \
  mwdumper/mwdumper.mds mwdumper/mwdumper.mdp \
  mwimport/mwimport.mds mwdumper/mwdumper.mdp \
  libs/ICSharpCode.SharpZipLib.dll \
  libs/COPYING.SharpZipLib.txt \
  libs/Readme.SharpZipLib.rtf

DISTFILES=$(SOURCES_IMPORT) $(SOURCES_DUMPER) $(MISCFILES)

all: $(ASSEMBLIES) $(SCRIPTS)

clean:
	rm -f build/*.dll build/*.exe build/*.sh build/mwdumper

distclean : clean
	rm -rf $(TMPDIST)
	rm -f $(TMPDIST).tar.gz

dist : $(DISTFILES) Makefile
	rm -rf $(TMPDIST)
	mkdir $(TMPDIST)
	for x in $(DISTDIRS); do mkdir $(TMPDIST)/$$x; done
	for x in $(DISTFILES); do cp -p $$x $(TMPDIST)/$$x; done
	tar zcvf $(TMPDIST).tar.gz $(TMPDIST)

rpm : dist
	cp $(TMPDIST).tar.gz /usr/src/redhat/SOURCES
	cp mwdumper.spec /usr/src/redhat/SPECS
	cd /usr/src/redhat/SPECS && rpmbuild -ba mwdumper.spec

bundle : build/mwdumper

install: all
	install -d $(PACKAGE_ASSEMBLYDIR)
	install $(ASSEMBLIES) $(PACKAGE_ASSEMBLYDIR)
	install $(LIBS) $(PACKAGE_ASSEMBLYDIR)
	install -d $(PACKAGE_BINDIR)
	install -m 0755 build/mwdumper.sh $(PACKAGE_BINDIR)/mwdumper

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

build/mwdumper : $(ASSEMBLIES) $(LIBS)
	mkbundle -o $@ --deps --static $(ASSEMBLIES) $(LIBS)
	strip $@

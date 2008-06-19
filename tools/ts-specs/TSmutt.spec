#
# Copyright (c) 2006 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.

%include Solaris.inc

Name:                TSmutt
Summary:             The mutt e-mail client
Version:             1.5.18
Source:              ftp://ftp.mutt.org/mutt/devel/mutt-%{version}.tar.gz
Patch1:              mutt-01-makefile.diff

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: TSslang
BuildRequires: TSgdbm
BuildRequires: TSgdbm-devel
Requires: TSslang
Requires: %{name}-root
Requires: TSgdbm

%package root
Summary:                 %{summary} - / filesystem
SUNW_BaseDir:            /
%include default-depend.inc

%prep
%setup -q -n mutt-%version
%patch1 -p0

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags -I/usr/include/idn -I%{_includedir}"
export LDFLAGS="%{_ldflags} -R/usr/sfw/lib -R%{_libdir} -L%{_libdir}"
export CPPFLAGS="-I/usr/sfw/include"

./configure --prefix=%{_prefix}  \
            --mandir=%{_mandir}  \
            --sysconfdir=%{_sysconfdir} \
	    --with-docdir=%{_docdir}/mutt \
	    --disable-nls \
	    --with-slang=/usr/lib \
	    --with-ssl=/usr/sfw \
	    --enable-pop \
	    --enable-imap \
            --enable-hcache \
            --without-qdbm

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT

gmake install DESTDIR=$RPM_BUILD_ROOT

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*.1
%dir %attr (0755, root, bin) %{_mandir}/man5
%{_mandir}/man5/*.5
%dir %attr (0755, root, other) %{_datadir}/doc
%{_datadir}/doc/*

%files root
%defattr (-, root, sys)
%dir %attr (0755, root, sys) %{_sysconfdir}
%{_sysconfdir}/*

%changelog
* Thu Jun 19 2008 - river@wikimedia.org
- modified for toolserver
* Sat May 17 2008 - river@wikimedia.org
- 1.5.18
* Wed May 14 2008 - river@wikimedia.org
- Add --enable-hcache to configure
- Depend on SFEgdbm(-devel)
* Tue Jan 01 2008 - Thomas Wagner
- bump to 1.5.17
* Sun Nov 25 2007 - Thomas Wagner
- PSARC 2007/167 "IDN" is found (new around build 73/74), add -I/usr/include/idn
* Sat May 26 2007 - dick@nagual.nl
- Corrected LDFLAGS setting
* Wed May 23 2007 - dick@nagual.nl
- Bump to v1.5.15 (devel)
* Mon May 21 2007 - dick@nagual.nl
- Added CPPFLAGS and LDFLAGS (/usr/sfw) to support openssl
  Without it ssl is not supported by mutt
- Added a patch for Makefile.in to change mime.types to mutt-mime.types
  to exclude a clash with a system mime.types in /etc
- Forced dependency from libcurses to slang
* Sun May 20 2007 - dick@nagual.nl
- Initial spec

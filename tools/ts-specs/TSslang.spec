#
# Copyright (c) 2006 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.

%include Solaris.inc

Name:                TSslang
Summary:             multi-platform programmer's library
Version:             2.1.4
Source:              ftp://ftp.fu-berlin.de/pub/unix/misc/slang/v2.1/slang-%{version}.tar.gz

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: SUNWpng

Requires: %name-root
%package root
Summary:                 %{summary} - / filesystem
SUNW_BaseDir:            /
%include default-depend.inc

%prep
%setup -q -n slang-%version

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CFLAGS="CC"
export CFLAGS="%optflags"
export LDFLAGS="%_ldflags -L../src/elfobjs"

./configure --prefix=%{_prefix}  \
            --mandir=%{_mandir} \
            --sysconfdir=%{_sysconfdir}

gmake -j$CPUS elf

%install
rm -rf $RPM_BUILD_ROOT
gmake install-elf DESTDIR=$RPM_BUILD_ROOT
#rm ${RPM_BUILD_ROOT}%{_libdir}/libslang.a

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so*
%dir %attr (0755, root, other) %{_libdir}/slang
%dir %attr (0755, root, other) %{_libdir}/slang/v2
%dir %attr (0755, root, other) %{_libdir}/slang/v2/modules
%{_libdir}/slang/v2/modules/*
%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, other) %{_datadir}/slsh
%{_datadir}/slsh/*.sl
%dir %attr (0755, root, other) %{_datadir}/slsh/local-packages
%dir %attr (0755, root, other) %{_datadir}/slsh/scripts
%{_datadir}/slsh/scripts/*
%dir %attr (0755, root, other) %{_datadir}/slsh/cmaps
%{_datadir}/slsh/cmaps/*
%dir %attr (0755, root, other) %{_datadir}/slsh/help
%{_datadir}/slsh/help/*
%dir %attr (0755, root, other) %{_datadir}/slsh/rline
%{_datadir}/slsh/rline/*
%dir %attr (0755, root, other) %{_datadir}/doc
%dir %attr (0755, root, other) %{_datadir}/doc/slang
%dir %attr (0755, root, other) %{_datadir}/doc/slang/v2
%{_datadir}/doc/slang/v2/*
%dir %attr (0755, root, other) %{_datadir}/doc/slsh
%dir %attr (0755, root, other) %{_datadir}/doc/slsh/html
%{_datadir}/doc/slsh/html/*.html
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*

%files root
%defattr (-, root, sys)
%dir %attr (0755, root, sys) %{_sysconfdir}
%{_sysconfdir}/slsh.rc

%changelog
* Wed Oct  8 2008 - river@wikimedia.org
- 2.1.4
* Thu Jun 19 2008 - river@wikimedia.org
- modified for toolserver
* Fri May 02 2008 - ananth@sun.com
- Bump to 2.1.3
* Mon May 21 2007 - dick@nagual.nl
- Bump to 2.0.7
* Thu Dec 14 2006 - Eric Boutilier
- Initial spec

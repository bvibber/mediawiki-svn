#
# Copyright (c) 2006 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.

%include Solaris.inc
%define cc_is_gcc 1
%include base.inc

Name:                TSlftp
Summary:             Sophisticated file transfer program
Version:             3.7.3
Source:              http://ftp.yars.free.net/pub/source/lftp/lftp-%{version}.tar.gz

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: TSreadline
Requires: TSreadline
Requires: %{name}-root

%package root
Summary:                 %{summary} - / filesystem
SUNW_BaseDir:            /
%include default-depend.inc

%prep
%setup -q -n lftp-%version

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="gcc"
export CXX="g++"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags} -R/usr/sfw/lib -L%{_libdir} -R%{_libdir}"
export CPPFLAGS="-I/usr/sfw/include -I%{_includedir}"

./configure --prefix=%{_prefix}  \
            --mandir=%{_mandir}  \
            --sysconfdir=%{_sysconfdir} \
	    --disable-nls \
	    --with-openssl=/usr/sfw \

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
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, sys) %{_datadir}/lftp
%{_datadir}/lftp/*
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*.1

%files root
%defattr (-, root, sys)
%dir %attr (0755, root, sys) %{_sysconfdir}
%{_sysconfdir}/*

%changelog
* Fri Sep 26 2008 - river@wikimedia.org
- modified for ts-specs
* Tue Apr 29 2008 - river@wikimedia.org
- Initial spec

#
# Copyright 2007 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.

%include Solaris.inc
%define cc_is_gcc 1

Name:			TSganglia
Summary:		Ganglia cluster monitor
Version:		3.1.2
Source:			%{sf_download}/ganglia/ganglia-%{version}.tar.gz
Source2:		gmetad.xml
Source3:		gmond.xml
SUNW_BaseDir:		%{_basedir}
BuildRoot:		%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: SUNWgccruntime
Requires: TSlibconfuse
BuildRequires: TSlibconfuse-devel

%package root
Summary:		%{summary} - / filesystem
SUNW_BaseDir:		/

%package devel
Summary:		%{summary} - development files
SUNW_BaseDir:		%{_basedir}
%include default-depend.inc

%package web
Summary:		%{summary} - PHP web interface
SUNW_BaseDir:		%{_basedir}

%package gmetad
Summary:		%{summary} - gmetad aggregation daemon
SUNW_BaseDir:		%{_basedir}
%include default-depend.inc
Requires: TSlibconfuse

%package gmetad-root
Summary:		%{summary} - gmetad aggregation daemon - / filesystem
SUNW_BaseDir:		/

%prep
%setup -q -n ganglia-%version

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

%include stdenv.inc
CFLAGS="$CFLAGS -std=c99"
%_configure --with-gmetad

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT
%include stdenv.inc

gmake install DESTDIR=$RPM_BUILD_ROOT

rm ${RPM_BUILD_ROOT}%{_libdir}/libganglia.la

mkdir -p $RPM_BUILD_ROOT%{_datadir}/ganglia
cp -r web $RPM_BUILD_ROOT%{_datadir}/ganglia/web

mkdir -p $RPM_BUILD_ROOT/var/svc/manifest/application
cp %SOURCE2 %SOURCE3 $RPM_BUILD_ROOT/var/svc/manifest/application

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_sbindir}/gmond
%{_bindir}/gstat
%{_bindir}/gmetric
%{_libdir}/*.so.*
%{_libdir}/ganglia

%files devel
%defattr (-, root, root)
%{_includedir}
%{_bindir}/ganglia-config
%{_libdir}/*.so

%files web
%defattr (-, root, root)
%{_datadir}/ganglia/web

%files gmetad
%defattr (-, root, root)
%{_sbindir}/gmetad

%files root
%defattr (-, root, root)
%dir %attr(0755, root, sys) /var
%dir %attr(0755, root, sys) /var/svc
%dir %attr(0755, root, sys) /var/svc/manifest
%dir %attr(0755, root, sys) /var/svc/manifest/application
%class(manifest) %attr (0644, root, sys) /var/svc/manifest/application/gmond.xml

%files gmetad-root
%defattr (-, root, root)
%dir %attr(0755, root, sys) /var
%dir %attr(0755, root, sys) /var/svc
%dir %attr(0755, root, sys) /var/svc/manifest
%dir %attr(0755, root, sys) /var/svc/manifest/application
%class(manifest) %attr (0644, root, sys) /var/svc/manifest/application/gmetad.xml

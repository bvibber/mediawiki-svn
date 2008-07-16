#
# Copyright (c) 2006 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.

%include Solaris.inc

Name:                TSmapnik
Summary:             C++/Python GIS Toolkit
Version:             0.5.1
Source:              mapnik-%{version}.tar.gz

Patch0: mapnik-01-globalhpp-stdint.diff
Patch1: mapnik-02-plugin-ltdl.diff
Patch2: mapnik-03-nosuncc.diff
Patch3: mapnik-04-rpath.diff
Patch4: mapnik-05-fPIC.diff
Patch5: mapnik-06-shapeindex-rpath.diff
Patch6: mapnik-07-boost_system.diff

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

%prep
%setup -q -n mapnik-%version
%patch0 -p0
%patch1 -p0
%patch2 -p0
%patch3 -p0
%patch4 -p0
%patch5 -p0
%patch6 -p0

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

python scons/scons.py \
	PROJ_INCLUDES=/opt/ts/include \
	PROJ_LIBS=/opt/ts/lib \
	BOOST_INCLUDES=/opt/boost/include/boost-1_35 \
	BOOST_TOOLKIT=gcc34 \
	BOOST_LIBS=/opt/boost/lib \
	PREFIX=/opt/ts \
	BINDINGS=''

%install
rm -rf $RPM_BUILD_ROOT

python scons/scons.py \
	PROJ_INCLUDES=/opt/ts/include \
	PROJ_LIBS=/opt/ts/lib \
	BOOST_INCLUDES=/opt/boost/include/boost-1_35 \
	BOOST_TOOLKIT=gcc34 \
	BOOST_LIBS=/opt/boost/lib \
	PREFIX=/opt/ts \
	BINDINGS='' \
	DESTDIR=$RPM_BUILD_ROOT \
	install

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*

%changelog
* Wed Jul 16 2008 - river@wikimedia.org
- initial spec

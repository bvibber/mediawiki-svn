%include Solaris.inc
%define cc_is_gcc 1

%ifarch amd64 sparcv9
%include arch64.inc
%use proj4_64 = proj4.spec
%endif

%include base.inc
%use proj4 = proj4.spec

SUNW_BaseDir:	%{_basedir}

Name:         	TSproj4
Summary:      	PROJ.4 - Cartographic Projections LibrarY
Version:        4.6.0
Release:      	1
Source:         http://download.osgeo.org/proj/proj-4.6.0.tar.gz
BuildRoot:		%{_tmppath}/proj-%{version}-build
%include default-depend.inc

%package devel
Summary: %{summary} - development files
SUNW_BaseDir: %{_basedir}
%include default-depend.inc
Requires: %name

%prep 
rm -rf %name-%version
mkdir %name-%version

%ifarch amd64 sparcv9
mkdir %name-%version/%_arch64
%proj4_64.prep -d %name-%version/%_arch64
%endif

mkdir %name-%version/%base_arch
%proj4.prep -d %name-%version/%base_arch

%build
%ifarch amd64 sparcv9
%proj4_64.build -d %name-%version/%_arch64
%endif

%proj4.build -d %name-%version/%base_arch

%install
rm -rf $RPM_BUILD_ROOT

%ifarch amd64 sparcv9
%proj4_64.install -d %name-%version/%_arch64
%endif

%proj4.install -d %name-%version/%base_arch

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/cs2cs
%{_bindir}/geod
%{_bindir}/invgeod
%{_bindir}/invproj
%{_bindir}/nad2bin
%{_bindir}/nad2nad
%{_bindir}/proj
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*.so.*
%ifarch amd64 sparcv9
%dir %attr (0755, root, bin) %{_bindir}/%_arch64
%{_bindir}/%_arch64/*
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/*.so.*
%endif

%files devel
%defattr(-,root,bin)
%dir %attr (0755, root, sys) %{_datadir}
%{_datadir}/man
%dir %attr (0755, root, other) %{_datadir}/proj
%{_datadir}/proj/*

%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*.so
%ifarch amd64 sparcv9
%dir %attr (0755, root, bin) %{_libdir}/%{_arch64}
%{_libdir}/%{_arch64}/*.so
%endif

%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*

%changelog
* Wed Jul 16 2008 - river@wikimedia.org
- initial spec

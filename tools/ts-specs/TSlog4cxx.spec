%include Solaris.inc

%ifarch amd64 sparcv9
%include arch64.inc
%use log4cxx_64 = log4cxx.spec
%endif

%include base.inc
%use log4cxx = log4cxx.spec

%define src_name	apache-log4cxx
%define src_version	0.10.0

SUNW_BaseDir:	%{_basedir}

Name:         	TSlog4cxx
Summary:        Apache Logging services for C++
Version:      	%{src_version}
Release:      	%{pkg_release}
License:      	Apache
Source:         http://www.smudge-it.co.uk/pub/apache/logging/log4cxx/%{version}/apache-log4cxx-%{version}.tar.gz
BuildRoot:		%{_tmppath}/%{src_name}-%{version}-build
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
export APRBIN=%{_prefix}/bin/%_arch64
mkdir %name-%version/%_arch64
%log4cxx_64.prep -d %name-%version/%_arch64
%endif

export APRBIN=%{_prefix}/bin
mkdir %name-%version/%base_arch
%log4cxx.prep -d %name-%version/%base_arch

%build
%ifarch amd64 sparcv9
export APRBIN=%{_prefix}/bin/%_arch64
%log4cxx_64.build -d %name-%version/%_arch64
%endif

export APRBIN=%{_prefix}/bin
%log4cxx.build -d %name-%version/%base_arch

%install
rm -rf $RPM_BUILD_ROOT

%ifarch amd64 sparcv9
%log4cxx_64.install -d %name-%version/%_arch64
%endif

%log4cxx.install -d %name-%version/%base_arch

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*.so.*
%ifarch amd64 sparcv9
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/*.so.*
%endif

%files devel
%defattr(-,root,bin)

%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*
%dir %attr (0755, root, other) %{_libdir}/pkgconfig
%{_libdir}/pkgconfig/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*.so
%ifarch amd64 sparcv9
%dir %attr (0755, root, bin) %{_libdir}/%{_arch64}
%{_libdir}/%{_arch64}/*.so
%dir %attr (0755, root, other) %{_libdir}/%_arch64/pkgconfig
%{_libdir}/%_arch64/pkgconfig/*
%endif

%changelog
* Wed Jul  9 2008 - river@wikimedia.org
- initial spec

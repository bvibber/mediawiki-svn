%include Solaris.inc

%ifarch amd64 sparcv9
%include arch64.inc
%use apu_64 = apu.spec
%endif

%include base.inc
%use apu = apu.spec

%define src_name	apu
%define src_version	1.3.2

SUNW_BaseDir:	%{_basedir}

Name:         	TSapu
Summary:        Apache Portable Runtime utilities
Version:      	%{src_version}
Release:      	%{pkg_release}
License:      	Apache
Source:         http://mirrors.dedipower.com/ftp.apache.org/apr/apr-util-%{version}.tar.gz
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
export APR_CONFIG=%{_prefix}/bin/%_arch64/apr-1-config
export INCDIR=%{_includedir}/%_arch64
mkdir %name-%version/%_arch64
%apu_64.prep -d %name-%version/%_arch64
%endif

mkdir %name-%version/%base_arch
export APR_CONFIG=%{_prefix}/bin/apr-1-config
export INCDIR=%{_includedir}/%base_arch
%apu.prep -d %name-%version/%base_arch

%build
%ifarch amd64 sparcv9
export APR_CONFIG=%{_prefix}/bin/%_arch64/apr-1-config
export INCDIR=%{_includedir}/%_arch64
%apu_64.build -d %name-%version/%_arch64
%endif

export APR_CONFIG=%{_prefix}/bin/apr-1-config
export INCDIR=%{_includedir}/%base_arch
%apu.build -d %name-%version/%base_arch

%install
rm -rf $RPM_BUILD_ROOT

%ifarch amd64 sparcv9
%apu_64.install -d %name-%version/%_arch64
%endif

%apu.install -d %name-%version/%base_arch

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
%dir %attr (0755, root, other) %{_libdir}/%{_arch64}/pkgconfig
%{_libdir}/%{_arch64}/pkgconfig/*
%endif

%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*

%changelog
* Wed Jul  9 2008 - river@wikimedia.org
- initial spec

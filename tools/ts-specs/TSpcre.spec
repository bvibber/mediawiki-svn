%include Solaris.inc

%ifarch amd64 sparcv9
%include arch64.inc
%use pcre_64 = pcre.spec
%endif

%include base.inc
%use pcre = pcre.spec

%define src_name	pcre
%define src_version	7.7
%define pkg_release	1

SUNW_BaseDir:	%{_basedir}

Name:         	TS%{src_name}
Summary:      	PCRE - Perl Compatible Regular Expressions
Version:      	%{src_version}
Release:      	%{pkg_release}
License:      	BSD
Source:         %{sf_download}/pcre/%{src_name}-%{version}.tar.gz
Patch1:         pcre-01-cve-2008-2371
BuildRoot:		%{_tmppath}/%{src_name}-%{version}-build
Conflicts:      SUNWpcre
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
%pcre_64.prep -d %name-%version/%_arch64
%endif

mkdir %name-%version/%base_arch
%pcre.prep -d %name-%version/%base_arch

%build
%ifarch amd64 sparcv9
%pcre_64.build -d %name-%version/%_arch64
%endif

%pcre.build -d %name-%version/%base_arch

%install
rm -rf $RPM_BUILD_ROOT

%ifarch amd64 sparcv9
%pcre_64.install -d %name-%version/%_arch64
%endif

%pcre.install -d %name-%version/%base_arch

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
%dir %attr (0755, root, sys) %{_datadir}
%{_datadir}/man
%dir %attr (0755, root, other) %{_datadir}/doc
%{_datadir}/doc/*

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

%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*

%changelog
* Sun Jul  6 2008 - river@wikimedia.org
- modified for toolserver
* Fri Jan 11 2008 - moinak.ghosh@sun.com
- Add conflict with SUNWpcre, remove -i386 from package name
* Mon Oct 29 2007 - brian.cameron@sun.com
- Bump to 7.4 and fix Source URL.
* 2007.Aug.11 - <shivakumar dot gn at gmail dot com>
- Initial spec.

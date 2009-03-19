%include Solaris.inc

%include arch64.inc
%use pcre_64 = pcre.spec
%include base.inc
%use pcre = pcre.spec

Name:		%{pcre.name}
Summary:	PCRE - Perl Compatible Regular Expressions
Version:	%{pcre.version}
Source:		%{sf_download}/pcre/pcre-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

%package devel
Summary: %{summary} - development files
SUNW_BaseDir: %{_basedir}
%include default-depend.inc
Requires: %name

%prep 
rm -rf %name-%version
mkdir %name-%version

mkdir %name-%version/%_arch64
%pcre_64.prep -d %name-%version/%_arch64
mkdir %name-%version/%base_arch
%pcre.prep -d %name-%version/%base_arch

%build
%include arch64.inc
%include stdenv.inc
%pcre_64.build -d %name-%version/%_arch64
%include base.inc
%include stdenv.inc
%pcre.build -d %name-%version/%base_arch

%install
rm -rf $RPM_BUILD_ROOT

%pcre_64.install -d %name-%version/%_arch64
%pcre.install -d %name-%version/%base_arch

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-, root, root)
%{_libdir}/*.so.*
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/*.so.*

%files devel
%defattr(-, root, root)
%{_datadir}/man
%{_datadir}/doc

%{_includedir}
%{_libdir}/pkgconfig
%{_libdir}/*.so
%{_libdir}/%_arch64/*.so
%{_libdir}/%_arch64/pkgconfig
%{_bindir}

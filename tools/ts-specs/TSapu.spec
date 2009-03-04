%include Solaris.inc

%include arch64.inc
%use apu_64 = apu.spec

%include base.inc
%use apu = apu.spec

SUNW_BaseDir:	%{_basedir}

Name:         	%{apu.name}
Summary:        %{apu.summary}
Version:      	%{apu.version}
License:      	Apache
BuildRoot:		%{_tmppath}/apu-%{version}-build
%include default-depend.inc

Requires:	TSexpat
BuildRequires:	TSexpat-devel

%package devel
Summary: %{summary} - development files
SUNW_BaseDir: %{_basedir}
%include default-depend.inc
Requires: %name

%prep 
rm -rf %name-%version
mkdir %name-%version

export APR_CONFIG=%{_prefix}/bin/%_arch64/apr-1-config
export INCDIR=%{_includedir}/%base_arch
mkdir %name-%version/%_arch64
%apu_64.prep -d %name-%version/%_arch64

mkdir %name-%version/%base_arch
export APR_CONFIG=%{_prefix}/bin/apr-1-config
export INCDIR=%{_includedir}/%_arch64
%apu.prep -d %name-%version/%base_arch

%build
export APR_CONFIG=%{_prefix}/bin/%_arch64/apr-1-config
export INCDIR=%{_includedir}/%_arch64
%apu_64.build -d %name-%version/%_arch64

export APR_CONFIG=%{_prefix}/bin/apr-1-config
export INCDIR=%{_includedir}/%base_arch
%apu.build -d %name-%version/%base_arch

%install
rm -rf $RPM_BUILD_ROOT

%apu_64.install -d %name-%version/%_arch64
%apu.install -d %name-%version/%base_arch

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*.so.*
%dir %attr (0755, root, bin) %{_libdir}/apr-util-1
%{_libdir}/apr-util-1/*.so
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/*.so.*
%dir %attr (0755, root, bin) %{_libdir}/%_arch64/apr-util-1
%{_libdir}/%_arch64/apr-util-1/*.so

%files devel
%defattr(-,root,bin)

%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*
%dir %attr (0755, root, other) %{_libdir}/pkgconfig
%{_libdir}/pkgconfig/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*.so
%dir %attr (0755, root, bin) %{_libdir}/%{_arch64}
%{_libdir}/%{_arch64}/*.so
%dir %attr (0755, root, other) %{_libdir}/%{_arch64}/pkgconfig
%{_libdir}/%{_arch64}/pkgconfig/*
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*

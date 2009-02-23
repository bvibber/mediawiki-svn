%include Solaris.inc

%ifarch amd64 sparcv9
%include arch64.inc
%use apr_64 = apr.spec
%endif

%include base.inc
%use apr = apr.spec

SUNW_BaseDir:	%{_basedir}

Name:         	%{apr.name}
Summary:        %{apr.summary}
Version:      	%{apr.version}
License:      	Apache
BuildRoot:		%{_tmppath}/apr-%{version}-build
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
export INCDIR=%{_includedir}/%_arch64
mkdir %name-%version/%_arch64
%apr_64.prep -d %name-%version/%_arch64
%endif

export INCDIR=%{_includedir}/%{base_arch}
mkdir %name-%version/%base_arch
%apr.prep -d %name-%version/%base_arch

%build
%ifarch amd64 sparcv9
export INCDIR=%{_includedir}/%_arch64
%apr_64.build -d %name-%version/%_arch64
%endif

export INCDIR=%{_includedir}/%{base_arch}
%apr.build -d %name-%version/%base_arch

%install
rm -rf $RPM_BUILD_ROOT

%ifarch amd64 sparcv9
%apr_64.install -d %name-%version/%_arch64
%endif

%apr.install -d %name-%version/%base_arch

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
%dir %attr (0755, root, bin) %{_libdir}/apr-build-1
%{_libdir}/apr-build-1/*

%ifarch amd64 sparcv9
%dir %attr (0755, root, bin) %{_libdir}/%{_arch64}
%{_libdir}/%{_arch64}/*.so
%dir %attr (0755, root, bin) %{_libdir}/%{_arch64}/apr-build-1
%{_libdir}/%{_arch64}/apr-build-1/*
%dir %attr (0755, root, other) %{_libdir}/%{_arch64}/pkgconfig
%{_libdir}/%{_arch64}/pkgconfig/*
%endif

%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*

%changelog
* Mon Feb 23 2009 - river@loreley.flyingparchment.org.uk
- 1.3.3
* Wed Jul  9 2008 - river@wikimedia.org
- initial spec

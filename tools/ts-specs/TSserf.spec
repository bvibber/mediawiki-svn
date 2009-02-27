%include Solaris.inc

%ifarch amd64 sparcv9
%include arch64.inc
%use serf_64 = serf.spec
%endif

%include base.inc
%use serf = serf.spec

SUNW_BaseDir:	%{_basedir}

Name:         	%{serf.name}
Summary:        %{serf.summary}
Version:      	%{serf.version}
License:      	Apache
BuildRoot:		%{_tmppath}/serf-%{version}-build
%include default-depend.inc

BuildRequires: TSapr
Requires: TSapr
BuildRequires: TSapu
Requires: TSapu

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
export APR_UTIL_CONFIG=%{_prefix}/bin/%_arch64/apu-1-config
export INCDIR=%{_includedir}/%_arch64
mkdir %name-%version/%_arch64
%serf_64.prep -d %name-%version/%_arch64
%endif

mkdir %name-%version/%base_arch
export APR_CONFIG=%{_prefix}/bin/apr-1-config
export APR_UTIL_CONFIG=%{_prefix}/bin/apu-1-config
export INCDIR=%{_includedir}/%base_arch
%serf.prep -d %name-%version/%base_arch

%build
%ifarch amd64 sparcv9
export APR_CONFIG=%{_prefix}/bin/%_arch64/apr-1-config
export APR_UTIL_CONFIG=%{_prefix}/bin/%_arch64/apu-1-config
export INCDIR=%{_includedir}/%_arch64
%serf_64.build -d %name-%version/%_arch64
%endif

export APR_CONFIG=%{_prefix}/bin/apr-1-config
export APR_UTIL_CONFIG=%{_prefix}/bin/apu-1-config
export INCDIR=%{_includedir}/%base_arch
%serf.build -d %name-%version/%base_arch

%install
rm -rf $RPM_BUILD_ROOT

%ifarch amd64 sparcv9
%serf_64.install -d %name-%version/%_arch64
%endif

%serf.install -d %name-%version/%base_arch

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
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*.so
%{_libdir}/*.la
%ifarch amd64 sparcv9
%dir %attr (0755, root, bin) %{_libdir}/%{_arch64}
%{_libdir}/%{_arch64}/*.so
%{_libdir}/%{_arch64}/*.la
%endif

%changelog
* Mon Feb 23 2009 - river@loreley.flyingparchment.org.uk
- initial spec

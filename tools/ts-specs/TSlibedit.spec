%include Solaris.inc

%include arch64.inc
%use libedit64=libedit.spec
%include base.inc
%use libedit=libedit.spec

Name:                    %{libedit.name}
Summary:                 %{libedit.summary}
Version:                 %{libedit.version}
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%package devel
Summary:		 %{summary} - development files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:		 %name

%prep
rm -rf %name-%version
mkdir %name-%version

mkdir %name-%version/%_arch64
%libedit64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%libedit.prep -d %name-%version/%{base_arch}

%build
%include arch64.inc
%libedit64.build -d %name-%version/%_arch64

%include base.inc
%libedit.build -d %name-%version/%{base_arch}

%install
%libedit64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la

%libedit.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/*.a
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.a

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_libdir}/lib*.so.*
%{_libdir}/%_arch64/lib*.so.*
%{_mandir}/man5/*

%files devel
%defattr (-, root, root)
%{_includedir}/*.h
%{_includedir}/editline/*.h
%{_libdir}/*.so
%{_libdir}/%_arch64/*.so
%{_libdir}/pkgconfig/*.pc
%{_libdir}/%_arch64/pkgconfig/*.pc
%{_mandir}/man3/*

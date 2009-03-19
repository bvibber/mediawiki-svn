%include Solaris.inc

%include arch64.inc
%use pixman64=pixman.spec
%include base.inc
%use pixman=pixman.spec

Name:                    %{pixman.name}
Summary:                 %{pixman.summary}
Version:                 %{pixman.version}
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
%pixman64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%pixman.prep -d %name-%version/%{base_arch}

%build

%include arch64.inc
%include stdenv.inc
%pixman64.build -d %name-%version/%_arch64

%include base.inc
%include stdenv.inc
%pixman.build -d %name-%version/%{base_arch}

%install
%pixman64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.a

%pixman64.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/*.a
rm -rf $RPM_BUILD_ROOT%{_bindir}/%_arch64

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_libdir}/lib*.so.*
%{_libdir}/%_arch64/lib*.so*

%files devel
%defattr (-, root, root)
%{_libdir}/lib*.so
%{_libdir}/pkgconfig
%{_includedir}/pixman-1
%{_libdir}/%_arch64/lib*.so
%{_libdir}/%_arch64/pkgconfig

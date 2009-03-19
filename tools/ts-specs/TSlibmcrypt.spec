%include Solaris.inc

%include arch64.inc
%use libmcrypt64=libmcrypt.spec
%include base.inc
%use libmcrypt=libmcrypt.spec

Name:                    %{libmcrypt.name}
Summary:                 %{libmcrypt.summary}
Version:                 %{libmcrypt.version}
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

BuildRequires:	TSautomake
BuildRequires:	TSautoconf
BuildRequires: TSlibtool

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
%libmcrypt64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%libmcrypt.prep -d %name-%version/%{base_arch}

%build
%include arch64.inc
%include stdenv.inc
%libmcrypt64.build -d %name-%version/%_arch64
%include base.inc
%include stdenv.inc
%libmcrypt.build -d %name-%version/%{base_arch}

%install
%libmcrypt64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la

%libmcrypt.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/*.a
rm -f $RPM_BUILD_ROOT%{_libdir}/libltdl*
rm -f $RPM_BUILD_ROOT%{_libdir}/libmcrypt/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.a
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/libltdl*
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/libmcrypt/*.la
rm -rf $RPM_BUILD_ROOT%{_includedir}/libltdl
rm -rf $RPM_BUILD_ROOT%{_includedir}/ltdl.h
%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_libdir}/lib*.so*
%{_libdir}/libmcrypt/*.so
%{_libdir}/%_arch64/libmcrypt/*.so
%{_libdir}/%_arch64/lib*.so*

%files devel
%defattr (-, root, root)
%{_bindir}/libmcrypt-config
%{_bindir}/%_arch64
%{_includedir}
%{_datadir}/aclocal
%{_mandir}/man3

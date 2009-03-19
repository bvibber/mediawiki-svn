%include Solaris.inc

%include arch64.inc
%use pango64=pango.spec
%include base.inc
%use pango=pango.spec

Name:                    %{pango.name}
Summary:                 %{pango.summary}
Version:                 %{pango.version}
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

Requires: TScairo
BuildRequires: TScairo-devel

%package devel
Summary:		 %{summary} - development files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:	%name
Requires:	TScairo-devel

%prep
rm -rf %name-%version
mkdir %name-%version

mkdir %name-%version/%_arch64
%pango64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%pango.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

%include arch64.inc
%include stdenv.inc
%pango64.build -d %name-%version/%_arch64

%include base.inc
%include stdenv.inc
%pango.build -d %name-%version/%{base_arch}

%install
%include stdenv.inc
%pango64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.a

%include stdenv.inc
%pango.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/*.a

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%dir %{_bindir}
%{_bindir}/*
%dir %{_libdir}
%{_libdir}/lib*.so.*
%dir %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so.*
%dir %{_mandir}/man1
%{_mandir}/man1/*

%files devel
%defattr (-, root, root)
%dir %{_libdir}
%{_libdir}/lib*.so
%dir %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so
%{_datadir}/gtk-doc
%{_libdir}/pkgconfig
%{_libdir}/%_arch64/pkgconfig
%{_libdir}/pango
%{_libdir}/%_arch64/pango
%{_includedir}/pango-1.0

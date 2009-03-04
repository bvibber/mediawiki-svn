%include Solaris.inc

%include arch64.inc
%use cairo64=cairo.spec
%include base.inc
%use cairo=cairo.spec

Name:                    %{cairo.name}
Summary:                 %{cairo.summary}
Version:                 %{cairo.version}
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

Requires:	TSpixman
BuildRequires:	TSpixman-devel

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
%cairo64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%cairo.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

export CC="cc"
export CXX="CC"

%include arch64.inc
export CFLAGS="%optflags"
export RPM_OPT_FLAGS="$CFLAGS"
export LDFLAGS="%_ldflags -L/usr/sfw/lib/%_arch64 -R/usr/sfw/lib/%_arch64:%{_libdir}"
export PKG_CONFIG_PATH=/opt/ts/lib/%_arch64/pkgconfig:/usr/lib/%_arch64/pkgconfig:/usr/sfw/lib/%_arch64/pkgconfig
%cairo64.build -d %name-%version/%_arch64

%include base.inc
export LDFLAGS="%_ldflags -L/usr/sfw/lib -R/usr/sfw/lib:%{_libdir}"
export CFLAGS="%optflags"
export RPM_OPT_FLAGS="$CFLAGS"
export PKG_CONFIG_PATH=/opt/ts/lib/pkgconfig:/usr/lib/pkgconfig:/usr/sfw/lib/pkgconfig
%cairo.build -d %name-%version/%{base_arch}

%install
%cairo64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.a

%cairo64.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/*.a
rm -rf $RPM_BUILD_ROOT%{_bindir}/%_arch64

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so.*
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so*

%files devel
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so
%dir %attr (0755, root, other) %{_libdir}/pkgconfig
%{_libdir}/pkgconfig/*
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, other) %{_datadir}/gtk-doc
%{_datadir}/gtk-doc/*
%dir %attr (0755, root, bin) %{_includedir}
%dir %attr (0755, root, bin) %{_includedir}/cairo
%{_includedir}/cairo/*
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so
%dir %attr (0755, root, other) %{_libdir}/%_arch64/pkgconfig
%{_libdir}/%_arch64/pkgconfig/*

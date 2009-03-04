%include Solaris.inc

%include arch64.inc
%use glib64=glib.spec
%include base.inc
%use glib=glib.spec

Name:                    %{glib.name}
Summary:                 %{glib.summary}
Version:                 %{glib.version}
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
%glib64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%glib.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

export CC="cc"
export CXX="CC"

%include arch64.inc
export PATH=/opt/ts/bin/%_arch64:/opt/ts/bin:/opt/SUNWspro/bin:/usr/ccs/bin:/usr/bin
export CFLAGS="%optflags"
export RPM_OPT_FLAGS="$CFLAGS"
export LDFLAGS="%_ldflags -L%{_libdir} -L/usr/sfw/lib/%_arch64 -R%{_libdir}:/usr/sfw/lib/%_arch64"
export CPPFLAGS="-I%{_includedir}"
%glib64.build -d %name-%version/%_arch64

%include base.inc
export PATH=/opt/ts/bin:/opt/SUNWspro/bin:/usr/ccs/bin:/usr/bin
export LDFLAGS="%_ldflags -L%{_libdir} -L/usr/sfw/lib -R%{_libdir}:/usr/sfw/lib"
export CFLAGS="%optflags"
export CPPFLAGS="-I%{_includedir}"
export RPM_OPT_FLAGS="$CFLAGS"
%glib.build -d %name-%version/%{base_arch}

%install
%glib64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.a

%glib.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/*.a
rm -rf $RPM_BUILD_ROOT%{_bindir}/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/charset.alias
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/charset.alias

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so.*
%dir %attr (0755, root, bin) %{_libdir}/gio
%dir %attr (0755, root, bin) %{_libdir}/gio/modules
%dir %attr (0755, root, bin) %{_libdir}/%_arch64/gio
%dir %attr (0755, root, bin) %{_libdir}/%_arch64/gio/modules
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so.*
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*
%dir %attr(0755, root, other) %{_datadir}/locale
%dir %attr(0755, root, other) %{_datadir}/locale/*
%{_datadir}/locale/*/*
%dir %attr(0755, root, other) %{_datadir}/glib-2.0
%{_datadir}/glib-2.0/*

%files devel
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so
%dir %attr (0755, root, bin) %{_libdir}/glib-2.0
%{_libdir}/glib-2.0/*
%dir %attr (0755, root, bin) %{_libdir}/%_arch64/glib-2.0
%{_libdir}/%_arch64/glib-2.0/*
%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, other) %{_datadir}/gtk-doc
%{_datadir}/gtk-doc/*
%dir %attr (0755, root, other) %{_libdir}/pkgconfig
%{_libdir}/pkgconfig/*
%dir %attr (0755, root, other) %{_libdir}/%_arch64/pkgconfig
%{_libdir}/%_arch64/pkgconfig/*
%dir %attr(0755, root, other) %{_datadir}/aclocal
%{_datadir}/aclocal/*

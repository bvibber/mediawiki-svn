%include Solaris.inc

%include arch64.inc
%use pkgconfig64=pkgconfig.spec
%include base.inc
%use pkgconfig=pkgconfig.spec

Name:		TSpkgconfig
Summary:	pkgconfig
Version:	0.23
Source:		http://pkgconfig.freedesktop.org/releases/pkg-config-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

%prep
rm -rf %name-%version
mkdir %name-%version
 
mkdir %name-%version/%_arch64
%pkgconfig64.prep -d %name-%version/%_arch64
 
mkdir %name-%version/%{base_arch}
%pkgconfig.prep -d %name-%version/%{base_arch}

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
export LDFLAGS="%_ldflags -L%{_libdir} -L/usr/sfw/lib/%_arch64 -R%{_libdir}:/usr/sfw/lib/%_arch64"
export CPPFLAGS="-I%{_includedir}"
%pkgconfig64.build -d %name-%version/%_arch64
 
%include base.inc
export LDFLAGS="%_ldflags -L%{_libdir} -L/usr/sfw/lib -R%{_libdir}:/usr/sfw/lib"
export CFLAGS="%optflags"
export CPPFLAGS="-I%{_includedir}"
export RPM_OPT_FLAGS="$CFLAGS"
%pkgconfig.build -d %name-%version/%{base_arch}

%install
%pkgconfig64.install -d %name-%version/%_arch64
%pkgconfig.install -d %name-%version/%{base_arch}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/pkg-config
%dir %attr (0755, root, bin) %{_bindir}/%_arch64
%{_bindir}/%_arch64/pkg-config
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, other) %{_datadir}/aclocal
%{_datadir}/aclocal/*
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*


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
%pixman64.build -d %name-%version/%_arch64

%include base.inc
export LDFLAGS="%_ldflags -L/usr/sfw/lib -R/usr/sfw/lib:%{_libdir}"
export CFLAGS="%optflags"
export RPM_OPT_FLAGS="$CFLAGS"
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
%dir %attr (0755, root, bin) %{_includedir}
%dir %attr (0755, root, bin) %{_includedir}/pixman-1
%{_includedir}/pixman-1/*
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so
%dir %attr (0755, root, other) %{_libdir}/%_arch64/pkgconfig
%{_libdir}/%_arch64/pkgconfig/*

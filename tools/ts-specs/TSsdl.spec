# spec file for package TSlsdl

%include Solaris.inc

Name:                    TSsdl
Summary:                 Simple DirectMedia Library
Version:                 1.2.13
Source:                  http://www.libsdl.org/release/SDL-%{version}.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%ifarch amd64 sparcv9
%include arch64.inc
%use sdl64=sdl.spec
%endif
%include base.inc
%use sdl = sdl.spec

%package devel
Summary:		 %{summary} - development files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:		 %name

%prep
rm -rf %name-%version
mkdir %name-%version

%ifarch amd64 sparcv9
mkdir %name-%version/%_arch64
%sdl64.prep -d %name-%version/%_arch64
%endif

mkdir %name-%version/%{base_arch}
%sdl.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

export CC="cc"
export CXX="CC"
export CPPFLAGS="-I/usr/sfw/include"
export MSGFMT="/usr/bin/msgfmt"

%ifarch amd64 sparcv9
%include arch64.inc
export CFLAGS="%optflags -m64 -I/usr/sfw/include -DANSICPP -L/usr/sfw/lib/%_arch64"
export RPM_OPT_FLAGS="$CFLAGS"
export LDFLAGS="-m64 -L/usr/sfw/lib/%_arch64 -R/usr/sfw/lib/%_arch64"
%sdl64.build -d %name-%version/%_arch64
%endif
%include base.inc
export LDFLAGS="-L/usr/sfw/lib -R/usr/sfw/lib"
export CFLAGS="%optflags -I/usr/sfw/include -DANSICPP -L/usr/sfw/lib"
export RPM_OPT_FLAGS="$CFLAGS"
%sdl.build -d %name-%version/%{base_arch}

%install
%ifarch amd64 sparcv9
%sdl64.install -d %name-%version/%_arch64
%endif

%sdl.install -d %name-%version/%{base_arch}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so.*
%ifarch amd64 sparcv9
%{_libdir}/%_arch64/*.so.*
%endif

%files devel
%defattr (-, root, bin)

%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/man3
%dir %attr(0755, root, bin) %{_mandir}/man3/*

%dir %attr(0755, root, other) %{_datadir}/aclocal
%{_datadir}/aclocal/*

%dir %attr(0755, root, bin) %{_bindir}
%{_bindir}/sdl-config

%ifarch amd64 sparcv9
%dir %attr(0755, root, bin) %{_bindir}/%_arch64
%{_bindir}/%_arch64/sdl-config
%endif

%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.a
%{_libdir}/lib*.so

%ifarch amd64 sparcv9
%{_libdir}/%_arch64/lib*.a
%{_libdir}/%_arch64/lib*.so
%endif

%dir %attr (0755, root, other) %{_libdir}/pkgconfig
%{_libdir}/pkgconfig/*

%ifarch amd64 sparcv9
%dir %attr(0755, root, other) %{_libdir}/%_arch64/pkgconfig
%{_libdir}/%_arch64/pkgconfig/*
%endif

%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*

%changelog
* Wed Dec 03 2008 - river@loreley.flyingparchment.org.uk
- new spec

#
# spec file for package TSdjvulibre

%include Solaris.inc

%include arch64.inc
%use djvu64=djvu.spec
%include base.inc
%use djvu=djvu.spec

Name:                    %{djvu.name}
Summary:                 %{djvu.summary}
Version:                 %{djvu.version}
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
%djvu64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%djvu.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

export CC="cc"
export CXX="CC"
export PTHREAD_CFLAGS=-mt
export PTHREAD_LIBS=-mt
export CPPFLAGS="-I/usr/sfw/include"
export MSGFMT="/usr/bin/msgfmt"

%include arch64.inc
export CFLAGS="%optflags -m64"
export CXXFLAGS="%cxx_optflags -m64"
export RPM_OPT_FLAGS="$CFLAGS"
export LDFLAGS="-m64 -L/usr/sfw/lib/%_arch64 -R/usr/sfw/lib/%_arch64:%{_libdir}"
%djvu64.build -d %name-%version/%_arch64
%
%include base.inc
export LDFLAGS="-L/usr/sfw/lib -R/usr/sfw/lib:%{_libdir}"
export CFLAGS="%optflags -DANSICPP"
export CXXFLAGS="%cxx_optflags"
export RPM_OPT_FLAGS="$CFLAGS"
%djvu.build -d %name-%version/%{base_arch}

%install
%djvu64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la

%djvu64.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -rf $RPM_BUILD_ROOT%{_bindir}/%_arch64

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so.*
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, sys) %{_datadir}/djvu
%{_datadir}/djvu/*
%dir %attr(0755, root, bin) %{_mandir}
%{_mandir}/*

%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so*

%files devel
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%dir %attr (0755, root, other) %{_libdir}/pkgconfig
%{_libdir}/lib*.so
%{_libdir}/pkgconfig/ddjvuapi.pc
%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*
%dir %attr(0755, root, sys) %{_datadir}

%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%dir %attr (0755, root, other) %{_libdir}/%_arch64/pkgconfig
%{_libdir}/%_arch64/pkgconfig/ddjvuapi.pc
%{_libdir}/%_arch64/lib*.so

%changelog
* Sun Oct  5 2008 - river@wikimedia.org
- initial spec

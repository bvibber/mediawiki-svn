#
# spec file for package TSlibiconv
#
# includes module(s): libiconv

%include Solaris.inc
%define cc_is_gcc 1
#%include base.inc

%ifarch amd64 sparcv9
%include arch64.inc
%use libiconv64=libiconv.spec
%endif
%include base.inc
%use libiconv=libiconv.spec

Name:                    %{libiconv.name}
Summary:                 %{libiconv.summary}
Version:                 %{libiconv.version}
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

%ifarch amd64 sparcv9
mkdir %name-%version/%_arch64
%libiconv64.prep -d %name-%version/%_arch64
%endif

mkdir %name-%version/%{base_arch}
%libiconv.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

export CC="gcc"

%ifarch amd64 sparcv9
%include arch64.inc
export CFLAGS="%gcc_optflags -m64 -I/usr/sfw/include -DANSICPP -L/usr/sfw/lib/%_arch64"
export RPM_OPT_FLAGS="$CFLAGS"
export LDFLAGS="-m64 -L/usr/sfw/lib/%_arch64 -R/usr/sfw/lib/%_arch64"
%libiconv64.build -d %name-%version/%_arch64
%endif
%include base.inc
export LDFLAGS="-L/usr/sfw/lib -R/usr/sfw/lib"
export CFLAGS="%gcc_optflags -I/usr/sfw/include -DANSICPP -L/usr/sfw/lib"
export RPM_OPT_FLAGS="$CFLAGS"
%libiconv.build -d %name-%version/%{base_arch}

%install
%ifarch amd64 sparcv9
%libiconv64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.a
%endif

%libiconv.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/*.a
%ifarch amd64 sparcv9
rm -rf $RPM_BUILD_ROOT%{_bindir}/%_arch64
%endif
%
%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/iconv
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*.so*
%{_libdir}/charset.alias
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*
%dir %attr(0755, root, other) %{_docdir}
%dir %attr(0755, root, other) %{_docdir}/libiconv
%{_docdir}/libiconv/*
%ifarch amd64 sparcv9
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/*.so*
%{_libdir}/%_arch64/charset.alias
%endif

%files devel
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_libdir}
%dir %attr(0755, root, bin) %{_mandir}/man3
%{_mandir}/man3/*

%changelog
* Mon Feb 23 2009 - river@loreley.flyingparchment.org.uk
- initial spec

%include Solaris.inc

%include arch64.inc
%use gettext64=gettext.spec
%include base.inc
%use gettext=gettext.spec

Name:			%{gettext.name}
Summary:		%{gettext.summary}
Version:		%{gettext.version}
SUNW_BaseDir:		%{_basedir}
BuildRoot:		%{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

BuildRequires: TSautomake

%package devel
Summary:		 %{summary} - development files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:		 %name

%prep
rm -rf %name-%version
mkdir %name-%version

mkdir %name-%version/%_arch64
%gettext64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%gettext.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

export CC="cc"
export CXX="CC"

%include arch64.inc
export CFLAGS="%optflags"
export CXXFLAGS="%cxx_optflags"
export RPM_OPT_FLAGS="$CFLAGS"
export LDFLAGS="%_ldflags -L/usr/sfw/lib/%_arch64 -R/usr/sfw/lib/%_arch64:%{_libdir} -m64"
%gettext64.build -d %name-%version/%_arch64

%include base.inc
export LDFLAGS="%_ldflags -L/usr/sfw/lib -R/usr/sfw/lib:%{_libdir}"
export CFLAGS="%optflags"
export CXXFLAGS="%cxx_optflags"
export RPM_OPT_FLAGS="$CFLAGS"
%gettext.build -d %name-%version/%{base_arch}

%install
%gettext64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.a

%gettext.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/*.a
rm -rf $RPM_BUILD_ROOT%{_bindir}/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/charset.alias
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/charset.alias

rm -f $RPM_BUILD_ROOT%{_datadir}/info/dir

%clean
rm -rf $RPM_BUILD_ROOT

%post
( echo 'PATH=/usr/bin:/usr/sfw/bin; export PATH' ;
  echo 'infos="';
  echo 'autosprintf gettext' ;
  echo '"';
  echo 'retval=0';
  echo 'for info in $infos; do';
  echo '  install-info --info-dir=%{_infodir} %{_infodir}/$info || retval=1';
  echo 'done';
  echo 'exit $retval' ) | $PKG_INSTALL_ROOT/usr/lib/postrun -b -c TS

%preun
( echo 'PATH=/usr/bin:/usr/sfw/bin; export PATH' ;
  echo 'infos="';
  echo 'autosprintf gettext' ;
  echo '"';
  echo 'for info in $infos; do';
  echo '  install-info --info-dir=%{_infodir} --delete %{_infodir}/$info';
  echo 'done';
  echo 'exit 0' ) | $PKG_INSTALL_ROOT/usr/lib/postrun -b -c TS

%files
%defattr (-, root, root)
%{_bindir}
%dir %{_libdir}
%{_libdir}/lib*.so.*
%{_libdir}/gettext
%dir %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so.*
%{_libdir}/%_arch64/gettext

%dir %{_datadir}
%{_datadir}/locale
%{_datadir}/info
%{_datadir}/doc
%{_datadir}/gettext
%dir %{_mandir}
%{_mandir}/man1

%files devel
%defattr (-, root, root)
%dir %{_datadir}
%{_datadir}/aclocal
%dir %{_mandir}
%{_mandir}/man3
%dir %{_libdir}
%{_libdir}/lib*.so
%dir %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so
%dir %{_includedir}
%{_includedir}/*

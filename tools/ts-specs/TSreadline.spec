#
# spec file for package TSreadline
#
# includes module(s): GNU readline
#
%include Solaris.inc

Name:                    TSreadline
Summary:                 GNU readline - library for editing typed command lines
Version:                 5.2
Source:			 http://ftp.gnu.org/gnu/readline/readline-%{version}.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc
Requires: SUNWpostrun
Requires: SUNWtexi

%package devel
Summary:                 %{summary} - development files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires: %name

%prep
%setup -q -c -n %name-%version
%ifarch amd64 sparcv9
cp -pr readline-%{version} readline-%{version}-64
%endif

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS32="%optflags -I/usr/sfw/include -DANSICPP"
export CFLAGS64="%optflags64 -I/usr/sfw/include -DANSICPP"
export LDFLAGS32="%_ldflags -lcurses"
export LDFLAGS64="%_ldflags -lcurses"

%ifarch amd64 sparcv9
export CC=${CC64:-$CC}
export CXX=${CXX64:-$CXX}
export CFLAGS="$CFLAGS64"
export CXXFLAGS="$CXXFLAGS64"
export LDFLAGS="$LDFLAGS64"

cd readline-%{version}-64

./configure --prefix=%{_prefix}				\
	    --libdir=%{_libdir}/%{_arch64}		\
	    --libexecdir=%{_libexecdir}/%{_arch64}	\
	    --mandir=%{_mandir}                 	\
	    --datadir=%{_datadir}               	\
            --infodir=%{_datadir}/info
	    		
gmake -j$CPUS
cd ..
%endif

cd readline-%{version}

export CC=${CC32:-$CC}
export CFLAGS="$CFLAGS32"
export LDFLAGS="$LDFLAGS32"

./configure --prefix=%{_prefix}			\
	    --libdir=%{_libdir}                 \
	    --libexecdir=%{_libexecdir}         \
	    --mandir=%{_mandir}                 \
	    --datadir=%{_datadir}               \
            --infodir=%{_datadir}/info
	    		
gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT
%ifarch amd64 sparcv9
cd readline-%{version}-64
gmake install DESTDIR=$RPM_BUILD_ROOT
rm -f $RPM_BUILD_ROOT/%{_libdir}/%{_arch64}/*.a
rm -f $RPM_BUILD_ROOT/%{_libdir}/%{_arch64}/*.la
cd ..
%endif

cd readline-%{version}
gmake install DESTDIR=$RPM_BUILD_ROOT

mkdir -p $RPM_BUILD_ROOT%{_mandir}/man3gnu
sed -e 's/^\.TH \([^ ]*\) "*3"*/.TH \1 "3GNU"/' $RPM_BUILD_ROOT%{_mandir}/man3/history.3 > $RPM_BUILD_ROOT%{_mandir}/man3gnu/history.3
rm $RPM_BUILD_ROOT%{_mandir}/man3/history.3
rm $RPM_BUILD_ROOT%{_datadir}/info/dir

rm $RPM_BUILD_ROOT%{_libdir}/lib*a

%clean
rm -rf $RPM_BUILD_ROOT

%post
( echo 'PATH=/usr/bin:/usr/sfw/bin; export PATH' ;
  echo 'infos="';
  echo 'readline.info history.info rluserman.info' ;
  echo '"';
  echo 'retval=0';
  echo 'for info in $infos; do';
  echo '  install-info --info-dir=%{_infodir} %{_infodir}/$info || retval=1';
  echo 'done';
  echo 'exit $retval' ) | $PKG_INSTALL_ROOT/usr/lib/postrun -b -c TS

%preun
( echo 'PATH=/usr/bin:/usr/sfw/bin; export PATH' ;
  echo 'infos="';
  echo 'readline.info history.info rluserman.info' ;
  echo '"';
  echo 'for info in $infos; do';
  echo '  install-info --info-dir=%{_infodir} --delete %{_infodir}/$info';
  echo 'done';
  echo 'exit 0' ) | $PKG_INSTALL_ROOT/usr/lib/postrun -b -c TS

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so*
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, bin) %{_datadir}/info
%{_datadir}/info/*
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/*
%{_mandir}/*/*
%ifarch amd64 sparcv9
%dir %attr (0755, root, bin) %{_libdir}/%{_arch64}
%{_libdir}/%{_arch64}/lib*.so*
%endif

%files devel
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*

%changelog
* Fri Sep 26 2008 - river@wikimedia.org
- modified for ts-specs
* Mon May 14 2007 - dougs@truemail.co.th
- Forced to link with libcurses
* Tue Mar  7 2007 - dougs@truemail.co.th
- enabled 64-bit build
* Mon Jan 15 2007 - daymobrew@users.sourceforge.net
- Add SUNWtexi dependency.
* Sun Nov  5 2006 - laca@sun.com
- Create

#
# Copyright (c) 2006 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.

%include Solaris.inc

Name:                TSlibtool
Summary:             Generic library support script
Version:             2.2.4
Source:              http://ftp.gnu.org/gnu/libtool/libtool-%{version}.tar.gz
Patch1:              libtool-01-bash.diff
SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: SUNWbash
Requires: SUNWpostrun

%prep
%setup -q -n libtool-%version
%patch1 -p1 -b .patch01

%build
%include stdenv.inc
%_configure 
%_make -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT
%_make install DESTDIR=$RPM_BUILD_ROOT
rm $RPM_BUILD_ROOT%{_libdir}/libltdl.la
rm $RPM_BUILD_ROOT%{_datadir}/info/dir

%clean
rm -rf $RPM_BUILD_ROOT

%post
( echo 'PATH=/usr/bin:/usr/sfw/bin; export PATH' ;
  echo 'infos="';
  echo 'libtool.info' ;
  echo '"';
  echo 'retval=0';
  echo 'for info in $infos; do';
  echo '  install-info --info-dir=%{_infodir} %{_infodir}/$info || retval=1';
  echo 'done';
  echo 'exit $retval' ) | $PKG_INSTALL_ROOT/usr/lib/postrun -b -c TS

%preun
( echo 'PATH=/usr/bin:/usr/sfw/bin; export PATH' ;
  echo 'infos="';
  echo 'libtool.info' ;
  echo '"';
  echo 'for info in $infos; do';
  echo '  install-info --info-dir=%{_infodir} --delete %{_infodir}/$info';
  echo 'done';
  echo 'exit 0' ) | $PKG_INSTALL_ROOT/usr/lib/postrun -b -c TS

%files
%defattr (-, root, root)
%{_bindir}
%{_libdir}/lib*.so*
%{_includedir}
%{_datadir}/info
%{_datadir}/aclocal
%{_datadir}/libtool

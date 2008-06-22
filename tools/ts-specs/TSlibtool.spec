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
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%_ldflags"

./configure \
    --prefix=%{_prefix} \
    --infodir=%{_infodir}

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT
gmake install DESTDIR=$RPM_BUILD_ROOT
rm $RPM_BUILD_ROOT%{_libdir}/libltdl.a  
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
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so*
%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*.h
%dir %attr (0755, root, bin) %{_includedir}/libltdl
%{_includedir}/libltdl/*.h
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_datadir}/info
%{_datadir}/info/libtool.info
%{_datadir}/info/libtool.info-1
%dir %attr (0755, root, other) %{_datadir}/aclocal
%{_datadir}/aclocal/*
%dir %attr (0755, root, other) %{_datadir}/libtool
%{_datadir}/libtool/*

%changelog
* Sun Jun 22 2008 - river@wikimedia.org
- modified for toolserver
* Sat May 24 2008 - Mark Wright <markwright@internode.on.net>
- Bump to 2.2.4.  Add patch1 to use bash.
* Sun Mar 2 2008 - Mark Wright <markwright@internode.on.net>
- Bump to 1.5.26.
* Thu Mar 22 2007 - nonsea@users.sourceforge.net
- Bump to 1.5.24
- Use http url in Source.
* Thu Mar 22 2007 - nonsea@users.sourceforge.net
- Add Requires/BuildRequries after check-deps.pl run.
* Mon Jan 15 2007 - daymobrew@users.sourceforge.net
- Add SUNWtexi dependency.
* Sun Jan  7 2007 - laca@sun.com
- fix infodir permissions, update info dir file using postrun scripts
* Wed Dec 20 2006 - Eric Boutilier
- Initial spec

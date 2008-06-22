# Copyright 2006 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.
#

%include Solaris.inc

Name:			TSautomake
Summary:                GNU automake
%define minmaj          1.10
Version:		%{minmaj}.1
Vendor:			Sun Microsystems, Inc.
Source:			ftp://ftp.gnu.org/pub/gnu/automake/automake-%{version}.tar.bz2
SUNW_BaseDir:		%{_basedir}
BuildRoot:		%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc
Requires:               SUNWperl584core
Requires: SUNWgm4
Requires:               SUNWpostrun

%prep
%setup -q -n automake-%{version}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
  CPUS=1
fi
CFLAGS="$RPM_OPT_FLAGS"			\
./configure \
    --prefix=%{_prefix}         \
    --infodir=%{_infodir}
gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT
gmake install DESTDIR=$RPM_BUILD_ROOT

# make aclocal always look for macros in /usr/share/aclocal
test "%{_datadir}" = "/usr/share" || \
    echo "/usr/share/aclocal" > $RPM_BUILD_ROOT%{_datadir}/aclocal-%{minmaj}/dirlist

# Uncomment the following if %{_datadir} is not /usr/share
mkdir -p $RPM_BUILD_ROOT%{_datadir}/aclocal-%{minmaj}

rm $RPM_BUILD_ROOT%{_infodir}/dir

%clean
rm -rf $RPM_BUILD_ROOT

%post
( echo 'PATH=/usr/bin:/usr/sfw/bin; export PATH' ;
  echo 'infos="';
  echo 'automake.info automake.info-1 automake.info-2' ;
  echo '"';
  echo 'retval=0';
  echo 'for info in $infos; do';
  echo '  install-info --info-dir=%{_infodir} %{_infodir}/$info || retval=1';
  echo 'done';
  echo 'exit $retval' ) | $PKG_INSTALL_ROOT/usr/lib/postrun -b -c TS

%preun
( echo 'PATH=/usr/bin:/usr/sfw/bin; export PATH' ;
  echo 'infos="';
  echo 'automake.info automake.info-1 automake.info-2' ;
  echo '"';
  echo 'for info in $infos; do';
  echo '  install-info --info-dir=%{_infodir} --delete %{_infodir}/$info';
  echo 'done';
  echo 'exit 0' ) | $PKG_INSTALL_ROOT/usr/lib/postrun -b -c TS

%files
%defattr(-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, sys) %{_datadir}
%{_datadir}/aclocal-*
%{_datadir}/automake-*
%dir %attr (0755, root, other) %{_datadir}/doc
%{_datadir}/doc/automake/amhello-1.0.tar.gz
%dir %attr (0755, root, bin) %{_infodir}
%{_infodir}/*

# Uncomment the following if %{_datadir} is not /usr/share
#%dir %attr (0755, root, other) %{_datadir}/aclocal
#%{_datadir}/aclocal-%{minmaj}/*

%changelog
* Sun Jun 22 2008 - river@wikimedia.org
- modified for toolserver
* Sun Mar 2 2008 - Mark Wright <markwright@internode.on.net>
- Bump to 1.10.1.
* Wed Oct 17 2007 - laca@sun.com
- add support for building with either SFEm4 or SUNWgm4
* Wed Jul 24 2007 - markwright@internode.on.net
- Bump to 1.10
* Sat Apr 21 2007 - dougs@truemail.co.th
- Changed Required Gnu m4 from SUNWgm4
* Sat Jan  6 2006 - laca@sun.com
- add /usr/gnu/share/aclocal to the aclocal search path
- install info files and update info dir file using postrun scripts
* Wed Nov 15 2006  <eric.boutilier@sun.com>
- Copied and transposed CBEautomake to SFEautomake
* Tue Aug 22 2006  <laca@sun.com>
- fix %files attributes
- move to /opt/jdsbld by default
* Wed Aug 16 2006  <laca@sun.com>
- add missing deps
* Tue Oct 18 2005 - <laca@sun.com>
- add /usr/share/aclocal to the default search path
* Wed Aug 31 2005 - <laca@sun.com>
- update to 1.9.6
* Sun Sep 05 2004 - <laca@sun.com>
- enable parallel build
* Fri Mar 05 2004 - <laca@sun.com>
- fix %files
- change the pkg category

#
# spec file for package TScoreutils
#
# includes module(s): GNU coreutils
#

%include Solaris.inc
%include opt-gnu.inc

%define _prefix /opt/gnu

Name:                    TScoreutils
Summary:                 GNU coreutils - basic file, shell and text manipulation utilities
Version:		 6.12
Source:                  http://ftp.gnu.org/pub/gnu/coreutils/coreutils-%{version}.tar.gz
Patch1:                  coreutils-01-configure.diff
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc
BuildConflicts: SUNWgnu-coreutils
Requires: SUNWlibms
Requires: SUNWtexi
Requires: SUNWpostrun
Requires: SUNWuiu8


%if %build_l10n
%package l10n
Summary:                 %{summary} - l10n files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:                %{name}
%endif

%prep
%setup -q -n coreutils-%version
%patch1 -p0

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

# export PATH=/usr/bin:$PATH
export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%_ldflags"
export LIBS="-lm"

./configure --prefix=%{_prefix}			\
	    --mandir=%{_mandir}                 \
            --infodir=%{_infodir}

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT
make install DESTDIR=$RPM_BUILD_ROOT

cd $RPM_BUILD_ROOT%{_prefix}
ln -s share/man man

rm -f $RPM_BUILD_ROOT%{_infodir}/dir

%if %build_l10n
%else
# REMOVE l10n FILES
rm -rf $RPM_BUILD_ROOT%{_datadir}/locale
%endif

%clean
rm -rf $RPM_BUILD_ROOT

%post
( echo 'PATH=/usr/bin:/usr/sfw/bin; export PATH' ;
  echo 'infos="';
  echo 'coreutils.info' ;
  echo '"';
  echo 'retval=0';
  echo 'for info in $infos; do';
  echo '  install-info --info-dir=%{_infodir} %{_infodir}/$info || retval=1';
  echo 'done';
  echo 'exit $retval' ) | $PKG_INSTALL_ROOT/usr/lib/postrun -b -c TS

%preun
( echo 'PATH=/usr/bin:/usr/sfw/bin; export PATH' ;
  echo 'infos="';
  echo 'coreutils.info' ;
  echo '"';
  echo 'for info in $infos; do';
  echo '  install-info --info-dir=%{_infodir} --delete %{_infodir}/$info';
  echo 'done';
  echo 'exit 0' ) | $PKG_INSTALL_ROOT/usr/lib/postrun -b -c TS

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_prefix}
%{_prefix}/man
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*
%dir %attr(0755, root, bin) %{_infodir}
%{_infodir}/*

%if %build_l10n
%files l10n
%defattr (-, root, bin)
%dir %attr (0755, root, sys) %{_datadir}
%attr (-, root, other) %{_datadir}/locale
%endif

%changelog
* Thu Jun 19 2008 - river@wikimedia.org
- modified for toolserver
* Sun Nov 18 2007 - daymobrew@users.sourceforge.net
- Add BuildConflicts SUNWgnu-coreutils, a package that is available on Indiana
  systems.
* Fri Jul 13 2007 - dougs@truemail.co.th
- Bump to 6.9
* Sat Apr 21 2007 - dougs@truemail.co.th
- Use gmake rather than /usr/bin/make
* Mon Jan 15 2007 - daymobrew@users.sourceforge.net
- Add SUNWtexi dependency.
* Sat Jan  6 2007 - laca@sun.com
- update for latest /usr/gnu proposal
- add postrun script for updating info dir
* Fri Jan 05 2007 - daymobrew@users.sourceforge.net
- Bump to 6.7.
* Tue Jun 27 2006 - laca@sun.com
- Initial spec

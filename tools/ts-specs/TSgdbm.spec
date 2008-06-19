#
# spec file for package TSgdbm
#
# includes module(s): gdbm
#

%include Solaris.inc

Name:         TSgdbm
Summary:      GNU Database Routines
Group:        libraries/database
Version:      1.8.3
License:      GPL
Group:        Development/Libraries/C and C++
Release:      1
BuildRoot:    %{_tmppath}/gdbm-%{version}-build
Source0:      http://ftp.gnu.org/gnu/gdbm/gdbm-%{version}.tar.gz
URL:          http://directory.fsf.org/gdbm.html
Patch1:       gdbm-01-fixmake.diff
SUNW_BaseDir: %{_basedir}
%include default-depend.inc

%description
GNU database routines

%package devel
Summary:                 %{summary} - development files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires: %name

%prep
%setup -q -n gdbm-%version
%patch1 -p1

%build
export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%_ldflags"

CFLAGS="$CFLAGS $RPM_OPT_FLAGS"         \
        ./configure                     \
                --prefix=%{_prefix}     \
                --infodir=%{_datadir}/info \
                --mandir=%{_mandir}     \
                --libdir=%{_libdir}     \
                --disable-static

%install
rm -rf $RPM_BUILD_ROOT
gmake INSTALL_ROOT=$RPM_BUILD_ROOT install

rm -f $RPM_BUILD_ROOT%{_libdir}/lib*.la

%{?pkgbuild_postprocess: %pkgbuild_postprocess -v -c "%{version}:%{jds_version}:%{name}:$RPM_ARCH:%(date +%%Y-%%m-%%d):%{support_level}" $RPM_BUILD_ROOT}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_datadir}/info
%{_datadir}/info/*

%files devel
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/man3
%{_mandir}/man3/*

%changelog
* Thu Jun 19 2008 - river@wikimedia.org
- modified for toolserver
* Fri Jun 23 2006 - laca@sun.com
- rename to SFEgdbm
- delete -share subpkg
- update file attributes
* Fri May 05 2006 - damien.carbery@sun.com
- Remove unnecessary intltoolize call.
* Wed Mar 08 2006 - brian.cameron@sun.com
- Created.

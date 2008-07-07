#
# spec file for package TSpython-setuptools
#
# Copyright 2008 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.
#
# Owner: dkenny
#

%include Solaris.inc

%define oname setuptools
%define name python-%oname
%define version 0.6.8
%define tarball_version 0.6c8

Name:            TS%{name}
Summary:         Download, build, install, upgrade, and uninstall Python packages easily
URL:             http://peak.telecommunity.com/DevCenter/setuptools
Version:         %{version}
Source0:         http://cheeseshop.python.org/packages/source/s/%{oname}/%{oname}-%{tarball_version}.tar.gz
SUNW_BaseDir:    %{_basedir}
BuildRoot:       %{_tmppath}/%{name}-%{version}-build
BuildRequires:   SUNWPython-devel
Requires:        SUNWPython

%include default-depend.inc

%define pythonver 2.4

%prep
%setup -q -n %oname-%tarball_version

%build
python setup.py build
perl -pi -e 's|^#!python|#!/usr/bin/python|' easy_install.py setuptools/command/easy_install.py

%install
rm -rf $RPM_BUILD_ROOT
python setup.py install --prefix=$RPM_BUILD_ROOT/%_prefix --old-and-unmanageable

%{?pkgbuild_postprocess: %pkgbuild_postprocess -v -c "%{tarball_version}:%{jds_version}:%{name}:$RPM_ARCH:%(date +%%Y-%%m-%%d):%{support_level}" $RPM_BUILD_ROOT}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/easy_install
%{_bindir}/easy_install-2.4
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/python%{pythonver}/site-packages/setuptools-%{tarball_version}-py%{pythonver}.egg-info
%{_libdir}/python%{pythonver}/site-packages/setuptools/*
%{_libdir}/python%{pythonver}/site-packages/pkg_resources.pyc
%{_libdir}/python%{pythonver}/site-packages/easy_install.pyc
%{_libdir}/python%{pythonver}/site-packages/site.pyc
%{_libdir}/python%{pythonver}/site-packages/pkg_resources.py
%{_libdir}/python%{pythonver}/site-packages/easy_install.py
%{_libdir}/python%{pythonver}/site-packages/site.py

%changelog
* Mon Jul  7 2008 - river@wikimedia.org
- modified for toolserver
* Wed May 14 2008 - darren.kenny@sun.com
- Add SUWNPython dependency.
* Mon May 05 2008 - brian.cameron@sun.com
- Bump to 0.6.8
* Tue Mar 11 2008 - damien.carbery@sun.com
- Use %tarball_version as appropriate in %files and %pre and %install.
* Fri Mar 07 2008 - damien.carbery@sun.com
- Change package version to be numeric.
* Tue Feb 12 2008 - dermot.mccluskey@sun.com
- initial version

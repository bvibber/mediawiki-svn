#
# spec file for package TSbzr
#
# includes module(s): bzr
#
%include Solaris.inc

%define python_version 2.4

Name:			TSbzr
Summary:		Bazaar Source Code Management System
License:		GPL
Group:			system/dscm
Version:		1.5
Distribution:		spec-files-extra
Vendor:			http://pkgbuild.sf.net/spec-files-extra
Source:			http://launchpad.net/bzr/%{version}/%{version}/+download/bzr-%{version}.tar.gz
URL:			http://bazaar-vcs.org
BuildRoot:		%{_tmppath}/%{name}-%{version}-build
SUNW_BaseDir:		%{_prefix}
Requires: SUNWPython
%include default-depend.inc
BuildRequires: SUNWPython-devel


%description
Bazaar source code management system.

%prep
%setup -q -n bzr-%{version}

%build
export PYTHON="/usr/bin/python"
CFLAGS="$RPM_OPT_FLAGS"
python setup.py build

%install
rm -rf $RPM_BUILD_ROOT
python setup.py install --prefix=$RPM_BUILD_ROOT%{_prefix}

# Delete optimized py code
find $RPM_BUILD_ROOT%{_prefix} -type f -name "*.pyo" -exec rm -f {} ';'
mkdir -p $RPM_BUILD_ROOT%{_datadir}
mv $RPM_BUILD_ROOT%{_prefix}/man $RPM_BUILD_ROOT%{_mandir}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%dir %attr (0755, root, bin) %{_libdir}/python%{python_version}
%dir %attr (0755, root, bin) %{_libdir}/python%{python_version}/site-packages
%{_libdir}/python%{python_version}/site-packages/*
%dir %attr (0755, root, sys) %{_datadir}
%{_mandir}/man1/bzr.1

%changelog
* Sat Jun 22 2008 - river@wikimedia.org
- modified for toolserver
* Wed Jan  3 2007 - laca@sun.com
- bump to 0.13
* Mon Jun 12 2006 - laca@sun.com
- rename to SFEbzr
- change to root:bin to follow other JDS pkgs.
* Sat Jan 7 2006  <glynn.foster@sun.com>
- initial version

%include Solaris.inc

%define python_version 2.4

Name:		TSpython-beaker
URL:		http://pypi.python.org/pypi/Beaker
Summary:	Beaker middleware
Version:	1.0.1
Source:		http://pypi.python.org/packages/source/B/Beaker/Beaker-1.0.1.tar.gz
SUNW_BaseDir:	/opt/ts
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: SUNWPython-devel
Requires: SUNWPython

%prep
%setup -q -n Beaker-%version

%build
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages
export PYTHONPATH=$PYTHONPATH:$RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages
#/usr/bin/python%{python_version} ./setup.py install --prefix=$RPM_BUILD_ROOT%{_prefix} --single-version-externally-managed --record=$RPM_BUILD_ROOT%{_libdir}/python%python_version}/site-packages/pathlist
/usr/bin/python%{python_version} ./setup.py install --prefix=%{_prefix} --root=$RPM_BUILD_ROOT --single-version-externally-managed --record=$RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages/pathlist.beaker
rm -f $RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages/site.py
rm -f $RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages/site.pyc
rm -f $RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages/easy-install.pth

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*

%changelog
* Tue 16 Sep 2008 - river@wikimedia.org
- initial spec

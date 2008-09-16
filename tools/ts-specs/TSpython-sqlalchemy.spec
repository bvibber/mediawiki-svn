%include Solaris.inc

%define python_version 2.4

Name:		TSpython-sqlalchemy
URL:		http://pypi.python.org/pypi/Beaker
Summary:	SQL Alchemy
Version:	0.4.7p1
Source:		http://pypi.python.org/packages/source/S/SQLAlchemy/SQLAlchemy-0.4.7p1.tar.gz
SUNW_BaseDir:	/opt/ts
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: SUNWPython-devel
Requires: SUNWPython

%prep
%setup -q -n SQLAlchemy-%version

%build
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages
export PYTHONPATH=$PYTHONPATH:$RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages
/usr/bin/python%{python_version} ./setup.py install --prefix=%{_prefix} --root=$RPM_BUILD_ROOT --single-version-externally-managed --record=$RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages/pathlist.sqlalchemy
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

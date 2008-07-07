%include Solaris.inc

%define python_version 2.4

Name:		TSpython-flup
URL:		http://trac.saddi.com/flup
Summary:	random Python WSGI stuff
Version:	1.0
Source:		http://www.saddi.com/software/flup/dist/flup-%{version}.tar.gz
SUNW_BaseDir:	/opt/ts
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: SUNWPython-devel
Requires: SUNWPython

%prep
%setup -q -n flup-%version

%build
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages
export PYTHONPATH=$PYTHONPATH:$RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages
/usr/bin/python%{python_version} ./setup.py install --prefix=$RPM_BUILD_ROOT%{_prefix}
rm -f $RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages/site.py
rm -f $RPM_BUILD_ROOT%{_libdir}/python%{python_version}/site-packages/site.pyc

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*

%changelog
* Mon Jul  7 2008 - river@wikimedia.org
- initial spec

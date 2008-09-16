%include Solaris.inc

%define python_version 2.4

Name:		TSpython-cheetah
URL:		http://www.cheetahtemplate.org/
Summary:	Cheetah template engine
Version:	2.0.1
Source:		http://mesh.dl.sourceforge.net/sourceforge/cheetahtemplate/Cheetah-%{version}.tar.gz
SUNW_BaseDir:	/opt/ts
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: SUNWPython-devel
Requires: SUNWPython

%prep
%setup -q -n Cheetah-%version

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
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*

%changelog
* Tue 16 Sep 2008 - river@wikimedia.org
- initial spec

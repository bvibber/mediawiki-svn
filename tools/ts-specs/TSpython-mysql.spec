#
# spec file for package TSpython-mysql
#
# includes module(s): mysql
#
%include Solaris.inc

Name:                    TSpython-mysql
Summary:                 A MySQL database adapter for the Python programming language
URL:                     http://sourceforge.net/projects/mysql-python
Version:                 1.2.2
Source:                  %{sf_download}/mysql-python/MySQL-python-%{version}.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc
BuildRequires:           SUNWPython-devel
BuildRequires:           TSpython-setuptools
Requires:                SUNWPython
Requires:                SUNWmysqlu

%define python_version  2.4

%prep
%setup -q -n MySQL-python-%{version}

%build
#export CC=cc
#export CXX=CC
export LDFLAGS="-R/opt/mysql/lib"
export PATH="/opt/mysql/bin:$PATH"
python setup.py build

%install
rm -rf $RPM_BUILD_ROOT
python setup.py install --root=$RPM_BUILD_ROOT --prefix=%{_prefix} --no-compile

%{?pkgbuild_postprocess: %pkgbuild_postprocess -v -c "%{version}:%{jds_version}:%{name}:$RPM_ARCH:%(date +%%Y-%%m-%%d):%{support_level}" $RPM_BUILD_ROOT}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/python%{python_version}/site-packages/

%changelog
* Sun Sep 02 2007 - Ananth Shrinivas <ananth@sun.com>
- Initial Version

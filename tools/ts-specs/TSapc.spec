%include Solaris.inc

Name:                TSapc
Summary:             Alternative PHP Cache
Version:             3.0.19
Source:              http://pecl.php.net/get/APC-%{version}.tgz

SUNW_BaseDir:        /opt/php
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: TSphp
BuildRequires: TSautoconf

%prep
%setup -q -n APC-%version

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags}"
export PATH=/opt/php/bin:$PATH

phpize
./configure --with-php-config=/opt/php/bin/php-config
gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT

gmake install INSTALL_ROOT=$RPM_BUILD_ROOT

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, sys) /opt/php/lib
/opt/php/lib/php/extensions/no-debug-non-zts-20060613/apc.so

%changelog
* Sat Jun 21 2008 - river@wikimedia.org
- Initial spec

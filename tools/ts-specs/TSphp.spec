%include Solaris.inc

%define _prefix /opt/php

Name:                TSphp
Summary:             PHP web scripting language
Version:             5.2.6
Release:             2
Source:              http://uk.php.net/distributions/php-%{version}.tar.bz2

SUNW_BaseDir:        /opt/php
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: TSpcre-devel
BuildRequires: TScurl-devel

Requires: TSpcre
Requires: TScurl

%package root
Summary:                 %{summary} - / filesystem
SUNW_BaseDir:            /
%include default-depend.inc

%prep
%setup -q -n php-%version

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags}"

export EXTRA_LDFLAGS_PROGRAM='-L/opt/mysql/lib -R/opt/mysql/lib -L/opt/ts/lib -R/opt/ts/lib'
export CPPFLAGS='-I/opt/ts/include -I/opt/mysql/include -I/usr/sfw/include' 

./configure  --prefix=%{_prefix} \
	--with-xmlrpc \
	--enable-sockets \
	--enable-soap \
	--with-pgsql=/usr/postgres/8.2 \
	--enable-mbstring \
	--enable-fastcgi \
	--enable-pcntl \
	--with-openssl \
	--with-curl=/opt/ts \
	--sysconfdir=/etc/opt/php \
	--with-config-file-path=/etc/opt/php \
	--with-mysql=/opt/mysql \
	--disable-path-info-check \
	--with-pcre-regex=/opt/ts \
        --with-zlib \
        --with-bz2 \
        --enable-exif \
        --enable-ftp \
        --with-mysqli=/opt/mysql/bin/mysql_config


gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT

make install INSTALL_ROOT=$RPM_BUILD_ROOT
for x in .registry .channels .filemap .lock .depdblock .depdb; do
	#mv $RPM_BUILD_ROOT/$x $RPM_BUILD_ROOT/opt/php/lib/php/
	rm -rf $RPM_BUILD_ROOT/$x
done

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_prefix}/bin
%dir %attr (0755, root, sys) %{_prefix}/lib
%dir %attr (0755, root, sys) %{_prefix}/man
%dir %attr (0755, root, sys) %{_prefix}/include
%{_prefix}/bin/*
%{_prefix}/lib/*
%{_prefix}/man/*
%{_prefix}/include/*

%files root
%dir %attr (0755, root, sys) /etc
%dir %attr (0755, root, sys) /etc/opt
%dir %attr (0755, root, sys) /etc/opt/php
/etc/opt/php/pear.conf

%changelog
* Thu Oct  2 2008 - river@wikimedia.org
- add build options: --with-zlib --with-bz2 --enable-exif --enable-ftp --with-mysqli
* Sun Jul  6 2008 - river@wikimedia.org
- build with external pcre 
* Sat Jun 21 2008 - river@wikimedia.org
- initial spec

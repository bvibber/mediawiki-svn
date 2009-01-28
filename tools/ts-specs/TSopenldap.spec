%include Solaris.inc

%define _prefix /opt/TSopenldap

Name:                TSopenldap
Summary:             OpenLDAP client and libraries
Version:             2.4.13
Source:              ftp://ftp.openldap.org/pub/OpenLDAP/openldap-release/openldap-%{version}.tgz

SUNW_BaseDir:        /opt/TSopenldap
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
Requires: TSopenldap-root
%include default-depend.inc

%package root
Summary:                 %{summary} - / filesystem
SUNW_BaseDir:            /

%prep
%setup -q -n openldap-%version

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags} -R/usr/sfw/lib -L/usr/sfw/lib"
export CPPFLAGS='-I/usr/sfw/include'
./configure  --prefix=%{_prefix} \
	--disable-slapd --with-tls=openssl \
	--sysconfdir=/etc/opt/TSopenldap \
	--with-subdir=no

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT

make install DESTDIR=$RPM_BUILD_ROOT
rm $RPM_BUILD_ROOT/etc/opt/TSopenldap/ldap.conf

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
%defattr (-, root, bin)
%dir %attr (0755, root, sys) /etc
%dir %attr (0755, root, sys) /etc/opt
%dir %attr (0755, root, sys) /etc/opt/TSopenldap
/etc/opt/TSopenldap/ldap.conf.default

%changelog
* Wed Jan 28 2009 - river@wikimedia.org
- initial version

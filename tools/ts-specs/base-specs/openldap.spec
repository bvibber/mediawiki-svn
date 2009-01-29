%define _prefix /opt/TSopenldap

Name:                TSopenldap
Summary:             OpenLDAP client and libraries
Version:             2.4.13
Source:              ftp://ftp.openldap.org/pub/OpenLDAP/openldap-release/openldap-%{version}.tgz

SUNW_BaseDir:        /opt/TSopenldap
BuildRoot:           %{_tmppath}/%{name}-%{version}-build

%prep
%setup -q -n openldap-%version

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

./configure  --prefix=%{_prefix} \
	--disable-slapd --with-tls=openssl \
	--sysconfdir=/etc/opt/TSopenldap \
	--with-subdir=no \
	--bindir=%{_bindir} \
	--libdir=%{_libdir} \

gmake -j$CPUS

%install
gmake install DESTDIR=$RPM_BUILD_ROOT
rm $RPM_BUILD_ROOT/etc/opt/TSopenldap/ldap.conf
rm $RPM_BUILD_ROOT/%{_libdir}/*.la
rm $RPM_BUILD_ROOT/%{_libdir}/*.a

%clean
rm -rf $RPM_BUILD_ROOT

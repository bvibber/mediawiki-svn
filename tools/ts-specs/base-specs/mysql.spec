%define _prefix /opt/TSmysql

Name:                TSmysql
Summary:             MySQL database server
Version:             5.1.30
Source:              http://mysql.mirrors.pair.com/Downloads/MySQL-5.1/mysql-%{version}.tar.gz

SUNW_BaseDir:        /opt/TSmysql
BuildRoot:           %{_tmppath}/%{name}-%{version}-build

%prep
%setup -q -n mysql-%version

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

./configure \
	--prefix=%{_prefix}				\
	--bindir=%{_bindir}				\
	--libdir=%{_libdir}				\
	--libexecdir=%{_libdir}				\
	--includedir=%{_includedir}			\
	--with-extra-charsets=all			\
	--with-mysqld-user=mysql			\
	--with-zlib-dir=/usr				\
	--with-big-tables				\
	--with-ssl=/usr/sfw				\
	--with-plugins=max-no-ndb

gmake -j$CPUS

%install
gmake install DESTDIR=$RPM_BUILD_ROOT
rm $RPM_BUILD_ROOT/%{_libdir}/mysql/*.la
rm $RPM_BUILD_ROOT/%{_libdir}/mysql/*.a
rm $RPM_BUILD_ROOT/%{_libdir}/mysql/plugin/*.la
rm $RPM_BUILD_ROOT/%{_libdir}/mysql/plugin/*.a

%clean
rm -rf $RPM_BUILD_ROOT

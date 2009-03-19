%include Solaris.inc

Name:		TSzabbix
Summary:	Zabbix monitoring system
Version:	1.6.2
Source:		http://freefr.dl.sourceforge.net/sourceforge/zabbix/zabbix-%{version}.tar.gz
Source1:	zabbix_server.xml
Source2:	zabbix_agentd.xml
Patch1:		zabbix-01-configure.diff
Patch2:		zabbix-02-snmp-localname.diff
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: TSmysql
BuildRequires: TScurl

Requires:	TSmysql
Requires:	TScurl

%package agent
Summary:	%{summary} - agent daemon
SUNW_BaseDir:	%{_basedir}

%package root
Summary:	%{summary} - / filesystem
SUNW_BaseDir:	/

%package agent-root
Summary: 	%{summary} - agent daemon - / filesystem
SUNW_BaseDir:	/

%package proxy
Summary:	%{summary} - proxy daemon
SUNW_BaseDir:	%{_basedir}
Requires:	TSmysql

%package proxy-root
Summary:	%{summary} - proxy daemon - / filesystem
SUNW_BaseDir:	/

%prep
%setup -q -n zabbix-%version
%patch1 -p0
%patch2 -p0
 
%build
%include stdenv.inc
autoconf
%_configure 	--enable-server					\
		--enable-proxy					\
		--enable-agent					\
		--enable-ipv6					\
		--with-pgsql=/usr/postgres/8.3/bin/pg_config	\
		--with-libcurl=/opt/ts/bin/curl-config		\
		--with-net-snmp=/usr/sfw/bin/net-snmp-config	\
		--with-ldap

%_make
 
%install
%include stdenv.inc
%_make install DESTDIR=$RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT/etc/zabbix
cp misc/conf/zabbix_agentd.conf $RPM_BUILD_ROOT/etc/zabbix/zabbix_agentd.conf.example
cp misc/conf/zabbix_agent.conf $RPM_BUILD_ROOT/etc/zabbix/zabbix_agent.conf.example
cp misc/conf/zabbix_server.conf $RPM_BUILD_ROOT/etc/zabbix/zabbix_server.conf.example
cp misc/conf/zabbix_proxy.conf $RPM_BUILD_ROOT/etc/zabbix/zabbix_proxy.conf.example
mkdir -p $RPM_BUILD_ROOT/var/svc/manifest/network
cp %SOURCE1 %SOURCE2 $RPM_BUILD_ROOT/var/svc/manifest/network

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_sbindir}/zabbix_sender
%{_sbindir}/zabbix_get
%{_sbindir}/zabbix_server

%files root
%defattr (-, root, sys)
%dir /etc
%dir /etc/zabbix
/etc/zabbix/zabbix_server.conf.example
%dir /var
%dir /var/svc
%dir /var/svc/manifest
%dir /var/svc/manifest/network
%class(manifest) /var/svc/manifest/network/zabbix_server.xml

%files agent
%defattr (-, root, root)
%{_sbindir}/zabbix_agent
%{_sbindir}/zabbix_agentd

%files agent-root
%defattr (-, root, sys)
%dir /etc
%dir /etc/zabbix
/etc/zabbix/zabbix_agent.conf.example
/etc/zabbix/zabbix_agentd.conf.example
%dir /var
%dir /var/svc
%dir /var/svc/manifest
%dir /var/svc/manifest/network
%class(manifest) /var/svc/manifest/network/zabbix_agentd.xml

%files proxy
%defattr (-, root, root)
%{_sbindir}/zabbix_proxy

%files proxy-root
%defattr (-, root, sys)
%dir /etc
%dir /etc/zabbix
/etc/zabbix/zabbix_proxy.conf.example

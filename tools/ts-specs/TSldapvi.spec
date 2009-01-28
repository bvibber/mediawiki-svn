%include Solaris.inc

Name:			TSldapvi
Summary:		interactive LDAP editor
Version:		1.7
Source:			http://www.lichteblau.com/download/ldapvi-%{version}.tar.gz
Patch0:			ldapvi-01-destdir.diff

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: TSopenldap

%prep
%setup -q -n ldapvi-%version
%patch0 -p0

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CPPFLAGS='-I/opt/TSopenldap/include -I/usr/sfw/include -I/opt/ts/include'
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags} -L/opt/TSopenldap/lib -L/usr/sfw/lib -L/opt/ts/lib -R/opt/TSopenldap/lib:/usr/sfw/lib:/opt/ts/lib"

./configure --prefix=%{_prefix}  \
	    --sysconfdir=%{_sysconfdir} \
            --mandir=%{_mandir}

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT

chmod 755 install-sh
gmake install DESTDIR=$RPM_BUILD_ROOT

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_docdir}
%dir %attr (0755, root, bin) %{_docdir}/ldapvi
%{_docdir}/ldapvi/*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*

%changelog
* Wed Jan 28 2009 - river@wikimedia.org
- initial spec

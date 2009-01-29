%define _basedir /opt/TSopenldap
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

%ifarch amd64 sparcv9
%include arch64.inc
%use openldap64=openldap.spec
%endif
%include base.inc
%use openldap = openldap.spec
 
%package root
Summary:                 %{summary} - / filesystem
SUNW_BaseDir:            /

%package devel
Summary:                 %{summary} - development files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:                %name

%prep
rm -rf %name-%version
mkdir %name-%version
 
%ifarch amd64 sparcv9
mkdir %name-%version/%_arch64
%openldap64.prep -d %name-%version/%_arch64
%endif
 
mkdir %name-%version/%{base_arch}
%openldap.prep -d %name-%version/%{base_arch}

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags}"
export CPPFLAGS='-I/usr/sfw/include'

%ifarch amd64 sparcv9
%include arch64.inc
export CFLAGS="%optflags -m64 -I/usr/sfw/include"
export LDFLAGS="-m64 -L/usr/sfw/lib/%_arch64 -R/usr/sfw/lib/%_arch64"
%openldap64.build -d %name-%version/%_arch64
%endif
%include base.inc
export LDFLAGS="-L/usr/sfw/lib -R/usr/sfw/lib"
export CFLAGS="%optflags -I/usr/sfw/include"
%openldap.build -d %name-%version/%{base_arch}

%install
%ifarch amd64 sparcv9
%openldap64.install -d %name-%version/%_arch64
%endif
 
rm -rf $RPM_BUILD_ROOT/%{_bindir}/%_arch64

%openldap.install -d %name-%version/%{base_arch}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_prefix}/bin
%{_prefix}/bin/*
%dir %attr (0755, root, sys) %{_prefix}/lib
%{_prefix}/lib/*.so.*
%dir %attr (0755, root, sys) %{_prefix}/man
%dir %attr (0755, root, sys) %{_prefix}/man/man1
%{_prefix}/man/man1/*
%dir %attr (0755, root, sys) %{_prefix}/man/man5
%{_prefix}/man/man5/*
%dir %attr (0755, root, sys) %{_prefix}/man/man8
%{_prefix}/man/man8/*

%ifarch amd64 sparcv9
%dir %attr (0755, root, sys) %{_prefix}/lib/%_arch64
%{_prefix}/lib/%_arch64/*.so.*
%endif

%files devel
%defattr (-, root, bin)
%dir %attr (0755, root, sys) %{_prefix}/lib
%{_prefix}/lib/*.so
%dir %attr (0755, root, sys) %{_prefix}/man
%dir %attr (0755, root, sys) %{_prefix}/man/man3
%{_prefix}/man/man3/*
%dir %attr (0755, root, sys) %{_prefix}/include
%{_prefix}/include/*

%ifarch amd64 sparcv9
%dir %attr (0755, root, sys) %{_prefix}/lib/%_arch64
%{_prefix}/lib/%_arch64/*.so
%endif

%files root
%defattr (-, root, bin)
%dir %attr (0755, root, sys) /etc
%dir %attr (0755, root, sys) /etc/opt
%dir %attr (0755, root, sys) /etc/opt/TSopenldap
/etc/opt/TSopenldap/ldap.conf.default

%changelog
* Wed Jan 28 2009 - river@wikimedia.org
- initial version

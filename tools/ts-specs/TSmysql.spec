%define _basedir /opt/TSmysql
%include Solaris.inc

%define _prefix /opt/TSmysql

Name:                TSmysql
Summary:             MySQL database server
Version:             5.1.30
Source:              http://mysql.mirrors.pair.com/Downloads/MySQL-5.1/mysql-%{version}.tar.gz

SUNW_BaseDir:        /opt/TSmysql
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: TSisaexec

%ifarch amd64 sparcv9
%include arch64.inc
%use mysql64=mysql.spec
%endif
%include base.inc
%use mysql = mysql.spec
 
%package devel
Summary:                 %{summary} - development files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:                %name

%package extra
Summary:                 %{summary} - test suite and benchmark tools
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:                %name

%prep
rm -rf %name-%version
mkdir %name-%version
 
%ifarch amd64 sparcv9
mkdir %name-%version/%_arch64
%mysql64.prep -d %name-%version/%_arch64
%endif
 
mkdir %name-%version/%{base_arch}
%mysql.prep -d %name-%version/%{base_arch}

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CPPFLAGS='-I/usr/sfw/include'

%ifarch amd64 sparcv9
%include arch64.inc
export CFLAGS="%optflags -m64"
export CXXFLAGS="%cxx_optflags -m64"
export LDFLAGS="%{_ldflags} -m64 -L/usr/sfw/lib/%_arch64 -R/usr/sfw/lib/%_arch64"
%mysql64.build -d %name-%version/%_arch64
%endif
%include base.inc
export LDFLAGS="%{_ldflags} -L/usr/sfw/lib -R/usr/sfw/lib"
export CFLAGS="%optflags"
export CXXFLAGS="%cxx_optflags"
%mysql.build -d %name-%version/%{base_arch}

%install
rm -rf $RPM_BUILD_ROOT

%ifarch amd64 sparcv9
%mysql64.install -d %name-%version/%_arch64
mv $RPM_BUILD_ROOT/%{_bindir}/%_arch64/mysql_config $RPM_BUILD_ROOT/%{_bindir}
rm -rf $RPM_BUILD_ROOT/%{_bindir}/%_arch64
mkdir -p $RPM_BUILD_ROOT/%{_bindir}/%_arch64
mv $RPM_BUILD_ROOT/%{_bindir}/mysql_config $RPM_BUILD_ROOT/%{_bindir}/%_arch64
rm -f $RPM_BUILD_ROOT/%{_libdir}/%_arch64/mysqlmanager
%endif

%mysql.install -d %name-%version/%{base_arch}

%ifarch i386
mkdir -p $RPM_BUILD_ROOT/%{_libdir}/i86
mv $RPM_BUILD_ROOT/%{_libdir}/mysqld $RPM_BUILD_ROOT/%{_libdir}/i86
%else
mkdir -p $RPM_BUILD_ROOT/%{_libdir}/sparcv7
mv $RPM_BUILD_ROOT/%{_libdir}/mysqld $RPM_BUILD_ROOT/%{_libdir}/sparcv7
%endif

ln -s ../../ts/lib/isaexec $RPM_BUILD_ROOT%{_libdir}/mysqld

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, sys) %{_libdir}
%hard %{_libdir}/mysqld
%{_libdir}/mysqlmanager
%dir %attr (0755, root, sys) %{_libdir}/i86
%{_libdir}/i86/mysqld
%dir %attr (0755, root, sys) %{_libdir}/mysql
%{_libdir}/mysql/*.so.*
%dir %attr (0755, root, sys) %{_libdir}/mysql/plugin
%{_libdir}/mysql/plugin/*.so*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, sys) %{_datadir}/info
%{_datadir}/info/*
%dir %attr (0755, root, sys) %{_datadir}/mysql
%{_datadir}/mysql/*
%dir %attr (0755, root, sys) %{_mandir}
%dir %attr (0755, root, sys) %{_mandir}/man1
%{_mandir}/man1/*
%dir %attr (0755, root, sys) %{_mandir}/man8
%{_mandir}/man8/*

%ifarch amd64 sparcv9
%dir %attr (0755, root, sys) %{_libdir}/%_arch64
%{_libdir}/%_arch64/mysqld
%dir %attr (0755, root, sys) %{_libdir}/%_arch64/mysql
%{_prefix}/lib/%_arch64/mysql/*.so.*
%dir %attr (0755, root, sys) %{_libdir}/%_arch64/mysql/plugin
%{_prefix}/lib/%_arch64/mysql/plugin/*.so*
%endif

%files devel
%defattr (-, root, bin)
%dir %attr (0755, root, sys) %{_libdir}
%dir %attr (0755, root, sys) %{_libdir}/mysql
%{_libdir}/mysql/*.so
%dir %attr (0755, root, sys) %{_prefix}/include
%{_prefix}/include/*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, sys) %{_datadir}/aclocal
%{_datadir}/aclocal/*

%ifarch amd64 sparcv9
%dir %attr (0755, root, sys) %{_libdir}/%_arch64
%dir %attr (0755, root, sys) %{_libdir}/%_arch64/mysql
%{_prefix}/lib/%_arch64/mysql/*.so
%dir %attr (0755, root, bin) %{_bindir}
%dir %attr (0755, root, bin) %{_bindir}/%_arch64
%{_bindir}/%_arch64/*
%endif

%files extra
%defattr (-, root, bin)
%dir %attr (0755, root, sys) %{_prefix}/mysql-test
%{_prefix}/mysql-test/*
%dir %attr (0755, root, sys) %{_prefix}/sql-bench
%{_prefix}/sql-bench/*

%changelog
* Thu Jan 29 2009 - river@wikimedia.org
- initial version

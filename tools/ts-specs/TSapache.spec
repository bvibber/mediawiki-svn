%define _basedir /opt/TSapache
%include Solaris.inc

Name:			TSapache
Summary:		Apache HTTP server
Version:		2.2.11
Source:			http://mirrors.enquira.co.uk/apache/httpd/httpd-%{version}.tar.gz

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: TSapr
Requires: TSapu
Requires: TSpcre
BuildRequires: TSapr
BuildRequires: TSapu
BuildRequires: TSpcre-devel

%package root
Summary:		%{summary} - / filesystem
SUNW_BaseDir:		/

%package doc
Summary:		%{summary} - documentation
SUNW_BaseDir:		%{_basedir}

%package devel
Summary:		%{summary} - development files
SUNW_BaseDir:		%{_basedir}

%prep
%setup -q -n httpd-%version

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export CPPFLAGS="-I/opt/ts/include -I/usr/sfw/include"
export LDFLAGS="%{_ldflags} -L/opt/ts/lib -L/usr/sfw/lib -R/opt/ts/lib:/usr/sfw/lib"

./configure 						\
	--prefix=/opt/TSapache				\
	--sysconfdir=/etc/opt/TSapache			\
	--with-suexec-docroot=/var/www	 		\
	--with-suexec-logfile=/var/log/apache/suexec	\
	--with-mpm=worker 				\
	--with-ssl=/usr/sfw				\
	--with-apr=/opt/ts				\
	--with-apr-util=/opt/ts				\
	--with-pcre=/opt/ts				\
	--with-z=/usr/sfw				\
	--enable-authnz-ldap=shared			\
	--enable-auth-digest=shared			\
	--enable-charset-lite=shared			\
	--enable-deflate=shared				\
	--enable-ldap=shared				\
	--enable-log-forensic=shared			\
	--enable-logio=shared				\
	--enable-mime-magic=shared			\
	--enable-expires=shared 			\
	--enable-headers=shared				\
	--enable-ident					\
	--enable-version				\
	--enable-proxy=shared				\
	--enable-proxy-connect=shared			\
	--enable-proxy-ftp=shared 			\
	--enable-proxy-http=shared			\
	--enable-proxy-ajp=shared			\
	--enable-proxy-balancer=shared			\
	--enable-ssl=shared				\
	--enable-http					\
	--enable-dav=shared				\
	--enable-suexec 				\
	--enable-cgi=shared 				\
	--enable-dav-fs=shared				\
	--enable-dav-lock=shared			\
	--enable-vhost-alias=shared			\
	--enable-imagemap=shared			\
	--enable-speling=shared				\
	--enable-rewrite=shared				\
	--enable-so

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT

make install DESTDIR=$RPM_BUILD_ROOT
rm -rf $RPM_BUILD_ROOT%{_basedir}/htdocs
rmdir $RPM_BUILD_ROOT%{_basedir}/logs

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr(0755, root, bin) %{_basedir}/modules
%{_basedir}/modules/*
%dir %attr(0755, root, bin) %{_basedir}/build
%{_basedir}/build/*
%dir %attr(0755, root, bin) %{_basedir}/bin
%{_basedir}/bin/*
%dir %attr(0755, root, bin) %{_basedir}/error
%{_basedir}/error/*
%dir %attr(0755, root, bin) %{_basedir}/icons
%{_basedir}/icons/*
%dir %attr(0755, root, bin) %{_basedir}/cgi-bin
%{_basedir}/cgi-bin/*
%dir %attr(0755, root, bin) %{_basedir}/man
%dir %attr(0755, root, bin) %{_basedir}/man/man1
%dir %attr(0755, root, bin) %{_basedir}/man/man8
%{_basedir}/man/man1/*
%{_basedir}/man/man8/*

%files doc
%defattr (-, root, bin)
%dir %attr(0755, root, bin) %{_basedir}/manual
%{_basedir}/manual/*

%files devel
%defattr (-, root, bin)
%dir %attr(0755, root, bin) %{_basedir}/include
%{_basedir}/include/*

%files root
%defattr (-, root, bin)
%dir %attr(0755, root, sys) /etc
%dir %attr(0755, root, sys) /etc/opt
%dir %attr(0755, root, sys) /etc/opt/TSapache
%dir %attr(0755, root, sys) /etc/opt/TSapache/extra
/etc/opt/TSapache/extra/*
%dir %attr(0755, root, sys) /etc/opt/TSapache/original
/etc/opt/TSapache/original/*
%class(preserve) /etc/opt/TSapache/httpd.conf
%class(preserve) /etc/opt/TSapache/mime.types
%class(preserve) /etc/opt/TSapache/magic

%changelog
* Mon Feb 23 2009 - river@loreley.flyingparchment.org.uk
- initial spec

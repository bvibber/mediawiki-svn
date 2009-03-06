%include Solaris.inc

Name:		TSopenssh
Summary:	OpenSSH Secure Shell
Version:	5.1p1
Source:		ftp://ftp.openbsd.org/pub/OpenBSD/OpenSSH/portable/openssh-%{version}.tar.gz
Source1:	TSopenssh
Source2:	TSopenssh.xml
Patch1:		openssh-01-hpn.diff

SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: %{name}-root

%package root
Summary:	%{summary} - / filesystem
SUNW_BaseDir:	/
%include default-depend.inc

%package server
Summary:	%{summary} - server
Requires: %{name}-server-root
%include default-depend.inc

%package server-root
Summary:	%{summary} - server - / filesystem
SUNW_BaseDir:	/
%include default-depend.inc

%prep
%setup -q -n openssh-%version
%patch1 -p1

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags} -L/usr/sfw/lib -R/usr/sfw/lib"
export CPPFLAGS="-I/usr/sfw/include"

./configure 						\
	--prefix=%{_prefix} 				\
	--mandir=%{_mandir}  				\
	--sysconfdir=%{_sysconfdir} 			\
	--with-solaris-contracts			\
	--with-zlib=/usr/sfw				\
	--with-audit=bsm				\
	--with-ssl-dir=/usr/sfw				\
	--with-pam					\
	--with-privsep-user=daemon			\
	--with-privsep-path=/var/opt/ts/ssh/empty	\
	--with-xauth=/usr/openwin/bin/xauth		\
	--sysconfdir=/etc/opt/ts/ssh			\
	--libexecdir=/opt/ts/lib/openssh

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT

gmake install DESTDIR=$RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT/var/svc/manifest/network
cp %SOURCE2 $RPM_BUILD_ROOT/var/svc/manifest/network
mkdir -p $RPM_BUILD_ROOT/lib/svc/method
cp %SOURCE1 $RPM_BUILD_ROOT/lib/svc/method

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%dir %attr (0755, root, bin) %{_libdir}/openssh
%{_libdir}/openssh/ssh-keysign
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*
%dir %attr (0755, root, bin) %{_mandir}/man5
%{_mandir}/man5/ssh_config.5
%dir %attr (0755, root, bin) %{_mandir}/man8
%{_mandir}/man8/ssh-keysign.8

%files root
%defattr (-, root, bin)
%dir %attr (0755, root, sys) /etc
%dir %attr (0755, root, sys) /etc/opt
%dir %attr (0755, root, sys) /etc/opt/ts
%dir %attr (0755, root, sys) /etc/opt/ts/ssh
%class(preserve) /etc/opt/ts/ssh/ssh_config

%files server
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_sbindir}
%{_sbindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%dir %attr (0755, root, bin) %{_libdir}/openssh
%{_libdir}/openssh/sftp-server
%dir %attr (0755, root, sys) %{_datadir}
%{_datadir}/Ssh.bin
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man5
%{_mandir}/man5/sshd_config.5
%{_mandir}/man5/moduli.5
%dir %attr (0755, root, bin) %{_mandir}/man8
%{_mandir}/man8/sshd.8
%{_mandir}/man8/sftp-server.8

%files server-root
%defattr (-, root, bin)
%dir %attr (0755, root, sys) /etc
%dir %attr (0755, root, sys) /etc/opt
%dir %attr (0755, root, sys) /etc/opt/ts
%dir %attr (0755, root, sys) /etc/opt/ts/ssh
%class(preserve) /etc/opt/ts/ssh/sshd_config
%class(preserve) /etc/opt/ts/ssh/moduli
%dir %attr (0755, root, sys) /var
%dir %attr (0755, root, sys) /var/svc
%dir %attr (0755, root, sys) /var/svc/manifest
%dir %attr (0755, root, sys) /var/svc/manifest/network
%class(manifest) /var/svc/manifest/network/TSopenssh.xml
%dir %attr (0755, root, sys) /var/opt
%dir %attr (0755, root, root) /var/opt/ts
%dir %attr (0755, root, root) /var/opt/ts/ssh
%dir %attr (0755, root, root) /var/opt/ts/ssh/empty
%dir %attr (0755, root, bin) /lib
%dir %attr (0755, root, bin) /lib/svc
%dir %attr (0755, root, bin) /lib/svc/method
%attr (0755, root, bin) /lib/svc/method/TSopenssh

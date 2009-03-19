%include Solaris.inc

Name:		TSopenssh
Summary:	OpenSSH Secure Shell
Version:	5.1p1
Source:		ftp://ftp.openbsd.org/pub/OpenBSD/OpenSSH/portable/openssh-%{version}.tar.gz
Source1:	TSopenssh
Source2:	TSopenssh.xml
Patch1:		openssh-01-hpn.diff

Requires:	TSlibedit
BuildRequires:	TSlibedit

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

%include stdenv.inc
%_configure	--with-solaris-contracts			\
		--with-zlib=/usr/sfw				\
		--with-audit=bsm				\
		--with-ssl-dir=/usr/sfw				\
		--with-pam					\
		--with-privsep-user=daemon			\
		--with-privsep-path=/var/opt/ts/ssh/empty	\
		--with-xauth=/usr/openwin/bin/xauth		\
		--sysconfdir=/etc/opt/ts/ssh			\
		--libexecdir=/opt/ts/lib/openssh		\
		--with-libedit					\
		--with-default-path=/opt/ts/bin:/usr/bin:/sbin:/usr/sbin

%_make

%install
rm -rf $RPM_BUILD_ROOT

%include stdenv.inc
%_make install DESTDIR=$RPM_BUILD_ROOT

mkdir -p $RPM_BUILD_ROOT/var/svc/manifest/network
cp %SOURCE2 $RPM_BUILD_ROOT/var/svc/manifest/network
mkdir -p $RPM_BUILD_ROOT/lib/svc/method
cp %SOURCE1 $RPM_BUILD_ROOT/lib/svc/method

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_bindir}/*
%{_libdir}/openssh/ssh-keysign
%{_mandir}/man1/*
%{_mandir}/man5/ssh_config.5
%{_mandir}/man8/ssh-keysign.8

%files root
%defattr (-, root, sys)
%class(preserve) /etc/opt/ts/ssh/ssh_config

%files server
%defattr (-, root, root)
%{_sbindir}/*
%{_libdir}/openssh/sftp-server
%{_datadir}/Ssh.bin
%{_mandir}/man5/sshd_config.5
%{_mandir}/man5/moduli.5
%{_mandir}/man8/sshd.8
%{_mandir}/man8/sftp-server.8

%files server-root
%defattr (-, root, sys)
%class(preserve) /etc/opt/ts/ssh/sshd_config
%class(preserve) /etc/opt/ts/ssh/moduli
%class(manifest) /var/svc/manifest/network/TSopenssh.xml
%dir /var/opt
%dir %attr (0755, root, root) /var/opt/ts
%dir %attr (0755, root, root) /var/opt/ts/ssh
%dir %attr (0755, root, root) /var/opt/ts/ssh/empty
%dir %attr (0755, root, bin) /lib
%dir %attr (0755, root, bin) /lib/svc
%dir %attr (0755, root, bin) /lib/svc/method
%attr (0755, root, bin) /lib/svc/method/TSopenssh

%include Solaris.inc

Name:		TSscponly
Summary:	scponly shell
Version:	4.8
Source:		http://heanet.dl.sourceforge.net/sourceforge/scponly/scponly-%{version}.tgz
Patch1:		scponly-01-install.diff

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: %{name}-root

%package root
Summary:                 %{summary} - / filesystem
SUNW_BaseDir:            /
%include default-depend.inc

%prep
%setup -q -n scponly-%version
%patch1 -p0

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags}"

./configure							\
	--prefix=%{_prefix}  					\
	--mandir=%{_mandir}  					\
	--sysconfdir=/etc/opt/ts				\
	--with-sftp-server=/opt/ts/lib/openssh/sftp-server	\
	--disable-wildcards					\
	--disable-gftp-compat					\
	--enable-chrooted-binary				\
	--prefix=/opt/ts					\
	--disable-winscp-compat

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT

gmake install DESTDIR=$RPM_BUILD_ROOT

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_sbindir}
%attr (4755, root, bin) %{_sbindir}/scponlyc
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man8
%{_mandir}/man8/*

%files root
%defattr (-, root, sys)
%dir %attr (0755, root, sys) /etc
%dir %attr (0755, root, sys) /etc/opt
%dir %attr (0755, root, sys) /etc/opt/ts
%dir %attr (0755, root, sys) /etc/opt/ts/scponly
/etc/opt/ts/scponly/*

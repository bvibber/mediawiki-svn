#
# spec file for package TSexim
#
# includes module(s): Exim

%include Solaris.inc

Name:         TSexim
Summary:      Exim internet mailer
Version:      4.69
Source0:      ftp://gd.tuwien.ac.at/infosys/mail/exim/exim/exim4/exim-%{version}.tar.gz
Source1:	Makefile.exim
SUNW_BaseDir: /opt/exim
BuildRoot:    %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc
Requires: TSexim-root

%package root
Summary:                 %{summary} - root filesystem
SUNW_BaseDir:            /
%include default-depend.inc

%prep
%setup -q -n exim-%version

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

cp %{SOURCE1} Local/Makefile
make

%install
rm -rf $RPM_BUILD_ROOT
# Exim's 'make install' only works as root, so we use our own

mkdir -p $RPM_BUILD_ROOT/opt/exim/bin

for x in exim exim_fixdb exicyclog exiqgrep exim_dbmbuild eximstats \
	exim_dumpdb exigrep exiwhat exim_tidydb exim_checkaccess exipick \
	exinext exiqsumm exim_lock; do
	
	cp build-SunOS5-5.10-i386/$x $RPM_BUILD_ROOT/opt/exim/bin
done

mkdir -p $RPM_BUILD_ROOT/etc/opt/exim
sed -e "/SYSTEM_ALIASES_FILE/ s'SYSTEM_ALIASES_FILE'/etc/mail/aliases'" \
	<src/configure.default >$RPM_BUILD_ROOT/etc/opt/exim/configure.sample
mkdir -p $RPM_BUILD_ROOT/var/spool/exim
mkdir -p $RPM_BUILD_ROOT/var/log/exim

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) /opt/exim/bin
/opt/exim/bin/*

%files root
%defattr (-, root, sys)
%dir %attr (0755, root, sys) /etc/opt/exim
/etc/opt/exim/configure.sample
%dir %attr (0755, root, sys) /var
%dir %attr (0755, root, sys) /var/log
%dir %attr (0750, exim, exim) /var/log/exim
%dir %attr (0755, root, bin) /var/spool
%dir %attr (0750, exim, exim) /var/spool/exim

%changelog
* Sun Jan 29 2008 - river@loreley.flyingparchment.org.uk
- initial version

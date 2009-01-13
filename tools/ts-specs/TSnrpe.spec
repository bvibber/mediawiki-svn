# spec file for package TSnrpe
%include Solaris.inc

Name:                    TSnrpe
Summary:                 Nagios NRPE monitor daemon
Version:                 2.12
Source:			 http://mesh.dl.sourceforge.net/sourceforge/nagios/nrpe-%{version}.tar.gz
Patch0:			nrpe-00-log-defines.diff
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

%package root
Summary:                 %{summary} - / filesystem
SUNW_BaseDir:            /
%include default-depend.inc

%prep
%setup -q -n nrpe-%version
%patch0 -p0

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

LDFLAGS='-L/usr/sfw/lib -R/usr/sfw/lib'

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags -I/usr/sfw/include"
export LDFLAGS="-L/usr/sfw/lib -R/usr/sfw/lib"
export RPM_OPT_FLAGS="$CFLAGS"

./configure --prefix=%{_prefix}			\
	    --libexecdir=%{_libexecdir}         \
	    --mandir=%{_mandir}                 \
	    --datadir=%{_datadir}               \
            --infodir=%{_datadir}/info		\
            --sysconfdir=/etc/opt/TSnrpe	\
            --with-ssl=/usr/sfw --with-ssl-lib=/usr/sfw/lib \
		--with-nrpe-user=nobody --with-nrpe-group=nogroup \
		--enable-ssl
	    		
gmake -j$CPUS

%install
mkdir -p $RPM_BUILD_ROOT%{_libdir}
cp src/nrpe $RPM_BUILD_ROOT%{_libdir}

mkdir -p $RPM_BUILD_ROOT/etc/opt/TSnrpe
cp sample-config/nrpe.cfg $RPM_BUILD_ROOT/etc/opt/TSnrpe/nrpe.cfg.sample

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*

%files root
%dir %attr (0755, root, sys) /etc
%dir %attr (0755, root, sys) /etc/opt
%dir %attr (0755, root, sys) /etc/opt/TSnrpe
/etc/opt/TSnrpe/nrpe.cfg.sample

%changelog
* Tue Jan 13 2008 - river@loreley.flyingparchment.org.uk
- initial spec

%include Solaris.inc

Name:			TSpkgbuild
Summary:		generates SVR4 packages from RPM spec files
Version:		1.3.3
Source:			http://mesh.dl.sourceforge.net/sourceforge/pkgbuild/pkgbuild-%{version}.tar.bz2
Patch0:			pkgbuild-01-notify-send.diff

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

# Requires:

%prep
%setup -q -n pkgbuild-%version
%patch0 -p0

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags}"

./configure --prefix=%{_prefix}  \
	    --sysconfdir=%{_sysconfdir} \
            --mandir=%{_mandir}

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT

make install DESTDIR=$RPM_BUILD_ROOT

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%dir %attr (0755, root, bin) %{_libdir}/pkgbuild-%{version}
%{_libdir}/pkgbuild-%{version}/*

%changelog
* Sun Oct  5 2008 - river@wikimedia.org
- initial spec

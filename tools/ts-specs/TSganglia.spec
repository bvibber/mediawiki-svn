#
# Copyright 2007 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.

%include Solaris.inc

Name:                TSganglia
Summary:             Ganglia cluster monitor, monitoring daemon
Version:             3.0.7
Source:              %{sf_download}/ganglia/ganglia-%{version}.tar.gz
SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: SUNWgccruntime
BuildRequires: TSlibconfuse

# If gmetad support is desired, then see documentation about
# needing rrdtool, etc. and uncomment the following line:
# BuildRequires: SFErrdtool
# Also see --with-gmetad below...

%prep
%setup -q -n ganglia-%version

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

# This source is gcc-centric, therefore...
export CC=/usr/sfw/bin/gcc
# export CFLAGS="%optflags"
export CFLAGS="-D__EXTENSIONS__ -I%{_includedir} -O2 -fPIC -DPIC -Xlinker -i -fno-omit-frame-pointer"

export LDFLAGS="%_ldflags -L%{_libdir} -R%{_libdir}"

./configure --prefix=%{_prefix}  \
            --mandir=%{_mandir}

# If gmetad support is desired, enable:
#         --with-gmetad
# and see doc about needing rrdtool...

gmake -j$CPUS

%install
rm -rf $RPM_BUILD_ROOT

gmake install DESTDIR=$RPM_BUILD_ROOT

rm ${RPM_BUILD_ROOT}%{_libdir}/libganglia.la
rm ${RPM_BUILD_ROOT}%{_libdir}/libganglia.a

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_sbindir}
%{_sbindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*
%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*

%changelog
* Thu Oct 16 2008 - river@wikimedia.org
- modified for toolserver
- 3.1.1
* Mon Mar 19 2007 - dougs@truemail.co.th
- Fixed -fno-omit-frame-pointer flag
* Sun Nov 05 2006 - Eric Boutilier
- Force gcc
* Sun Sep 24 2006 - Eric Boutilier
- Initial spec

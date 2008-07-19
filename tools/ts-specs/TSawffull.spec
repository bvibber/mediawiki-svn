#
# Copyright (c) 2006 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.

%include Solaris.inc

Name:                TSawffull
Summary:             A Webalizer Fork
Version:             3.8.2
Source:              http://www.stedee.id.au/files/awffull-%{version}.tar.gz

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: TSgd-devel
Requires: TSgd
BuildRequires: TSpcre-devel
Requires: TSpcre

%prep
%setup -q -n awffull-%version

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

export CC="cc"
export CXX="CC"
export CPPFLAGS="-I/opt/ts/include"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags} -L/opt/ts/lib -R/opt/ts/lib"

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
%dir %attr (0755, root, sys) %{_datadir}
%{_datadir}/man/man1/*

%changelog
* Sat Jul 19 2008 - river@wikimedia.org
- initial spec

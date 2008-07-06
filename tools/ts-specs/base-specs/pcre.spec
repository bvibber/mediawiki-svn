Name:         	TSpcre
Version:      	7.7
Source:         %{sf_download}/pcre/pcre-%{version}.tar.gz
Patch1:         pcre-01-cve-2008-2371

%prep 
%setup -q -n %{src_name}-%{version}
export CC=cc
export CXX=CC
export CFLAGS="%optflags"
export CXXFLAGS="%cxx_optflags"
export LDFLAGS="%_ldflags"

./configure --prefix=%{_prefix} --libdir=%{_libdir} --bindir=%{_bindir}

%patch1 -p0

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

gmake -j$CPUS

%install
gmake install DESTDIR=$RPM_BUILD_ROOT
rm $RPM_BUILD_ROOT%{_libdir}/*.a
rm $RPM_BUILD_ROOT%{_libdir}/*.la

%clean
rm -rf $RPM_BUILD_ROOT

#%files
#%defattr(-,root,bin)
#%dir %attr (0755, root, bin) %{_libdir}
#%{_libdir}/*.so.*

#%files devel
#%defattr(-,root,bin)
#%dir %attr (0755, root, sys) %{_datadir}
#%{_datadir}/man
#%dir %attr (0755, root, other) %{_datadir}/doc
#%{_datadir}/doc/*

#%dir %attr (0755, root, bin) %{_includedir}
#%{_includedir}/*
#%dir %attr (0755, root, other) %{_libdir}/pkgconfig
#%{_libdir}/pkgconfig/*
#%dir %attr (0755, root, bin) %{_libdir}
#%{_libdir}/*.so
#%dir %attr (0755, root, bin) %{_bindir}
#%{_bindir}/*

#%changelog
#* Sun Jul  6 2008 - river@wikimedia.org
#- modified for toolserver
#* Fri Jan 11 2008 - moinak.ghosh@sun.com
#- Add conflict with SUNWpcre, remove -i386 from package name
#* Mon Oct 29 2007 - brian.cameron@sun.com
#- Bump to 7.4 and fix Source URL.
#* 2007.Aug.11 - <shivakumar dot gn at gmail dot com>
##- Initial spec.

Name:         	TSapr
Version:      	1.3.2
Source:         http://mirrors.dedipower.com/ftp.apache.org/apr/apr-%{version}.tar.gz

%prep 
%setup -q -n %{src_name}-%{version}
export CC=cc
export CXX=CC
export CFLAGS="%optflags"
export CXXFLAGS="%cxx_optflags"
export LDFLAGS="%_ldflags"

./configure --prefix=%{_prefix} --libdir=%{_libdir} --bindir=%{_bindir} --with-installbuilddir=%{_libdir}/apr-build-1 --includedir=$INCDIR

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
rm $RPM_BUILD_ROOT%{_libdir}/apr.exp

%clean
rm -rf $RPM_BUILD_ROOT

#%changelog
#* Sun Jul  9 2008 - river@wikimedia.org
#- initial spec

Name:         	TSproj4
Version:      	4.6.0
Source:         http://download.osgeo.org/proj/proj-%{version}.tar.gz

%prep 
%setup -q -n proj-%{version}
export CC=gcc
export CXX=g++
export CFLAGS="%optflags"
export CXXFLAGS="%cxx_optflags"
export LDFLAGS="%_ldflags"

./configure --prefix=%{_prefix} --libdir=%{_libdir} --bindir=%{_bindir}

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

#%changelog
#* Wed Jul 16 2008 - river@wikimedia.org
#- initial spec

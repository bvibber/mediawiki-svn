Name:         	TSserf
Summary:	Serf HTTP library
Version:      	0.3.0
Source:         http://serf.googlecode.com/files/serf-%{version}.tar.bz2
Patch1:		serf-01-cflags.diff

%prep 
%setup -q -n serf-%{version}
%patch1 -p0

%if %opt_arch64
%define _libsubdir %_arch64
%else
%define _libsubdir ""
%endif

export CC=cc
export CXX=CC
export CFLAGS="%optflags"
export CPPFLAGS=-I/usr/sfw/include
export CXXFLAGS="%cxx_optflags"
export LDFLAGS="%_ldflags -L%{_libdir}/%_libsubdir -L/usr/sfw/lib/%_libsubdir -R%{_libdir}/%_libsubdir:/usr/sfw/lib/%_libsubdir"

./configure 	\
		--prefix=%{_prefix}			\
		--libdir=%{_libdir}			\
		--bindir=%{_bindir}			\
		--with-apr=$APR_CONFIG			\
		--with-apr-util=$APR_UTIL_CONFIG	\
		--includedir=$INCDIR			\

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

gmake -j$CPUS

%install
gmake install DESTDIR=$RPM_BUILD_ROOT
rm $RPM_BUILD_ROOT%{_libdir}/*.a
#rm $RPM_BUILD_ROOT%{_libdir}/*.la

%clean
rm -rf $RPM_BUILD_ROOT

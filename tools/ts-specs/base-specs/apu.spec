Name:         	TSapu
Summary:	Apache Portable Runtime utility functions
Version:      	1.3.4
Source:         http://mirrors.dedipower.com/ftp.apache.org/apr/apr-util-%{version}.tar.gz

%prep 
%setup -q -n apr-util-%{version}
gzip -dc %SOURCE1 | tar xvf -

export CC=cc
export CXX=CC
export CFLAGS="%optflags"
export CXXFLAGS="%cxx_optflags"
export LDFLAGS="%_ldflags -L%{_libdir}/%_arch64 -R%{_libdir}/%_arch64"
export CPPFLAGS="-I/opt/ts/include"

export PKG_CONF
./configure 	\
		--prefix=%{_prefix}	\
		--libdir=%{_libdir}	\
		--bindir=%{_bindir}	\
		--with-apr=$APR_CONFIG	\
		--includedir=$INCDIR	\
		--with-ldap=ldap	\
		--with-expat=/opt/ts

		#--with-iconv=/opt/ts

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
rm -f $RPM_BUILD_ROOT%{_libdir}/apr-util-1/*.a
rm -f $RPM_BUILD_ROOT%{_libdir}/apr-util-1/*.la
rm $RPM_BUILD_ROOT%{_libdir}/aprutil.exp

%clean
rm -rf $RPM_BUILD_ROOT

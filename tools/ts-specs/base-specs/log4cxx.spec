Name:         	TSlog4cxx
Version:      	0.10.0
Source:         http://www.smudge-it.co.uk/pub/apache/logging/log4cxx/%{version}/apache-log4cxx-%{version}.tar.gz
Patch1:		log4cxx-01-inputstreamreader-std.diff
Patch2:		log4cxx-02-socketoutputstream-std.diff
Patch3:		log4cxx-03-systemerrwriter-std.diff
Patch4:		log4cxx-04-stringhelper-std.diff
Patch5:		log4cxx-05-systemoutwriter-std.diff
Patch6:		log4cxx-06-console-std.diff
Patch7:		log4cxx-07-messagebuffertest-std.diff

%prep 
%setup -q -n apache-log4cxx-%{version}
%patch1 -p0
%patch2 -p0
%patch3 -p0
%patch4 -p0
%patch5 -p0
%patch6 -p0
%patch7 -p0

export CC=cc
export CXX=CC
export CFLAGS="%optflags"
export CXXFLAGS="%cxx_optflags -library=stlport4"
export LDFLAGS="%_ldflags -R%{_libdir}"

bash autogen.sh

./configure --prefix=%{_prefix} --libdir=%{_libdir} --bindir=%{_bindir} --with-apr=$APRBIN/apr-1-config --with-apr-util=$APRBIN/apu-1-config

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
#* Sun Jul  9 2008 - river@wikimedia.org
#- initial spec

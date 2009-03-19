Name:         	TSpcre
Version:      	7.8
Source:         %{sf_download}/pcre/pcre-%{version}.tar.gz

%prep 
%setup -q -n pcre-%{version}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

%_configure --enable-utf8 --enable-unicode-properties
gmake -j$CPUS

%install
gmake install DESTDIR=$RPM_BUILD_ROOT
rm $RPM_BUILD_ROOT%{_libdir}/*.la

%clean
rm -rf $RPM_BUILD_ROOT

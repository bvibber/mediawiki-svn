%include Solaris.inc

%include arch64.inc
%use cairo64=cairo.spec
%include base.inc
%use cairo=cairo.spec

Name:                    %{cairo.name}
Summary:                 %{cairo.summary}
Version:                 %{cairo.version}
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

Requires:	TSpixman
BuildRequires:	TSpixman-devel

%include default-depend.inc

%package devel
Summary:		 %{summary} - development files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:		 %name

%prep
rm -rf %name-%version
mkdir %name-%version

mkdir %name-%version/%_arch64
%cairo64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%cairo.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

export CC="cc"
export CXX="CC"

%include arch64.inc
%include stdenv.inc
%cairo64.build -d %name-%version/%_arch64

%include base.inc
%include stdenv.inc
%cairo.build -d %name-%version/%{base_arch}

%install
%cairo64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.a

%cairo64.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/*.a
rm -rf $RPM_BUILD_ROOT%{_bindir}/%_arch64

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_libdir}/lib*.so.*
%{_libdir}/%_arch64/lib*.so*

%files devel
%defattr (-, root, root)
%{_libdir}/lib*.so
%{_libdir}/pkgconfig
%{_datadir}/gtk-doc
%{_includedir}/cairo
%{_libdir}/%_arch64/lib*.so
%{_libdir}/%_arch64/pkgconfig

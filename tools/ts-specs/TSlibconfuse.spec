%include Solaris.inc

%include arch64.inc
%use libconfuse64=libconfuse.spec
%include base.inc
%use libconfuse = libconfuse.spec

Name:		%{libconfuse.name}
Summary:	%{libconfuse.summary}
Version:	%{libconfuse.version}
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build

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
%libconfuse64.prep -d %name-%version/%_arch64
mkdir %name-%version/%{base_arch}
%libconfuse.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

%include arch64.inc
%include stdenv.inc
%libconfuse64.build -d %name-%version/%_arch64

%include base.inc
%include stdenv.inc
%libconfuse.build -d %name-%version/%{base_arch}

%install
%include stdenv.inc
%libconfuse64.install -d %name-%version/%_arch64

%include stdenv.inc
%libconfuse.install -d %name-%version/%{base_arch}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%dir %{_libdir}
%{_libdir}/lib*.so.*
%{_libdir}/%_arch64/*.so.*

%files devel
%defattr (-, root, root)
%dir %{_libdir}
%{_libdir}/lib*.so
%{_libdir}/%_arch64/lib*.so
%{_libdir}/pkgconfig
%{_libdir}/%_arch64/pkgconfig
%{_includedir}

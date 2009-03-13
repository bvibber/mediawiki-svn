%include Solaris.inc

%include arch64.inc
%use rrdtool64=rrdtool.spec
%include base.inc
%use rrdtool=rrdtool.spec

Name:                    %{rrdtool.name}
Summary:                 %{rrdtool.summary}
Version:                 %{rrdtool.version}
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

Requires: TSpango
BuildRequires: TSpango-devel

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
%rrdtool64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%rrdtool.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

%include arch64.inc
%include stdenv.inc
%rrdtool64.build -d %name-%version/%_arch64

%include base.inc
%include stdenv.inc
%rrdtool.build -d %name-%version/%{base_arch}

%install
%include stdenv.inc
%rrdtool64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.a

%include stdenv.inc
%rrdtool.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -f $RPM_BUILD_ROOT%{_libdir}/*.a

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_bindir}
%{_libdir}/*.so.*
%{_libdir}/%_arch64/*.so.*
%{_datadir}

%files devel
%defattr (-, root, root)
%dir %{_libdir}
%{_libdir}/*.so
%{_libdir}/%_arch64/*.so
%{_libdir}/pkgconfig
%{_libdir}/%_arch64/pkgconfig
%{_includedir}

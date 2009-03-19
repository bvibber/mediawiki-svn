%include Solaris.inc

%include arch64.inc
%use glib64=glib.spec
%include base.inc
%use glib=glib.spec

Name:                    %{glib.name}
Summary:                 %{glib.summary}
Version:                 %{glib.version}
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

BuildRequires: TSpkgconfig
BuildRequires: TSwhich

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
%glib64.prep -d %name-%version/%_arch64

mkdir %name-%version/%{base_arch}
%glib.prep -d %name-%version/%{base_arch}

%build
%include arch64.inc
%include stdenv.inc
%glib64.build -d %name-%version/%_arch64

%include base.inc
%include stdenv.inc
%glib.build -d %name-%version/%{base_arch}

%install
%include arch64.inc
%include stdenv.inc
%glib64.install -d %name-%version/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/*.la

%include base.inc
%include stdenv.inc
%glib.install -d %name-%version/%{base_arch}
rm -f $RPM_BUILD_ROOT%{_libdir}/*.la
rm -rf $RPM_BUILD_ROOT%{_bindir}/%_arch64
rm -f $RPM_BUILD_ROOT%{_libdir}/charset.alias
rm -f $RPM_BUILD_ROOT%{_libdir}/%_arch64/charset.alias

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_bindir}
%dir %{_libdir}
%{_libdir}/lib*.so.*
%dir %{_libdir}/gio
%dir %{_libdir}/gio/modules
%dir %{_libdir}/%_arch64/gio
%dir %{_libdir}/%_arch64/gio/modules
%dir %{_libdir}/%_arch64
%{_libdir}/%_arch64/lib*.so.*
%{_mandir}/man1
%{_datadir}/locale
%{_datadir}/glib-2.0

%files devel
%defattr (-, root, root)
%{_libdir}/lib*.so
%{_libdir}/glib-2.0
%{_libdir}/%_arch64/glib-2.0
%{_includedir}
%{_libdir}/%_arch64/lib*.so
%{_datadir}/gtk-doc
%{_libdir}/pkgconfig
%{_libdir}/%_arch64/pkgconfig
%{_datadir}/aclocal

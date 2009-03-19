%include Solaris.inc

%include arch64.inc
%use pkgconfig64=pkgconfig.spec
%include base.inc
%use pkgconfig=pkgconfig.spec

Name:		%{pkgconfig.name}
Summary:	pkgconfig
Version:	%{pkgconfig.version}
Source:		http://pkgconfig.freedesktop.org/releases/pkg-config-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

%prep
rm -rf %name-%version
mkdir %name-%version
 
mkdir %name-%version/%_arch64
%pkgconfig64.prep -d %name-%version/%_arch64
 
mkdir %name-%version/%{base_arch}
%pkgconfig.prep -d %name-%version/%{base_arch}

%build
%include arch64.inc
%include stdenv.inc
%pkgconfig64.build -d %name-%version/%_arch64
 
%include base.inc
%include stdenv.inc
%pkgconfig.build -d %name-%version/%{base_arch}

%install
%pkgconfig64.install -d %name-%version/%_arch64
%pkgconfig.install -d %name-%version/%{base_arch}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_bindir}
%{_datadir}/aclocal
%{_mandir}/man1

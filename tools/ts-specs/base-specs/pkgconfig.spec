Name:		TSpkgconfig
Summary:	pkgconfig
Version:	0.23
Source:		http://pkgconfig.freedesktop.org/releases/pkg-config-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

%prep
%setup -q -n pkg-config-%version

%build
%_configure
%_make

%install
%_make install DESTDIR=$RPM_BUILD_ROOT

%clean
rm -rf $RPM_BUILD_ROOT

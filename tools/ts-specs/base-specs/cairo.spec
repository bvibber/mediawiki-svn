Name:		TScairo
Summary:	Cairo vector graphics library
Version:	1.8.6
Source:		http://cairographics.org/releases/cairo-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n cairo-%version

%build

%_configure	--without-x		\
		--enable-svg=yes

%_make all

%install
%_make DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

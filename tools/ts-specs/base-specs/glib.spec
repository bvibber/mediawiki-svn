Name:		TSglib
Summary:	Glib utility library
Version:	2.18.4
Source:		http://ftp.gnome.org/pub/gnome/sources/glib/2.18/glib-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n glib-%version

%build

%_configure 				\
	--with-pcre=system		\
	--with-libiconv=gnu		\
	--disable-visibility		\
	--disable-gtk-doc

%_make all

%install
%_make DESTDIR=${RPM_BUILD_ROOT} install
rm -f $RPM_BUILD_ROOT/%_libdir/*.la

%clean
rm -rf $RPM_BUILD_ROOT

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

export PATH=/opt/ts/bin:$PATH
./configure --prefix=%{_prefix}			\
	    --bindir=%{_bindir}			\
	    --includedir=%{_includedir}		\
	    --mandir=%{_mandir}			\
            --libdir=%{_libdir}                 \
	    --disable-static			\
	    --enable-shared			\
		--with-pcre=system		\
		--with-libiconv=gnu		\
		--disable-visibility		\
		--disable-gtk-doc		\
            %{?configure_options}

/usr/sfw/bin/gmake -j$CPUS all

%install
export PATH=/opt/ts/bin:$PATH
/usr/sfw/bin/gmake DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

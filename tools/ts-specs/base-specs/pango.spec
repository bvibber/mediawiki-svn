Name:		TSpango
Summary:	Pango text rendering library
Version:	1.23.0
Source:		http://ftp.gnome.org/pub/GNOME/sources/pango/1.23/pango-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n pango-%version

%build
./configure --prefix=%{_prefix}			\
	    --bindir=%{_bindir}			\
	    --includedir=%{_includedir}		\
	    --mandir=%{_mandir}			\
            --libdir=%{_libdir}                 \
	    --disable-static			\
	    --enable-shared			\
		--without-x			\
		--enable-debug=no

gmake -j$CPUS all

%install
gmake DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

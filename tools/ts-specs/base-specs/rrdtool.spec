Name:		TSrrdtool
Summary:	rrdtool data graphing library
Version:	1.3.6
Source:		http://oss.oetiker.ch/rrdtool/pub/rrdtool-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n rrdtool-%version

%build
./configure --prefix=%{_prefix}			\
	    --bindir=%{_bindir}			\
	    --includedir=%{_includedir}		\
	    --mandir=%{_mandir}			\
            --libdir=%{_libdir}                 \
	    --disable-static			\
	    --enable-shared			\
		--disable-libintl		\
		--disable-python		\
		--disable-perl			\
		--disable-ruby

gmake -j$CPUS all

%install
gmake DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

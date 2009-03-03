Name:		TSlcms
Summary:	Little CMS
Version:	1.17
Source:		http://www.littlecms.com/lcms-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n lcms-%version

%build

./configure --prefix=%{_prefix}			\
	    --bindir=%{_bindir}			\
	    --includedir=%{_includedir}		\
	    --mandir=%{_mandir}			\
            --libdir=%{_libdir}                 \
            --enable-rpath			\
	    --disable-static			\
	    --enable-shared			\
	    --without-python			\
            %{?configure_options}

gmake -j$CPUS all

%install
gmake DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

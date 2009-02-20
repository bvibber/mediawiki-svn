#
# spec file for package TSlibogg
#
# includes module(s): libogg
#
Name:                    TSlibogg
Summary:                 Xiph Ogg library
Version:                 1.1.3
Source:                  http://downloads.xiph.org/releases/ogg/libogg-1.1.3.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n libogg-%version

%build

./configure --prefix=%{_prefix}			\
	    --bindir=%{_bindir}			\
	    --includedir=%{_includedir}		\
	    --mandir=%{_mandir}			\
            --libdir=%{_libdir}                 \
            --enable-rpath			\
            %{?configure_options}

dmake -j$CPUS all

%install
make DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

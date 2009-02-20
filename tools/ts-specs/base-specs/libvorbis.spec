#
# spec file for package TSlibvorbis
#
# includes module(s): libvorbis
#
Name:                    TSlibvorbis
Summary:                 Xiph Vorbis library
Version:                 1.2.0
Source:                  http://downloads.xiph.org/releases/vorbis/libvorbis-%{version}.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n libvorbis-%version

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

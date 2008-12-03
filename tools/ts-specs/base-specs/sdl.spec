# spec file for package TSsdl
Name:                    TSsdl
Summary:                 Simple DirectMedia Layer
Version:                 1.2.13
Source:                  http://www.libsdl.org/release/SDL-%{version}.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n SDL-%version

%build

./configure --prefix=%{_prefix}			\
	    --bindir=%{_bindir}			\
	    --includedir=%{_includedir}		\
	    --mandir=%{_mandir}			\
            --libdir=%{_libdir}                 \
            --enable-rpath			\
            %{?configure_options}

gmake -j$CPUS all

%install
gmake DESTDIR=${RPM_BUILD_ROOT} install
rm -f ${RPM_BUILD_ROOT}%{_libdir}/*.la

%clean
rm -rf $RPM_BUILD_ROOT

%changelog
* Wed Deec  3 2008 - river@loreley.flyingparchment.org.uk
- Initial spec


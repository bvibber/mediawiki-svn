#
# spec file for package TSlibiconv
#
# includes module(s): libiconv
#
Name:                    TSlibiconv
Summary:                 GNU iconv library
Version:                 1.12
Source:                  ftp://ftp.gnu.org/gnu/libiconv/libiconv-%{version}.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n libiconv-%version

%build

./configure --prefix=%{_prefix}			\
	    --bindir=%{_bindir}			\
	    --includedir=%{_includedir}		\
	    --mandir=%{_mandir}			\
            --libdir=%{_libdir}                 \
	    %{?configure_options}

gmake -j$CPUS all

%install
make DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

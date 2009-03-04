Name:		TSliblqr
Summary:	Liquid Rescale Library
Version:	0.3.0
Source:		http://liblqr.wikidot.com/local--files/en:download-page/liblqr-1-%{version}.tar.bz2
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n liblqr-1-%version

%build
PATH=/opt/ts/bin:$PATH 
export PATH

aclocal
automake
autoconf

./configure --prefix=%{_prefix}			\
	    --bindir=%{_bindir}			\
	    --includedir=%{_includedir}		\
	    --mandir=%{_mandir}			\
            --libdir=%{_libdir}                 \
	    --disable-static			\
	    --enable-shared			\
	    --enable-install-man		\
	    %{?configure_options}

gmake -j$CPUS all

%install
gmake DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

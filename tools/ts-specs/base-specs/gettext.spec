Name:		TSgettext
Summary:	GNU gettext
Version:	0.17
Source:		ftp://ftp.gnu.org/gnu/gettext/gettext-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
Patch1:		gettext-01-libsec.diff

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n gettext-%version
%patch1 -p0

%build

./configure --prefix=%{_prefix}			\
	    --bindir=%{_bindir}			\
	    --includedir=%{_includedir}		\
	    --mandir=%{_mandir}			\
            --libdir=%{_libdir}                 \
	    --disable-static			\
	    --enable-shared			\
 	    --program-prefix=g			\
	    %{?configure_options}

gmake -j$CPUS all

%install
gmake DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

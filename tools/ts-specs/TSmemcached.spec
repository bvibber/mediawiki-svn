#
# spec file for package TSmemcached

%include Solaris.inc

Name:                    TSmemcached
Summary:                 Fast in-memory object cache
Version:                 1.2.6
Source:			 http://www.danga.com/memcached/dist/memcached-%{version}.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

BuildRequires: TSlibevent
Requires: TSlibevent

%prep
rm -rf %name-%version
%setup -q -n memcached-%version

%build
export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags} -R%{_libdir}"
./configure --prefix=%{_prefix}			\
	    --bindir=%{_bindir}			\
	    --mandir=%{_mandir}                 \
            --infodir=%{_infodir}

gmake

%install
rm -rf $RPM_BUILD_ROOT
gmake install DESTDIR=$RPM_BUILD_ROOT
rm -f $RPM_BUILD_ROOT%{_infodir}/dir

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/*
%{_mandir}/*/*

%changelog
* Sun Jan 24 2008 - river@loreley.flyingparchment.org.uk
- initial spec

#
# spec file for package TSnano
#
# includes module(s): nano
#

%include Solaris.inc

Name:                    TSnano
Summary:                 GNU nano text editor
Version:                 1.2.5
Source:			 http://www.nano-editor.org/dist/v1.2/nano-%{version}.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n nano-%version

%build
export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags}"
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
%{_infodir}/*
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/*
%{_mandir}/*/*

%changelog
* Sun Jun 22 2008 - river@wikimedia.org
- modified for toolserver
* Wed Jul  5 2006 - laca@sun.com
- rename to SFEnano
- delete -share subpkg
- update file attributes
* Fri Feb  3 2006 - mike kiedrowski (lakeside-AT-cybrzn-DOT-com)
- Initial spec


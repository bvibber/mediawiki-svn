#
# spec file for package TSgnupg
#
# includes module(s): gnupg
#
#

%include Solaris.inc
%use gnupg = gnupg.spec

Name:          TSgnupg
Summary:       %{gnupg.summary}
Version:       %{gnupg.version}
SUNW_BaseDir:  %{_basedir}
BuildRoot:     %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc
Requires: SUNWbzip
Requires: SUNWzlib

%if %build_l10n
%package l10n
Summary:        %{summary} - l10n files
SUNW_BaseDir:   %{_basedir}
%include default-depend.inc
Requires:       %{name}
%endif

%prep
rm -rf %name-%version
mkdir -p %name-%version
%gnupg.prep -d %name-%version

%build
export CC=cc
export CFLAGS="%optflags"
export MSGFMT="/usr/bin/msgfmt"
export LDFLAGS="%_ldflags -R/opt/rt/lib"
%gnupg.build -d %name-%version

%install
rm -rf $RPM_BUILD_ROOT
%gnupg.install -d %name-%version
rm -rf $RPM_BUILD_ROOT%{_libdir}/lib*a
rm -rf $RPM_BUILD_ROOT%{_libdir}/charset.alias
rm -rf $RPM_BUILD_ROOT%{_datadir}/info

%if %build_l10n
%else
rm -rf $RPM_BUILD_ROOT%{_datadir}/locale
%endif

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (0755, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*
%dir %attr (0755, root, sys) %{_datadir}
%{_datadir}/gnupg
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/*
%{_mandir}/*/*

%if %build_l10n
%files l10n
%defattr (0755, root, bin)
%dir %attr (0755, root, sys) %{_datadir}
%attr (0755, root, other) %{_datadir}/locale
%endif

%changelog
* Tue Oct 14 2008 - river@wikimedia.org
- modified for toolserver
* Fri Jun 23 2006 - laca@sun.com
- rename to SFEgnupg
- update file attributes
* Mon Feb 20 2006 - damien.carbery@sun.com
- Update Build/Requires after running check-deps.pl script.
* Thu Dec 15 2005 - halton.huo@sun.com
- Remove /usr/lib/charset.alias for it's already installed by glib
* Sat Dec 08 2005 - halton.huo@sun.com
- initial version created

#
# spec file for package TSvim.spec
#
# includes module(s): vim
#
%include Solaris.inc

%define vim_version 72

Name:         TSvim
Summary:      Vim - vi improved
Version:      7.2
Source1:      ftp://ftp.vim.org/pub/vim/unix/vim-%{version}.tar.bz2
Source2:      ftp://ftp.vim.org/pub/vim/extra/vim-%{version}-lang.tar.gz
Source3:      ftp://ftp.vim.org/pub/vim/extra/vim-%{version}-extra.tar.gz
Patch1:       7.2.001
Patch2:       7.2.002
Patch3:       7.2.003
Patch4:       7.2.004
Patch5:       7.2.005
Patch6:       7.2.006
Patch7:       7.2.007
Patch8:       7.2.008
Patch9:       7.2.009
Patch10:       7.2.010
Patch11:       7.2.011
Patch12:       7.2.012
Patch13:       7.2.013
Patch14:       7.2.014
Patch15:       7.2.015
Patch16:       7.2.016
Patch17:       7.2.017
Patch18:       7.2.018
Patch19:       7.2.019
Patch20:       7.2.020
Patch21:       7.2.021
Patch22:       7.2.022
Patch23:       7.2.023
Patch24:       7.2.024
Patch25:       7.2.025
URL:          http://www.vim.org
SUNW_BaseDir: %{_basedir}
BuildRoot:    %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc
Requires: SUNWlibms
Requires: SUNWmlib
BuildRequires: SUNWmlibh

%if %build_l10n
%package l10n
Summary:                 %{summary} - l10n files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:                %{name}
%endif

%prep
%setup -q -T -b 1 -c -n %name-%version
gzip -dc %{SOURCE2} | gtar xf -
gzip -dc %{SOURCE3} | gtar xf -
cd vim%{vim_version}

for p in %{PATCH1}  %{PATCH2} %{PATCH3} %{PATCH4} %{PATCH5} %{PATCH6} %{PATCH7} \
	%{PATCH8} %{PATCH9} %{PATCH10} %{PATCH11} %{PATCH12} %{PATCH13} %{PATCH14} \
	%{PATCH15} %{PATCH16} %{PATCH17} %{PATCH18} %{PATCH19} %{PATCH20} %{PATCH21} \
	%{PATCH22} %{PATCH23} %{PATCH24} %{PATCH25} ; do
	echo $p
	gpatch -p0 <$p;
done

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi
export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS="%{_ldflags} -R/usr/sfw/lib"
cd vim%{vim_version}
./configure --prefix=%{_prefix} --mandir=%{_mandir} \
            --libdir=%{_libdir}              \
            --libexecdir=%{_libexecdir}      \
            --sysconfdir=%{_sysconfdir}	     \
            --enable-multibyte \
            --disable-hangulinput \
            --disable-gui \
            --without-x \
            --disable-fontset

gmake -j$CPUS 

%install
rm -rf $RPM_BUILD_ROOT
cd vim%{vim_version}
make DESTDIR=$RPM_BUILD_ROOT install
rm $RPM_BUILD_ROOT%{_mandir}/man1/ex.1
rm $RPM_BUILD_ROOT%{_mandir}/man1/view.1

rm -f $RPM_BUILD_ROOT%{_bindir}/ex
rm -f $RPM_BUILD_ROOT%{_bindir}/view

find $RPM_BUILD_ROOT%{_mandir} -name view.1 -exec rm -f {} \;
find $RPM_BUILD_ROOT%{_mandir} -name ex.1 -exec rm -f {} \;

%if %build_l10n
%else
# REMOVE l10n FILES
rm -rf $RPM_BUILD_ROOT%{_datadir}/vim/vim%{vim_version}/lang
rm -rf $RPM_BUILD_ROOT%{_mandir}/[a-z][a-z]
rm -rf $RPM_BUILD_ROOT%{_mandir}/[a-z][a-z].*
%endif

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, sys) %{_datadir}
%{_datadir}/vim
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/*
%{_mandir}/man1/*

%if %build_l10n
%files l10n
%defattr (-, root, bin)
%dir %attr (0755, root, sys) %{_datadir}
%{_datadir}/vim/vim%{vim_version}/lang
%{_mandir}/[a-z][a-z]
%{_mandir}/[a-z][a-z].*
%endif

%changelog
* Wed Oct  8 2008 - river@wikimedia.org
- 7.2
* Thu Jun 19 2008 - river@wikimedia.org
- modified for toolserver
* Tue Jul 17 2007 - halton.huo@sun.com
- Bump to 7.1
* Fri Jul 13 2007 - dougs@truemail.co.th
- Fixed cscope requirement clash
* Mon Sep 11 2006 - halton.huo@sun.com
- Correct remove l10n files part
* Mon Jul 10 2006 - laca@sun.com
- rename to SFEvim
- bump to 7.0
- delete -share subpkg, add -l10n subpkg
- update file attributes
- enable a bunch of features, add dependencies
* Wed Jun 28 2006 - halton.huo@sun.com
- Enable cscope plugin.
* Thu Apr  6 2006 - damien.carbery@sun.com
- Update Build/Requires after check-deps.pl run.
* Fri Jan 27 2005 - glynn.foster@sun.com
- Initial version

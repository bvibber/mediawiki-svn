#
# spec file for package SFEcurl
#
# includes module(s): curl
#
# 64 bit stuff shanelessly stolen from SFEncurses

%include Solaris.inc

Name:                    TScurl
Summary:                 curl - Get a file from FTP or HTTP server.
Version:                 7.19.0
URL:                     http://curl.haxx.se/
Source:                  http://curl.haxx.se/download/curl-%{version}.tar.bz2
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc
Requires:               SUNWzlib
Requires:               SUNWopenssl-libraries
BuildRequires:          SUNWopenssl-include

%ifarch amd64 sparcv9
%include arch64.inc
%use curl64=curl.spec
%endif
%include base.inc
%use curl = curl.spec

%package devel
Summary:		 %{summary} - development files
SUNW_BaseDir:            %{_basedir}
%include default-depend.inc
Requires:		 %name

%prep
rm -rf %name-%version
mkdir %name-%version

%ifarch amd64 sparcv9
mkdir %name-%version/%_arch64
%curl64.prep -d %name-%version/%_arch64
%endif

mkdir %name-%version/%{base_arch}
%curl.prep -d %name-%version/%{base_arch}

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

# -L/usr/sfw/lib added to CFLAGS to workaround what seems to be a libtool bug
export CC="cc"
export CXX="CC"
export CPPFLAGS="-I/usr/sfw/include"
export MSGFMT="/usr/bin/msgfmt"

%ifarch amd64 sparcv9
%include arch64.inc
export CFLAGS="%optflags -m64 -I/usr/sfw/include -DANSICPP -L/usr/sfw/lib/%_arch64"
export RPM_OPT_FLAGS="$CFLAGS"
export LDFLAGS="-m64 -L/usr/sfw/lib/%_arch64 -R/usr/sfw/lib/%_arch64"
%curl64.build -d %name-%version/%_arch64
%endif
%include base.inc
export LDFLAGS="-L/usr/sfw/lib -R/usr/sfw/lib"
export CFLAGS="%optflags -I/usr/sfw/include -DANSICPP -L/usr/sfw/lib"
export RPM_OPT_FLAGS="$CFLAGS"
%curl.build -d %name-%version/%{base_arch}

%install
%ifarch amd64 sparcv9
%curl64.install -d %name-%version/%_arch64
%endif

%curl.install -d %name-%version/%{base_arch}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/curl
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/lib*.so*
%dir %attr(0755, root, sys) %{_datadir}
#%dir %attr(0755, root, sys) %{_datadir}/curl
#%{_datadir}/curl/*
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/curl.1
%ifarch amd64 sparcv9
%{_bindir}/%_arch64/curl
%{_libdir}/%_arch64/lib*.so*
%endif

%files devel
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/curl-config
%dir %attr (0755, root, bin) %{_includedir}
%{_includedir}/*
%dir %attr (0755, root, bin) %{_libdir}
%{_libdir}/*.la
%{_libdir}/*.a
%dir %attr (0755, root, other) %{_libdir}/pkgconfig
%{_libdir}/pkgconfig/*
%dir %attr(0755, root, sys) %{_datadir}
#%dir %attr(0755, root, sys) %{_datadir}/curl
#%{_datadir}/curl/*
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/curl-config.1
%dir %attr(0755, root, bin) %{_mandir}/man3
%{_mandir}/man3/*
%ifarch amd64 sparcv9
%dir %attr(0755, root, bin) %{_libdir}/%_arch64
%{_libdir}/%_arch64/*.la
%{_libdir}/%_arch64/*.a
%dir %attr(0755, root, other) %{_libdir}/%_arch64/pkgconfig
%{_libdir}/%_arch64/pkgconfig/libcurl.pc
%{_bindir}/%_arch64/curl-config
%endif

%changelog
* Sat Jun 21 2008 - river@wikimedia.org
- modified for toolserver
* Mon May 05 2008 - brian.cameron@sun.com
- Bump to 7.18.1.
* Thu Feb 21 2008 - nonsea@users.sourceforge.net
- Bump to 7.18.0.
* Sun Jan 06 2008 - moinak.ghosh@sun.com
- Fixed pkgconfig directory permission
* Wed Dec 12 2007 - Michal Bielicki
- change the package to be combined 32/64 bit (thanks to Thomas Wagner for all
  his help with this)
* Mon Nov 26 2007 - Thomas Wagner
- move SFEcurl into /usr/gnu by %include usr-gnu.inc (never OS builds have
  SUNWcurl)
* Mon Oct 29 2007 - brian.cameron@sun.com
- Bump to 7.17.1
* Tue Sep 18 2007 - nonsea@users.sourceforge.net
- Bump to 7.17.0
* Mon May 28 2007 - Thomas Wagner
- bump to 7.16.2
- --disable-static
* Thu Feb 15 2007 - laca@sun.com
- bump to 7.16.1
* Wed Jan  3 2007 - laca@sun.com
- bump to 7.16.0
* Fri Jun 23 2006 - laca@sun.com
- rename to SFEcurl
- delete -share subpkg
- update attributes to match JDS
- add missing deps
* Sun May 14 2006 - mike kiedrowski (lakeside-AT-cybrzn-DOT-com)
- Delete *.la.
* Mon Apr  3 2006 - mike kiedrowski (lakeside-AT-cybrzn-DOT-com)
- Initial spec


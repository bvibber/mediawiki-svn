## % define _use_internal_dependency_generator 0
%define __perl_requires %{SOURCE98}
## % define __find_requires %{SOURCE99}

Summary: The Squid proxy caching server.
Name: squid
Version: 2.5.STABLE13
Release: 1wm
Epoch: 7
License: GPL
Group: System Environment/Daemons
Vendor: Wikimedia
Source: http://www.squid-cache.org/Squid/Versions/v2/2.5/squid-%{version}.tar.bz2
Source1: http://www.squid-cache.org/Squid/FAQ/FAQ.sgml
Source2: squid.init
Source3: squid.logrotate
Source4: squid.sysconfig
Source5: squid.pam
Source6: squid.cron
Source98: perl-requires-squid.sh

# Upstream patches
Patch100: squid-2.5.STABLE12-epoll.patch

# Local patches
# Putting upstream patches first lowers the chances that we'll need to modify
# them because of local patch changes.
Patch201: squid-2.5.STABLE11-config.patch
Patch202: squid-2.5.STABLE4-location.patch
Patch203: squid-2.5.STABLE7-build.patch
Patch204: squid-2.5.STABLE4-perlpath.patch
Patch205: squid-2.5.STABLE5-pipe.patch
#Patch206: squid-2.5.STABLE11-libbind.patch

# Wikimedia patches
Patch251: squid-htcp-clr.diff
Patch252: squid-2.5.STABLE12RC1-errors.patch

BuildRoot: %{_tmppath}/%{name}-%{version}-root
Prereq: /sbin/chkconfig logrotate shadow-utils
Requires: bash >= 2.0
BuildPrereq: openjade linuxdoc-tools openssl-devel automake15 autoconf213
Obsoletes: squid-novm

%description
Squid is a high-performance proxy caching server for Web clients,
supporting FTP, gopher, and HTTP data objects. Unlike traditional
caching software, Squid handles all requests in a single,
non-blocking, I/O-driven process. Squid keeps meta data and especially
hot objects cached in RAM, caches DNS lookups, supports non-blocking
DNS lookups, and implements negative caching of failed requests.

Squid consists of a main server program squid, a Domain Name System
lookup program (dnsserver), a program for retrieving FTP data
(ftpget), and some management and client tools.

%prep
%setup -q

%patch100 -p1
./bootstrap.sh

%patch201 -p1 -b .config
%patch202 -p1 -b .location
%patch203 -p1 -b .build
%patch204 -p1 -b .perlpath
%patch205 -p1 -b .pipe
#%patch206 -p1 -b .libbind
%patch251 -p0 -b .htcpclr
%patch252 -p1 -b .errors

%build
 export CFLAGS="-fPIE -Os -g -pipe -fsigned-char" ; export LDFLAGS=-pie ;
%configure \
   --exec_prefix=/usr \
   --bindir=%{_sbindir} \
   --libexecdir=%{_libdir}/squid \
   --localstatedir=/var \
   --sysconfdir=/etc/squid \
   --enable-epoll \
   --enable-snmp \
   --enable-removal-policies="heap,lru" \
   --enable-storeio="aufs,coss,diskd,null,ufs" \
   --enable-ssl \
   --with-openssl=/usr/kerberos \
   --enable-delay-pools \
   --enable-linux-netfilter \
   --with-pthreads \
   --enable-ntlm-auth-helpers="SMB,winbind" \
   --enable-external-acl-helpers="ip_user,unix_group,wbinfo_group,winbind_group" \
   --enable-auth="basic,ntlm" \
   --with-winbind-auth-challenge \
   --enable-useragent-log \
   --enable-referer-log \
   --disable-dependency-tracking \
   --enable-cachemgr-hostname=localhost \
   --disable-ident-lookups \
   --enable-truncate \
   --enable-underscores \
   --enable-gnuregex \
   --enable-htcp \
   --enable-time-hack \
   --with-maxfd=8192 \
   --disable-icmp \
   --enable-cache-digests \
   --enable-carp \
   --datadir=%{_datadir}

export CFLAGS="-fPIE -Os -g -pipe -fsigned-char" ; export LDFLAGS=-pie ;
make %{?_smp_mflags}

mkdir faq
cp %{SOURCE1} faq
cd faq
sgml2html FAQ.sgml

#cd ..

%install
rm -rf $RPM_BUILD_ROOT
%makeinstall  \
	sysconfdir=$RPM_BUILD_ROOT/etc/squid \
	localstatedir=$RPM_BUILD_ROOT/var \
	bindir=$RPM_BUILD_ROOT/%{_sbindir} \
	libexecdir=$RPM_BUILD_ROOT/%{_libdir}/squid

ln -s %{_datadir}/squid/errors/English $RPM_BUILD_ROOT/etc/squid/errors
ln -s %{_datadir}/squid/icons $RPM_BUILD_ROOT/etc/squid/icons

mkdir -p $RPM_BUILD_ROOT/etc/rc.d/init.d
mkdir -p $RPM_BUILD_ROOT/etc/logrotate.d
mkdir -p $RPM_BUILD_ROOT/etc/sysconfig
mkdir -p $RPM_BUILD_ROOT/etc/pam.d
mkdir -p $RPM_BUILD_ROOT/etc/cron.d
install -m 755 %{SOURCE2} $RPM_BUILD_ROOT/etc/rc.d/init.d/squid
install -m 644 %{SOURCE3} $RPM_BUILD_ROOT/etc/logrotate.d/squid
install -m 644 %{SOURCE4} $RPM_BUILD_ROOT/etc/sysconfig/squid
install -m 644 %{SOURCE5} $RPM_BUILD_ROOT/etc/pam.d/squid
install -m 644 %{SOURCE6} $RPM_BUILD_ROOT/etc/cron.d/squid
mkdir -p $RPM_BUILD_ROOT/var/log/squid
mkdir -p $RPM_BUILD_ROOT/var/spool/squid

# remove unpackaged files from the buildroot
rm -f $RPM_BUILD_ROOT%{_sbindir}/{RunAccel,RunCache}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,root)
%doc faq/* README ChangeLog QUICKSTART doc/*
%doc contrib/url-normalizer.pl contrib/rredir.* contrib/user-agents.pl

%attr(755,root,root) %dir /etc/squid
%attr(755,root,root) %dir %{_libdir}/squid
%attr(750,squid,squid) %dir /var/log/squid
%attr(750,squid,squid) %dir /var/spool/squid
%attr(644,root,root) /etc/pam.d/squid
%config(noreplace) %attr(640,root,squid) /etc/squid/squid.conf
%config(noreplace) %attr(640,root,squid) /etc/squid/cachemgr.conf
%config(noreplace) /etc/squid/mime.conf
%config(noreplace) /etc/sysconfig/squid
#%config(noreplace) /etc/squid/msntauth.conf
%config(noreplace) /etc/squid/mib.txt
#/etc/squid/msntauth.conf.default
/etc/squid/squid.conf.default
/etc/squid/mime.conf.default

%{_datadir}/squid/icons
%config(noreplace) %{_datadir}/squid/errors
%config(noreplace) /etc/squid/errors
%{_sbindir}/squid
%{_sbindir}/squidclient
%config(noreplace) /etc/squid/icons
%config(noreplace) /etc/rc.d/init.d/squid
%config(noreplace) /etc/logrotate.d/squid
%config(noreplace) /etc/cron.d/squid
%doc faq/* README ChangeLog QUICKSTART doc/*
#%doc contrib/url-normalizer.pl contrib/rredir.* contrib/user-agents.pl
%{_mandir}/man8/*
%{_libdir}/squid/*

%pre
if getent passwd squid >/dev/null 2>&1 ; then : ; else /usr/sbin/useradd -u 23 -d /var/spool/squid -r -s /sbin/nologin squid >/dev/null 2>&1 || exit 1 ; fi

for i in /var/log/squid /var/spool/squid ; do
	if [ -d $i ] ; then
		for adir in `find $i -maxdepth 0 \! -user squid`; do
			chown -R squid:squid $adir
		done
	fi
done

exit 0

%post
/sbin/chkconfig --add squid
if [ $1 = 0 ]; then
 case "$LANG" in
  bg*)
     DIR=Bulgarian
     ;;
  ca*)
     DIR=Catalan
     ;;
  cs*)
     DIR=Czech
     ;;
  da*)
     DIR=Danish
     ;;
  nl*)
     DIR=Dutch
     ;;
  en*)
     DIR=English
     ;;
  ea*)
     DIR=Estonian
     ;;
  fi*)
     DIR=Finnish
     ;;
  fr*)
     DIR=French
     ;;
  de*)
     DIR=German
     ;;
  he*)
     DIR=Hebrew
     ;;
  hu*)
     DIR=Hungarian
     ;;
  it*)
     DIR=Italian
     ;;
  ja*)
     DIR=Japanese
     ;;
  kr*)
     DIR=Korean
     ;;
  pl*)
     DIR=Polish
     ;;
  pt*)
     DIR=Portuguese
     ;;
  ro*)
     DIR=Romanian
     ;;
  ru*)
     DIR=Russian-koi8-r
     ;;
  sr*)
     DIR=Serbian
     ;;
  sk*)
     DIR=Slovak
     ;;
  es*)
     DIR=Spanish
     ;;
  sv*)
     DIR=Swedish
     ;;
  zh_TW*)
     DIR=Traditional_Chinese
     ;;
  zh_CN*)
     DIR=Simplify_Chinese
     ;;
  tr*)
     DIR=Turkish
     ;;
  greek)
     DIR=Greek
     ;;
  *)
     DIR=English
     ;;
 esac
 ln -snf %{_datadir}/squid/errors/$DIR /etc/squid/errors
fi


%preun
if [ $1 = 0 ] ; then
	service squid stop >/dev/null 2>&1
	rm -f /var/log/squid/*
	/sbin/chkconfig --del squid
fi

%postun
if [ "$1" -ge "1" ] ; then
	service squid condrestart >/dev/null 2>&1
fi

%triggerin -- samba-common
chgrp squid /var/cache/samba/winbindd_privileged > /dev/null 2>& 1 || true

%changelog
* Wed Apr 26 2006 Mark Bergsma <mark@nedworks.org> 7:2.5.STABLE13-1.WM
- New upstream version 2.5.STABLE13 which hopefully fixes the grave memleak

* Tue Feb 2 2006 Mark Bergsma <mark@nedworks.org> 7:2.5.STABLE12-3.WM
- Built for FC4

* Sun Oct 30 2005 Mark Bergsma <mark@nedworks.org> 7:2.5.STABLE12-1.WM
- Upgrade upstream to 2.5.STABLE12
- Include a cron job that checks whether squid should be running and restarts
  it if it isn't

* Fri Oct 28 2005 Mark Bergsma <mark@nedworks.org> 7:2.5.STABLE12RC1-1noepoll.WM
- Build without epoll, to compare memory leaking behaviour

* Mon Oct 17 2005 Mark Bergsma <mark@nedworks.org> 7:2.5.STABLE12RC1-1.WM
- Adapted FC3 RPM to Wikimedia needs
- Changed upstream version to 2.5.STABLE12-RC1
- Removed most of the FC3/upstream patches
- Patched in epoll support
- Patched in HTCP PURGE support
- Removed some (build) dependencies Wikimedia doesn't need, e.g. LDAP
- Adapted configure options to Wikimedia needs
- Extended the init script and sysconfig file with maximum file
  descriptor setting
- Incorporated modified Wikimedia error pages
- Rotate logs every 10 minutes

* Thu Sep 29 2005 Martin Stransky <stransky@redhat.com> 7:2.5.STABLE11-2.FC3
- added patch for delay pools and some minor fixes

* Fri Sep 23 2005 Martin Stransky <stransky@redhat.com> 7:2.5.STABLE11-1.FC3
- update to STABLE11

* Tue Sep 6 2005 Martin Stransky <stransky@redhat.com> 7:2.5.STABLE9-1.FC3.7
- Three upstream patches for #167414
- Spanish and Greek messages
- patch for -D_FORTIFY_SOURCE=2 

* Mon May 16 2005 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE9-1.FC3.6
- More upstream patches, including ones for
  bz#157456 CAN-2005-1519 DNS lookups unreliable on untrusted networks
  bz#156162 CVE-1999-0710 cachemgr.cgi access control bypass

- The following bugs had already been fixed, but the announcements were lost
  bz#156711 CAN-2005-1390 HTTP Request Smuggling Vulnerabilities
  bz#156703 CAN-2005-1389 HTTP Response Splitting Vulnerabilities
  (Both fixed by squid-7:2.5.STABLE8-1.FC3.1)
  bz#151419 Unexpected access control results on configuration errors
  (Fixed by 7:2.5.STABLE9-1.FC3.2)
  bz#152647#squid-2.5.STABLE9-1.FC3.4.x86_64.rpm is broken
  (fixed by 7:2.5.STABLE9-1.FC3.5)
  bz#141938 squid ldap authentification broken
  (Fixed by 7:2.5.STABLE7-1.FC3)

* Fri Apr 1 2005 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE9-1.FC3.5
- More upstream patches, including a new version of the -2GB patch
  that doesn't break diskd.

* Wed Mar 23 2005 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE9-1.FC3.4
- Add more upstream patches.
- add the -libbind patch, to avoid picking up a new dependency on libbind.
- Remove references to /etc/squid/errors from this spec, since squid
  now uses {_datadir}/squid/errors/English/ by default (overridable in
  /etc/squid/squid.conf, as always)
- mark {_datadir}/squid/errors as config(noreplace) so custom error messages
  won't get stomped on.

* Wed Mar 16 2005 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE9-1.FC3.3
- Actually apply the -date patch.

* Wed Mar 16 2005 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE9-1.FC3.2
- New upstream version, with 14 patches.  Includes fix for
  bz#150234 cookie leak in squid

* Fri Feb 18 2005 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE8-1.FC3.1
- New upstream version, includes fix for bz#148884 CAN-2005-0446
- Reorganize spec file to put local patches after upstream ones.

* Tue Feb 1 2005 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE7-1.FC3.1
- Add more upstream patches, including fixes for
  bz#146783  Correct handling of oversized reply headers
  bz#146778  CAN-2005-0211 Buffer overflow in WCCP recvfrom() call

* Thu Jan 20 2005 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE7-1.FC3
- Upgrade to 2.5.STABLE7 and 18 upstream patches.
- This includes fixes for CAN-2005-0094 CAN-2005-0095 CAN-2004-0096
  and CAN-2004-0097.  This closes bz#145543 and bz#141938
- This obsoletes Ulrich Drepper's -nonbl patch.
- Add a triggerin on samba-common to make /var/cache/samba/winbindd_privileged
  accessable so that ntlm_auth will work.
  This fixes bz#103726

* Mon Oct 18 2004 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE6-3
- include patch from Ulrich Drepper <drepper@redhat.com> to stop
  problems with O_NONBLOCK.  This closes #136049

* Tue Oct 12 2004 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE6-2
- Include fix for CAN-2004-0918

* Tue Sep 28 2004 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE6-1
- New upstream version, with 32 upstream patches.
  This closes #133970, #133931, #131728, #128143, #126726

- Change the permissions on /etc/squid/squid.conf to 640.  This closes
  bugzilla #125007

* Mon Jun 28 2004 Jay Fenlason <fenlason@redhat.com> 7:2.5STABLE5-5
- Merge current upstream patches.
- Fix the -pipe patch to have the correct name of the winbind pipe.

* Tue Jun 15 2004 Elliot Lee <sopwith@redhat.com>
- rebuilt

* Mon Apr 5 2004 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE5-2
- Include the first 10 upstream patches
- Add a patch for the correct location of the winbindd pipe.  This closes
  bugzilla #107561
- Remove the change to ssl_support.c from squid-2.5.STABLE3-build patch
  This closes #117851
- Include /etc/pam.d/squid .  This closes #113404
- Include a patch to close #111254 (assignment in assert)
- Change squid.init to put output messages in /var/log/squid/squid.out
  This closes #104697
- Only useradd the squid user if it doesn't already exist, and error out
  if the useradd fails.  This closes #118718.

* Tue Mar 2 2004 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE5-1
- New upstream version, obsoletes many patches.
- Fix --datadir passed to configure.  Configure automatically adds /squid
  so we shouldn't.
- Remove the problematic triggerpostun trigger, since is's broken, and FC2
  never shipped with that old version.
- add %{?_smp_mflags} to make line.

* Tue Mar 02 2004 Elliot Lee <sopwith@redhat.com>
- rebuilt

* Mon Feb 23 2004 Tim Waugh <twaugh@redhat.com>
- Use ':' instead of '.' as separator for chown.

* Fri Feb 20 2004 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE4-3
- Clean up the spec file to work on 64-bit platforms (use %{_libdir}
  instead of /usr/lib, etc)
- Make the release number in the changelog section agree with reality.
- use -fPIE rather than -fpie.  s390 fails with just -fpie

* Fri Feb 13 2004 Elliot Lee <sopwith@redhat.com>
- rebuilt

* Thu Feb 5 2004 Jay Fenlason <fenlason@redhat.com>
- Incorporate many upstream patches
- Include many spec file changes from D.Johnson <dj@www.uk.linux.org>

* Tue Sep 23 2003 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE4-1
- New upstream version.
- Fix the Source: line in this spec file to point to the correct URL.
- redo the -location patch to work with the new upstream version.

* Mon Jun 30 2003 Jay Fenlason <fenlason@redhat.com> 7:2.5.STABLE3-0
- Spec file change to enable the nul storage module. bugzilla #74654
- Upgrade to 2.5STABLE3 with current official patches.
- Added --enable-auth="basic,ntlm": closes bugzilla #90145
- Added --with-winbind-auth-challenge: closes bugzilla #78691
- Added --enable-useragent-log and --enable-referer-log, closes
- bugzilla #91884
# - Changed configure line to enable pie
# (Disabled due to broken compilers on ia64 build machines)
#- Patched to increase the maximum number of file descriptors #72896
#- (disabled for now--needs more testing)

* Wed Jun 04 2003 Elliot Lee <sopwith@redhat.com>
- rebuilt

* Wed Jan 22 2003 Tim Powers <timp@redhat.com>
- rebuilt

* Wed Jan 15 2003 Bill Nottingham <notting@redhat.com> 7:2.5.STABLE1-1
- update to 2.5.STABLE1

* Wed Nov 27 2002 Tim Powers <timp@redhat.com> 7:2.4.STABLE7-5
- remove unpackaged files from the buildroot

* Tue Aug 27 2002 Nalin Dahyabhai <nalin@redhat.com> 2.4.STABLE7-4
- rebuild

* Wed Jul 31 2002 Karsten Hopp <karsten@redhat.de>
- don't raise an error if the config file is incomplete
  set defaults instead (#69322, #70065)

* Thu Jul 18 2002 Bill Nottingham <notting@redhat.com> 2.4.STABLE7-2
- don't strip binaries

* Mon Jul  8 2002 Bill Nottingham <notting@redhat.com>
- update to 2.4.STABLE7
- fix restart (#53761)

* Tue Jun 25 2002 Bill Nottingham <notting@redhat.com>
- add various upstream bugfix patches

* Fri Jun 21 2002 Tim Powers <timp@redhat.com>
- automated rebuild

* Thu May 23 2002 Tim Powers <timp@redhat.com>
- automated rebuild

* Fri Mar 22 2002 Bill Nottingham <notting@redhat.com>
- 2.4.STABLE6
- turn off carp

* Mon Feb 18 2002 Bill Nottingham <notting@redhat.com>
- 2.4.STABLE3 + patches
- turn off HTCP at request of maintainers
- leave SNMP enabled in the build, but disabled in the default config

* Fri Jan 25 2002 Tim Powers <timp@redhat.com>
- rebuild against new libssl

* Wed Jan 09 2002 Tim Powers <timp@redhat.com>
- automated rebuild

* Mon Jan 07 2002 Florian La Roche <Florian.LaRoche@redhat.de>
- require linuxdoc-tools instead of sgml-tools

* Tue Sep 25 2001 Bill Nottingham <notting@redhat.com>
- update to 2.4.STABLE2

* Mon Sep 24 2001 Bill Nottingham <notting@redhat.com>
- add patch to fix FTP crash

* Mon Aug  6 2001 Bill Nottingham <notting@redhat.com>
- fix uninstall (#50411)

* Mon Jul 23 2001 Bill Nottingham <notting@redhat.com>
- add some buildprereqs (#49705)

* Sun Jul 22 2001 Bill Nottingham <notting@redhat.com>
- update FAQ

* Tue Jul 17 2001 Bill Nottingham <notting@redhat.com>
- own /etc/squid, /usr/lib/squid

* Tue Jun 12 2001 Nalin Dahyabhai <nalin@redhat.com>
- rebuild in new environment
- s/Copyright:/License:/

* Tue Apr 24 2001 Bill Nottingham <notting@redhat.com>
- update to 2.4.STABLE1 + patches
- enable some more configure options (#24981)
- oops, ship /etc/sysconfig/squid

* Fri Mar  2 2001 Nalin Dahyabhai <nalin@redhat.com>
- rebuild in new environment

* Tue Feb  6 2001 Trond Eivind Glomsrød <teg@redhat.com>
- improve i18n
- make the initscript use the standard OK/FAILED

* Tue Jan 23 2001 Bill Nottingham <notting@redhat.com>
- change i18n mechanism

* Fri Jan 19 2001 Bill Nottingham <notting@redhat.com>
- fix path references in QUICKSTART (#15114)
- fix initscript translations (#24086)
- fix shutdown logic (#24234), patch from <jos@xos.nl>
- add /etc/sysconfig/squid for daemon options & shutdown timeouts
- three more bugfixes from the Squid people
- update FAQ.sgml
- build and ship auth modules (#23611)

* Thu Jan 11 2001 Bill Nottingham <notting@redhat.com>
- initscripts translations

* Mon Jan  8 2001 Bill Nottingham <notting@redhat.com>
- add patch to use mkstemp (greg@wirex.com)

* Fri Dec 01 2000 Bill Nottingham <notting@redhat.com>
- rebuild because of broken fileutils

* Sat Nov 11 2000 Bill Nottingham <notting@redhat.com>
- fix the acl matching cases (only need the second patch)

* Tue Nov  7 2000 Bill Nottingham <notting@redhat.com>
- add two patches to fix domain ACLs
- add 2 bugfix patches from the squid people

* Fri Jul 28 2000 Bill Nottingham <notting@redhat.com>
- clean up init script; fix condrestart
- update to STABLE4, more bugfixes
- update FAQ

* Tue Jul 18 2000 Nalin Dahyabhai <nalin@redhat.com>
- fix syntax error in init script
- finish adding condrestart support

* Fri Jul 14 2000 Bill Nottingham <notting@redhat.com>
- move initscript back

* Wed Jul 12 2000 Prospector <bugzilla@redhat.com>
- automatic rebuild

* Thu Jul  6 2000 Bill Nottingham <notting@redhat.com>
- prereq /etc/init.d
- add bugfix patch
- update FAQ

* Thu Jun 29 2000 Bill Nottingham <notting@redhat.com>
- fix init script

* Tue Jun 27 2000 Bill Nottingham <notting@redhat.com>
- don't prereq new initscripts

* Mon Jun 26 2000 Bill Nottingham <notting@redhat.com>
- initscript munging

* Sat Jun 10 2000 Bill Nottingham <notting@redhat.com>
- rebuild for exciting FHS stuff

* Wed May 31 2000 Bill Nottingham <notting@redhat.com>
- fix init script again (#11699)
- add --enable-delay-pools (#11695)
- update to STABLE3
- update FAQ

* Fri Apr 28 2000 Bill Nottingham <notting@redhat.com>
- fix init script (#11087)

* Fri Apr  7 2000 Bill Nottingham <notting@redhat.com>
- three more bugfix patches from the squid people
- buildprereq jade, sgmltools

* Sun Mar 26 2000 Florian La Roche <Florian.LaRoche@redhat.com>
- make %pre more portable

* Thu Mar 16 2000 Bill Nottingham <notting@redhat.com>
- bugfix patches
- fix dependency on /usr/local/bin/perl

* Sat Mar  4 2000 Bill Nottingham <notting@redhat.com>
- 2.3.STABLE2

* Mon Feb 14 2000 Bill Nottingham <notting@redhat.com>
- Yet More Bugfix Patches

* Tue Feb  8 2000 Bill Nottingham <notting@redhat.com>
- add more bugfix patches
- --enable-heap-replacement

* Mon Jan 31 2000 Cristian Gafton <gafton@redhat.com>
- rebuild to fix dependencies

* Fri Jan 28 2000 Bill Nottingham <notting@redhat.com>
- grab some bugfix patches

* Mon Jan 10 2000 Bill Nottingham <notting@redhat.com>
- 2.3.STABLE1 (whee, another serial number)

* Tue Dec 21 1999 Bernhard Rosenkraenzer <bero@redhat.com>
- Fix compliance with ftp RFCs
  (http://www.wu-ftpd.org/broken-clients.html)
- Work around a bug in some versions of autoconf
- BuildPrereq sgml-tools - we're using sgml2html

* Mon Oct 18 1999 Bill Nottingham <notting@redhat.com>
- add a couple of bugfix patches

* Wed Oct 13 1999 Bill Nottingham <notting@redhat.com>
- update to 2.2.STABLE5.
- update FAQ, fix URLs.

* Sat Sep 11 1999 Cristian Gafton <gafton@redhat.com>
- transform restart in reload and add restart to the init script

* Tue Aug 31 1999 Bill Nottingham <notting@redhat.com>
- add squid user as user 23.

* Mon Aug 16 1999 Bill Nottingham <notting@redhat.com>
- initscript munging
- fix conflict between logrotate & squid -k (#4562)

* Wed Jul 28 1999 Bill Nottingham <notting@redhat.com>
- put cachemgr.cgi back in /usr/lib/squid

* Wed Jul 14 1999 Bill Nottingham <notting@redhat.com>
- add webdav bugfix patch (#4027)

* Mon Jul 12 1999 Bill Nottingham <notting@redhat.com>
- fix path to config in squid.init (confuses linuxconf)

* Wed Jul  7 1999 Bill Nottingham <notting@redhat.com>
- 2.2.STABLE4

* Wed Jun 9 1999 Dale Lovelace <dale@redhat.com>
- logrotate changes
- errors from find when /var/spool/squid or
- /var/log/squid didn't exist

* Thu May 20 1999 Bill Nottingham <notting@redhat.com>
- 2.2.STABLE3

* Thu Apr 22 1999 Bill Nottingham <notting@redhat.com>
- update to 2.2.STABLE.2

* Sun Apr 18 1999 Bill Nottingham <notting@redhat.com>
- update to 2.2.STABLE1

* Thu Apr 15 1999 Bill Nottingham <notting@redhat.com>
- don't need to run groupdel on remove
- fix useradd

* Mon Apr 12 1999 Bill Nottingham <notting@redhat.com>
- fix effective_user (bug #2124)

* Mon Apr  5 1999 Bill Nottingham <notting@redhat.com>
- strip binaries

* Thu Apr  1 1999 Bill Nottingham <notting@redhat.com>
- duh. adduser does require a user name.
- add a serial number

* Tue Mar 30 1999 Bill Nottingham <notting@redhat.com>
- add an adduser in %pre, too

* Thu Mar 25 1999 Bill Nottingham <notting@redhat.com>
- oog. chkconfig must be in %preun, not %postun

* Wed Mar 24 1999 Bill Nottingham <notting@redhat.com>
- switch to using group squid
- turn off icmp (insecure)
- update to 2.2.DEVEL3
- build FAQ docs from source

* Tue Mar 23 1999 Bill Nottingham <notting@redhat.com>
- logrotate changes

* Sun Mar 21 1999 Cristian Gafton <gafton@redhat.com>
- auto rebuild in the new build environment (release 4)

* Wed Feb 10 1999 Bill Nottingham <notting@redhat.com>
- update to 2.2.PRE2

* Wed Dec 30 1998 Bill Nottingham <notting@redhat.com>
- cache & log dirs shouldn't be world readable
- remove preun script (leave logs & cache @ uninstall)

* Tue Dec 29 1998 Bill Nottingham <notting@redhat.com>
- fix initscript to get cache_dir correct

* Fri Dec 18 1998 Bill Nottingham <notting@redhat.com>
- update to 2.1.PATCH2
- merge in some changes from RHCN version

* Sat Oct 10 1998 Cristian Gafton <gafton@redhat.com>
- strip binaries
- version 1.1.22

* Sun May 10 1998 Cristian Gafton <gafton@redhat.com>
- don't make packages conflict with each other...

* Sat May 02 1998 Cristian Gafton <gafton@redhat.com>
- added a proxy auth patch from Alex deVries <adevries@engsoc.carleton.ca>
- fixed initscripts

* Thu Apr 09 1998 Cristian Gafton <gafton@redhat.com>
- rebuilt for Manhattan

* Fri Mar 20 1998 Cristian Gafton <gafton@redhat.com>
- upgraded to 1.1.21/1.NOVM.21

* Mon Mar 02 1998 Cristian Gafton <gafton@redhat.com>
- updated the init script to use reconfigure option to restart squid instead
  of shutdown/restart (both safer and quicker)

* Sat Feb 07 1998 Cristian Gafton <gafton@redhat.com>
- upgraded to 1.1.20
- added the NOVM package and tryied to reduce the mess in the spec file

* Wed Jan 7 1998 Cristian Gafton <gafton@redhat.com>
- first build against glibc
- patched out the use of setresuid(), which is available only on kernels
  2.1.44 and later

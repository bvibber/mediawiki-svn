#
# spec file for package gnupg
#

Name:         gnupg
Version:      1.4.9
Release:      1
Summary:      gnupg - GNU Utility for data encryption and digital signatures.
License:      GPL
Group:        Applications/Cryptography
Copyright:    GPL
Autoreqprov:  on
URL:          http://www.gnupg.org/
Source:       ftp://ftp.gnupg.org/gcrypt/gnupg/%{name}-%{version}.tar.bz2
BuildRoot:    %{_tmppath}/%{name}-%{version}-build

%description
GnuPG (GNU Privacy Guard) is a GNU utility for encrypting data and
creating digital signatures. GnuPG has advanced key management
capabilities and is compliant with the proposed OpenPGP Internet
standard described in RFC-2440.  Since GnuPG doesn't use any patented
algorithms, it is not compatible with some versions of PGP 2 which use
only the patented IDEA algorithm.  See
http://www.gnupg.org/why-not-idea.html for information on using IDEA
if the patent does not apply to you and you need to be compatible with
these versions of PGP 2.

%prep
%setup -n %{name}-%{version}

%build
export CC=cc
CFLAGS="$RPM_OPT_FLAGS" ./configure \
                        --prefix=%{_prefix} \
                        --libexecdir=%{_libexecdir} \
			--mandir=%{_mandir} \
			--infodir=%{_datadir}/info \
			--disable-nls
make 

%install
[ "$RPM_BUILD_ROOT" != "/" ] && [ -d $RPM_BUILD_ROOT ] && rm -rf $RPM_BUILD_ROOT;
make DESTDIR=$RPM_BUILD_ROOT install

%clean
rm -fr $RPM_BUILD_ROOT
make distclean

%post
/sbin/install-info %{_infodir}/gpg.info %{_infodir}/dir 2>/dev/null || :
/sbin/install-info %{_infodir}/gpgv.info %{_infodir}/dir 2>/dev/null || :

%preun
if [ $1 = 0 ]; then
   /sbin/install-info --delete %{_infodir}/gpg.info \
        %{_infodir}/dir 2>/dev/null || :
   /sbin/install-info --delete %{_infodir}/gpgv.info \
        %{_infodir}/dir 2>/dev/null || :
fi

%files
%defattr (-,root,root)

%doc INSTALL AUTHORS COPYING NEWS README THANKS TODO PROJECTS doc/DETAILS
%doc doc/FAQ doc/faq.html doc/HACKING doc/OpenPGP doc/samplekeys.asc
%doc %attr (0755,root,root) tools/convert-from-106
%config %{_datadir}/%{name}/options.skel
%{_mandir}/man1/*
%{_mandir}/man7/*
%{_infodir}/gpg.info*
%{_infodir}/gpgv.info*
%attr (4755,root,root) %{_bindir}/gpg
%attr (0755,root,root) %{_bindir}/gpgv
%attr (0755,root,root) %{_bindir}/gpgsplit
%attr (0755,root,root) %{_libexecdir}/gnupg/*

%changelog -n gnupg
* Tue Oct 14 2008 - river@wikimedia.org
- 1.4.9
* Fri Dec 21 2007 - jijun.yu@sun.com
- Bump to 1.4.8
* Mon Jul 16 2007 - dick@nagual.nl
- Bump to 1.4.7
* Thu Mar 16 2006 - damien.carbery@sun.com
- Bump to 1.4.2.2.
* Sat Dec 08 2005 - halton.huo@sun.com
- create package

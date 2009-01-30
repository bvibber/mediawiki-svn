%define _basedir /opt/TSperl
%include Solaris.inc

Name:		TSperl-Encode-Detect
Summary:	Encode::Detect module for Perl
Version:	1.01
Source:		http://search.cpan.org/CPAN/authors/id/J/JG/JGMYERS/Encode-Detect-%{version}.tar.gz
Patch1:		encode-detect-01-cc.spec

SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: TSperl
BuildRequires: TSperl

%prep
%setup -q -n Encode-Detect-%version
%patch1 -p0

%build

/opt/TSperl/bin/perl Makefile.PL INSTALLDIRS=vendor CC=CC
make

%install
rm -rf $RPM_BUILD_ROOT

make DESTDIR=$RPM_BUILD_ROOT install

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%dir %attr (0755, root, bin) %{_libdir}/vendor_perl
%dir %attr (0755, root, bin) %{_libdir}/vendor_perl/5.8
%{_libdir}/vendor_perl/5.8/*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man3
%{_mandir}/man3/*

%changelog
* Fri Jan 30 2009 - river@loreley.flyingparchment.org.uk
- Initial spec

%define _basedir /opt/TSperl
%include Solaris.inc

Name:		TSperl-String-Flogger
Summary:	String::Flogger module for Perl
Version:	1.001
Source:		http://search.cpan.org/CPAN/authors/id/R/RJ/RJBS/String-Flogger-%{version}.tar.gz

SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: TSperl
Requires: TSperl-Sub-Exporter
Requires: TSperl-JSON
BuildRequires: TSperl

%prep
%setup -q -n String-Flogger-%version

%build

PERL_MM_USE_DEFAULT=yes /opt/TSperl/bin/perl Makefile.PL INSTALLDIRS=vendor
make

%install
rm -rf $RPM_BUILD_ROOT

make DESTDIR=$RPM_BUILD_ROOT install
rm $RPM_BUILD_ROOT%{_libdir}/5.8/perllocal.pod
rmdir $RPM_BUILD_ROOT%{_libdir}/5.8

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

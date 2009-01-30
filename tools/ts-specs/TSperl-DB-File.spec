%define _basedir /opt/TSperl
%include Solaris.inc

Name:		TSperl-DB-File
Summary:	DB_File module for Perl
Version:	1.817
Source:		http://search.cpan.org/CPAN/authors/id/P/PM/PMQS/DB_File-%{version}.tar.gz
Patch1:		DB_File-01-installdirs.diff

SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: TSperl
Requires: TSbdb
BuildRequires: TSperl
BuildRequires: TSbdb

%prep
%setup -q -n DB_File-%version
%patch1 -p0

%build

PERL_MM_USE_DEFAULT=yes /opt/TSperl/bin/perl Makefile.PL \
	INC=-I/opt/ts/include \
	LIBS='-L/opt/ts/lib -R/opt/ts/lib -ldb'
make

%install
rm -rf $RPM_BUILD_ROOT

make DESTDIR=$RPM_BUILD_ROOT install
rm $RPM_BUILD_ROOT%{_libdir}/5.8/perllocal.pod
rmdir $RPM_BUILD_ROOT%{_libdir}/5.8
rm $RPM_BUILD_ROOT%{_libdir}/vendor_perl/5.8/auto/DB_File/.packlist

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

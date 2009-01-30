%define _basedir /opt/TSperl
%include Solaris.inc

Name:		TSperl-Archive-Tar
Summary:	Archive::Tar module for Perl
Version:	1.44
Source:		http://search.cpan.org/CPAN/authors/id/K/KA/KANE/Archive-Tar-%{version}.tar.gz

SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: TSperl
Requires: TSperl-Text-Diff
Requires: TSperl-IO-Compress-Base
Requires: TSperl-IO-Compress-Bzip2
RequireS: TSperl-IO-Zlib
Requires: TSperl-Package-Constants

BuildRequires: TSperl
BuildRequires: TSperl-Text-Diff
BuildRequires: TSperl-IO-Compress-Base
BuildRequires: TSperl-IO-Compress-Bzip2
BuildRequireS: TSperl-IO-Zlib
BuildRequires: TSperl-Package-Constants

%prep
%setup -q -n Archive-Tar-%version

%build

echo n | /opt/TSperl/bin/perl Makefile.PL INSTALLDIRS=vendor
make

%install
rm -rf $RPM_BUILD_ROOT

make DESTDIR=$RPM_BUILD_ROOT install
rm $RPM_BUILD_ROOT%{_libdir}/5.8/perllocal.pod
rmdir $RPM_BUILD_ROOT%{_libdir}/5.8
find $RPM_BUILD_ROOT%{_libdir} -name .packlist -exec rm {} +

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%dir %attr (0755, root, bin) %{_libdir}/vendor_perl
%dir %attr (0755, root, bin) %{_libdir}/vendor_perl/5.8
%{_libdir}/vendor_perl/5.8/*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*
%dir %attr (0755, root, bin) %{_mandir}/man3
%{_mandir}/man3/*

%changelog
* Fri Jan 30 2009 - river@loreley.flyingparchment.org.uk
- Initial spec

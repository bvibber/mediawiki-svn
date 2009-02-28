%include Solaris.inc

Name:		TSmytop
Summary:	mytop - show MySQL database state
Version:	1.6
Source:		http://jeremy.zawodny.com/mysql/mytop/mytop-%{version}.tar.gz
Patch1:		mytop-01-specials.diff

SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: TSperl
Requires: TSperl-Term-ReadKey
Requires: TSperl-DBD-mysql
Requires: TSperl-DBI
BuildRequires: TSperl
BuildRequires: TSperl-Term-ReadKey
BuildRequires: TSperl-DBD-mysql
BuildRequires: TSperl-DBI

%prep
%setup -q -n mytop-%version
%patch1 -p0

%build

/opt/TSperl/bin/perl Makefile.PL INSTALLDIRS=vendor 
make

%install
rm -rf $RPM_BUILD_ROOT

make DESTDIR=$RPM_BUILD_ROOT INSTALLVENDORSCRIPT=/opt/ts/bin INSTALLVENDORMAN1DIR=/opt/ts/share/man/man1 install
rm -rf $RPM_BUILD_ROOT/opt/TSperl

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*

%changelog
* Fri Jan 30 2009 - river@loreley.flyingparchment.org.uk
- Initial spec

%include Solaris.inc

Name:		TScronjob
Summary:	'cronjob' utility
Version:	1.000
Source:		http://search.cpan.org/CPAN/authors/id/R/RJ/RJBS/App-Cronjob-%{version}.tar.gz

SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: TSperl
Requires: TSperl-IPC-Run3
Requires: TSperl-Email-Simple
Requires: TSperl-Email-Simple-Creator
Requires: TSperl-Email-Sender
Requires: TSperl-Log-Dispatchouli
Requires: TSperl-String-Flogger
Requires: TSperl-Sys-Hostname-Long
Requires: TSperl-Text-Template
Requires: TSperl-Getopt-Long-Descriptive
Requires: TSperl-Params-Util

BuildRequires: TSperl
BuildRequires: TSperl-IPC-Run3
BuildRequires: TSperl-Email-Simple
BuildRequires: TSperl-Email-Simple-Creator
BuildRequires: TSperl-Email-Sender
BuildRequires: TSperl-Log-Dispatchouli
BuildRequires: TSperl-String-Flogger
BuildRequires: TSperl-Sys-Hostname-Long
BuildRequires: TSperl-Text-Template
BuildRequires: TSperl-Getopt-Long-Descriptive

%prep
%setup -q -n App-Cronjob-%version

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

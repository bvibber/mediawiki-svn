%define _basedir /opt/TSperl
%include Solaris.inc

%define base_version 5.8
%define patchlevel 9

Name:		TSperl
Summary:	Perl scripting language
Version:	%{base_version}.%{patchlevel}
Source:		http://www.cpan.org/src/perl-%{version}.tar.gz
Source1:	perl-config.sh

SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

# Requires:

%prep
%setup -q -n perl-%version
cp %SOURCE1 config.sh

%build

CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
     CPUS=1
fi

./Configure -de
gmake -j$CPUS
%
%install
rm -rf $RPM_BUILD_ROOT

gmake DESTDIR=$RPM_BUILD_ROOT install
rm $RPM_BUILD_ROOT%{_libdir}/%{base_version}/.packlist
mkdir -p $RPM_BUILD_ROOT%{_libdir}/vendor_perl/%{base_version}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%dir %attr (0755, root, bin) %{_libdir}/%{base_version}
%{_libdir}/%{base_version}/*
%dir %attr (0755, root, bin) %{_libdir}/site_perl
%dir %attr (0755, root, bin) %{_libdir}/site_perl/%{base_version}
%dir %attr (0755, root, bin) %{_libdir}/vendor_perl/%{base_version}
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/*
%{_mandir}/man1/*
%{_mandir}/man3/*

%changelog
* Fri Jan 30 2009 - river@loreley.flyingparchment.org.uk
- Initial spec

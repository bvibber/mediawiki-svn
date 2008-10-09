%include Solaris.inc

Name:			TSnicstat
Summary:		iostat(1m)-like utility for network interfaces
Version:		1
Source1:		http://blogs.sun.com/roller/resources/timc/nicstat.c

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

# Requires:

%prep
%setup -q -T -c -n %name-%version
cp %SOURCE1 .

%build

cc nicstat.c -o nicstat -lkstat -lrt -lsocket -lgen

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_bindir}
cp nicstat $RPM_BUILD_ROOT%{_bindir}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/nicstat

%changelog
* Thu Oct  9 2008 - river@wikimedia.org
- initial spec

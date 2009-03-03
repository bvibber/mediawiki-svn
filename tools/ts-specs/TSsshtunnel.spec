%include Solaris.inc

Name:			TSsshtunnel
Summary:		SSH tunnel service
Version:		1
Source1:		sshtunnel.xml
Source2:		sshtunnel-smf

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

%package root
Summary:		%{summary} - / filesystem
SUNW_BaseDir:		/
%include default-depend.inc

%prep
%setup -q -T -c -n %name-%version

%build

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT/var/svc/manifest/network
cp %SOURCE1 $RPM_BUILD_ROOT/var/svc/manifest/network
mkdir -p $RPM_BUILD_ROOT%{_bindir}
cp %SOURCE2 $RPM_BUILD_ROOT%{_bindir}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%attr (0755, root, bin) %{_bindir}/sshtunnel-smf

%files root
%defattr (-, root, sys)
%dir %attr (0755, root, sys) /var/svc/manifest/network
%class(manifest) /var/svc/manifest/network/sshtunnel.xml

%changelog
* Sun Mar 01 2009 - river@loreley.flyingparchment.org.uk
- initial spec

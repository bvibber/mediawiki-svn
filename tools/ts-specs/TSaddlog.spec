%include Solaris.inc

Name:			TSaddlog
Summary:		addlog tool
Version:		1.0
Source1:		addlog

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

# Requires:

%prep
%setup -q -T -c -n %name-%version
cp %SOURCE1 .

%build

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_bindir}
cp addlog $RPM_BUILD_ROOT%{_bindir}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%attr (0755, root, bin) %{_bindir}/*

%changelog
* Wed Jan 28 2009 - river@wikimedia.org
- initial spec

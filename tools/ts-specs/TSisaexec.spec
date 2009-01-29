%include Solaris.inc

Name:			TSisaexec
Summary:		TS version of isaexec
Version:		2618
Source1:		isaexec.c

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

# Requires:

%prep
%setup -q -T -c -n %name-%version

%build
cc %optflags %_ldflags %SOURCE1 -o isaexec

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_libdir}
cp isaexec $RPM_BUILD_ROOT%{_libdir}

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%attr (0755, root, bin) %{_libdir}/isaexec

%changelog
* Thu Jan 29 2009 - river@wikimedia.org
- initial spec

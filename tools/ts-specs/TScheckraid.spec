%include Solaris.inc

Name:			TScheckraid
Summary:		checkraid utility for Adapter RAID controllers
Version:		1
Source1:		checkraid.sh
Source2:		checkraid.awk

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

# Requires:

%prep
%setup -q -T -c -n %name-%version
%build

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_libdir}
cp %SOURCE2 $RPM_BUILD_ROOT%{_libdir}
mkdir -p $RPM_BUILD_ROOT%{_bindir}
cp %SOURCE1 $RPM_BUILD_ROOT%{_bindir}/checkraid

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%attr (0755, root, bin) %{_bindir}/checkraid
%dir %attr (0755, root, bin) %{_libdir}
%attr (0755, root, bin) %{_libdir}/checkraid.awk

%changelog
* Sat Feb 28 2009 - river@loreley.flyingparchment.org.uk
- initial spec

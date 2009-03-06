%include Solaris.inc

Name:			TScheckzfs
Summary:		checkraid utility for ZFS pools
Version:		1
Source1:		checkzfs.sh

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

# Requires:

%prep
%setup -q -T -c -n %name-%version
%build

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_bindir}
cp %SOURCE1 $RPM_BUILD_ROOT%{_bindir}/checkzfs

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%attr (0755, root, bin) %{_bindir}/checkzfs

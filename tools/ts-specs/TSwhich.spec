%include Solaris.inc

Name:			TSwhich
Summary:		'which' command
Version:		1
Source1:		which.sh

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
cp which.sh $RPM_BUILD_ROOT%{_bindir}/which

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%attr (0755, root, bin) %{_bindir}/which

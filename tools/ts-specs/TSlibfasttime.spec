%include Solaris.inc

Name:			TSlibfasttime
Summary:		libfasttime high-performance time()
Version:		1
Source1:		fasttime.c

SUNW_BaseDir:        %{_basedir}
BuildRoot:           %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

# Requires:

%prep
%setup -q -T -c -n %name-%version
cp %SOURCE1 .

%build
mkdir 32 64
cc -Kpic -G -xO4 fasttime.c -o 32/libfasttime.so

%ifarch amd64 sparcv9
cc -Kpic -G -xO4 -m64 fasttime.c -o 64/libfasttime.so
%endif

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT%{_libdir}
cp 32/libfasttime.so $RPM_BUILD_ROOT%{_libdir}

%ifarch amd64 sparcv9
mkdir -p $RPM_BUILD_ROOT%{_libdir}/%_arch64
cp 64/libfasttime.so $RPM_BUILD_ROOT%{_libdir}/%_arch64
%endif

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_libdir}
%attr (0755, root, bin) %{_libdir}/*.so
%ifarch amd64 sparcv9
%dir %attr (0755, root, bin) %{_libdir}/%_arch64
%attr (0755, root, bin) %{_libdir}/%_arch64/*.so
%endif

%changelog
* Sat Feb 28 2009 - river@loreley.flyingparchment.org.uk
- initial spec

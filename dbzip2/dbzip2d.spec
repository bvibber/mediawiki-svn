Summary: Distributed bzip2 file compression daemon.
Name: dbzip2d
Version: 0.0.2
Release: 1
Copyright: BSD
Group: Applications/File
Source: dbzip2-%{version}.tar.gz
BuildRoot: /var/tmp/%{name}-buildroot
BuildArch: noarch
Requires: bzip2, python

%description
dbzip2d is the network daemon component of dbzip2, a utility for
compressing files in bzip2 format using multiple local and network
threads.

%prep
%setup -q -n dbzip2-%{version}

%build
echo "Nothing to build."

%install
rm -rf $RPM_BUILD_ROOT
install -d "$RPM_BUILD_ROOT/usr/lib/dbzip2d"
install -m 0644 DistBits.py "$RPM_BUILD_ROOT/usr/lib/dbzip2d/DistBits.py"
install -m 0644 dbzip2d "$RPM_BUILD_ROOT/usr/lib/dbzip2d/dbzip2d"
install -d "$RPM_BUILD_ROOT/usr/bin"
echo 'exec /usr/bin/python /usr/lib/dbzip2d/dbzip2d "$@"' > "$RPM_BUILD_ROOT/usr/bin/dbzip2d"
chmod 0755 "$RPM_BUILD_ROOT/usr/bin/dbzip2d"
install -d "$RPM_BUILD_ROOT/etc/init.d"
install -m 0755 dbzip2d.service "$RPM_BUILD_ROOT/etc/init.d/dbzip2d"

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,root)
%dir /usr/lib/dbzip2d

/etc/init.d/dbzip2d
/usr/bin/dbzip2d
/usr/lib/dbzip2d
/usr/lib/dbzip2d/dbzip2d
/usr/lib/dbzip2d/DistBits.py

%changelog
* Tue May 30 2006 Brion Vibber <brion@pobox.com>
- Initial packaging.
* Wed Jun 21 2006 Brion Vibber <brion@pobox.com>
- 0.0.2; daemonizing hopefully better; error log.

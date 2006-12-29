Summary: Distributed bzip2 file compression client and server.
Name: dbzip2
Version: 0.0.3
Release: 1
License: BSD
Group: Applications/File
Source: dbzip2-%{version}.tar.gz
BuildRoot: /var/tmp/%{name}-buildroot
Requires: bzip2, python
BuildRequires: bzip2-devel, python-devel
Obsoletes: dbzip2

%description
dbzip2 is a utility for compressing files in bzip2 format using
multiple local and network threads. It provides a client command,
dbzip2, and a network server dbzip2d. The server is not enabled
by default but can be started from init scripts.

%prep
%setup -q -n dbzip2-%{version}

%build
make

%install
rm -rf "$RPM_BUILD_ROOT"
make INSTALL_ROOT="$RPM_BUILD_ROOT" install

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,root)
%dir /usr/lib/dbzip2
%config /etc/dbzip2.conf

/etc/dbzip2.conf
/etc/init.d/dbzip2d
/usr/bin/dbzip2
/usr/bin/dbzip2d
/usr/lib/dbzip2
/usr/lib/dbzip2/dbzip2d
/usr/lib/dbzip2/BitShifter.py
/usr/lib/dbzip2/DistBits.py
/usr/lib/dbzip2/dbzutil.so
/usr/lib/dbzip2/sigcheck.py

%changelog
* Mon Jun 26 2006 Brion Vibber <brion@pobox.com>
- 0.0.3; new client+server package replaces old server-only one.
* Wed Jun 21 2006 Brion Vibber <brion@pobox.com>
- 0.0.2; daemonizing hopefully better; error log.
* Tue May 30 2006 Brion Vibber <brion@pobox.com>
- Initial packaging.

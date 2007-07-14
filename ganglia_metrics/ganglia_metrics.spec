%define name ganglia_metrics
%define version 1.1
%define release 1

Summary: Ganglia metric daemon
Name: %{name}
Version: %{version}
Release: %{release}
Source0: %{name}-%{version}.tar.gz
License: GPL or something
Group: Development/Libraries
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-buildroot
Prefix: %{_prefix}
BuildArch: noarch
Vendor: Tim Starling <tstarling@wikimedia.org>
Url: http://svn.wikimedia.org/viewvc/mediawiki/trunk/ganglia_metrics/

%description
Ganglia metric daemon

%prep
%setup

%build
make

%install
make install DESTDIR=$RPM_BUILD_ROOT
install -d $RPM_BUILD_ROOT/etc/rc.d/init.d
install -m755 init.d/gmetricd $RPM_BUILD_ROOT/etc/rc.d/init.d/gmetricd

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,root)
/usr/lib/ganglia-metrics
/usr/lib/ganglia-metrics/gmetricd.py
/usr/lib/ganglia-metrics/SelectServer.py
/usr/lib/ganglia-metrics/gmetricd.pyo
/usr/lib/ganglia-metrics/GangliaMetrics.py
/usr/lib/ganglia-metrics/__init__.py
/usr/lib/ganglia-metrics/SelectServer.pyo
/usr/lib/ganglia-metrics/GangliaMetrics.pyo
/usr/sbin/gmetricd
/etc/rc.d/init.d/gmetricd

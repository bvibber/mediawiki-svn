Name:                    TSlibedit
Summary:                 BSD editline
Version:                 20090111-3.0
Source:                  http://thrysoee.dk/editline/libedit-%{version}.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n libedit-%version

%build

%include stdenv.inc
PATH=/opt/ts/bin:$PATH

%_configure --enable-rpath
%_make

%install
%_make DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

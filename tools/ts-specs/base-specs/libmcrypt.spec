#
# spec file for package TSlibmcrypt
#
# includes module(s): libmcrypt
#
Name:                    TSlibmcrypt
Summary:                 mcrypt - replacement for the old crypt() package and crypt(1) command
Version:                 2.5.8
URL:                     http://mcrypt.sourceforge.net/
Source:                  http://ovh.dl.sourceforge.net/sourceforge/mcrypt/libmcrypt-2.5.8.tar.gz
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build

%include default-depend.inc

%prep
rm -rf %name-%version
%setup -q -n libmcrypt-%version

%build

%include stdenv.inc

rm -rf libltdl
libtoolize --ltdl
aclocal
automake
autoconf

%_configure --enable-rpath			\
            --enable-dynamic-loading

%_make all

%install
%include stdenv.inc

%_make DESTDIR=${RPM_BUILD_ROOT} install

%clean
rm -rf $RPM_BUILD_ROOT

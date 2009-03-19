%include Solaris.inc

Name:		TSzsh
Summary:	Z shell
Version:	4.3.9
Source:		ftp://ftp.zsh.org/pub/zsh-%{version}.tar.gz
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc
Requires:	SUNWpostrun
Requires:	TSpcre
Requires:	TSgdbm
Requires:	TSlibiconv
BuildRequires:	TSpcre-devel
BuildRequires:	TSgdbm-devel
BuildRequires:	TSlibiconv-devel

%prep
%setup -q -n zsh-%version

%build
%include stdenv.inc
LIBS="$LIBS -liconv"
%_configure	\
		--enable-multibyte		\
		--enable-pcre			\
		--enable-cap			\
		--enable-etcdir=/etc/opt/ts/zsh	\
		--enable-function-subdirs	\
		--enable-maildir-support	\
		--enable-readnullcmd=less
%_make

%install
%_make install DESTDIR=$RPM_BUILD_ROOT

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%_bindir
%{_mandir}/man1
%{_datadir}/zsh
%{_libdir}/zsh

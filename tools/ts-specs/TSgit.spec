#
# spec file for package TSgit
#
# includes module(s): git
#
%include Solaris.inc

Name:                    TSgit
Summary:                 git revision control system
Version:                 1.6.1.3
Source:			 http://kernel.org/pub/software/scm/git/git-%{version}.tar.bz2
Patch1:			git-01-ginstall.diff
SUNW_BaseDir:            %{_basedir}
BuildRoot:               %{_tmppath}/%{name}-%{version}-build
%include default-depend.inc
Requires: SUNWlibms
BuildRequires: TScoreutils

%prep
%setup -q -n git-%version
%patch1 -p0

%build
CPUS=`/usr/sbin/psrinfo | grep on-line | wc -l | tr -d ' '`
if test "x$CPUS" = "x" -o $CPUS = 0; then
    CPUS=1
fi

export CC="cc"
export CXX="CC"
export CFLAGS="%optflags"
export LDFLAGS='-R/opt/ts/lib'

./configure --prefix=%{_prefix}			\
	    --libexecdir=%{_libexecdir}         \
	    --mandir=%{_mandir}                 \
	    --datadir=%{_datadir}               \
            --infodir=%{_datadir}/info		\
		--with-openssl=/usr/sfw		\
		--with-expat=/usr/sfw		\
		--with-tcltk=no 		\
		--with-curl=/opt/ts
	    		
gmake -j$CPUS

%install
gmake install DESTDIR=$RPM_BUILD_ROOT
rm -f $RPM_BUILD_ROOT%{_libdir}/i86pc-solaris-64int/perllocal.pod
rmdir $RPM_BUILD_ROOT%{_libdir}/i86pc-solaris-64int
rm -rf $RPM_BUILD_ROOT%{_libdir}/site_perl/5.8.4/i86pc-solaris-64int

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%{_bindir}/*
%dir %attr (0755, root, bin) %{_libdir}
%dir %attr (0755, root, bin) %{_libdir}/git-core
%{_libdir}/git-core/*
%dir %attr (0755, root, bin) %{_libdir}/site_perl
%{_libdir}/site_perl/*
%dir %attr(0755, root, sys) %{_datadir}
%dir %attr(0755, root, sys) %{_datadir}/git-core
%{_datadir}/git-core/*
%dir %attr(0755, root, bin) %{_mandir}
%dir %attr(0755, root, bin) %{_mandir}/man3
%{_mandir}/man3/*

%changelog
* Mon Feb 23 2009 - river@loreley.flyingparchment.org.uk
- initial spec

#
# Copyright (c) 2006 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.

%include Solaris.inc

%ifarch amd64 sparcv9
%include arch64.inc
%use pbzip264=pbzip2.spec
%endif
%include base.inc
%use pbzip2=pbzip2.spec

Name:			TSpbzip2
Summary:		parallel bzip2
Version:		%{pbzip2.version}

SUNW_BaseDir:		%{_basedir}
BuildRoot:		%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

Requires: TSisaexec
BuildRequires: TSisaexec

%prep
rm -rf %name-%version
mkdir %name-%version
  
%ifarch amd64 sparcv9
mkdir %name-%version/%_arch64
%pbzip264.prep -d %name-%version/%_arch64
%endif
  
mkdir %name-%version/%{base_arch}
%pbzip2.prep -d %name-%version/%{base_arch}

%build
export CXX=CC

%ifarch amd64 sparcv9
%include arch64.inc
export CXXFLAGS="%cxx_optflags -m64"
%pbzip264.build -d %name-%version/%_arch64
%endif
%include base.inc
export LDFLAGS=""
export CXXFLAGS="%cxx_optflags"
%pbzip2.build -d %name-%version/%{base_arch}

%install
rm -rf $RPM_BUILD_ROOT

%ifarch amd64 sparcv9
%include arch64.inc
export BINDIR=%{_bindir}
%pbzip264.install -d %name-%version/%_arch64
%endif
%include base.inc
export BINDIR=%{_bindir}
  
%pbzip2.install -d %name-%version/%{base_arch}
mkdir -p $RPM_BUILD_ROOT%{_bindir}/%{base_isa}
mv $RPM_BUILD_ROOT%{_bindir}/pbzip2 $RPM_BUILD_ROOT%{_bindir}/%{base_isa}

ln -s ../lib/isaexec $RPM_BUILD_ROOT%{_bindir}/pbzip2
ln -s pbzip2 $RPM_BUILD_ROOT%{_bindir}/pbunzip2
ln -s pbzip2 $RPM_BUILD_ROOT%{_bindir}/pbzcat

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, bin)
%dir %attr (0755, root, bin) %{_bindir}
%hard %{_bindir}/pbzip2
%{_bindir}/pbunzip2
%{_bindir}/pbzcat
%dir %attr (0755, root, bin) %{_bindir}/%{base_isa}
%{_bindir}/%{base_isa}/*
%ifarch amd64 sparcv9
%dir %attr (0755, root, bin) %{_bindir}/%_arch64
%{_bindir}/%_arch64/*
%endif
%dir %attr (0755, root, sys) %{_datadir}
%dir %attr (0755, root, bin) %{_mandir}
%dir %attr (0755, root, bin) %{_mandir}/man1
%{_mandir}/man1/*.1

%changelog
* Thu Jan 29 2009 - river@loreley.flyingparchment.org.uk
- Initial spec

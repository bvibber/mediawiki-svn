#
# Copyright (c) 2006 Sun Microsystems, Inc.
# This file and all modifications and additions to the pristine
# package are under the same license as the package itself.

%include Solaris.inc

Name:			TSpbzip2
Summary:		parallel bzip2
Version:		1.0.5
Source:			http://compression.ca/pbzip2/pbzip2-%{version}.tar.gz

SUNW_BaseDir:		%{_basedir}
BuildRoot:		%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc

%prep
%setup -q -n pbzip2-%version

# The makefile for pbzip2 requires so much patching, we don't even
# bother with it.  Build and install by hand.
%build
$CXX $CXXFLAGS $LDFLAGS -mt pbzip2.cpp -o pbzip2 -lbz2

%install
mkdir -p $RPM_BUILD_ROOT$BINDIR
cp pbzip2 $RPM_BUILD_ROOT$BINDIR
mkdir -p $RPM_BUILD_ROOT%{_mandir}/man1
cp pbzip2.1 $RPM_BUILD_ROOT%{_mandir}/man1

%clean
rm -rf $RPM_BUILD_ROOT

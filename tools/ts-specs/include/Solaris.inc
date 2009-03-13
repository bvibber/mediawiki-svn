#
# Macro definitions for Solaris packages
#
# Copyright 2007 Sun Microsystems, Inc.
#

%include prod.inc
%include options.inc

SUNW_Pkg_AllZones:         false
SUNW_Pkg_Hollow:           false
SUNW_Pkg_ThisZone:         false

%define cc_is_gcc 0

%define is_nevada %(test `uname -r` = 5.11 && echo 1 || echo 0)
%define is_s10    %(test `uname -r` = 5.10 && echo 1 || echo 0)
%define _patch_options --fuzz=0 --unified

SUNW_Category:             %{default_category}
SUNW_HotLine:              Please contact your local service provider
SUNW_MaxInst:              1000
SUNW_Rev:		   110.0.4.%(date +%Y.%m.%d.%H.%M)
SUNW_Copyright:            default-copyright.txt

%define _libexecdir        %{_prefix}/lib
%define _mandir		   %{_datadir}/man

# by default we don't build optimised versions
%define opt_sparcv9 0
%define opt_amd64   0
%define opt_sse2    0
# opt_arch64 means opt_sparcv9 or opt_amd64
%define opt_arch64  0
# can_isaexec gets redefined to 1 if multiple isas are built
%define can_isaexec 0
%ifarch sparc
%define base_isa sparcv7
%define base_arch sparc
%else
%define base_isa i86
%define base_arch i386
%endif

%if %option_with_gnu_iconv
%define arch_ldadd -Wl,-zignore -R/usr/gnu/lib -lgnuintl -lgnuiconv
%else
%define arch_ldadd
%endif
%define ldadd

%if %debug_build
%define _ldflags           %arch_ldadd %ldadd ${EXTRA_LDFLAGS}
%else
%define _ldflags           %arch_ldadd %ldadd -Wl,-zignore -Wl,-zcombreloc -Wl,-Bdirect ${EXTRA_LDFLAGS}
%endif

%define gcc_picflags -fPIC -DPIC
%define suncc_picflags -KPIC

# ifarch doesn't support checking for sse2
# this macros allows you to use %if %arch_sse2 instead
%define arch_sse2 %(isainfo -x | grep sse2 > /dev/null && echo 1 || echo 0)

# temporary, until all spec files are converted to the new multi-isa build
# technique
%ifarch amd64 sparcv9
%include arch64.inc
%endif

%include base.inc

%define _basedir           %{?_basedir}%{?!_basedir:/opt/ts}
%define _subdir            
%define _prefix		   %{_basedir}
%define _sysconfdir	   /etc/opt/ts
%define _localstatedir	   /var
%define _localedir	   %{_datadir}/locale
%define _pkg_config_path   %{_libdir}/pkgconfig

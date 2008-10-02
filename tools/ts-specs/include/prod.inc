#
# Default settings for GNOME packages on Solaris
#
# Portions Copyright 2007 Sun Microsystems, Inc.
#
SUNW_ProdName:             SpecFilesExtra
SUNW_ProdVers:             1.0

%define default_pkg_version 2.18.0
%define jds_version         JDSosol
%define default_category   TS,application
%define support_level      unsupported

# use --with-tjds to build the JDS Trusted Extensions
# default: no tjds
%define build_tjds %{?_with_tjds:1}%{?!_with_tjds:0}

Vendor:			   River Tarnell <river@wikimedia.org>
%define                    pkg_prefix TS
%{?sf_mirror:#}%define sf_mirror easynews.dl.sourceforge.net

%{?sf_download:#}%define sf_download http://downloads.sourceforge.net

# ChangeLog:
# 2007-03-16  Laszlo (Laca) Peter  <laca@sun.com>
# - copied from the JDS spec-files and updated for SFE
# 2007-03-15  Laszlo (Laca) Peter  <laca@sun.com>
# - create by moving product specific stuff out of Solaris.inc

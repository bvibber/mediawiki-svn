#
# Default system dependencies for PERL packages on Solaris
#

%define 		perl_version 5.8.4

BuildRequires:  SUNWperl584core
Requires:       SUNWperl584core

%ifarch sparc
%define perl_dir sun4-solaris-64int
%else
%define perl_dir i86pc-solaris-64int 
%endif

%define perl_bin /usr/perl5/5.8.4/bin

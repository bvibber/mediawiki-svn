#
# Intel SSE2 ISA settings for Solaris packages
#
# Copyright 2007 Sun Microsystems, Inc.
#
# Solaris.inc must be included before this file

%define bld_arch	pentium_pro+mmx
%define sse2_arch       pentium_pro+mmx

%define opt_sse2 1
%define can_isaexec 1
%define gtk_doc_option --disable-gtk-doc

%define arch_ldadd           -xarch=sse2 -L%{_libdir} -R%{_libdir}

%if %debug_build
%define pkgbuild_postprocess /bin/true
%define _cc_opt_level      -g
%define _cxx_opt_level     -g
%define _gcc_opt_level     -g
%define _gxx_opt_level     -g
%else
%if %cc_is_gcc
%define _gcc_opt_level     -O3
%define _gxx_opt_level     -O3
%else
%define _cc_opt_level      -xO4 -xspace -xvector
%define _cxx_opt_level     -xO3 -xspace -xvector
%endif
%endif

%define gcc_optflags           %_gcc_opt_level -mcpu=pentiumpro -mtune=pentiumpro -msse2 -mfpmath=sse -Xlinker -i -fno-omit-frame-pointer %gcc_picflags
%define gcc_cxx_optflags       %_gxx_opt_level -mcpu=pentiumpro -mtune=pentiumpro -msse2 -mfpmath=sse -Xlinker -i -fno-omit-frame-pointer %gcc_picflags

%define suncc_optflags           -i %_cc_opt_level -xstrconst -xarch=sse2 -mr -xregs=no%%frameptr
%define suncc_cxx_optflags       -i %_cxx_opt_level -xarch=sse2 -mr -norunpath -xregs=no%%frameptr

%if %cc_is_gcc

%define picflags %gcc_picflags
%define optflags %gcc_optflags
%define cxx_optflags %gcc_cxx_optflags

%else
# cc is not gcc

%define picflags %suncc_picflags
%define optflags %suncc_optflags ${EXTRA_CFLAGS}
%define cxx_optflags %suncc_cxx_optflags ${EXTRA_CXXFLAGS}

%endif

%define _bindir		 %{_prefix}/bin/%{bld_arch}
%define _libdir		 %{_prefix}/lib/%{bld_arch}
%define _libexecdir	 %{_prefix}/lib/%{bld_arch}
%define _pkg_config_path %{_libdir}/%{bld_arch}/pkgconfig

# Define some standard directories
%define gnu_bin         /usr/gnu/bin/%{bld_arch}
%define gnu_inc         /usr/gnu/include
%define gnu_lib         /usr/gnu/lib/%{bld_arch}
%define gnu_lib_path	-L%gnu_lib -L/usr/gnu/lib -R%gnu_lib -R/usr/gnu/lib
%define sfw_bin         /usr/sfw/bin/%{bld_arch}
%define sfw_inc         /usr/sfw/include
%define sfw_lib         /usr/sfw/lib/%{bld_arch}
%define sfw_lib_path	-L%sfw_lib -L/usr/sfw/lib -R%sfw_lib -R/usr/sfw/lib
%define xorg_bin        /usr/X11/bin/%{bld_arch}
%define xorg_lib        /usr/X11/lib/%{bld_arch}
%define xorg_inc        /usr/X11/include
%define xorg_lib_path	-L%xorg_lib -L/usr/X11/lib -R%xorg_lib -R/usr/X11/lib

# ChangeLog:
# 2007-04-23  Doug Scott  <dougs@truemail.co.th>
# - move -xvector the optimized flags as it required -xO3 or higher
# 2007-03-20  Doug Scott  <dougs@truemail.co.th>
# added standard directories definitions
# 2007-03-20  Doug Scott  <dougs@truemail.co.th>
# - Add -mfpmath=sse for gcc.
# 2007-03-19  Doug Scott  <dougs@truemail.co.th>
# - Fixed -fno-omit-frame-pointer flag
# 2007-03-15  Laszlo (Laca) Peter  <laca@sun.com>
# - define suncc_optflags, suncc_cxx_optflags, gcc_optflags, gcc_cxx_optflags
# 2007-03-12  Doug Scott <dougs@truemail.co.th>

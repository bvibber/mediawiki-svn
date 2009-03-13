#
# Base (32-bit) platform settings for Solaris packages
#
# Copyright 2007 Sun Microsystems, Inc.
#
# Solaris.inc must be included before this file

%ifarch sparc
%define bld_arch        "sparc"
%else
%define bld_arch	"i386"
%endif

%define opt_arch64 0

# use --without-gtk-doc to disable building gtk-docs (it takes quite
# a while to build them)
# default: build gtk-doc API docs
%define gtk_doc_option %{?!_without_gtk_doc:--enable-gtk-doc}%{?_without_gtk_doc:--disable-gtk-doc}

%define arch_ldadd

%if %debug_build
%define pkgbuild_postprocess /bin/true
%define _cc_opt_level      -g
%define _cxx_opt_level     -g
%define _gcc_opt_level      -g
%define _gxx_opt_level     -g
%else
%define _gcc_opt_level      -O3
%define _gxx_opt_level     -O3
%define _cc_opt_level      -xO4 -xspace
%define _cxx_opt_level     -xO3 -xspace
%endif

%ifarch sparc
%define gcc_optflags           %_gcc_opt_level -mv8plus -Xlinker -i %gcc_picflags
%define gcc_cxx_optflags       %_gxx_opt_level -mv8plus -Xlinker -i %gcc_picflags
%endif

%ifarch i386
%define gcc_optflags           %_gcc_opt_level -Xlinker -i -fno-omit-frame-pointer %gcc_picflags
%define gcc_cxx_optflags       %_gxx_opt_level -Xlinker -i -fno-omit-frame-pointer %gcc_picflags
%endif

%ifarch sparc
%define suncc_optflags           -i %_cc_opt_level -xstrconst -xarch=v8a -mr
%define suncc_cxx_optflags       -i %_cxx_opt_level -xarch=v8a -mr -norunpath
%endif

%ifarch i386
%define suncc_optflags           -i %_cc_opt_level -xstrconst -xpentium -mr -xregs=no%%frameptr
%define suncc_cxx_optflags       -i %_cxx_opt_level -pentium -mr -norunpath -xregs=no%%frameptr
%endif

%if %cc_is_gcc

%define picflags %gcc_picflags
%define optflags %gcc_optflags
%define cxx_optflags %gcc_cxx_optflags
%define _gpp_version %(%{?_gpp:%_gpp}%{!?_gpp:g++} --version | head -1 | cut -f3 -d' ')
%define _cxx_libdir	   %{_prefix}/lib/g++/%{_gpp_version}

%else
# cc is not gcc

%define picflags %suncc_picflags
%define optflags %suncc_optflags ${EXTRA_CFLAGS}
%define cxx_optflags %suncc_cxx_optflags ${EXTRA_CXXFLAGS}
%define _cxx_libdir %{_prefix}/lib

%endif

%define _bindir		 %{_prefix}/bin
%define _libdir		 %{_prefix}/lib
%define _libexecdir	 %{_prefix}/lib
%define _pkg_config_path %{_libdir}/pkgconfig:/usr/lib/pkgconfig:/usr/sfw/lib/pkgconfig
%define _sysconfdir      /etc/opt/ts

# Define some standard directories
%define gnu_bin         /usr/gnu/bin
%define gnu_inc         /usr/gnu/include
%define gnu_lib         /usr/gnu/lib
%define gnu_lib_path    -L%gnu_lib -R%gnu_lib
%define sfw_bin         /usr/sfw/bin
%define sfw_inc         /usr/sfw/include
%define sfw_lib         /usr/sfw/lib
%define sfw_lib_path    -L%sfw_lib -R%sfw_lib
%define xorg_bin        /usr/X11/bin
%define xorg_lib        /usr/X11/lib
%define xorg_inc        /usr/X11/include
%define xorg_lib_path   -L%xorg_lib -R%xorg_lib

%define _configure ./configure --prefix=%_prefix --bindir=%_bindir --libdir=%_libdir --mandir=%_mandir --datadir=%_datadir --includedir=%_includedir --disable-nls --disable-static

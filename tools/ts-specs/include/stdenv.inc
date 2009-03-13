%if %cc_is_gcc
  CC=gcc
  CXX=g++
  CFLAGS="%gcc_optflags"
  CXXFLAGS="%gcc_cxx_optflags"
%else
  CC=cc
  CXX=CC
  CFLAGS="%optflags"
  CXXFLAGS="%cxx_optflags"
%endif

CPPFLAGS="-I%{_includedir} -I/usr/sfw/include -D__EXTENSIONS__"
LDFLAGS="-L%_libdir -L%sfw_lib -R%_libdir:%sfw_lib"

%if %opt_arch64
PATH=/opt/ts/bin/amd64:/opt/ts/bin:/opt/SUNWspro/bin:/usr/bin:/usr/sfw/bin:/usr/ccs/bin
%else
PATH=/opt/ts/bin:/opt/SUNWspro/bin:/usr/bin:/usr/sfw/bin:/usr/ccs/bin
%endif

PKG_CONFIG_PATH="%_pkg_config_path"

export PATH CC CXX CFLAGS CXXFLAGS CPPFLAGS
export LDFLAGS PKG_CONFIG_PATH

#
# spec file for package TSvim.spec
#
# includes module(s): vim
#
%include Solaris.inc

Name:		TSvim
Summary:	Vim - vi improved
Version:	7.2
Source1:	ftp://ftp.vim.org/pub/vim/unix/vim-%{version}.tar.bz2
Source2:	ftp://ftp.vim.org/pub/vim/extra/vim-%{version}-lang.tar.gz
Source3:	ftp://ftp.vim.org/pub/vim/extra/vim-%{version}-extra.tar.gz
Source4:	vimrc
Source5:	ts.vim
Patch1:		7.2.001
Patch2:		7.2.002
Patch3:		7.2.003
Patch4:		7.2.004
Patch5:		7.2.005
Patch6:		7.2.006
Patch7:		7.2.007
Patch8:		7.2.008
Patch9:		7.2.009
Patch10:	7.2.010
Patch11:	7.2.011
Patch12:	7.2.012
Patch13:	7.2.013
Patch14:	7.2.014
Patch15:	7.2.015
Patch16:	7.2.016
Patch17:	7.2.017
Patch18:	7.2.018
Patch19:	7.2.019
Patch20:	7.2.020
Patch21:	7.2.021
Patch22:	7.2.022
Patch23:	7.2.023
Patch24:	7.2.024
Patch25:	7.2.025
Patch26:	7.2.026
Patch27:	7.2.027
Patch28:	7.2.028
Patch29:	7.2.029
Patch30:	7.2.030
Patch31:	7.2.031
Patch32:	7.2.032
Patch33:	7.2.033
Patch34:	7.2.034
Patch35:	7.2.035
Patch36:	7.2.036
Patch37:	7.2.037
Patch38:	7.2.038
Patch39:	7.2.039
Patch40:	7.2.040
Patch41:	7.2.041
Patch42:	7.2.042
Patch43:	7.2.043
Patch44:	7.2.044
Patch45:	7.2.045
Patch46:	7.2.046
Patch47:	7.2.047
Patch48:	7.2.048
Patch49:	7.2.049
Patch50:	7.2.050
Patch51:	7.2.051
Patch52:	7.2.052
Patch53:	7.2.053
Patch54:	7.2.054
Patch55:	7.2.055
Patch56:	7.2.056
Patch57:	7.2.057
Patch58:	7.2.058
Patch59:	7.2.059
Patch60:	7.2.060
Patch61:	7.2.061
Patch62:	7.2.062
Patch63:	7.2.063
Patch64:	7.2.064
Patch65:	7.2.065
Patch66:	7.2.066
Patch67:	7.2.067
Patch68:	7.2.068
Patch69:	7.2.069
Patch70:	7.2.070
Patch71:	7.2.071
Patch72:	7.2.072
Patch73:	7.2.073
Patch74:	7.2.074
Patch75:	7.2.075
Patch76:	7.2.076
Patch77:	7.2.077
Patch78:	7.2.078
Patch79:	7.2.079
Patch80:	7.2.080
Patch81:	7.2.081
Patch82:	7.2.082
Patch83:	7.2.083
Patch84:	7.2.084
Patch85:	7.2.085
Patch86:	7.2.086
Patch87:	7.2.087
Patch88:	7.2.088
Patch89:	7.2.089
Patch90:	7.2.090
Patch91:	7.2.091
Patch92:	7.2.092
Patch93:	7.2.093
Patch94:	7.2.094
Patch95:	7.2.095
Patch96:	7.2.096
Patch97:	7.2.097
Patch98:	7.2.098
Patch99:	7.2.099
Patch100:	7.2.100
Patch101:	7.2.101
Patch102:	7.2.102
Patch103:	7.2.103
Patch104:	7.2.104
Patch105:	7.2.105
Patch106:	7.2.106
Patch107:	7.2.107
Patch108:	7.2.108
Patch109:	7.2.109
Patch110:	7.2.110
Patch111:	7.2.111
Patch112:	7.2.112
Patch113:	7.2.113
Patch114:	7.2.114
Patch115:	7.2.115
Patch116:	7.2.116
Patch117:	7.2.117
Patch118:	7.2.118
Patch119:	7.2.119
Patch120:	7.2.120
Patch121:	7.2.121
Patch122:	7.2.122
Patch123:	7.2.123
Patch124:	7.2.124
Patch125:	7.2.125
Patch126:	7.2.126
Patch127:	7.2.127
Patch128:	7.2.128
Patch129:	7.2.129
Patch130:	7.2.130
Patch131:	7.2.131
Patch132:	7.2.132
Patch133:	7.2.133
Patch134:	7.2.134
Patch135:	7.2.135
Patch136:	7.2.136
Patch137:	7.2.137
Patch138:	7.2.138
Patch139:	7.2.139
Patch140:	7.2.140
Patch141:	7.2.141
Patch142:	7.2.142
Patch143:	7.2.143
Patch144:	7.2.144
Patch145:	7.2.145
Patch146:	7.2.146
Patch147:	7.2.147
URL:		http://www.vim.org
SUNW_BaseDir:	%{_basedir}
BuildRoot:	%{_tmppath}/%{name}-%{version}-build
%include default-depend.inc
Requires:	SUNWlibms
Requires:	SUNWmlib
BuildRequires:	SUNWmlibh

%package root
Summary:	%{summary} - / filesystem
SUNW_BaseDir:	/
Requires:	%{name}

%prep
%setup -q -T -b 1 -c -n %name-%version
gzip -dc %{SOURCE2} | gtar xf -
gzip -dc %{SOURCE3} | gtar xf -
cd vim72

gpatch -p0 <%{PATCH1}
gpatch -p0 <%{PATCH2}
gpatch -p0 <%{PATCH3}
gpatch -p0 <%{PATCH4}
gpatch -p0 <%{PATCH5}
gpatch -p0 <%{PATCH6}
gpatch -p0 <%{PATCH7}
gpatch -p0 <%{PATCH8}
gpatch -p0 <%{PATCH9}
gpatch -p0 <%{PATCH10}
gpatch -p0 <%{PATCH11}
gpatch -p0 <%{PATCH12}
gpatch -p0 <%{PATCH13}
gpatch -p0 <%{PATCH14}
gpatch -p0 <%{PATCH15}
gpatch -p0 <%{PATCH16}
gpatch -p0 <%{PATCH17}
gpatch -p0 <%{PATCH18}
gpatch -p0 <%{PATCH19}
gpatch -p0 <%{PATCH20}
gpatch -p0 <%{PATCH21}
gpatch -p0 <%{PATCH22}
gpatch -p0 <%{PATCH23}
gpatch -p0 <%{PATCH24}
gpatch -p0 <%{PATCH25}
gpatch -p0 <%{PATCH26}
gpatch -p0 <%{PATCH27}
gpatch -p0 <%{PATCH28}
gpatch -p0 <%{PATCH29}
gpatch -p0 <%{PATCH30}
gpatch -p0 <%{PATCH31}
gpatch -p0 <%{PATCH32}
gpatch -p0 <%{PATCH33}
gpatch -p0 <%{PATCH34}
gpatch -p0 <%{PATCH35}
gpatch -p0 <%{PATCH36}
gpatch -p0 <%{PATCH37}
gpatch -p0 <%{PATCH38}
gpatch -p0 <%{PATCH39}
gpatch -p0 <%{PATCH40}
gpatch -p0 <%{PATCH41}
gpatch -p0 <%{PATCH42}
gpatch -p0 <%{PATCH43}
gpatch -p0 <%{PATCH44}
gpatch -p0 <%{PATCH45}
gpatch -p0 <%{PATCH46}
gpatch -p0 <%{PATCH47}
gpatch -p0 <%{PATCH48}
gpatch -p0 <%{PATCH49}
gpatch -p0 <%{PATCH50}
gpatch -p0 <%{PATCH51}
gpatch -p0 <%{PATCH52}
gpatch -p0 <%{PATCH53}
gpatch -p0 <%{PATCH54}
gpatch -p0 <%{PATCH55}
gpatch -p0 <%{PATCH56}
gpatch -p0 <%{PATCH57}
gpatch -p0 <%{PATCH58}
gpatch -p0 <%{PATCH59}
gpatch -p0 <%{PATCH60}
gpatch -p0 <%{PATCH61}
gpatch -p0 <%{PATCH62}
gpatch -p0 <%{PATCH63}
gpatch -p0 <%{PATCH64}
gpatch -p0 <%{PATCH65}
gpatch -p0 <%{PATCH66}
gpatch -p0 <%{PATCH67}
gpatch -p0 <%{PATCH68}
gpatch -p0 <%{PATCH69}
gpatch -p0 <%{PATCH70}
gpatch -p0 <%{PATCH71}
gpatch -p0 <%{PATCH72}
gpatch -p0 <%{PATCH73}
gpatch -p0 <%{PATCH74}
gpatch -p0 <%{PATCH75}
gpatch -p0 <%{PATCH76}
gpatch -p0 <%{PATCH77}
gpatch -p0 <%{PATCH78}
gpatch -p0 <%{PATCH79}
gpatch -p0 <%{PATCH80}
gpatch -p0 <%{PATCH81}
gpatch -p0 <%{PATCH82}
gpatch -p0 <%{PATCH83}
gpatch -p0 <%{PATCH84}
gpatch -p0 <%{PATCH85}
gpatch -p0 <%{PATCH86}
gpatch -p0 <%{PATCH87}
gpatch -p0 <%{PATCH88}
gpatch -p0 <%{PATCH89}
gpatch -p0 <%{PATCH90}
gpatch -p0 <%{PATCH91}
gpatch -p0 <%{PATCH92}
gpatch -p0 <%{PATCH93}
gpatch -p0 <%{PATCH94}
gpatch -p0 <%{PATCH95}
gpatch -p0 <%{PATCH96}
gpatch -p0 <%{PATCH97}
gpatch -p0 <%{PATCH98}
gpatch -p0 <%{PATCH99}
gpatch -p0 <%{PATCH100}
gpatch -p0 <%{PATCH101}
gpatch -p0 <%{PATCH102}
gpatch -p0 <%{PATCH103}
gpatch -p0 <%{PATCH104}
gpatch -p0 <%{PATCH105}
gpatch -p0 <%{PATCH106}
gpatch -p0 <%{PATCH107}
gpatch -p0 <%{PATCH108}
gpatch -p0 <%{PATCH109}
gpatch -p0 <%{PATCH110}
gpatch -p0 <%{PATCH111}
gpatch -p0 <%{PATCH112}
gpatch -p0 <%{PATCH113}
gpatch -p0 <%{PATCH114}
gpatch -p0 <%{PATCH115}
gpatch -p0 <%{PATCH116}
gpatch -p0 <%{PATCH117}
gpatch -p0 <%{PATCH118}
gpatch -p0 <%{PATCH119}
gpatch -p0 <%{PATCH120}
gpatch -p0 <%{PATCH121}
gpatch -p0 <%{PATCH122}
gpatch -p0 <%{PATCH123}
gpatch -p0 <%{PATCH124}
gpatch -p0 <%{PATCH125}
gpatch -p0 <%{PATCH126}
gpatch -p0 <%{PATCH127}
gpatch -p0 <%{PATCH128}
gpatch -p0 <%{PATCH129}
gpatch -p0 <%{PATCH130}
gpatch -p0 <%{PATCH131}
gpatch -p0 <%{PATCH132}
gpatch -p0 <%{PATCH133}
gpatch -p0 <%{PATCH134}
gpatch -p0 <%{PATCH135}
gpatch -p0 <%{PATCH136}
gpatch -p0 <%{PATCH137}
gpatch -p0 <%{PATCH138}
gpatch -p0 <%{PATCH139}
gpatch -p0 <%{PATCH140}
gpatch -p0 <%{PATCH141}
gpatch -p0 <%{PATCH142}
gpatch -p0 <%{PATCH143}
gpatch -p0 <%{PATCH144}
gpatch -p0 <%{PATCH145}
gpatch -p0 <%{PATCH146}
gpatch -p0 <%{PATCH147}

%build
%include stdenv.inc
CPPFLAGS="$CPPFLAGS -DSYS_VIMRC_FILE=\\\"/etc/opt/ts/vim/vimrc\\\""
cd vim72
%_configure	--with-features=huge	\
		--enable-multibyte	\
		--disable-hangulinput	\
		--disable-gui		\
		--disable-perlinterp	\
		--disable-pythoninterp	\
		--disable-tclinterp	\
		--disable-rubyinterp	\
		--enable-cscope		\
		--without-x		\
		--disable-fontset	\
		--with-ex-name=exm	\
		--with-view-name=viewm
%_make

%install
rm -rf $RPM_BUILD_ROOT
cd vim72
%_make DESTDIR=$RPM_BUILD_ROOT install

mkdir -p $RPM_BUILD_ROOT/etc/opt/ts/vim
cp %SOURCE4 $RPM_BUILD_ROOT/etc/opt/ts/vim
cp %SOURCE5 $RPM_BUILD_ROOT/opt/ts/share/vim/vim72
rm -rf $RPM_BUILD_ROOT%{_datadir}/vim/vim72/lang
rm -rf $RPM_BUILD_ROOT%{_mandir}/[a-z][a-z]
rm -rf $RPM_BUILD_ROOT%{_mandir}/[a-z][a-z].*

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr (-, root, root)
%{_bindir}/*
%{_datadir}/vim
%{_mandir}/man1/*

%files root
%defattr (-, root, sys)
%dir /etc
%dir /etc/opt
%dir /etc/opt/ts
%dir /etc/opt/ts/vim
%class(preserve) /etc/opt/ts/vim/vimrc

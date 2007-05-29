LINT		= lint
CC		= suncc
CFLAGS		= -m64 -O -g -errwarn -xc99=none
LINTFLAGS	= -axsm -u -errtags=yes -s -Xc99=%none -Xarch=amd64 -errsecurity=core -erroff=E_INCONS_ARG_DECL2

# CC		= gcc
# CFLAGS	= -W -Wall -Werror

/* Copyright (c) 2007-2009 River Tarnell <river@loreley.flyingparchment.org.uk>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

#if defined(__linux__)
# include	"proc_linux.cc"
#elif defined(__FreeBSD__)
# include	"proc_freebsd.cc"
#elif defined(__sun) && defined(__SVR4)
# include	"proc_solaris.cc"
#else
# error dont know how to enumerate processes on this platform
#endif

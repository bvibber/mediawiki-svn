/*
 * copyright (c) 2007 Doug Scott <dougs@truemail.co.th>
 *
 * This is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with FFmpeg; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

#ifndef HAVE_ENDIAN_COMPAT_H
#define HAVE_ENDIAN_COMPAT_H 1

#define __LITTLE_ENDIAN 1234
#define __BIG_ENDIAN    4321
#define LITTLE_ENDIAN	__LITTLE_ENDIAN
#define BIG_ENDIAN	__BIG_ENDIAN

#include <sys/byteorder.h>

#if defined(_BIG_ENDIAN)
#define __BYTE_ORDER  __BIG_ENDIAN
#else
#define __BYTE_ORDER  __LITTLE_ENDIAN
#endif

#define BYTE_ORDER __BYTE_ORDER

#endif /* HAVE_ENDIAN_COMPAT_H */

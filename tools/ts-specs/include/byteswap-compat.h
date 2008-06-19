/*
 * copyright (c) 2006 Michael Niedermayer <michaelni@gmx.at>
 * copyright (c) 2007 Doug Scott <dougs@truemail.co.th>
 *
 * This file is came from FFmpeg.
 *
 * FFmpeg is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * FFmpeg is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with FFmpeg; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

#include <stdint.h>
#ifndef HAVE_BYTESWAP_COMPAT_H
#define HAVE_BYTESWAP_COMPAT_H 1

#ifdef __amd64
#  define LEGACY_REGS "=Q"
#else
#  define LEGACY_REGS "=q"
#endif

static inline uint16_t bswap_16(uint16_t x)
{
#if defined(__i386) && !defined(__SUNPRO_CC)
  __asm("rorw $8, %0"   :
        LEGACY_REGS (x) :
        "0" (x));
#else
    x= (x>>8) | (x<<8);
#endif
    return x;
}

static inline uint32_t bswap_32(uint32_t x)
{
#if defined(__i386) && !defined(__SUNPRO_CC)
#if __CPU__ != 386
 __asm("bswap   %0":
      "=r" (x)     :
#else
 __asm("xchgb   %b0,%h0\n"
      "         rorl    $16,%0\n"
      "         xchgb   %b0,%h0":
      LEGACY_REGS (x)                :
#endif
      "0" (x));
#else
    x= ((x<<8)&0xFF00FF00) | ((x>>8)&0x00FF00FF);
    x= (x>>16) | (x<<16);
#endif
    return x;
}

static inline uint64_t bswap_64(uint64_t x)
{
#if 0
    x= ((x<< 8)&0xFF00FF00FF00FF00ULL) | ((x>> 8)&0x00FF00FF00FF00FFULL);
    x= ((x<<16)&0xFFFF0000FFFF0000ULL) | ((x>>16)&0x0000FFFF0000FFFFULL);
    return (x>>32) | (x<<32);
#elif defined(__amd64) && !defined(__SUNPRO_CC)
  __asm("bswap  %0":
        "=r" (x)   :
        "0" (x));
  return x;
#else
    union {
        uint64_t ll;
        uint32_t l[2];
    } w, r;
    w.ll = x;
    r.l[0] = bswap_32 (w.l[1]);
    r.l[1] = bswap_32 (w.l[0]);
    return r.ll;
#endif
}

#if __BYTE_ORDER == __LITTLE_ENDIAN
#define __cpu_to_le32(x) (x)
#define __cpu_to_be32(x) bswap_32(x)
#define __cpu_to_le16(x) (x)
#define __cpu_to_be16(x) bswap_16(x)
#else
#define __cpu_to_le32(x) bswap_32(x)
#define __cpu_to_be32(x) (x)
#define __cpu_to_le16(x) bswap_16(x)
#define __cpu_to_be16(x) (x)
#endif

#define __le32_to_cpu __cpu_to_le32
#define __be32_to_cpu __cpu_to_be32
#define __le16_to_cpu __cpu_to_le16
#define __be16_to_cpu __cpu_to_be16

#endif /* HAVE_BYTESWAP_COMPAT_H */

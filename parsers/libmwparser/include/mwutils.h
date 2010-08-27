#ifndef MWUTILS_H_
#define MWUTILS_H_

#include <wchar.h>
#include <antlr3.h>
#include "config.h"

static inline const wchar_t *
mwAntlr3stows(pANTLR3_STRING string, void **state)
{
#if (SIZEOF_WCHAR_T == 4)
    return (wchar_t*)string->chars;
#elif (SIZEOF_WCHAR_T == 2)
#error Unsupported wchar_t size!
#else
#error Unsupported wchar_t size!
#endif
}

static inline void 
mwFreeStringConversionState(void *state)
{
#if(SIZEOF_WCHAR_T == 4)
    /* do nothing */
#elif (SIZEOF_WCHAR_T == 2)
#error Unsupported wchar_t size!
#else
#error Unsupported wchar_t size!
#endif
}

#endif

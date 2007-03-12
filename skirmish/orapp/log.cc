/*
 * $Id$
 */

#include <string.h>
#include <stdarg.h>
#include <stdio.h>

#include "constants.hh"
#include "log.hh"


using namespace ORAPP;


namespace ORAPP {
    static void (*_log_func)(const char *) = 0;
}

void ORAPP::log_to(void(*func)(const char *)) {
    _log_func = func;
}

void ORAPP::_log(const char *format, ...) {
    if (!_log_func) return;

    char *buf = new char [ORAPP_ERR_BUFSIZ];
    memset(buf, 0, ORAPP_ERR_BUFSIZ);

    {
        va_list args;
        va_start(args, format);
        vsnprintf(buf, ORAPP_ERR_BUFSIZ-2, format, args);
        va_end(args);
    }

    _log_func(buf);

    delete[] buf;
}


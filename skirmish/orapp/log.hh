#ifndef ORAPP_LOG_HH
#define ORAPP_LOG_HH

/*
 * $Id$
 */

namespace ORAPP {

    void log_to(void(*)(const char *));
    void _log(const char *format, ...);

}

#endif

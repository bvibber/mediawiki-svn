#ifndef ORAPP_CONSTANTS_HH
#define ORAPP_CONSTANTS_HH

/*
 * $Id$
 */

extern "C" {
#include <oci.h>
}

#define ORAPP_INFO_BUFSIZ          1024
#define ORAPP_ERR_BUFSIZ           1024
#define ORAPP_FORMATSTR_BUFSIZ     1024
#define ORAPP_MAX_FIELD_WIDTH      100

#define ORAPP_SUCCESS(s) (s == OCI_SUCCESS || s == OCI_SUCCESS_WITH_INFO)

#ifndef MIN
#define MIN(a,b) ((a) < (b) ? (a) : (b))
#endif

#ifndef MAX
#define MAX(a,b) ((a) > (b) ? (a) : (b))
#endif

namespace ORAPP {
    extern const char *VERSION;
}

#endif

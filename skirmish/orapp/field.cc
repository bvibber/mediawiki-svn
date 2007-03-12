/*
 * $Id$
 */

extern "C" {
#include <oci.h>
}

#include "constants.hh"
#include "log.hh"
#include "field.hh"


using namespace ORAPP;


Field::Field(const char n[], unsigned l, unsigned w, signed &errno_) : _errno(errno_) {
    ocidefine = NULL;

    name.assign(n, l);
    width = w;

    value = new char [width];
    memset(value, 0, width);
}

Field::~Field(void) {
    if (value)
        delete[] value;

    if (ocidefine) {
        _errno = OCIHandleFree(ocidefine, OCI_HTYPE_DEFINE);

        /*
         * FIXME: The previous incarnation of this didn't check the
         * error, but in testing it appears that this happens some of
         * the time, but not all of the time.  Oracle probably
         * releases these things on its own when other handles upon
         * which these DEFINEs depend are released.  So, for now we'll
         * disable this.

         if (!ORAPP_SUCCESS(_errno))
            _log("OCIHandleFree(OCI_HTYPE_DEFINE) for %s failed", name.c_str());

        */
    }
}

Field::operator char(void) {
    return value[0];
}

Field::operator char *(void) {
    return value;
}

Field::operator const char *(void) {
    return value;
}

Field::operator int(void) {
    return strtol(value, NULL, 10);
}

Field::operator unsigned(void) {
    return strtoul(value, NULL, 10);
}

Field::operator long(void) {
    return strtol(value, NULL, 10);
}

Field::operator unsigned long(void) {
    return strtoul(value, NULL, 10);
}



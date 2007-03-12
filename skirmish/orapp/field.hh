#ifndef ORAPP_FIELD_HH
#define ORAPP_FIELD_HH

/*
 * $Id$
 */

extern "C" {
#include <oci.h>
}

#include <string>


namespace ORAPP {

class Field {

protected:
    friend class Row;

    signed &_errno;

    OCIDefine *ocidefine;

    unsigned width;
    char *value;
    sb2 isnull;

    Field(const char [], unsigned, unsigned width, signed &);
    ~Field(void);

    bool init(unsigned pos);
    bool deinit(void);

public:

    std::string name;

    /*
     * Bunch of type conversions for individual fields.
     */

    operator char(void);
    operator char*(void);
    operator const char *(void);
    operator int(void);
    operator unsigned(void);
    operator long(void);
    operator unsigned long(void);
};

}

#endif

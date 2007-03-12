#ifndef ORAPP_ERROR_HH
#define ORAPP_ERROR_HH

/*
 * $Id$
 */

extern "C" {
#include <oci.h>
}

#include <string>


namespace ORAPP {

    std::string interpret_error(OCIError *&, signed &);

}


#endif

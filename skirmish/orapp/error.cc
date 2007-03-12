/*
 * $Id$
 */

#include <string>

#include "constants.hh"
#include "log.hh"
#include "error.hh"


std::string ORAPP::interpret_error(OCIError *&_err, signed &_errno) {
    if (_errno == OCI_SUCCESS)
        return std::string("no error: success");

    if (!_err) {
        _log("error() called without initialization");
        return std::string("unknown error: handle not initialized");
    }

    text  buf[ORAPP_ERR_BUFSIZ] = {0};
    sb4   code = 0;
    sword ret;

    ret = OCIErrorGet(_err, (ub4)1, (text*)NULL, &code, buf, (ub4)sizeof(buf), OCI_HTYPE_ERROR);
    if (ret != OCI_SUCCESS) {
        _log("OCIErrorGet failed");
        return std::string("unknown error: unable to get error information");
    }

    std::string str;

    str.assign((const char *)buf);
    str.resize(str.length()-1); // nuke crlf

    switch (_errno) {
        case OCI_SUCCESS_WITH_INFO: str += " (OCI_SUCCESS_WITH_INFO)"; break;
        case OCI_NEED_DATA:         str += " (OCI_NEED_DATA)"; break;
        case OCI_NO_DATA:           str += " (OCI_NODATA)"; break;
        case OCI_ERROR:             str += " (OCI_ERROR)"; break;
        case OCI_INVALID_HANDLE:    str += " (OCI_INVALID_HANDLE)"; break;
        case OCI_STILL_EXECUTING:   str += " (OCI_STILL_EXECUTING)"; break;
        default:                    str += " (UNKNOWN)";
    }

    return str;
}

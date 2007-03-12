#ifndef ORAPP_CONN_HH
#define ORAPP_CONN_HH

/*
 * $Id$
 *
 * TODO:
 *   investigate OCIHandleFree(OCI_HTYPE_BIND) error
 *   seek method for getting full result set size during fetch
 */

extern "C" {
#include <oci.h>
}

#include <string>

#include "query.hh"


namespace ORAPP {

class Connection {

private:
    OCIEnv    *_env;
    OCIError  *_err;
    OCISvcCtx *_svc;

    std::string _ver, _tns, _user, _pass;

    Query *_query;

protected:

    static signed int _errno;

public:

    Connection(void);
    ~Connection(void);

    bool connect(const char *, const char *, const char *);
    bool connect(std::string &, std::string &, std::string &);

    bool disconnect(void);

          std::string  error(void);
    const std::string &version(void);

    Query *query(void);
    Query *query(const char *);

    bool execute(const char *);
};

}

#endif

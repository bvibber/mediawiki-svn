/*
 * $Id$
 */

extern "C" {
#include <oci.h>
}

#include "constants.hh"
#include "log.hh"
#include "error.hh"
#include "query.hh"
#include "conn.hh"


using namespace ORAPP;


namespace ORAPP {
    signed Connection::_errno = 0;
}

Connection::Connection(void) {
    _env   = NULL;
    _svc   = NULL;
    _err   = NULL;
    _query = NULL;

    _errno = 0;
}

Connection::~Connection(void) {
    disconnect();
}

bool Connection::connect(const char *tns, const char *user, const char *pass) {
    /*
     * If we had a pre-existing service-context, disconnect and
     * deallocate everything before we reconnect.
     */

    if (!disconnect()) {
        _log("failed to disconnect during connect");
        return false;
    }

    /*
     * We're either disconnected now, or freshly unconnected.  Either
     * way, whatever the previous settings were they are now
     * irrelevant.
     */

    _tns  = tns;
    _user = user;
    _pass = pass;

    /*
     * Now connect.
     */

    _errno = OCIEnvCreate(&_env, OCI_DEFAULT, NULL, NULL, NULL, NULL, 0, NULL);
    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCIEnvCreate failed");
        return false;
    }

    _errno = OCIHandleAlloc(_env, (void**)&_err, OCI_HTYPE_ERROR, 0, NULL);
    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCIHandleAlloc(error) failed");
        return false;
    }

    _errno = OCILogon(_env, _err, &_svc,
                      (unsigned char *)_user.c_str(), _user.length(),
                      (unsigned char *)_pass.c_str(), _pass.length(),
                      (unsigned char *)_tns.c_str(),  _tns.length());
    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCILogon failed");
        return false;
    }

    /*
     * And get server information for good measure.
     */

    text buf[ORAPP_INFO_BUFSIZ] = {0};

    _errno = OCIServerVersion(_svc, _err, (text *)&buf, (ub4)sizeof(buf), OCI_HTYPE_SVCCTX);
    if (!ORAPP_SUCCESS(_errno)) {

        _log("OCIServerVersion failed");
        _ver = "unknown: failed to retrieve version information";

    } else {
        _ver = (char*)buf;

        /*
         * Nuke out stupid embedded carriage returns/linefeeds.
         */

        std::string::size_type i = 0;
        while ((i = _ver.find("\n", i)) != std::string::npos)
            _ver.replace(i++, 1, ", ");
    }

    return true;
}

bool Connection::connect(std::string &tns, std::string &user, std::string &pass) {
    return connect(tns.c_str(), user.c_str(), pass.c_str());
}

/*
 * Here we deallocate things in reverse order, since they have
 * dependencies on each other.
 */

bool Connection::disconnect(void) {
    if (_query) {
        delete _query;
        _query = NULL;
    }

    if (_svc) {
        _errno = OCILogoff(_svc, _err);
        if (!ORAPP_SUCCESS(_errno)) {
            _log("OCILogoff failed");
            return false;
        }
        _svc = NULL;
    }

    if (_err) {
        _errno = OCIHandleFree(_err, OCI_HTYPE_ERROR);
        if (!ORAPP_SUCCESS(_errno)) {
            _log("OCIHandleFree(OCI_HTYPE_ERROR) failed");
            return false;
        }
        _err = NULL;
    }

    if (_env) {
        _errno = OCIHandleFree(_env, OCI_HTYPE_ENV);
        if (!ORAPP_SUCCESS(_errno)) {
            _log("OCIHandleFree(OCI_HTYPE_ENV) failed");
            return false;
        }
        _env = NULL;
    }

    return true;
}

std::string Connection::error(void) {
    return ORAPP::interpret_error(_err, _errno);
}

const std::string &Connection::version(void) {
    return _ver;
}

/*
 * This is the prime place to parallelize the ORAPP library -- here
 * we're just caching one Query object, but we could potentially cache
 * many more, and this would be the once place to change the logic and
 * dole them out, perhaps limiting the total number, how many are left
 * around, etc.
 */

ORAPP::Query *Connection::query(void) {
    if (_query)
        return _query;

    /*
     * Otherwise, create an object and cache it.
     *
     * TODO: might be nice to have a (const) dummy query object laying
     * around so that we don't completely fuck callers who do the
     * typical thing like so:
     *
     *    q = db->query();
     *   *q << "SELECT ... ";
     */

    _query = new Query(_env, _err, _svc, _errno);

    if (!_query->init()) {
        _log("failed to create new query object");
        delete _query;
        _query = NULL;
    }

    return _query;
}

Query *Connection::query(const char *s) {
    ORAPP::Query *q = query();
    if (!q)
        return NULL;

    *q << s;

    return q;
}


bool Connection::execute(const char *s) {
    ORAPP::Query *q = query();
    return q ? q->execute(s) : false;
}

/*
 * $Id$
 */

#include <stdlib.h>
#include <stdio.h>
#include <stdarg.h>
#include <string.h>

extern "C" {
#include <oci.h>
}

#include <string>
#include <list>

#include "constants.hh"
#include "log.hh"
#include "error.hh"
#include "conn.hh"
#include "row.hh"
#include "query.hh"


using namespace ORAPP;


/*
 * This constructor is protected from general use, and is only
 * instantiated by the Connection object.
 */

Query::Query(OCIEnv *&env, OCIError *&err, OCISvcCtx *&svc, signed &errno_)
                          : _env(env), _err(err), _svc(svc), _errno(errno_) {
    _stmt = NULL;
    _row  = NULL;

    _rows    = 0;
    _type    = 0;
    _typelen = sizeof(_type);

    _prepared = false;

    _execmode = OCI_COMMIT_ON_SUCCESS;
}

/*
 * Deinit is hidden from public view, as is the destructor.  We only
 * want the main Connection object doing any instantiation or
 * destruction.
 */

Query::~Query(void) {
    deinit();
}

bool Query::init(unsigned execmode) {
    _errno = OCIHandleAlloc(_env, (void**)&_stmt, OCI_HTYPE_STMT, 0, NULL);
    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCIHandleAlloc(OCI_HTYPE_STMT) failed");
        return false;
    }

    _execmode = execmode;

    return true;
}

bool Query::deinit(void) {
    if (reset())
        return false;

    if (_stmt) {
        _errno = OCIHandleFree(_stmt, OCI_HTYPE_STMT);
        if (!ORAPP_SUCCESS(_errno)) {
            _log("OCIHandleFree(OCI_HTYPE_STMT) failed");
            return false;
        }
        _stmt = NULL;
    }

    return true;
}

void Query::autocommit(bool enabled) {
    _execmode = enabled ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT;
}

bool Query::commit(void) {
    return execute("commit");
}

bool Query::rollback(void) {
    return execute("rollback");
}

bool Query::clear(void) {
    SQL     = "";
    nextSQL = "";
    return reset();
}

/*
 * Apparently in the case of a SELECT statement/cursor, fetching 0
 * rows will reset the cursor.  I have to believe there is a
 * simpler/less kludgy way to accomplish a cursor reset.
 */

bool Query::reset(void) {
    if (_stmt && _type == OCI_STMT_SELECT) {
        _errno = OCIStmtFetch(_stmt, _err, 0, OCI_FETCH_NEXT, OCI_DEFAULT);
        if (!ORAPP_SUCCESS(_errno)) {
            _log("OCIStmtFetch(reset) failed");
            return false;
        }
    }

    if (_row) {
        delete _row;
        _row = NULL;
    }

    _rows     = 0;
    _prepared = false;

    /*
     * The following code to free any bindings seems to return an
     * error every single time, saying 'ORA-01403: no data found
     * (OCI_ERROR)'.  It is not clear why, or how to fix it, but it
     * should be noted that the previous incarnation (ora++) suffered
     * from the same exact error, and wasn't catching it (either by
     * ignorance or specific ommission, that too is unclear).
     */

    bindlist_t::iterator i;
    for (i = _binds.begin(); i != _binds.end(); ++i) {
        _errno = OCIHandleFree((*i), OCI_HTYPE_BIND);

        if (!ORAPP_SUCCESS(_errno))
            ;//_log("OCIHandleFree(OCI_HTYPE_BIND) failed during reset");
    }
    _binds.clear();

    SQL = "";

    return true;
}

bool Query::prepare(void) {
    if (nextSQL.empty()) {
        _log("prepare called with empty SQL");
        return false;
    }

    if (_prepared) {
        _log("prepare called when already prepared (%s)", SQL.c_str());
        return true;
    }

    if (!reset()) {
        _log("reset failed during execute");
        return false;
    }

    SQL = nextSQL.c_str();
    nextSQL = "";

    _errno = OCIStmtPrepare(_stmt, _err, (unsigned char *)SQL.c_str(), (ub4)SQL.length(), OCI_NTV_SYNTAX, OCI_DEFAULT);
    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCIStmtPrepare failed");
        return false;
    }

    /*
     * Now that it's prepared, ask the parser what statement type it
     * thinks it has, and store it for later (need it in the execute()
     * step, but we get it here because we might use it separately in
     * a bind(), which must be called before execute() but _after_
     * prepare()).  NOTE: If this ends up not being required, we can
     * move this following code chunk to a more logical location
     * within execute().
     */

    _errno = OCIAttrGet(_stmt, OCI_HTYPE_STMT, &_type, &_typelen, OCI_ATTR_STMT_TYPE, _err);
    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCIAttrGet(OCI_ATTR_STMT_TYPE) failed");
        return false;
    }

    return _prepared = true;
}

bool Query::execute(void) {
    /*
     * Get ourselves prepared if we're not already.  Prepare will
     * reset() us, cleaning up any previous activity.
     */

    if (!_prepared && !prepare()) {
        _log("prepare during execute failed");
        return false;
    }

    /*
     * OK, we're prepared now, now let's execute.  By default, a
     * SELECT requires _0_ iterations because the API requires the
     * caller to use fetch() separately.  To be clever we use a
     * boolean test to return 0 or 1 in the proper cases for this
     * below.
     */

    _errno = OCIStmtExecute(_svc, _stmt, _err, (ub4)(_type != OCI_STMT_SELECT), 0, NULL, NULL, _execmode);

    /*
     * Catch-all for any subsequent calls to bind() before ever
     * calling execute() again (the latter of which guarantees
     * preparation occurs because it calls reset()). Reset _prepared
     * for any condition, failure or success.
     */

    _prepared = false;

    /*
     * If successful, then we don't need to do anything since Row's
     * are created on demand and cached by the fetch() method.
     */

    if (_errno != OCI_SUCCESS && _errno != OCI_SUCCESS_WITH_INFO) {

        /*
         * If this _wasn't_ a ``no data'' return from a PL/SQL
         * function block (which is not an error), then we indicate an
         * error.
         */

        if (!(_errno == OCI_NO_DATA && (_type == OCI_STMT_BEGIN || _type == OCI_STMT_DECLARE))) {
            _log("OCIStmtExecute failed");
            return false;
        }
    }

    /*
     * Finally, update our internal row count.
     *
     * You must update the row count on every fetch if you care that
     * rows() return something valid... technically it returns
     * OCI_ATTR_ROWS_PROCESSED, which is to say if we were to
     * fast-forward to the last of several rows (OCI_FETCH_LAST)
     * first, we would only see OCI_ATTR_ROW_COUNT return a value of
     * 1, since we had only looked at one row thus far.
     *
     * NOTE: previous ora++ library didn't abort from an error of the
     * following kind, but it appears as though it should have given
     * how important a detail this is to any caller..
     */

    ub4 rows_size = sizeof(_rows);

    _errno = OCIAttrGet(_stmt, OCI_HTYPE_STMT, &_rows, &rows_size, OCI_ATTR_ROW_COUNT, _err);
    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCIAttrGet(OCI_ATTR_ROW_COUNT) failed");
        return false;
    }

    return true;
}

bool Query::execute(const char *s) {
    nextSQL = s;
    return execute();
}

/*
 * TODO: As with Queries, it would be nice for the caller if we had a
 * dummy empty row object to return in lieu of a potential NULL in a
 * few failure cases below.
 */

Row *Query::fetch(void) {
    /*
     * Because of the fact that Oracle wants the caller to define
     * memory areas to put data before it's retrieved, we must create
     * a new Row (which triggers the necessary code, OCIDefine*)
     * before we can fetch anything.  Oh well.
     */

    if (!_row) {
        _row = new Row(_stmt, _err, _errno);

        if (!_row->init()) {
            _log("failed to instantiate new row");
            delete _row;
            return _row = NULL;
        }
    }

    _row->reset();

    /*
     * Do the actual fetch.  OCI_NO_DATA is an error that indicates no
     * more rows to retrieve, thus any other non-SUCCESS error is a
     * bailout condition.
     */

    _errno = OCIStmtFetch(_stmt, _err, 1, OCI_FETCH_NEXT, OCI_DEFAULT);

    if (_errno == OCI_NO_DATA)
        return NULL;

    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCIStmtFetch(OCI_FETCH_NEXT) failed");
        return NULL;
    }

    /*
     * NOTE: With iterative SELECTs, the current implementation does
     * not know how to predict how many rows we're going to get back,
     * and can only update the count as the cursor moves along.  I'm
     * sure there must be a way to do this, but I haven't figured it
     * out yet.  UPDATE: Tried OCI_ATTR_ROWS_RETURNED, tried
     * fast-forwarding to OCI_FETCH_LAST and then checking the row
     * count, but to no avail.
     */

    ub4 rows_size = sizeof(_rows);
    _errno = OCIAttrGet(_stmt, OCI_HTYPE_STMT, &_rows, &rows_size, OCI_ATTR_ROW_COUNT, _err);
    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCIAttrGet(OCI_ATTR_ROW_COUNT) during fetch failed");
        return NULL;
    }

    return _row;
}

unsigned Query::rows(void) {
    return _rows;
}

const char *Query::statement(void) {
    return nextSQL.length() ? nextSQL.c_str() : SQL.c_str();
}

std::string Query::error(void) {
    return ORAPP::interpret_error(_err, _errno);
}

/*
 * Various methods for Oracle's directly-bound SQL input/output
 * mechanisms/types.
 */

bool Query::bind(const char *name, char &value) {
    return bind(name, &value, sizeof(value), SQLT_CHR);
}

bool Query::bind(const char *name, const char &value) {
    return bind(name, (void*)&value, sizeof(value), SQLT_CHR);
}

bool Query::bind(const char *name, char *value) {
    return bind(name, (void*)value, strlen(value) + 1, SQLT_STR);
}

bool Query::bind(const char *name, const char *value) {
    return bind(name, (void*)value, strlen(value) + 1, SQLT_STR);
}

bool Query::bind(const char *name, void *addr, unsigned width) {
    return bind(name, addr, width, SQLT_STR);
}

bool Query::bind(const char *name, char *addr, unsigned width) {
    return bind(name, addr, width, SQLT_STR);
}

bool Query::bind(const char *name, const char *addr, unsigned width) {
    return bind(name, (char*)addr, width, SQLT_STR);
}

bool Query::bind(const char *name, signed short *value) {
    return bind(name, value, sizeof(*value), SQLT_INT);
}

bool Query::bind(const char *name, signed short &value) {
    return bind(name, &value, sizeof(signed short), SQLT_INT);
}

bool Query::bind(const char *name, signed int *value) {
    return bind(name, value, sizeof(*value), SQLT_INT);
}

bool Query::bind(const char *name, signed int &value) {
    return bind(name, &value, sizeof(signed int), SQLT_INT);
}

bool Query::bind(const char *name, signed long *value) {
    return bind(name, value, sizeof(*value), SQLT_INT);
}

bool Query::bind(const char *name, signed long &value) {
    return bind(name, &value, sizeof(signed long), SQLT_INT);
}

bool Query::bind(const char *name, unsigned short *value) {
    return bind(name, value, sizeof(*value), SQLT_UIN);
}

bool Query::bind(const char *name, unsigned int *value) {
    return bind(name, value, sizeof(*value), SQLT_UIN);
}

bool Query::bind(const char *name, unsigned int &value) {
    return bind(name, &value, sizeof(unsigned int), SQLT_UIN);
}

bool Query::bind(const char *name, unsigned long *value) {
    return bind(name, (void *) value, sizeof(*value), SQLT_UIN);
}

bool Query::bind(const char *name, unsigned long &value) {
    return bind(name, (void *) &value, sizeof(unsigned long), SQLT_UIN);
}

/*
 * If the SQL statement contains a name variable (a symbol beginning
 * with a ``:''), it designates a memory area for the value of the
 * variable and must be assigned here before the statement is
 * executed.  The variable names and the value memory area must be
 * kept around until the statement is fully executed (including
 * subsequent fetch()s, if need be).  For that reason, we allocate
 * memory here to keep a copy of the bind, though it doesn't seem to
 * serve us much of a purpose given the fact that releasing the bind's
 * produces an error (see above in reset()), and also since only _two_
 * of the numerous, varied and sundry Oracle demo programs actually
 * bother to release them.
 */

bool Query::bind(const char *name, void *addr, unsigned width, ub2 type) {
    OCIBind *orabind = NULL;

    if (!_prepared && !prepare()) {
        _log("prepare() failed during bind(%s)", name);
        return false;
    }

    _errno = OCIBindByName(_stmt, &orabind, _err, (unsigned char *)name, (sb4)-1, addr, (sb4)width, type,
                           NULL, NULL, NULL, 0, NULL, OCI_DEFAULT);
    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCIBindByName(%s) failed", name);
        return false;
    }

    _binds.push_back(orabind);

    return true;
}


/**
 ** A few stream operators to make SQL insertion convenient.
 **/

Query& Query::operator<<(const char *s) {
    nextSQL += s;
    return *this;
}

Query& Query::operator<<(const unsigned char *s) {
    nextSQL += (const char *)s;
    return *this;
}

Query& Query::operator<<(const signed char *s) {
    nextSQL += (const char *)s;
    return *this;
}

Query& Query::operator<<(char c) {
    nextSQL += c;
    return *this;
}

Query& Query::operator<<(unsigned char c) {
    nextSQL += c;
    return *this;
}

Query& Query::operator<<(signed char c) {
    nextSQL += c;
    return *this;
}

/*
 * An oldie but a goodie: formatstr's for putting together queries
 * with esoteric types that aren't covered by the usual stream
 * operators above (which could be expanded, but won't, because this
 * method is more useful and overall less work than to do each
 * conversion ourselves).
 */

void Query::assign(const char *format, ...) {
    char buf[ORAPP_FORMATSTR_BUFSIZ] = {0};

    va_list args;
    va_start(args, format);

    vsnprintf(buf, sizeof(buf)-1, format, args);
    va_end(args);

    nextSQL.assign(buf);
}

void Query::append(const char *format, ...) {
    char buf[ORAPP_FORMATSTR_BUFSIZ] = {0};

    va_list args;
    va_start(args, format);

    vsnprintf(buf, sizeof(buf)-1, format, args);
    va_end(args);

    nextSQL.append(buf);
}


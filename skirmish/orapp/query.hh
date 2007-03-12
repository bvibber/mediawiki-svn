#ifndef ORAPP_QUERY_HH
#define ORAPP_QUERY_HH

/*
 * $Id$
 */

extern "C" {
#include <oci.h>
}

#include <string>
#include <list>

#include "row.hh"


namespace ORAPP {

class Query {

public:

    typedef std::list<OCIBind*> bindlist_t;

private:

    OCIEnv    *&_env;
    OCIError  *&_err;
    OCISvcCtx *&_svc;

    signed     &_errno;

    OCIStmt    *_stmt;

    ub2 _type;
    ub4 _typelen, _rows, _execmode;

    bool _prepared;
    bindlist_t _binds;

    ORAPP::Row *_row;

protected:

    friend class Connection;

    Query(OCIEnv *&, OCIError *&, OCISvcCtx *&, signed &);
    ~Query(void);

    bool init(unsigned = OCI_COMMIT_ON_SUCCESS);
    bool deinit(void);

    std::string SQL, nextSQL;

public:

    void autocommit(bool = true); /* on by default in init() */

    bool clear(void);
    bool reset(void);
    bool prepare(void);
    bool execute(void);
    bool execute(const char *);

    bool commit(void);
    bool rollback(void);

    Row *fetch(void);

    unsigned rows(void);
    const char *statement(void);

    std::string error(void);

    /*
     * Various methods for using Oracle to bind variables directly to
     * SQL inputs/outputs.
     */

    bool bind(const char *, char &);
    bool bind(const char *, const char &);
    bool bind(const char *, char *);
    bool bind(const char *, const char *);
    bool bind(const char *, void *, unsigned);
    bool bind(const char *, char *, unsigned);
    bool bind(const char *, const char *, unsigned);
    bool bind(const char *, signed short *);
    bool bind(const char *, signed short &);
    bool bind(const char *, signed int *);
    bool bind(const char *, signed int &);
    bool bind(const char *, signed long *);
    bool bind(const char *, signed long &);
    bool bind(const char *, unsigned short *);
    bool bind(const char *, unsigned int *);
    bool bind(const char *, unsigned int &);
    bool bind(const char *, unsigned long *);
    bool bind(const char *, unsigned long &);

    bool bind(const char *, void *, unsigned, ub2);

    /*
     * Some stream operators for stuffing SQL into the Query.
     */

    Query& operator<<(const char *);
    Query& operator<<(const unsigned char *);
    Query& operator<<(const signed char *);
    Query& operator<<(char);
    Query& operator<<(unsigned char);
    Query& operator<<(signed char);

    /*
     * And finally, some very useful methods for putting together
     * queries using uncommon types not covered by above's stream
     * operator overloads.  Goes nicely with the STL metaphor and
     * already matches ``clear'' from above.
     */

    void assign(const char *, ...);
    void append(const char *, ...);

};

}

#endif

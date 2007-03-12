#ifndef ORAPP_ROW_HH
#define ORAPP_ROW_HH

/*
 * $Id$
 */

#include <vector>

#include "field.hh"


namespace ORAPP {

class Row {

public:

    typedef std::vector<Field *> fieldlist_t;

private:

    OCIStmt  *&_stmt;
    OCIError *&_err;

    signed    &_errno;

    fieldlist_t _columns;

    Field *EMPTY;

protected:

    friend class Query;

    Row(OCIStmt *&, OCIError *&, signed &);
    ~Row(void);

    bool init(void);
    bool deinit(void);

    Field *field(const char *);

public:

    unsigned width(void);

    int position(const char *);
    const char *name(unsigned);
    bool isnull(unsigned);

    bool reset(void);

    Field& operator[] (signed);
    Field& operator[] (unsigned);
    Field& operator[] (std::string &);
    Field& operator[] (const char *);
};

}

#endif

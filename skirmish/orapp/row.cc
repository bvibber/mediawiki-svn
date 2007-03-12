/*
 * $Id$
 */


extern "C" {
#include <oci.h>
}

#include "constants.hh"
#include "log.hh"
#include "field.hh"
#include "row.hh"


using namespace ORAPP;


Row::Row(OCIStmt *&stmt, OCIError *&err, signed &errno_) : _stmt(stmt), _err(err), _errno(errno_) {
    EMPTY = NULL;
}

Row::~Row(void) {
    deinit();
}

/*
 * Strategy: since each Row is technically a defined constant inside
 * an individual query (same # of fields, same field names, same
 * widths), we'll first scan for the field attributes and cache them
 * in our object.  Then instead of tearing down the whole thing for
 * every fetch (which the old API did), we'll instead simply reset the
 * field values to null.
 *
 * The big thing to keep in mind is that we don't Row::init() more
 * than once per unique query, and that the Query object instead calls
 * Row::reset() for every fetch() instead of delete'ing and
 * reconstructing a Row object every time.
 */

bool Row::init(void) {
    static const char *UNKNOWN = "unknown field";
    ub4 count, countsz = sizeof(count);

    _errno = OCIAttrGet(_stmt, OCI_HTYPE_STMT, &count, &countsz, OCI_ATTR_PARAM_COUNT, _err);
    if (!ORAPP_SUCCESS(_errno)) {
        _log("OCIAttrGet(OCI_ATTR_PARAM_COUNT) failed during Row initialization");
        return false;
    }

    /*
     * First thing's first, we need to create our EMPTY field that we
     * use when we either encounter an OCI error (and yet still need
     * to define an equivalent Field), or when the caller asks for a
     * field that legitimately does not exist (Row["unknown field"]).
     */

    if (!EMPTY)
        EMPTY = new Field(UNKNOWN, strlen(UNKNOWN), 1, _errno);

    /*
     * We know how many fields/columns there are now, so let's process
     * each one and create a Field object into our vector.
     */

    Field *f;
    OCIParam *p;
    ub2 rcode, len; // throwaway

    for (unsigned i = 0; i < count; i++) {

        /*
         * ``position'' is an Oracle-relative (1-based) concept, not
         * UNIX-relative (0-based), hence i+1..
         */

        _errno = OCIParamGet(_stmt, OCI_HTYPE_STMT, _err, (void**)&p, i+1);
        if (!ORAPP_SUCCESS(_errno)) {
            _log("OCIParamGet(OCI_HTYPE_STMT) failed, using EMPTY");
            _columns.push_back(EMPTY);
            continue;
        }

        /*
         * Retrieve field name.
         */

        char *name;
        unsigned name_len;

        _errno = OCIAttrGet(p, OCI_DTYPE_PARAM, &name, &name_len, OCI_ATTR_NAME, _err);
        if (!ORAPP_SUCCESS(_errno)) {
            _log("OCIAttrGet(OCI_DTYPE_PARAM, OCI_ATTR_NAME) failed, using EMPTY");
            _columns.push_back(EMPTY);
            continue;
        }

        /*
         * And field width.
         */

        ub2 width;

        _errno = OCIAttrGet(p, OCI_DTYPE_PARAM, &width, 0, OCI_ATTR_DATA_SIZE, _err);
        if (!ORAPP_SUCCESS(_errno)) {
            _log("OCIAttrGet(OCI_DTYPE_PARAM, OCI_ATTR_DATA_SIZE) for %s failed, using EMPTY", name);
            _columns.push_back(EMPTY);
            continue;
        }

        width = MIN(width, ORAPP_MAX_FIELD_WIDTH) + 1; // include null

        /*
         * OK, we know enough about our field, now we need to tell
         * Oracle where to put data by calling one of the OCIDefine*
         * functions.
         */

        f = new Field(name, name_len, width, _errno);

        _errno = OCIDefineByPos(_stmt, &f->ocidefine, _err, i+1, f->value, width, SQLT_STR, &f->isnull, &len, &rcode, OCI_DEFAULT);
        if (!ORAPP_SUCCESS(_errno)) {
            _log("OCIDefineByPos() for %s failed, using EMPTY", name);
            _columns.push_back(EMPTY);
            delete f;
            continue;
        }

        /*
         * Tweak the logic around this one particular field.  TODO:
         * need to understand the possible values and their actual
         * meanings.  NOTE: there is an OCIGetAttr(OCI_ATTR_IS_NULL)
         * type of thing available if necessary.
         */

        f->isnull = (unsigned)(f->isnull != 0);

        _columns.push_back(f);
    }

    return true;
}

bool Row::deinit(void) {
    fieldlist_t::iterator i;
    for (i = _columns.begin(); i != _columns.end(); ++i)
        if (*i != EMPTY)
            delete *i;

    _columns.clear();

    delete EMPTY;

    return true;
}

Field *Row::field(const char *name) {
    fieldlist_t::iterator i;
    for (i = _columns.begin(); i != _columns.end(); ++i)
        if (!strcasecmp((*i)->name.c_str(), name))
            return *i;

    return NULL;
}

unsigned Row::width(void) {
    return _columns.size();
}

int Row::position(const char *name) {
    unsigned n = 0;
    fieldlist_t::iterator i;
    for (i = _columns.begin(); i != _columns.end(); ++i, n++)
        if (!strcasecmp((*i)->name.c_str(), name))
            return n;

    return -1;
}

const char *Row::name(unsigned position) {
    if (position+1 > _columns.size())
        return NULL;

    return _columns[position]->name.c_str();
}

bool Row::isnull(unsigned position) {
    if (position+1 > _columns.size())
        return true;

    return _columns[position]->isnull;
}

Field& Row::operator[](unsigned position) {
    if (position+1 <= _columns.size())
        return *(_columns[position]);
    else
        return (Field&)*EMPTY;
}

Field& Row::operator[](signed position) {
    return (*this)[(unsigned)position];
}

Field& Row::operator[](const char *name) {
    Field *f = field(name);
    return f ? *f : (Field&)*EMPTY;
}

Field& Row::operator[](std::string &name) {
    Field *f = field(name.c_str());
    return f ? *f : (Field&)*EMPTY;
}

bool Row::reset(void) {
    fieldlist_t::iterator i;
    for (i = _columns.begin(); i != _columns.end(); ++i)
        memset((*i)->value, 0, (*i)->width);

    return true;
}


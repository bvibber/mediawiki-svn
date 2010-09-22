#ifndef _MWSCRIPT_BUF_H
#define _MWSCRIPT_BUF_H

#include <mwattributes.h>

struct MWSCRIPTBUF_struct;

static inline void appendBytes(struct MWSCRIPTBUF_struct *buf, const char *bytes, size_t numBytes);

#define BUFSIZE 4096

typedef struct MWSCRIPTBUF_INDEX_struct
{
    int row;
    int col;
}
    MWSCRIPTBUF_INDEX;

#ifdef TARGET_LANGUAGE_PERL

#include <EXTERN.h>
#include <perl.h>
#include <XSUB.h>

typedef struct MWSCRIPTBUF_struct
{
    AV *av;
    SV *cur;
    size_t len;
    char * buf;
    char *p;
}
    MWSCRIPTBUF;

static inline MWSCRIPTBUF_INDEX
getIndex(MWSCRIPTBUF *buf)
{
    MWSCRIPTBUF_INDEX index = {
        /* av_len returns the highest _index_ in the array */
        av_len(buf->av),
        buf->p - buf->buf,
    };
    return index;
}

static inline void
copyAppendRegion(MWSCRIPTBUF *buf, MWSCRIPTBUF_INDEX start, MWSCRIPTBUF_INDEX end)
{
    int row;
    for (row = start.row; row <= end.row; row++) {
        SV *sv = *(av_fetch(buf->av, row, 0));
        int startCol = row == start.row ? start.col : 0;
        int endCol   = row ==   end.row ?   end.col : SvCUR(sv);
        appendBytes(buf, SvPVX(sv) + startCol, endCol - startCol);
    }
}


static inline void *
scriptBufResult(MWSCRIPTBUF *buf)
{
    SvCUR_set(buf->cur, buf->p - buf->buf);
    SV * ret = newRV_noinc((SV*)buf->av);
    buf->av = NULL;
    return ret;
}

static inline bool
resetBuffer(MWSCRIPTBUF * buf)
{
    buf->av = newAV();
    if (buf->av == NULL) {
        ANTLR3_FREE(buf);
        return false;
    }

    SV *sv = newSV(BUFSIZE);
    SvPOK_on(sv);
    buf->len = SvLEN(sv) - 1;
    buf->buf = SvPVX(sv);
    buf->p = buf->buf;
    buf->cur = sv;

    av_push(buf->av, sv);

    return true;
}

static inline void
freeBuffer(MWSCRIPTBUF * buf)
{
    if (buf->av != NULL) {
        SvREFCNT_dec(buf->av);
        buf->av = NULL;
    }
}

static inline void
appendBytes(MWSCRIPTBUF *buf, const char *bytes, size_t numBytes)
{
    bool tooLarge;
    const char *p0 = bytes;
    do {
        int bytesRemaining = numBytes - (p0 - bytes);
        tooLarge = bytesRemaining > buf->len;
        int n =  tooLarge ? buf->len : bytesRemaining;
        memcpy(buf->p, p0, n);
        buf->p += n;
        if (tooLarge) {
            p0 += n;
            SvCUR_set(buf->cur, buf->p - buf->buf);
            SV *sv = newSV(BUFSIZE);
            buf->len = SvLEN(sv) - 1;
            SvPOK_on(sv);
            buf->buf = SvPVX(sv);
            av_push(buf->av, sv);
            buf->cur = sv;
            buf->p = buf->buf;
        }
    } while (tooLarge);
}
#elif defined(TARGET_LANGUAGE_PHP)

#include <php.h>
#include <glib.h>

typedef struct MWSCRIPTBUF_struct
{
    zval *av;
    zval *cur;
    size_t len;
    char * buf;
    char *p;
}
    MWSCRIPTBUF;

static inline MWSCRIPTBUF_INDEX
getIndex(MWSCRIPTBUF *buf)
{
    MWSCRIPTBUF_INDEX index = {
        buf->av->value.ht->nNumOfElements - 1,
        buf->p - buf->buf,
    };
    return index;
}

static inline void
copyAppendRegion(MWSCRIPTBUF *buf, MWSCRIPTBUF_INDEX start, MWSCRIPTBUF_INDEX end)
{
    int row;
    for (row = start.row; row <= end.row; row++) {
        zval **tmp;
        int ret = zend_hash_index_find(buf->av->value.ht, row, (void **) &tmp);
        zval *sv = *tmp;
        int startCol = row == start.row ? start.col : 0;
        int endCol   = row ==   end.row ?   end.col : sv->value.str.len - 1;
        appendBytes(buf, sv->value.str.val + startCol, endCol - startCol);
    }
}

static inline void *
scriptBufResult(MWSCRIPTBUF *buf)
{
    buf->cur->value.str.len = buf->p - buf->buf;
    zval * ret = buf->av;
    buf->av = NULL;
    return ret;
}

static inline bool
resetBuffer(MWSCRIPTBUF * buf)
{
    MAKE_STD_ZVAL(buf->av);
    if (buf->av == NULL) {
        ANTLR3_FREE(buf);
        return false;
    }
    array_init(buf->av);

    zval *sv;
    MAKE_STD_ZVAL(sv);
    sv->type = IS_STRING;
    sv->value.str.len = 0;
    sv->value.str.val = emalloc(BUFSIZE);
    buf->len = BUFSIZE - 1;
    buf->buf = sv->value.str.val;
    buf->p = buf->buf;
    buf->cur = sv;

    add_next_index_zval(buf->av, sv);

    return true;
}

static inline void
freeBuffer(MWSCRIPTBUF * buf)
{
    if (buf->av != NULL) {
        Z_DELREF_P(buf->av);
        buf->av = NULL;
    }
}

static inline void
appendBytes(MWSCRIPTBUF *buf, const char *bytes, size_t numBytes)
{
    bool tooLarge;
    const char *p0 = bytes;
    do {
        int bytesRemaining = numBytes - (p0 - bytes);
        tooLarge = bytesRemaining > buf->len;
        int n =  tooLarge ? buf->len : bytesRemaining;
        memcpy(buf->p, p0, n);
        buf->p += n;
        if (tooLarge) {
            p0 += n;
            buf->cur->value.str.len = buf->p - buf->buf;
            zval *sv;
            MAKE_STD_ZVAL(sv);
            sv->type = IS_STRING;
            sv->value.str.len = 0;
            sv->value.str.val = emalloc(BUFSIZE);
            buf->len = BUFSIZE - 1;
            buf->buf = sv->value.str.val;
            buf->p = buf->buf;
            buf->cur = sv;
            add_next_index_zval(buf->av, sv);
        }
    } while (tooLarge);
}

#endif

static inline bool
initBuffer(MWSCRIPTBUF * buf)
{
    return resetBuffer(buf);
}

static inline void
appendAntlr3String(MWSCRIPTBUF *buf, pANTLR3_STRING string)
{
    appendBytes(buf, (char *)string->chars, string->size - 1);
}

static inline void
appendAttrVector(MWSCRIPTBUF *buf, const char *element, pANTLR3_VECTOR attr)
{
    int i;
    for (i = 0; i < attr->count; i++) {
        char *attrBuf;
        size_t len;
        MWAttributesValidate(&attrBuf, &len, element, attr);
        appendBytes(buf, attrBuf, len);
    }
}

#define APPEND_ATTR_VECTOR(element, attr) (appendAttrVector(BUF, element, attr))

#define APPEND_CONST_STRING(string) (appendBytes(BUF, string, sizeof(string) - 1))

#define APPEND_STRING(string) (appendBytes(BUF, string, strlen(string)))

#define APPEND_ANTLR3_STRING(string) (appendAntlr3String(BUF, string))

#define CLEAR_STRING(string) do {                               \
        if (string != NULL) {                                   \
            APPEND_ANTLR3_STRING(string);                       \
            string->factory->destroy(string->factory, string);  \
            string = NULL;                                      \
        }                                                       \
    } while (0)

struct MWLINKCOLLECTION_struct;

void MWScriptCallbackResolveLinks(struct MWLINKCOLLECTION_struct *linkCollection, void *data);

#endif

%module mwp

%{
#include <antlr3.h>
#include <mwparser.h>
#include <mwlistener.h>
#include <mwlinkresolution.h>
#include <mwlinkcollection.h>
extern MWLISTENER mwScriptBufferListener;
static MWPARSER_OPTIONS *null_options = NULL;
%}

%include <mwlinkresolution.h>

#ifdef SWIGPERL
%typemap(in) SV *string {
    SvREFCNT_inc($input);
    $1 = $input;
}

%typemap(in) SV *callback {
    $1 = $input;
}

%typemap(in) SV *callbackData {
    $1 = $input;
}

%typemap(in) MWLINKRESOLUTION *resolution {
    SvREFCNT_inc($input);
    if ((SWIG_ConvertPtr($input,(void **) &$1, $1_descriptor,0)) == -1) {
        SWIG_exception_fail(SWIG_ArgError(SWIG_ValueError), "Invalid class for link resolution pointer.");
    }
    $1->free = freeScriptVar;
    $1->freeData = $input;
    
}

%{
    static SV *MWParseArticle(MWPARSER *parser) 
    {
        MWParserParseArticle(parser, NULL);
        return MWParserGetResult(parser);
    }

    static void freeScriptVar(void *v)
    {
        SV *sv = v;
        SvREFCNT_dec(sv);
    }

    static MWPARSER_INPUT_STREAM * new_MWParserInput(SV *string)
    {
        return MWParserOpenStringWithCleanup("input", SvPVX(string), SvCUR(string), MWPARSER_UTF8, string, freeScriptVar);
    }

    static int addKey(MWLCKEY *key, void *data)
    {
        AV *av = data;
        SV *sv = newSV(0);
        SWIG_MakePtr(sv, SWIG_as_voidptr(key), SWIGTYPE_p_MWLCKEY, 0);
        av_push(av, sv);
        return 0;
    }

    static SV* MWLinkCollectionGet(MWLINKCOLLECTION *linkCollection)
    {
        AV *av = newAV();
        if (av == NULL) {
            return NULL;
        }

        MWLinkCollectionTraverse(linkCollection, addKey, av);

        SV *rv = newRV_noinc((SV*) av);
        if (rv == NULL) {
            av_undef(av);
            return NULL;
        }
        return rv;
    }
%}

%typemap(out) SV * sv {
    $result = $1;
}

%typemap(in) MWLINKCOLLECTION * linkCollection {
    if (!SvOK($input)) {
        SWIG_exception_fail(SWIG_ArgError(SWIG_ValueError), "Link collection argument undefined.");
    }
    if (!SvROK($input)) {
        SWIG_exception_fail(SWIG_ArgError(SWIG_ValueError), "Link collection argument not a reference.");
    }
    
    $1 = INT2PTR(MWLINKCOLLECTION *, SvIV(SvRV($input)));
}

#elif defined (SWIGPHP)
%{
    static zval *MWParseArticle(MWPARSER *parser) 
    {
        MWParserParseArticle(parser, NULL);
        return MWParserGetResult(parser);
    }

    static void freeScriptVar(void *v)
    {
        zval *z = v;
        Z_DELREF_P(z);
    }

    static MWPARSER_INPUT_STREAM * new_MWParserInput(zval *string)
    {
        return MWParserOpenStringWithCleanup("input", string->value.str.val, string->value.str.len, MWPARSER_UTF8, string, freeScriptVar);
    }

    static int addKey(MWLCKEY *key, void *data)
    {
        zval *av = data;
        zval *sv;
        MAKE_STD_ZVAL(sv);

        SWIG_SetPointerZval(sv, (void *) key, SWIGTYPE_p_MWLCKEY, 0);

        add_next_index_zval(av, sv);
        return 0;
    }

    static zval* MWLinkCollectionGet(MWLINKCOLLECTION *linkCollection)
    {
        zval *av;
        MAKE_STD_ZVAL(av);
        if (av == NULL) {
            return NULL;
        }
        array_init(av);

        MWLinkCollectionTraverse(linkCollection, addKey, av);

        return av;
    }

%}

%typemap(out) zval * {
    *$result = *$1;
}

%typemap(in) (zval *string) {
    if ((*$input)->type != IS_STRING) {
        SWIG_PHP_Error(E_ERROR ,"Expected string value for parameter." );
    }
    Z_ADDREF_P(*$input);
    $1 = *$input;
}

%typemap(in) MWLINKRESOLUTION *resolution {
    Z_ADDREF_P(*$input);
    if ((SWIG_ConvertPtr(*$input,(void **) &$1, $1_descriptor,0)) == -1) {
        SWIG_PHP_Error(E_ERROR, "Invalid class for link resolution pointer.");
    }
    $1->free = freeScriptVar;
    $1->freeData = $input;
    
}

%typemap(in) MWLINKCOLLECTION * linkCollection {
    $1 = (long) Z_LVAL_P(*$input);
}

#endif

extern MWLCKEY * getMWLCKey(void);


%{
    static MWPARSER *new_MWParser(MWPARSER_INPUT_STREAM *inputStream) 
    {
        return MWParserNew(&mwScriptBufferListener, inputStream);
    }

    static void delete_MWParser(MWPARSER *parser)
    {
        MWParserFree(parser);
    }

    static void delete_MWParserInput(MWPARSER_INPUT_STREAM *inputStream)
    {
        MWParserCloseInputStream(inputStream);
    }
%}

extern MWPARSER *MWParserNew(const MWLISTENER *listener, MWPARSER_INPUT_STREAM *inputStream);

static MWPARSER *new_MWParser(MWPARSER_INPUT_STREAM *inputStream);

static void delete_MWParser(MWPARSER *parser);

static void delete_MWParserInput(MWPARSER_INPUT_STREAM *inputStream);

extern void MWParserReset(MWPARSER *parser, MWPARSER_INPUT_STREAM *inputStream);

extern bool MWParserSetLegalTitleRegexp(MWPARSER *parser, const char *perlRegexp);

extern bool MWParserSetMediaLinkTitleRegexp(MWPARSER *parser, const char *perlRegexp);

extern void MWLinkCollectionResolve(MWLINKCOLLECTION *linkCollection,
                                    MWLCKEY *key,
                                    MWLINKRESOLUTION *resolution);


typedef struct MWPARSER_TAGEXT_struct {
    char * name;
    bool isBlock;
}
    MWPARSER_TAGEXT;

extern bool MWParserRegisterTagExtension(MWPARSER *parser, const MWPARSER_TAGEXT *tagExtension);

#ifdef SWIGPERL
static SV *MWParseArticle(MWPARSER *parser);
static SV *MWLinkCollectionGet(MWLINKCOLLECTION *linkCollection);
static MWPARSER_INPUT_STREAM * new_MWParserInput(SV *string);
#elif defined(SWIGPHP)
static zval *MWParseArticle(MWPARSER *parser);
static MWPARSER_INPUT_STREAM * new_MWParserInput(zval *string);
static zval* MWLinkCollectionGet(MWLINKCOLLECTION *linkCollection);
#endif

typedef enum MWLINKTYPE { MWLT_INTERNAL, MWLT_EXTERNAL, MWLT_MEDIA, MWLT_LINKATTR } MWLINKTYPE;

extern const char *MWLCKeyGetLinkTitle(MWLCKEY *lckey);
extern MWLINKTYPE  MWLCKeyGetLinkType(MWLCKEY *lckey);

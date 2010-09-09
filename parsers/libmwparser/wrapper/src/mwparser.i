%module mwp

%{
#include <antlr3.h>
#include <mwparser.h>
#include <mwlistener.h>
extern MWLISTENER mwScriptBufferListener;
static MWPARSER_OPTIONS *null_options = NULL;
%}

#ifdef SWIGPERL
%typemap(in) SV *string {
    SvREFCNT_inc($input);
    $1 = $input;
}

%{
    static SV *MWParseArticle(MWPARSER *parser) 
    {
        MWParserParseArticle(parser, NULL);
        return MWParserGetResult(parser);
    }

    static void freeString(void *string) {
        SV *sv = string;
        SvREFCNT_dec(sv);
    }

    static MWPARSER_INPUT_STREAM * new_MWParserInput(SV *string)
    {
        return MWParserOpenStringWithCleanup("input", SvPVX(string), SvCUR(string), MWPARSER_UTF8, string, freeString);
    }
%}

%typemap(out) SV * sv {
    $result = $1;
}
#elif defined (SWIGPHP)
%{
    static zval *MWParseArticle(MWPARSER *parser) 
    {
        MWParserParseArticle(parser, NULL);
        return MWParserGetResult(parser);
    }
    static void freeString(void *string) {
        zval *z = string;
        Z_DELREF_P(z);
    }

    static MWPARSER_INPUT_STREAM * new_MWParserInput(zval *string)
    {
        return MWParserOpenStringWithCleanup("input", string->value.str.val, string->value.str.len, MWPARSER_UTF8, string, freeString);
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
#endif

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

typedef struct MWPARSER_TAGEXT_struct {
    char * name;
    bool isBlock;
}
    MWPARSER_TAGEXT;

extern bool MWParserRegisterTagExtension(MWPARSER *parser, const MWPARSER_TAGEXT *tagExtension);

#ifdef SWIGPERL
static SV *MWParseArticle(MWPARSER *parser);
static MWPARSER_INPUT_STREAM * new_MWParserInput(SV *string);
#elif defined(SWIGPHP)
static zval *MWParseArticle(MWPARSER *parser);
static MWPARSER_INPUT_STREAM * new_MWParserInput(zval *string);
#endif


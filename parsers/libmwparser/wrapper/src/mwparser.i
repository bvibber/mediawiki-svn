%module mwp

%{
#include <antlr3.h>
#include <mwparser.h>
#include <mwlistener.h>
extern MWLISTENER mwScriptBufferListener;
static MWPARSER_OPTIONS *null_options = NULL;
%}

%{
    static MWPARSER *new_MWPARSER(MWPARSER_INPUT_STREAM *inputStream) 
    {
        return MWParserNew(&mwScriptBufferListener, inputStream);
    }

    static void delete_MWPARSER(MWPARSER *parser)
    {
        MWParserFree(parser);
    }
%}

#ifdef SWIGPERL
%{
    static SV *MWParseArticle(MWPARSER *parser) 
    {
        MWParserParseArticle(parser, NULL);
        return MWParserGetResult(parser);
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
%}

%typemap(out) zval * {
    *$result = *$1;
}
#endif


#ifdef SWIGPHP
%typemap(in) (char *string, size_t size) {
    if ((*$input)->type != IS_STRING) {
        SWIG_PHP_Error(E_ERROR ,"Expected string value for parameter." );
    }
    $1 = (*$input)->value.str.val;
    $2 = (*$input)->value.str.len;
    Z_ADDREF_P(*$input);
}
#endif

%apply (char *STRING, size_t LENGTH) { (char *string, size_t size) };

typedef enum MWPARSER_ENCODING {
    MWPARSER_8BIT,
    MWPARSER_UTF8,
    MWPARSER_UTF16,
    MWPARSER_UTF16LE,
    MWPARSER_UTF16BE,
    MWPARSER_UTF32,
    MWPARSER_UTF32LE,
    MWPARSER_UTF32BE,
} MWPARSER_ENCODING;

extern MWPARSER *MWParserNew(const MWLISTENER *listener, MWPARSER_INPUT_STREAM *inputStream);

static MWPARSER *new_MWPARSER(MWPARSER_INPUT_STREAM *inputStream);

static void delete_MWPARSER(MWPARSER *parser);

extern void MWParserReset(MWPARSER *parser, MWPARSER_INPUT_STREAM *inputStream);

extern void MWParserFree(MWPARSER *parser);

extern MWPARSER_INPUT_STREAM * MWParserOpenString(char *name, char *string, size_t size, MWPARSER_ENCODING encoding);

extern MWPARSER_INPUT_STREAM * MWParserOpenFile(char *fileName, MWPARSER_ENCODING encoding);

extern void MWParserCloseInputStream(MWPARSER_INPUT_STREAM *stream);

#ifdef SWIGPERL
static SV *MWParseArticle(MWPARSER *parser);
#elif defined(SWIGPHP)
static zval *MWParseArticle(MWPARSER *parser);
#endif


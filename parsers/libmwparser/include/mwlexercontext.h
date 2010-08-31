#ifndef MWLEXERCONTEXT_H_
#define MWLEXERCONTEXT_H_

#include <stdbool.h>
#include <wchar.h>
#include <tre/regex.h>
#include <antlr3defs.h>
#include <iconv.h>

/*
 * Different table types can be nested, but not mixed.
 */
typedef enum {
    TCT_NONE,
    TCT_HTML,
    TCT_WIKITEXT,
}
    TABLE_CONTEXT_TYPE;
 

typedef enum {
    LCT_NONE,
    LCT_UL,
    LCT_OL,
    LCT_DL,
}
    LIST_CONTEXT_TYPE;

typedef struct MWLEXERCONTEXT_BACKUP_struct {
#include "mwlexerpredicatedefs.inc"
    pANTLR3_VECTOR blockContextStack;
    
}
    MWLEXERCONTEXT_BACKUP;

typedef struct MWLEXERSPECULATION_struct {
    bool active;
    MWLEXERCONTEXT_BACKUP contextBackup;
    ANTLR3_MARKER istreamMark;
    ANTLR3_MARKER failurePoint;
    ANTLR3_TOKEN_STREAM_MARKER tstreamMark;
    int istreamIndex;
}
    MWLEXERSPECULATION;

typedef struct MWLEXERCONTEXT_struct
{
    pANTLR3_LEXER lexer;
    /*
     * Lexer predicates.
     */

#include "mwlexerpredicatedefs.inc"

    /*
     * Lexer state.
     */
    pANTLR3_STACK blockContextStack;
    int headingLevel;
    regex_t legalTitleChars;
    regex_t mediaLinkTitle;

    /*
     * State for speculative execution.
     */
    MWLEXERSPECULATION indentSpeculation;
    MWLEXERSPECULATION headingSpeculation;
    MWLEXERSPECULATION internalLinkSpeculation;
    MWLEXERSPECULATION externalLinkSpeculation;
    MWLEXERSPECULATION mediaLinkSpeculation;
    int istreamIndex;

    /*
     * Character conversion.
     */

    iconv_t conversionState;

    /** Method for deallocating this instance. */
    void (*free)(void * context);
    /** Reset instance */
    bool (*reset)(struct MWLEXERCONTEXT_struct * context);

    /*
     * Utility.
     */
    pANTLR3_VECTOR_FACTORY vectorFactory;
    pANTLR3_STRING_FACTORY stringFactory;
    bool (*isLegalTitle)(struct MWLEXERCONTEXT_struct * context, pANTLR3_STRING text);
    bool (*isMediaLinkTitle)(struct MWLEXERCONTEXT_struct * context, pANTLR3_STRING text);


}
    MWLEXERCONTEXT;

MWLEXERCONTEXT *MWLexerContextNew(pANTLR3_LEXER);

#endif

#include <antlr3.h>
#include <mwlexercontext.h>
#include <assert.h>

#include <mwLexer.h>
#include "mwlexerpredicates.h"

/*
 * These instances are used for storing in a stack.
 */
static TABLE_CONTEXT_TYPE TCT_NONE_CONST = TCT_NONE;
static TABLE_CONTEXT_TYPE TCT_HTML_CONST = TCT_HTML;
static TABLE_CONTEXT_TYPE TCT_WIKITEXT_CONST = TCT_WIKITEXT;

static LIST_CONTEXT_TYPE LCT_NONE_CONST = LCT_NONE;
static LIST_CONTEXT_TYPE LCT_UL_CONST = LCT_UL;
static LIST_CONTEXT_TYPE LCT_OL_CONST = LCT_OL;
static LIST_CONTEXT_TYPE LCT_DL_CONST = LCT_DL;


static void MWLexerContextFree(void *lexerContext);
static bool MWLexerContextReset(MWLEXERCONTEXT *context);

static bool isLegalTitle(MWLEXERCONTEXT *context, pANTLR3_STRING linkTitle);
static bool isLegalExternalLink(MWLEXERCONTEXT *context, pANTLR3_STRING url);

/**
 * Set the characters allowed in a page title.
 *
 * Default is L" %!\"$&'()*,\\-.\\/0-9:;=?@A-Z\\\\^_`a-z~\\x80-\\xFF+"
 * 
 */
static int setLegalTitleChars(MWLEXERCONTEXT *context, const wchar_t *posixExtendedRegexp);




MWLEXERCONTEXT *MWLexerContextNew(pANTLR3_LEXER lexer)
{
    MWLEXERCONTEXT *context = ANTLR3_MALLOC(sizeof(*context));
    if (context == NULL) {
        return NULL;
    }
    context->free = MWLexerContextFree;
    context->reset = MWLexerContextReset;
    context->lexer = lexer;
    lexer->super = context;

    /*
     * The legal title chars must be reconfigurable, so we treat them
     * specially.
     */
    int err = regwcomp(&context->legalTitleChars,
                       L"^[- %!\"$&'()*,.\\/0-9:;=?@A-Z\\\\^_`a-z~\\x80-\\xFF+]+$",
                       REG_EXTENDED);
    if (err) {
        char errbuf[200];
        regerror(err, &context->legalTitleChars, errbuf, 200);
        fprintf(stderr, "Failed to compile legal title chars regular expression: %s\n", errbuf);
        context->free(context);
        return NULL;
    }

#define NULL_FAIL(p) do {                       \
    if (p == NULL) {                            \
        context->free(context);                 \
        return NULL;                            \
    }                                           \
} while (0)

    context->vectorFactory                = NULL;
    context->stringFactory                = NULL;
    context->blockContextStack             = NULL;
    context->indentSpeculation.contextBackup.blockContextStack       = NULL;
    context->internalLinkSpeculation.contextBackup.blockContextStack = NULL;
    context->externalLinkSpeculation.contextBackup.blockContextStack = NULL;
    context->headingSpeculation.contextBackup.blockContextStack      = NULL;
    context->mediaLinkSpeculation.contextBackup.blockContextStack    = NULL;

    context->vectorFactory = antlr3VectorFactoryNew(ANTLR3_SIZE_HINT);
    NULL_FAIL(context->vectorFactory);

    context->blockContextStack            = antlr3StackNew(ANTLR3_SIZE_HINT);
    NULL_FAIL(context->blockContextStack);

    context->indentSpeculation.contextBackup.blockContextStack       = antlr3VectorNew(ANTLR3_SIZE_HINT);
    NULL_FAIL(context->indentSpeculation.contextBackup.blockContextStack);
    context->internalLinkSpeculation.contextBackup.blockContextStack = antlr3VectorNew(ANTLR3_SIZE_HINT);
    NULL_FAIL(context->internalLinkSpeculation.contextBackup.blockContextStack);
    context->externalLinkSpeculation.contextBackup.blockContextStack = antlr3VectorNew(ANTLR3_SIZE_HINT);
    NULL_FAIL(context->externalLinkSpeculation.contextBackup.blockContextStack);
    context->headingSpeculation.contextBackup.blockContextStack      = antlr3VectorNew(ANTLR3_SIZE_HINT);
    NULL_FAIL(context->headingSpeculation.contextBackup.blockContextStack);
    context->mediaLinkSpeculation.contextBackup.blockContextStack    = antlr3VectorNew(ANTLR3_SIZE_HINT);
    NULL_FAIL(context->mediaLinkSpeculation.contextBackup.blockContextStack);

    context->isLegalTitle                 = isLegalTitle;
    context->isLegalExternalLink          = isLegalExternalLink;

    if (!context->reset(context)) {
        context->free(context);
        return NULL;
    }

    return context;
}

static bool
MWLexerContextReset(MWLEXERCONTEXT *context)
{
    context->headingLevel                   = 0;
    context->indentSpeculation.active       = false;
    context->internalLinkSpeculation.active = false;
    context->externalLinkSpeculation.active = false;
    context->headingSpeculation.active      = false;
    context->mediaLinkSpeculation.active    = false;
    context->istreamIndex                   = 0;
    context->indentSpeculation.contextBackup.blockContextStack->count       =  0;
    context->indentSpeculation.failurePoint                                 = -1;
    context->internalLinkSpeculation.contextBackup.blockContextStack->count =  0;
    context->internalLinkSpeculation.failurePoint                           = -1;
    context->externalLinkSpeculation.contextBackup.blockContextStack->count =  0;
    context->externalLinkSpeculation.failurePoint                           = -1;
    context->headingSpeculation.contextBackup.blockContextStack->count      =  0;
    context->headingSpeculation.failurePoint                                = -1;
    context->mediaLinkSpeculation.contextBackup.blockContextStack->count    =  0;
    context->mediaLinkSpeculation.failurePoint                              = -1;

    mwlexerpredicatesReset(context);

    pANTLR3_STACK s = context->blockContextStack;
    while (s->size(s) > 0) {
        s->pop(s);
    }

    if (context->vectorFactory != NULL) {
        context->vectorFactory->close(context->vectorFactory);
    }
    context->vectorFactory = antlr3VectorFactoryNew(ANTLR3_SIZE_HINT);
    if (context->vectorFactory == NULL) {
        return false;
    }

    if (context->stringFactory != NULL) {
        context->stringFactory->close(context->stringFactory);
    }
    context->stringFactory = antlr3StringFactoryNew(ANTLR3_ENC_8BIT);
    if (context->stringFactory == NULL) {
        return false;
    }

    return true;
}

static void
MWLexerContextFree(void *lexerContext)
{
    MWLEXERCONTEXT *context = lexerContext;

    if (context->stringFactory != NULL) {
        context->stringFactory->close(context->stringFactory);
    }
    if (context->vectorFactory != NULL) {
        context->vectorFactory->close(context->vectorFactory);
    }
    if (context->blockContextStack != NULL) {
        context->blockContextStack->free(context->blockContextStack);
    }
    if(context->indentSpeculation.contextBackup.blockContextStack != NULL) {
        context->indentSpeculation.contextBackup.blockContextStack
            ->free(context->indentSpeculation.contextBackup.blockContextStack);
    }
    if(context->internalLinkSpeculation.contextBackup.blockContextStack != NULL) {
        context->internalLinkSpeculation.contextBackup.blockContextStack
            ->free(context->internalLinkSpeculation.contextBackup.blockContextStack);
    }
    if(context->externalLinkSpeculation.contextBackup.blockContextStack != NULL) {
        context->externalLinkSpeculation.contextBackup.blockContextStack
            ->free(context->externalLinkSpeculation.contextBackup.blockContextStack);
    }
    if(context->headingSpeculation.contextBackup.blockContextStack != NULL) {
        context->headingSpeculation.contextBackup.blockContextStack
            ->free(context->headingSpeculation.contextBackup.blockContextStack);
    }
    if(context->mediaLinkSpeculation.contextBackup.blockContextStack != NULL) {
        context->mediaLinkSpeculation.contextBackup.blockContextStack
            ->free(context->mediaLinkSpeculation.contextBackup.blockContextStack);
    }
    
    regfree(&context->legalTitleChars);
    ANTLR3_FREE(lexerContext);
}

static bool
isLegalTitle(MWLEXERCONTEXT *context, pANTLR3_STRING linkTitle)
{
    void *state;
    const wchar_t *wsLinkTitle = mwAntlr3stows(linkTitle, &state);
    regmatch_t match;
    int err = regwexec(&context->legalTitleChars, wsLinkTitle, 1, &match, 0);
    mwFreeStringConversionState(state);
    char buf[256];
    regerror(err, &context->legalTitleChars, buf, 256);
    //printf("result was: %d, message: %s, string: '%ls'\n", err, buf, linkTitle->chars);
    return true;
}

static bool
isLegalExternalLink(MWLEXERCONTEXT *context, pANTLR3_STRING linkTitle)
{
    return true;
}

void printLexerInfo(pANTLR3_LEXER lexer)
{
    MWLEXERCONTEXT *context = lexer->super;
    fprintf(stderr, "type: %d, position in line: %d, matched text \"%s\" next char '%c' backtracking %d char index %d \n",
            lexer->rec->state->type,
            lexer->input->charPositionInLine,
            lexer->getText(lexer)->chars,
            lexer->input->istream->_LA(lexer->input->istream, 1),
            lexer->rec->state->backtracking,
            lexer->getCharIndex(lexer));
}


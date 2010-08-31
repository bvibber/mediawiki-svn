/*
 * Copyright 2010  Andreas Jonsson
 *
 * This file is part of libmwparser. 
 * 
 * Libmwparser is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

#include <antlr3.h>
#include <mwlexercontext.h>
#include <assert.h>
#include <iconv.h>
#include <errno.h>

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
static bool isMediaLinkTitle(MWLEXERCONTEXT *context, pANTLR3_STRING url);

static int openConversion(MWLEXERCONTEXT *context, ANTLR3_UINT8 encoding);
static const wchar_t *mwAntlr3stows(MWLEXERCONTEXT *context, pANTLR3_STRING string, void **state);
static void  mwFreeStringConversionState(void *state);

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
                       L"^[- %!\"$&'()*,./0-9:;=?@A-Z\\\\^_`a-z~\x80-\xFF+]+$",
                       REG_EXTENDED);
    if (err) {
        char errbuf[200];
        regerror(err, &context->legalTitleChars, errbuf, 200);
        fprintf(stderr, "Failed to compile legal title chars regular expression: %s\n", errbuf);
        context->free(context);
        return NULL;
    }

    err     = regwcomp(&context->mediaLinkTitle,
                       L"^File:[- %!\"$&'()*,./0-9:;=?@A-Z\\\\^_`a-z~\x80-\xFF+]+$",
                       REG_EXTENDED);
    if (err) {
        char errbuf[200];
        regerror(err, &context->mediaLinkTitle, errbuf, 200);
        fprintf(stderr, "Failed to compile media link title regular expression: %s\n", errbuf);
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

    context->conversionState = (iconv_t)-1;

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
    context->isMediaLinkTitle             = isMediaLinkTitle;

    if (!context->reset(context)) {
        context->free(context);
        return NULL;
    }

    if (openConversion(context, context->lexer->input->encoding) < 0) {
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
    if (context->conversionState != (iconv_t)-1) {
        iconv_close(context->conversionState);
    }
    
    regfree(&context->legalTitleChars);
    regfree(&context->mediaLinkTitle);
    ANTLR3_FREE(lexerContext);
}

static bool
isLegalTitle(MWLEXERCONTEXT *context, pANTLR3_STRING linkTitle)
{
    void *state;
    const wchar_t *wsLinkTitle = mwAntlr3stows(context, linkTitle, &state);
    regmatch_t match;
    int err = regwexec(&context->legalTitleChars, wsLinkTitle, 1, &match, 0);
    mwFreeStringConversionState(state);
    return err == 0;
}

static bool
isMediaLinkTitle(MWLEXERCONTEXT *context, pANTLR3_STRING linkTitle)
{
    void *state;
    const wchar_t *wsLinkTitle = mwAntlr3stows(context, linkTitle, &state);
    regmatch_t match;
    int err = regwexec(&context->mediaLinkTitle, wsLinkTitle, 1, &match, 0);
    mwFreeStringConversionState(state);
    return err == 0;
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


static int
openConversion(MWLEXERCONTEXT *context, ANTLR3_UINT8 encoding)
{
    static struct {
        ANTLR3_UINT8 antlrEncoding;
        const char* iconvEncoding;
    } encodingTable[] = {
        { ANTLR3_ENC_8BIT,    "ASCII"      },
        { ANTLR3_ENC_UTF8,    "UTF-8"      },
        { ANTLR3_ENC_UTF16,   "UTF-16"     },
        { ANTLR3_ENC_UTF16BE, "UTF-16BE"   },
        { ANTLR3_ENC_UTF16LE, "UTF-16LE"   },
        { ANTLR3_ENC_UTF32,   "UTF-32"     },
        { ANTLR3_ENC_UTF32BE, "UTF-32BE"   },
        { ANTLR3_ENC_UTF32LE, "UTF-32LE"   },
        { ANTLR3_ENC_EBCDIC,  "EBCDIC-INT" },
        { 0                ,   NULL        }
    };

    int i;
    for (i = 0; encodingTable[i].iconvEncoding != NULL; i++) {
        if (encodingTable[i].antlrEncoding == encoding) {
            break;
        }
    }
    if (encodingTable[i].iconvEncoding == NULL) {
        errno = EINVAL;
        return -1;
    }
#if (SIZEOF_WCHAR_T == 4)
#ifdef WORDS_BIGENDIAN
    context->conversionState = iconv_open("UTF-32BE", encodingTable[i].iconvEncoding);
#else
    context->conversionState = iconv_open("UTF-32LE", encodingTable[i].iconvEncoding);
#endif
#elif (SIZEOF_WCHAR_T == 2)
#ifdef WORDS_BIGENDIAN
    context->conversionState = iconv_open("UTF-16BE", encodingTable[i].iconvEncoding);
#else
    context->conversionState = iconv_open("UTF-16LE", encodingTable[i].iconvEncoding);
#endif
#else
#error Unsupported size of wchar_t!
#endif
    if (context->conversionState == (iconv_t)-1) {
        return -1;
    }
}

static size_t
convertString(MWLEXERCONTEXT *context, ANTLR3_STRING *string, void *buf, size_t bufSize) {
    size_t outBytesLeft = bufSize;
    size_t inBytesLeft = string->size;
    char *inBuf = string->chars;
    char *outBuf = buf;
    
    size_t ret = iconv(context->conversionState, NULL, NULL, NULL, NULL);

    ret = iconv(context->conversionState,  &inBuf, &inBytesLeft, &outBuf, &outBytesLeft);

    return ret;
}


static const wchar_t *
mwAntlr3stows(MWLEXERCONTEXT *context, pANTLR3_STRING string, void **state)
{
    size_t bufSize = (string->len + 1) * sizeof(wchar_t);
    wchar_t *buf = ANTLR3_MALLOC(bufSize);

    size_t ret = convertString(context, string, buf, bufSize);

    if (ret == (size_t)-1) {
        ANTLR3_FREE(buf);
        perror(NULL);
        return NULL;
    }

    *state = buf;
    
    return buf;
}

static void 
mwFreeStringConversionState(void *state)
{
    ANTLR3_FREE(state);
}

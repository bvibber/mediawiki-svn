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

#include "mwWikitextLexer.h"
#include "mwlexerpredicates.h"

static void MWLexerContextFree(void *lexerContext);
static bool MWLexerContextReset(MWLEXERCONTEXT *context);

static bool isLegalTitle(MWLEXERCONTEXT *context, pANTLR3_STRING linkTitle);
static bool isMediaLinkTitle(MWLEXERCONTEXT *context, pANTLR3_STRING url);
static MWPARSER_TAGEXT * getTagExtension(MWLEXERCONTEXT *context, pANTLR3_STRING name);
static bool registerTagExtension(struct MWLEXERCONTEXT_struct * context, const MWPARSER_TAGEXT *tagExt);

static int openConversion(MWLEXERCONTEXT *context, ANTLR3_UINT8 encoding);
static const wchar_t *mwAntlr3stows(MWLEXERCONTEXT *context, pANTLR3_STRING string, void **state);
static void  mwFreeStringConversionState(void *state);
static bool setLegalTitleRegexp(MWLEXERCONTEXT *context, const char *perlRegexp);
static bool setMediaLinkTitleRegexp(MWLEXERCONTEXT *context, const char *perlRegexp);

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
    context->legalTitleRegexp = NULL;
    context->mediaLinkTitle = NULL;

    /*
     * The legal title chars must be reconfigurable, so we treat them
     * specially.
     */

    GError *err = NULL;
    context->legalTitleRegexp = g_regex_new("^[- %!\"$&'()*,./0-9:;=?@A-Z\\\\^_`a-z~\x80-\u00FF+]+$", 0, 0, &err);
    if (err) {
        fprintf(stderr, "Failed to compile media link title regular expression: %s\n", err->message);
        g_error_free(err);
        context->free(context);
        return NULL;
    }

    context->mediaLinkTitle = g_regex_new("^File:[- %!\"$&'()*,./0-9:;=?@A-Z\\\\^_`a-z~\x80-\u00FF+]+$", 0, 0, &err);
    if (err) {
        fprintf(stderr, "Failed to compile media link title regular expression: %s\n", err->message);
        g_error_free(err);
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
    context->asciiStringFactory           = NULL;
    context->blockContextStack            = NULL;
    context->tagExtensionTable            = NULL;
    context->indentSpeculation.contextBackup.blockContextStack       = NULL;
    context->internalLinkSpeculation.contextBackup.blockContextStack = NULL;
    context->externalLinkSpeculation.contextBackup.blockContextStack = NULL;
    context->headingSpeculation.contextBackup.blockContextStack      = NULL;
    context->mediaLinkSpeculation[0].contextBackup.blockContextStack = NULL;
    context->mediaLinkSpeculation[1].contextBackup.blockContextStack = NULL;

    context->conversionState = (iconv_t)-1;

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
    context->mediaLinkSpeculation[0].contextBackup.blockContextStack = antlr3VectorNew(ANTLR3_SIZE_HINT);
    NULL_FAIL(context->mediaLinkSpeculation[0].contextBackup.blockContextStack);
    context->mediaLinkSpeculation[1].contextBackup.blockContextStack = antlr3VectorNew(ANTLR3_SIZE_HINT);
    NULL_FAIL(context->mediaLinkSpeculation[1].contextBackup.blockContextStack);
    context->tagExtensionTable                                       = antlr3HashTableNew(ANTLR3_SIZE_HINT);
    NULL_FAIL(context->tagExtensionTable);

    context->isLegalTitle                 = isLegalTitle;
    context->isMediaLinkTitle             = isMediaLinkTitle;
    context->getTagExtension              = getTagExtension;
    context->registerTagExtension         = registerTagExtension;
    context->setLegalTitleRegexp          = setLegalTitleRegexp;
    context->setMediaLinkTitleRegexp      = setMediaLinkTitleRegexp;

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
    context->mediaLinkSpeculation[0].active = false;
    context->mediaLinkSpeculation[1].active = false;
    context->istreamIndex                   = 0;
    context->indentSpeculation.contextBackup.blockContextStack->count       =  0;
    context->indentSpeculation.failurePoint                                 = -1;
    context->internalLinkSpeculation.contextBackup.blockContextStack->count =  0;
    context->internalLinkSpeculation.failurePoint                           = -1;
    context->externalLinkSpeculation.contextBackup.blockContextStack->count =  0;
    context->externalLinkSpeculation.failurePoint                           = -1;
    context->headingSpeculation.contextBackup.blockContextStack->count      =  0;
    context->headingSpeculation.failurePoint                                = -1;
    context->mediaLinkSpeculation[0].contextBackup.blockContextStack->count =  0;
    context->mediaLinkSpeculation[0].failurePoint                           = -1;
    context->mediaLinkSpeculation[1].contextBackup.blockContextStack->count =  0;
    context->mediaLinkSpeculation[1].failurePoint                           = -1;

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

    if (context->asciiStringFactory != NULL) {
        context->asciiStringFactory->close(context->asciiStringFactory);
    }
    context->asciiStringFactory = antlr3StringFactoryNew(ANTLR3_ENC_8BIT);
    if (context->asciiStringFactory == NULL) {
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
    if (context->asciiStringFactory != NULL) {
        context->asciiStringFactory->close(context->asciiStringFactory);
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
    if(context->mediaLinkSpeculation[0].contextBackup.blockContextStack != NULL) {
        context->mediaLinkSpeculation[0].contextBackup.blockContextStack
            ->free(context->mediaLinkSpeculation[0].contextBackup.blockContextStack);
    }
    if(context->mediaLinkSpeculation[1].contextBackup.blockContextStack != NULL) {
        context->mediaLinkSpeculation[1].contextBackup.blockContextStack
            ->free(context->mediaLinkSpeculation[1].contextBackup.blockContextStack);
    }
    if (context->conversionState != (iconv_t)-1) {
        iconv_close(context->conversionState);
    }
    if (context->tagExtensionTable != NULL) {
        context->tagExtensionTable->free(context->tagExtensionTable);
    }
    if (context->legalTitleRegexp != NULL) {
        g_regex_unref(context->legalTitleRegexp);
    }
    if (context->mediaLinkTitle != NULL) {
        g_regex_unref(context->mediaLinkTitle);
    }

    pmwWikitextLexer lxr = context->lexer->ctx;
    lxr->free(lxr);

    ANTLR3_FREE(lexerContext);
}

static bool
isLegalTitle(MWLEXERCONTEXT *context, pANTLR3_STRING linkTitle)
{
    pANTLR3_STRING utf8 = linkTitle->toUTF8(linkTitle);
    return g_regex_match(context->legalTitleRegexp, utf8->chars, 0, NULL);
}

static bool
isMediaLinkTitle(MWLEXERCONTEXT *context, pANTLR3_STRING linkTitle)
{
    pANTLR3_STRING utf8 = linkTitle->toUTF8(linkTitle);
    return g_regex_match(context->mediaLinkTitle, utf8->chars, 0, NULL);
}

/*
 * We need to use ascii encoded strings as name of tag extensions, so
 * they can be used as keys for the hash table.  Also, the tag names
 * should be case insensitive.
 */
static pANTLR3_STRING
lowerCaseAsciiString(MWLEXERCONTEXT *context, pANTLR3_STRING string)
{
    pANTLR3_STRING string8 = string->to8(string);
    pANTLR3_STRING lstring = context->asciiStringFactory->newSize(context->asciiStringFactory, string8->len + 1);
    int i;
    for (i = 0; i < string8->len; i++) {
        lstring->addc(lstring, tolower(string8->charAt(string8, i)));
    }
    return lstring;
}

static MWPARSER_TAGEXT *
getTagExtension(MWLEXERCONTEXT *context, pANTLR3_STRING name)
{
    pANTLR3_HASH_TABLE t = context->tagExtensionTable;
    return t->get(t, lowerCaseAsciiString(context, name)->chars);
}

static bool
registerTagExtension(MWLEXERCONTEXT *context, const MWPARSER_TAGEXT *tagExt)
{
    pANTLR3_HASH_TABLE t = context->tagExtensionTable;
    pANTLR3_STRING name = context->asciiStringFactory->newPtr8(context->asciiStringFactory, 
                                                               (pANTLR3_UINT8)tagExt->name,
                                                               strlen(tagExt->name) + 1);
    pANTLR3_STRING lname = lowerCaseAsciiString(context, name);
    MWPARSER_TAGEXT *tagExtCopy = MWTagextCopy(tagExt);
    ANTLR3_UINT32 err = t->put(t, lname->chars, tagExtCopy, MWTagextFree);
    if (err != ANTLR3_SUCCESS) {
        return false;
    }
    return true;
}

void printLexerInfo(pANTLR3_LEXER lexer)
{
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
    return 0;
}

static size_t
convertString(MWLEXERCONTEXT *context, ANTLR3_STRING *string, void *buf, size_t bufSize) {
    size_t outBytesLeft = bufSize;
    size_t inBytesLeft = string->size;
    char *inBuf = (char *)string->chars;
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

static bool
setLegalTitleRegexp(MWLEXERCONTEXT *context, const char *regexp)
{
    g_regex_unref(context->legalTitleRegexp);
    GError *err = NULL;
    context->legalTitleRegexp = g_regex_new(regexp, 0, 0, &err);    
    if (err) {
        fprintf(stderr, "Failed to compile link title regular expression: %s\n", err->message);
        g_error_free(err);
        return false;
    }

    return true;
}

static bool
setMediaLinkTitleRegexp(MWLEXERCONTEXT *context, const char *regexp)
{
    g_regex_unref(context->mediaLinkTitle);
    GError *err = NULL;
    context->mediaLinkTitle = g_regex_new(regexp, 0, 0, &err);    
    if (err) {
        fprintf(stderr, "Failed to compile media link title regular expression: %s\n", err->message);
        g_error_free(err);
        return false;
    }

    return true;
}

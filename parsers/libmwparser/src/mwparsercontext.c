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

#include <mwparsercontext.h>
#include <mwbasicevents.h>
#include <mwformats.h>
#include <mwparser.h>
#include <mwtables.h>
#include <mwheadings.h>
#include <mwhtml.h>
#include <assert.h>
#include <mwLexer.h>

/**
 * These values encodes the situation before a sequence of apostrophes.
 *
 * We are interested in the space character only.  Beginning of line
 * counts as two non-space characters here.
 */
typedef enum {
    /** We dont care about this unless the sequence is of length 3 or 4. */
    PA_DONT_CARE,
    /** A space is immediately preceeding the sequence. */
    PA_SPACE,
    /** A space that is really a space character (not tab!) is followed by exactly one non-space character. */
    PA_SPACE_NONSPACE,
    /** Two nonspace characters preceeds the sequence. */
    PA_MULTI_NONSPACE
}
    PRECEEDING_APOSTROPHE;

typedef struct APOSTROPHE_SEQUENCE_struct {
    int sequenceLength;
    PRECEEDING_APOSTROPHE preceeding;
}
    APOSTROPHE_SEQUENCE;

typedef enum {
    AD_APOSTROPHE,
    AD_ITALIC,
    AD_BOLD
}
    APOSTROPHE_DIRECTION;

#define LSTNR (&(context->listener))

static void closeFormats(MWPARSERCONTEXT *context);
static void openFormats(MWPARSERCONTEXT *context);
static void beginInlinePrescan(MWPARSERCONTEXT *context);
static void endInlinePrescan(MWPARSERCONTEXT *context);
static void beginFormat(MWPARSERCONTEXT *context, void (*begin)(), void (*end)(), void *parameter, void *identifier, bool isShortLived);
static void endFormat(MWPARSERCONTEXT *context, void (*begin)(), void (*end)(), void *identifier);
static void onApostrophesPrescan(MWPARSERCONTEXT *context, int length);
static void onInlineTokenPrescan(MWPARSERCONTEXT *context, pANTLR3_COMMON_TOKEN token);
static void onConsumedApostrophes(MWPARSERCONTEXT *context);
static void onListElement(MWPARSERCONTEXT *context, pANTLR3_STRING type);
static void onNonListBlockElement(MWPARSERCONTEXT *context);
static void MWAbstractParserContextFree(void *parserContext);

static void delayCall(struct MWPARSERCONTEXT_struct * context, void (*beginMethod)(), void (*endMethod)(), void *parameter, void *identifier);
static bool shouldSkip(struct MWPARSERCONTEXT_struct * context, void (*beginMethod)(), void (*endMethod)(), void *identifier);
static void triggerDelayedCalls(struct MWPARSERCONTEXT_struct * context);

static PRECEEDING_APOSTROPHE resolvePreceedingApostrophe(MWPARSERCONTEXT *context, int len);
static APOSTROPHE_SEQUENCE * checkPotentialVictim(APOSTROPHE_SEQUENCE *cur, APOSTROPHE_SEQUENCE *candidate);
static void updateListState(MWPARSERCONTEXT *context, pANTLR3_STRING type, int offset);
static void openLists(MWPARSERCONTEXT *context, int offset);
static void closeLists(MWPARSERCONTEXT *context, int offset);

/*
 * Opened formats are stored in tuples that are layed out according to
 * the below configuration.
 */
static const ORDERED_FORMAT_TUPLE_SIZE = 5;
static const DELAYED_CALLS_TUPLE_SIZE  = 4;
static const BEGIN_METHOD_OFFSET = 0;
static const END_METHOD_OFFSET   = 1;
static const PARAMETER_OFFSET    = 2;
static const IDENTIFIER_OFFSET   = 3;
static const IS_INLINE_OFFSET    = 4;

static void
addTuple(pANTLR3_VECTOR v, size_t tupleSize, void *tuple[])
{
    int i;
    for (i = 0; i < tupleSize; i++) {
        v->add(v, tuple[i], NULL);
    }
}

static void
removeTuple(pANTLR3_VECTOR v, size_t tupleSize, void *tuple[], int offset)
{
    int i;
    for (i = tupleSize - 1; i >= 0; i--) {
        tuple[i] = v->remove(v, offset * tupleSize + i);
    }
}

static void
getTuple(pANTLR3_VECTOR v, size_t tupleSize, void *tuple[], int offset)
{
    int i;
    for (i = tupleSize - 1; i >= 0; i--) {
        tuple[i] = v->get(v, offset * tupleSize + i);
    }
}


MWPARSERCONTEXT * MWAbstractParserContextNew(pANTLR3_PARSER parser)
{
    MWPARSERCONTEXT *context = ANTLR3_MALLOC(sizeof(*context));
    context->parser = parser;
    parser->super = context;

    if (context == NULL) {
        return NULL;
    }
    context->free = MWAbstractParserContextFree;

#define NULL_FAIL(p) do {                       \
    if (p == NULL) {                            \
        context->free(context);                 \
        return NULL;                            \
    }                                           \
} while (0)

    context->vectorFactory = antlr3VectorFactoryNew(ANTLR3_SIZE_HINT);
    if (context->vectorFactory == NULL) {
        context->free(context);
        return NULL;
    }

    context->stringFactory = antlr3StringFactoryNew(ANTLR3_ENC_8BIT);
    if (context->stringFactory == NULL) {
        context->free(context);
        return NULL;
    }

    mwBasicEventsInit(context);
    mwFormatsInit(context);
    mwTablesInit(context);
    mwHeadingsInit(context);
    mwLinksInit(context);
    mwHtmlInit(context);

    context->inlinePrescan                = false;
    context->takeBold                     = true;
    context->takeItalic                   = true;
    context->takeApostrophe               = true;

    context->closeFormats                 = closeFormats;
    context->openFormats                  = openFormats;
    context->beginInlinePrescan           = beginInlinePrescan;
    context->endInlinePrescan             = endInlinePrescan;
    context->onApostrophesPrescan         = onApostrophesPrescan;
    context->onInlineTokenPrescan         = onInlineTokenPrescan;
    context->beginFormat                  = beginFormat;
    context->endFormat                    = endFormat;

    context->delayCall                    = delayCall;
    context->shouldSkip                   = shouldSkip;
    context->triggerDelayedCalls          = triggerDelayedCalls;

    context->onConsumedApostrophes        = onConsumedApostrophes;
    context->onListElement                = onListElement;
    context->onNonListBlockElement        = onNonListBlockElement;

    context->apostropheSequences          = NULL;
    context->parseInlineInstruction       = NULL;
    context->prevInlineToken              = NULL;
    context->prevPrevInlineToken          = NULL;
    context->startOfTokenStream           = -1;
    context->listState                    = NULL;
    context->currentInlineInstruction     = 0;
    context->formatOrder                  = context->vectorFactory->newVector(context->vectorFactory);
    NULL_FAIL(context->formatOrder);
    context->delayedCalls                 = context->vectorFactory->newVector(context->vectorFactory);
    NULL_FAIL(context->delayedCalls);
    context->savedOrderedFormats          = context->vectorFactory->newVector(context->vectorFactory);
    NULL_FAIL(context->savedOrderedFormats);
    context->formatResolutionInProgress   = false;
    context->executingDelayedCalls        = false;
    context->haveGeneratedTableOfContents = false;
    context->parseInlineInstruction       = context->vectorFactory->newVector(context->vectorFactory);
    NULL_FAIL(context->parseInlineInstruction);

    return context;
}

static void
MWAbstractParserContextFree(void *parserContext)
{
    MWPARSERCONTEXT *context = parserContext;
    if (context->stringFactory != NULL) {
        context->stringFactory->close(context->stringFactory);
    }
    if (context->vectorFactory != NULL) {
        context->vectorFactory->close(context->vectorFactory);
    }
}

static void
openFormats(MWPARSERCONTEXT *context)
{
    pANTLR3_VECTOR v = context->savedOrderedFormats;
    const int tupleCount = v->count / ORDERED_FORMAT_TUPLE_SIZE;
    int i;
    for (i = 0; i < tupleCount; i++) {
        void *tuple[ORDERED_FORMAT_TUPLE_SIZE];
        getTuple(v, ORDERED_FORMAT_TUPLE_SIZE, tuple, i);
        void (*begin)() = tuple[BEGIN_METHOD_OFFSET];
        void *parameter = tuple[PARAMETER_OFFSET];
        begin(context, parameter);
    }
    v->clear(v);
}

static void
closeFormats(MWPARSERCONTEXT *context)
{
    int i;
    /*
     * Abort delayed calls by calling the end method without setting
     * 'executingDelayedCalls'.
     */
    pANTLR3_VECTOR v = context->delayedCalls;
    while (v->count > 0) {
        void (*end)(MWPARSERCONTEXT *context) = v->get(v, END_METHOD_OFFSET);
        end(context);
    }
    /*
     * Close formats in reverse order.
     */
    v = context->formatOrder;
    const int tupleCount = v->count/ORDERED_FORMAT_TUPLE_SIZE;
    void *tuple[v->count][ORDERED_FORMAT_TUPLE_SIZE];
    for (i = tupleCount - 1; i >= 0; i --) {
        getTuple(v, ORDERED_FORMAT_TUPLE_SIZE, tuple[i], i);
        void (*end)(MWPARSERCONTEXT *context) = tuple[i][END_METHOD_OFFSET];
        end(context);
    }

    /*
     * Save non-inlined formats so they may be opened again later.
     */
    v = context->savedOrderedFormats;
    assert(v->count == 0);
    for (i = 0; i < tupleCount; i++) {
        bool isShortLived = tuple[i][IS_INLINE_OFFSET] != NULL;
        if (!isShortLived) {
            addTuple(v, ORDERED_FORMAT_TUPLE_SIZE, tuple[i]);
        }
    }
}

static void
beginInlinePrescan(MWPARSERCONTEXT *context)
{
    context->inlinePrescan = true;
    context->prevInlineToken = NULL;
    context->prevPrevInlineToken = NULL;
}

/*
 * Static instances for storing pointers in vector.
 */
static APOSTROPHE_DIRECTION AD_ITALIC_CONST = AD_ITALIC;
static APOSTROPHE_DIRECTION AD_BOLD_CONST = AD_BOLD;
static APOSTROPHE_DIRECTION AD_APOSTROPHE_CONST = AD_APOSTROPHE;

static void
endInlinePrescan(MWPARSERCONTEXT *context)
{
    context->inlinePrescan = false;

    if (context->apostropheSequences != NULL) {
        pANTLR3_VECTOR v = context->apostropheSequences;

        int i;

        int two = 0;
        int three = 0;

        APOSTROPHE_SEQUENCE *victim = NULL;

        for (i=0; i < v->count; i++) {
            APOSTROPHE_SEQUENCE *s = v->get(v, i);
            switch (s->sequenceLength) {
            case 1:
                break;
            case 2:
                two++;
                break;
            case 3:
            case 4:
                three++;
                victim = checkPotentialVictim(victim, s);
                break;
            default:
                two++;
                three++;
                break;
            }
            
        }

        if (two % 2 != 1 || three % 2 != 1) {
            victim = NULL;
        }

        pANTLR3_VECTOR pi = context->parseInlineInstruction;
        pi->clear(pi);

        int openingFive = -1;
        int italic = 0;
        int bold = 0;

#define APOSTROPHE pi->add(pi, &AD_APOSTROPHE_CONST, NULL)

#define ITALIC                                                          \
    do {                                                                \
        italic++;                                                       \
        pi->add(pi, &AD_ITALIC_CONST, NULL);                            \
        if (openingFive != -1) {                                        \
            /*                                                          \
             * Swap bold and italic.                                    \
             */                                                         \
            assert(*(APOSTROPHE_DIRECTION*)pi->get(pi, openingFive) == AD_ITALIC && \
                   *(APOSTROPHE_DIRECTION*)pi->get(pi, openingFive + 1) == AD_BOLD); \
            pi->swap(pi, openingFive, openingFive + 1);                 \
            openingFive = -1;                                           \
        }                                                               \
    } while (0)

#define BOLD                                    \
do {                                            \
        bold++;                                 \
        pi->add(pi, &AD_BOLD_CONST, NULL);      \
        openingFive = -1;                       \
} while (0)

        for (i=0; i < v->count; i++) {
            APOSTROPHE_SEQUENCE *s = v->get(v, i);
            switch (s->sequenceLength) {
            case 1:
                APOSTROPHE;
                break;
            case 2:
                ITALIC;
                break;
            case 3:
                if (s == victim) {
                    APOSTROPHE; ITALIC;
                } else {
                    BOLD;
                }
                break;
            case 4:
                if (s == victim) {
                    APOSTROPHE; APOSTROPHE; ITALIC;
                } else {
                    APOSTROPHE; BOLD;
                }
                break;
            default:
                {
                    int j;
                    for (j = 0;j < s->sequenceLength - 5; j++) {
                        APOSTROPHE;
                    }
                    if (italic % 2 == 0 && bold % 2 == 0) {
                        /*
                         * Five (or more) apostrophes opening up new
                         * formattings.  We may need to swap the order
                         * later, so we save the index.
                         */
                        openingFive = pi->count;
                    }
                    pi->add(pi, &AD_ITALIC_CONST, NULL);
                    italic++;
                    pi->add(pi, &AD_BOLD_CONST, NULL);
                    bold++;
                }
                break;
            }
        }

        context->currentInlineInstruction = 0;

        APOSTROPHE_DIRECTION ad = *(APOSTROPHE_DIRECTION*)pi->get(pi, 0);

        context->takeApostrophe = ad == AD_APOSTROPHE;
        context->takeItalic = ad == AD_ITALIC;
        context->takeBold = ad == AD_BOLD;

        context->apostropheSequences->free(context->apostropheSequences);
        context->apostropheSequences = NULL;
    }
}

#undef APOSTROPHE
#undef ITALIC
#undef BOLD

/**
 * This method will be called for each sequence (of length 1 or
 * longer) of apostrophes found while prescanning inline contents.
 *
 * @param context
 * @param apostrophes The sequence of apostrophes.
 */
static void
onApostrophesPrescan(MWPARSERCONTEXT *context, int length)
{
    if (context->apostropheSequences == NULL) {
        context->apostropheSequences = context->vectorFactory->newVector(context->vectorFactory);
    }
    APOSTROPHE_SEQUENCE *seq = ANTLR3_MALLOC(sizeof(*seq));
    seq->sequenceLength = length;
    seq->preceeding = PA_DONT_CARE;
    if (length == 3 || length) {
        seq->preceeding = resolvePreceedingApostrophe(context, length);
    }
    context->apostropheSequences->add(context->apostropheSequences, seq, ANTLR3_FREE_FUNC);
}

static bool textEndsWithSpace(pANTLR3_COMMON_TOKEN t)
{
    if (t->type != SPACE_TAB) {
        return false;
    }
    pANTLR3_STRING s = t->getText(t);
    return s != NULL
        && s->len > 0
        && s->charAt(s, s->len - 1) == antlr3c8toAntlrc(' ');
}

static PRECEEDING_APOSTROPHE
resolvePreceedingApostrophe(MWPARSERCONTEXT *context, int len)
{
    pANTLR3_COMMON_TOKEN prev = context->prevInlineToken;
    pANTLR3_COMMON_TOKEN prevprev = context->prevPrevInlineToken;
    if (prev == NULL) {
        return PA_MULTI_NONSPACE;
    }
    if (prev->type == SPACE_TAB && textEndsWithSpace(prev)) {
        if (len == 4) {
            /*
             * '''' May be victimized as a ''' preceeded by a "one
             * letter word".
             */
            return PA_SPACE_NONSPACE;
        } else {
            return PA_SPACE;
        }
    }
    if (prev->getText(prev)->len == 1 && prevprev != NULL && textEndsWithSpace(prevprev)) {
        return PA_SPACE_NONSPACE;
    }
    return PA_MULTI_NONSPACE;
}

static APOSTROPHE_SEQUENCE *
checkPotentialVictim(APOSTROPHE_SEQUENCE *cur, APOSTROPHE_SEQUENCE *candidate)
{
    assert(candidate->preceeding != PA_DONT_CARE);

    if (cur == NULL)
        return candidate;

    if (cur->preceeding == PA_SPACE_NONSPACE)
        return cur;

    if (candidate->preceeding == PA_SPACE_NONSPACE)
        return candidate;

    if (cur->preceeding == PA_MULTI_NONSPACE)
        return cur;

    if (candidate->preceeding == PA_MULTI_NONSPACE)
        return candidate;

    return cur;
}

/**
 * This method will be called for each inlined token during
 * prescanning of inline content.
 *
 * This is used for determining the type of the token preceeding three
 * apostrophes.
 *
 * @param context
 * @param token
 */
static void
onInlineTokenPrescan(MWPARSERCONTEXT *context, pANTLR3_COMMON_TOKEN token)
{
    context->prevPrevInlineToken = context->prevInlineToken;
    context->prevInlineToken = token;
}

static void
onConsumedApostrophes(MWPARSERCONTEXT *context)
{
    pANTLR3_VECTOR pi = context->parseInlineInstruction;
    int i = context->currentInlineInstruction;

    assert(i < pi->count);

    context->currentInlineInstruction++;
    i = context->currentInlineInstruction;

    if (i < pi->count) {
        APOSTROPHE_DIRECTION ad = *(APOSTROPHE_DIRECTION*)pi->get(pi, i);

        context->takeApostrophe = ad == AD_APOSTROPHE;
        context->takeItalic = ad == AD_ITALIC;
        context->takeBold = ad == AD_BOLD;
    }
}

#define CASE(c, chr) if (c == antlr3c8toAntlrc(chr))
static void
onListElement(MWPARSERCONTEXT *context, pANTLR3_STRING type)
{
    pANTLR3_STRING s = context->listState;
    int i = 0;
    if (s != NULL) {
        for (i = 0; i < s->len && i < type->len; i++) {
            ANTLR3_UCHAR c1 = s->charAt(s, i);
            ANTLR3_UCHAR c2 = type->charAt(type, i);
            /*
             * At this stage, we consider terms and definitions equal.
             */
            CASE(c1, ';') { c1 = antlr3c8toAntlrc(':'); }
            CASE(c2, ';') { c2 = antlr3c8toAntlrc(':'); }
            if (c1 != c2) {
                break;
            }
        }
        closeLists(context, i);
    }
    if (s != NULL && s->len == type->len && i == s->len) {
        ANTLR3_UCHAR c = type->charAt(type, type->len - 1);
        CASE(c, '*') { LSTNR->endBulletListItem(LSTNR);    LSTNR->beginBulletListItem(LSTNR, NULL);  } else
        CASE(c, '#') { LSTNR->endEnumerationItem(LSTNR);   LSTNR->beginEnumerationItem(LSTNR, NULL); } else
        CASE(c, ';') { LSTNR->endDefinedTermItem(LSTNR);   LSTNR->beginDefinedTermItem(LSTNR, NULL); } else
        CASE(c, ':') { LSTNR->endDefinitionItem(LSTNR);    LSTNR->beginDefinitionItem(LSTNR, NULL);
        } else {
            assert(false); // Invalid character representing a list item.
        }
        if (type->charAt(type, type->len - 1) != s->charAt(s, s->len - 1)) {
            updateListState(context, type, s->len - 1);
        }
    } else {
        updateListState(context, type, i);
        openLists(context, i);
        ANTLR3_UCHAR c = type->charAt(type, type->len - 1);
        CASE(c, ';') { LSTNR->beginDefinedTermItem(LSTNR, NULL); }
    }
 }

static void
onNonListBlockElement(MWPARSERCONTEXT *context)
{
    if (context->listState != NULL) {
        closeLists(context, 0);
        context->listState = NULL;
    }
}

static void
updateListState(MWPARSERCONTEXT *context, pANTLR3_STRING type, int offset)
{
    if (context->listState == NULL) {
        context->listState = type;
        return;
    }
    /*
     * 'offset' is the index where the new and the old state differs.
     * ';' and ':' are considered equal, however, we need to preserve
     * the opening state exactly as it was opened, so the closing
     * sequence will match the opening sequence.
     */
    pANTLR3_STRING s = context->listState;
    s = s->subString(s, 0, offset);
    s->appendS(s, type->subString(type, offset, type->len));
    context->listState = s;
}

static void
openLists(MWPARSERCONTEXT *context, int offset)
{
    pANTLR3_STRING s = context->listState;
    int i;
    for (i = offset; i < s->len; i++) {
        ANTLR3_UCHAR c = s->charAt(s, i);
        CASE(c, '*') { LSTNR->beginBulletList(LSTNR, NULL);      LSTNR->beginBulletListItem(LSTNR, NULL);  } else
        CASE(c, '#') { LSTNR->beginEnumerationList(LSTNR, NULL); LSTNR->beginEnumerationItem(LSTNR, NULL); } else
        CASE(c, ':') { LSTNR->beginDefinitionList(LSTNR, NULL);  LSTNR->beginDefinitionItem(LSTNR, NULL);  } else
        CASE(c, ';') { LSTNR->beginDefinitionList(LSTNR, NULL); 
        } else {
            assert(false); // Invalid character representing a list item.
        }
    }
}

static void
closeLists(MWPARSERCONTEXT *context, int offset)
{
    pANTLR3_STRING s = context->listState;
    ANTLR3_UCHAR c = s->charAt(s, s->len - 1);
    CASE(c, ';') { LSTNR->endDefinedTermItem(LSTNR); }
    int i;
    for (i = s->len - 1; i >= offset; i--) {
        c = s->charAt(s, i);

        CASE(c, '*') { LSTNR->endBulletListItem(LSTNR);  LSTNR->endBulletList(LSTNR);       } else
        CASE(c, '#') { LSTNR->endEnumerationItem(LSTNR); LSTNR->endEnumerationList(LSTNR);  } else
        CASE(c, ':') { LSTNR->endDefinitionItem(LSTNR);  LSTNR->endDefinitionList(LSTNR);   } else
        CASE(c, ';') { LSTNR->endDefinitionList(LSTNR);
        } else {
            assert(false); // Invalid character representing a list item.
        }
    }
}
#undef CASE        

/**
 * This method is used to register that a format has been started in
 * order to keep track of the order of the formats.
 *
 * A format is represented by a (begin, end) tuple of methods to call
 * for the actual format.  These methods may later be called to
 * enforce a well ordered nesting.
 */
static void
beginFormat(MWPARSERCONTEXT *context, void (*begin)(), void (*end)(), void *parameter, void *identifier, bool isShortLived)
{
    void *formatOrderTuple[ORDERED_FORMAT_TUPLE_SIZE];
    formatOrderTuple[BEGIN_METHOD_OFFSET] = begin;
    formatOrderTuple[END_METHOD_OFFSET]   = end;
    formatOrderTuple[PARAMETER_OFFSET]    = parameter;
    formatOrderTuple[IDENTIFIER_OFFSET]   = identifier;
    formatOrderTuple[IS_INLINE_OFFSET]    = isShortLived ? context : NULL;
    addTuple(context->formatOrder, ORDERED_FORMAT_TUPLE_SIZE, formatOrderTuple);
}

static void
endFormat(MWPARSERCONTEXT *context,
          void (*beginMethod)(MWPARSERCONTEXT *context),
          void (*endMethod)(MWPARSERCONTEXT *context),
          void *identifier)
{
    pANTLR3_VECTOR v = context->formatOrder;
    const tupleCount = v->count / ORDERED_FORMAT_TUPLE_SIZE;
    int i;

    /*
     * Remove and store tuples for the inner formats, and the specified format.
     */

    void *tuples[tupleCount][ORDERED_FORMAT_TUPLE_SIZE];

    for (i = tupleCount - 1; i >= 0; i--) {
        removeTuple(v, ORDERED_FORMAT_TUPLE_SIZE, tuples[i], i);
        if (tuples[i][BEGIN_METHOD_OFFSET] == beginMethod &&
            tuples[i][IDENTIFIER_OFFSET]   == identifier) {
            break;
        }
    }

    assert(i >= 0);  // the format was opened.

    context->formatResolutionInProgress = true;

    /*
     * Execute end methods for inner formats in reverse order, followed by the
     * specified format (at index i).
     */

    int j;
    for (j = tupleCount - 1; j >= i; j--) {
        void (*end)()   = tuples[j][END_METHOD_OFFSET];
        end(context);
    }

    context->formatResolutionInProgress = false;

    /*
     * Execute the inner begin methods anew.
     */

    for (j = i + 1; j < tupleCount; j++) {
        void (*begin)()  = tuples[j][BEGIN_METHOD_OFFSET];
        void *parameter  = tuples[j][PARAMETER_OFFSET];
        begin(context, parameter);
    }

    assert(v->count % ORDERED_FORMAT_TUPLE_SIZE == 0);  // there are complete tuples in the vector
}

/**
 * Schedule a delayed call to a 'begin' method.  The call will be
 * executed once some actual contents have been found.  If there is no
 * content, the call will be skipped altoghether.
 * @param context
 * @param beginMethod Pointer to the begin method.
 * @param endMethod Pointer to the end method.
 */
static void
delayCall(struct MWPARSERCONTEXT_struct * context,
          void (*beginMethod)(),
          void (*endMethod)(),
          void *parameter,
          void *identifier)
{
    void *tuple[DELAYED_CALLS_TUPLE_SIZE];
    tuple[BEGIN_METHOD_OFFSET] = beginMethod;
    tuple[END_METHOD_OFFSET]   = endMethod;
    tuple[PARAMETER_OFFSET]    = parameter;
    tuple[IDENTIFIER_OFFSET]   = identifier;
    addTuple(context->delayedCalls, DELAYED_CALLS_TUPLE_SIZE, tuple);
}

/**
 * Remove a scheduled delayed call, if present.
 * @param context
 * @param beginMethod  Pointer to the begin method.
 * @param endMethod Pointer to the end method.
 *
 * @return true if a delayed call was descheduled and thus skipped.
 * (Implying that the 'end' call also should be skipped.)
 */
static bool
shouldSkip(struct MWPARSERCONTEXT_struct * context,
           void (*beginMethod)(struct MWPARSERCONTEXT_struct * context),
           void (*endMethod)(struct MWPARSERCONTEXT_struct * context),
           void *identifier)
{
    pANTLR3_VECTOR v = context->delayedCalls;
    if (v->count > 0) {
        void *begin      = v->get(v, v->count - DELAYED_CALLS_TUPLE_SIZE + BEGIN_METHOD_OFFSET);
        void *id         = v->get(v, v->count - DELAYED_CALLS_TUPLE_SIZE + IDENTIFIER_OFFSET);
        if (begin == beginMethod && identifier == id) {
            void *tuple[DELAYED_CALLS_TUPLE_SIZE];
            removeTuple(v, DELAYED_CALLS_TUPLE_SIZE, tuple, v->count/DELAYED_CALLS_TUPLE_SIZE - 1);
            return true;
        }
    } 
    return false;
}

/**
 * Execute scheduled delayed calls.
 * @param context
 */
static void
triggerDelayedCalls(struct MWPARSERCONTEXT_struct * context)
{
    pANTLR3_VECTOR v = context->delayedCalls;
    int i;

    context->executingDelayedCalls = true;

    for (i = 0; i < v->count; i += DELAYED_CALLS_TUPLE_SIZE) {
        void (*beginMethod)() = v->get(v, i);
        void *parameter = v->get(v, i + 2);
        beginMethod(context, parameter);
    }
    v->clear(v);

    assert(v->count == 0);  // The vector is empty.

    context->executingDelayedCalls = false;
}

void printParserInfo(pANTLR3_PARSER parser)
{
    pANTLR3_COMMON_TOKEN t1 = parser->tstream->_LT(parser->tstream, 1);
    pANTLR3_COMMON_TOKEN t2 = parser->tstream->_LT(parser->tstream, 2);
    if (t1 == NULL) {
        fprintf(stderr, "Token is NULL\n");
    } else {
        fprintf(stderr, "type: %d, text: '%s', type: %d, text: '%s'\n", t1->type, t1->getText(t1)->chars, t2->type, t2->getText(t2)->chars);
    }
    fprintf(stderr, "BACKTRACKING: %d\n", parser->rec->state->backtracking);
}


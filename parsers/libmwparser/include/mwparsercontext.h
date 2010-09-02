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

#ifndef MWPARSERCONTEXT_H_
#define MWPARSERCONTEXT_H_

#include <stdbool.h>
#include <mwlistener.h>

/**
 * This is the abstract data type that represents an object that
 * monitors the parser context.
 *
 * The API towards the parser consists of two parts: listening methods
 * that registers parser events and query methods/members that the
 * parser may use in, for instance, semantic predicates to enable
 * context sensitive parsing.
 */
typedef struct MWPARSERCONTEXT_struct 
{
    void * parser;
    
    /*
     * Configuration methods.
     */
    int (*setLegalTitleChars)(struct MWPARSERCONTEXT_struct * context, const wchar_t *posixExtendedRegexp);

    /*
     * Parser event listening methods.
     */

    void (*onWord)(struct MWPARSERCONTEXT_struct * context, pANTLR3_STRING word);
    void (*onSpecial)(struct MWPARSERCONTEXT_struct * context, pANTLR3_STRING special);
    void (*onSpace)(struct MWPARSERCONTEXT_struct * context, pANTLR3_STRING space);
    void (*onNewline)(struct MWPARSERCONTEXT_struct * context);
    void (*onBr)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*onHTMLEntity)(struct MWPARSERCONTEXT_struct * context, pANTLR3_STRING entity);
    void (*onNowiki)(struct MWPARSERCONTEXT_struct * context, pANTLR3_STRING nowiki);
    void (*onHorizontalRule)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*beginParagraph)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endParagraph)(struct MWPARSERCONTEXT_struct * context);
    void (*beginItalic)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endItalic)(struct MWPARSERCONTEXT_struct * context);
    void (*beginBold)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endBold)(struct MWPARSERCONTEXT_struct * context);
    void (*beginPre)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endPre)(struct MWPARSERCONTEXT_struct * context);
    void (*beginArticle)(struct MWPARSERCONTEXT_struct * context);
    void (*endArticle)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHeading)(struct MWPARSERCONTEXT_struct * context, int level, pANTLR3_VECTOR attr);
    void (*endHeading)(struct MWPARSERCONTEXT_struct * context);
    void (*beginTableOfContents)(struct MWPARSERCONTEXT_struct * context);
    void (*endTableOfContents)(struct MWPARSERCONTEXT_struct * context);
    void (*beginTableOfContentsItem)(struct MWPARSERCONTEXT_struct * context, int level);
    void (*endTableOfContentsItem)(struct MWPARSERCONTEXT_struct * context);
    void (*beginTable)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attributes);
    void (*endTable)(struct MWPARSERCONTEXT_struct * context);
    void (*beginTableRow)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attributes);
    void (*endTableRow)(struct MWPARSERCONTEXT_struct * context);
    void (*beginTableCell)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attributes);
    void (*endTableCell)(struct MWPARSERCONTEXT_struct * context);
    void (*beginTableHeading)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attributes);
    void (*endTableHeading)(struct MWPARSERCONTEXT_struct * context);
    void (*beginTableCaption)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attributes);
    void (*endTableCaption)(struct MWPARSERCONTEXT_struct * context);
    void (*beginTableBody)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attributes);
    void (*endTableBody)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlDiv)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlDiv)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlBlockquote)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlBlockquote)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlCenter)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlCenter)(struct MWPARSERCONTEXT_struct * context);

    void (*beginInternalLink)(struct MWPARSERCONTEXT_struct * context, pANTLR3_STRING linkTitle);
    void (*endInternalLink)(struct MWPARSERCONTEXT_struct * context);
    void (*onInternalLink)(struct MWPARSERCONTEXT_struct * context, pANTLR3_STRING linkTitle);
    void (*beginExternalLink)(struct MWPARSERCONTEXT_struct * context, pANTLR3_STRING linkUrl);
    void (*endExternalLink)(struct MWPARSERCONTEXT_struct * context);
    void (*onExternalLink)(struct MWPARSERCONTEXT_struct * context, pANTLR3_STRING linkUrl);
    void (*beginMediaLink)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endMediaLink)(struct MWPARSERCONTEXT_struct * context);
    void (*onMediaLink)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*onTagExtension)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*beginFormat)(struct MWPARSERCONTEXT_struct * context,
                        void (*begin)(),
                        void (*end)(),
                        void *parameter,
                        bool isInlined);
    void (*endFormat)(struct MWPARSERCONTEXT_struct * context,
                      void (*begin)(),
                      void (*end)());
    void (*beginInlinePrescan)(struct MWPARSERCONTEXT_struct * context);
    void (*endInlinePrescan)(struct MWPARSERCONTEXT_struct * context);
    void (*onApostrophesPrescan)(struct MWPARSERCONTEXT_struct * context, int length);
    void (*onInlineTokenPrescan)(struct MWPARSERCONTEXT_struct * context, pANTLR3_COMMON_TOKEN token);
    void (*onConsumedApostrophes)(struct MWPARSERCONTEXT_struct * context);
    void (*onListElement)(struct MWPARSERCONTEXT_struct * context, pANTLR3_STRING type);
    void (*onNonListBlockElement)(struct MWPARSERCONTEXT_struct * context);

    void (*beginHtmlB)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlB)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlI)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlI)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlU)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlU)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlDel)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlDel)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlIns)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlIns)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlFont)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlFont)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlBig)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlBig)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlSmall)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlSmall)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlSub)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlSub)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlSup)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlSup)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlCite)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlCite)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlCode)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlCode)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlStrike)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlStrike)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlStrong)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlStrong)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlSpan)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlSpan)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlTt)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlTt)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlVar)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlVar)(struct MWPARSERCONTEXT_struct * context);
    void (*beginHtmlAbbr)(struct MWPARSERCONTEXT_struct * context, pANTLR3_VECTOR attr);
    void (*endHtmlAbbr)(struct MWPARSERCONTEXT_struct * context);

    /*
     * The listener API.
     */
    MWLISTENER listener;

    /*
     * Parser predicates.
     */
    bool inShortItalic   :1;
    bool inLongItalic    :1;
    bool inShortBold     :1;
    bool inLongBold      :1;
    bool inlinePrescan   :1;
    bool takeApostrophe  :1;
    bool takeItalic      :1;
    bool takeBold        :1;

    /*
     * Parser state.
     */
    bool formatResolutionInProgress      :1;
    bool executingDelayedCalls           :1;
    bool haveGeneratedTableOfContents    :1;
    bool openingFormats                  :1;
    pANTLR3_VECTOR apostropheSequences;
    pANTLR3_VECTOR formatOrder;
    pANTLR3_VECTOR delayedCalls;
    pANTLR3_VECTOR savedOrderedFormats;
    pANTLR3_VECTOR parseInlineInstruction;
    pANTLR3_COMMON_TOKEN prevInlineToken;
    pANTLR3_COMMON_TOKEN prevPrevInlineToken;
    pANTLR3_STRING listState;
    ANTLR3_MARKER startOfTokenStream;
    int currentInlineInstruction;

    /*
     * Utility.
     */
    void (*closeFormats)(struct MWPARSERCONTEXT_struct * context);
    void (*openFormats)(struct MWPARSERCONTEXT_struct * context);
    void (*delayCall)(struct MWPARSERCONTEXT_struct * context,
                      void (*beginMethod)(),
                      void (*endMethod)(),
                      void *parameter);
    bool (*shouldSkip)(struct MWPARSERCONTEXT_struct * context,
                       void (*beginMethod)(),
                       void (*endMethod)());
    void (*triggerDelayedCalls)(struct MWPARSERCONTEXT_struct * context);
    pANTLR3_VECTOR_FACTORY vectorFactory;
    pANTLR3_STRING_FACTORY stringFactory;
    
    /** Method for deallocating this instance. */
    void (*free)(void * context);
    void (*reset)(struct MWPARSERCONTEXT_struct * context);

}
    MWPARSERCONTEXT;

/**
 * Constructor for the parser context super class.  
 */
MWPARSERCONTEXT * MWParserContextNew(void* parser, const MWLISTENER *listener);

/**
 * Put this macro at the beginning of begin methods that should not be
 * executed unless there are some contents.
 *
 * @param context 
 * @param beginmethod The name of the begin method.
 * @param endmethod The name of the corresponding end method.
 * @param delayFirst If also the first occurence should be delayed, i.e.,
 * if even explicitly inputted empty instances should be removed.
 * @param identifier Unique identifier in case of multiple active instances are allowed.                 
 */
#define MW_DELAYED_CALL(context, beginmethod, endmethod, parameter, delayFirst) \
do {                                                                    \
    if (!context->executingDelayedCalls                                 \
        && (delayFirst || context->openingFormats)) {                   \
        context->delayCall(context, beginmethod, endmethod, parameter); \
        return;                                                         \
    }                                                                   \
} while (0)

/**
 * Put this macro at the beginning of end methods that should not be
 * executed unless there was some contents.
 *
 * @param context 
 * @param The name of the begin method.
 * @param The name of the corresponding end method.
 */
#define MW_SKIP_IF_EMPTY(context, beginmethod, endmethod)      \
do {                                                            \
    if (context->shouldSkip(context, beginmethod, endmethod)) {        \
        return;                                                 \
    }                                                           \
} while (0)

/**
 * Put this macro at the beginning of methods that generate inline
 * content to trigger the execution of the delayed method calls.
 *
 * @param context
 */
#define MW_TRIGGER_DELAYED_CALLS(context)  context->triggerDelayedCalls(context)

/**
 * Put this macro at the begining of inline formatting elements that
 * should be nested in a well formed manner.
 *
 * @param context
 * @param beginmethod  The begin method to call.
 * @param endmethod    The corresponding end method.
 * @param parameter    Parameter to pass to the begin method.  NULL if
 *                     the begin method does not take parameters (other
 *                     than the context pointer).
 * @param isShortLived true, if the format should be terminated at end of line, if still open.
 */
#define MW_BEGIN_ORDERED_FORMAT(context, beginmethod, endmethod, parameter, isShortLived) \
do {                                                                    \
    if (!context->formatResolutionInProgress) {                         \
        context->beginFormat(context, beginmethod, endmethod, parameter, isShortLived); \
    }                                                                   \
} while (0)

/**
 * Put this macro at the beginning of the end method for inline
 * formatting elements that should be nested in a well formed manner.
 *
 * @param context
 * @param beginmethod
 * @param endmethod
 */
#define MW_END_ORDERED_FORMAT(context, beginmethod, endmethod) \
do {                                                            \
    if (!context->formatResolutionInProgress) {                 \
        context->endFormat(context, beginmethod, endmethod);   \
        return;                                                 \
    }                                                           \
} while (0)


#endif

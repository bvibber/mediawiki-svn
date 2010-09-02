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
#include <tracingcontext.h>
#include <mwkeyvalue.h>
#include <wchar.h>

static const int INDENT_SPACES = 4;

static void TCOnWord(MWLISTENER *listener, pANTLR3_STRING word);
static void TCOnSpecial(MWLISTENER *listener, pANTLR3_STRING special);
static void TCOnSpace(MWLISTENER *listener, pANTLR3_STRING space);
static void TCOnNewline(MWLISTENER *listener);
static void TCOnBr(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCOnNowiki(MWLISTENER *listener, pANTLR3_STRING nowiki);
static void TCOnHTMLEntity(MWLISTENER *listener, pANTLR3_STRING entity);
static void TCOnHorizontalRule(MWLISTENER *listener, pANTLR3_VECTOR attr);

static void TCBeginParagraph(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndParagraph(MWLISTENER *listener);
static void TCBeginArticle(MWLISTENER *listener);
static void TCEndArticle(MWLISTENER *listener);
static void TCBeginItalic(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndItalic(MWLISTENER *listener);
static void TCBeginBold(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndBold(MWLISTENER *listener);
static void TCBeginPre(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndPre(MWLISTENER *listener);
static void TCBeginBulletList(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndBulletList(MWLISTENER *listener);
static void TCBeginEnumerationList(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndEnumerationList(MWLISTENER *listener);
static void TCBeginDefinitionList(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndDefinitionList(MWLISTENER *listener);
static void TCBeginBulletListItem(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndBulletListItem(MWLISTENER *listener);
static void TCBeginEnumerationItem(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndEnumerationItem(MWLISTENER *listener);
static void TCBeginDefinedTermItem(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndDefinedTermItem(MWLISTENER *listener);
static void TCBeginDefinitionItem(MWLISTENER *listener, pANTLR3_VECTOR attr);
static void TCEndDefinitionItem(MWLISTENER *listener);
static void TCBeginTableOfContents(MWLISTENER *listener);
static void TCEndTableOfContents(MWLISTENER *listener);
static void TCBeginTableOfContentsItem(MWLISTENER *listener, int level);
static void TCEndTableOfContentsItem(MWLISTENER *listener);
static void TCBeginTable(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndTable(MWLISTENER *listener);
static void TCBeginTableRow(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndTableRow(MWLISTENER *listener);
static void TCBeginTableCell(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndTableCell(MWLISTENER *listener);
static void TCBeginTableHeading(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndTableHeading(MWLISTENER *listener);
static void TCBeginTableCaption(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndTableCaption(MWLISTENER *listener);
static void TCBeginTableBody(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndTableBody(MWLISTENER *listener);
static void TCBeginHeading(MWLISTENER *listener, int level, pANTLR3_VECTOR attributes);
static void TCEndHeading(MWLISTENER *listener);
static void TCBeginHtmlDiv(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlDiv(MWLISTENER *listener);
static void TCBeginHtmlBlockquote(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlBlockquote(MWLISTENER *listener);
static void TCBeginHtmlCenter(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlCenter(MWLISTENER *listener);
static void TCBeginInternalLink(MWLISTENER *listener, pANTLR3_STRING linkTitle);
static void TCEndInternalLink(MWLISTENER *listener);
static void TCOnInternalLink(MWLISTENER *listener, pANTLR3_STRING linkTitle);
static void TCBeginExternalLink(MWLISTENER *listener, pANTLR3_STRING linkUrl);
static void TCEndExternalLink(MWLISTENER *listener);
static void TCOnExternalLink(MWLISTENER *listener, pANTLR3_STRING linkUrl);
static void TCBeginMediaLink(MWLISTENER *listener, pANTLR3_STRING linkUrl, pANTLR3_VECTOR attr);
static void TCEndMediaLink(MWLISTENER *listener);
static void TCOnMediaLink(MWLISTENER *listener, pANTLR3_STRING linkUrl, pANTLR3_VECTOR attr);
static void TCOnTagExtension(MWLISTENER *listener, const char * name, pANTLR3_STRING body, pANTLR3_VECTOR attr);
static void TCBeginHtmlU(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlU(MWLISTENER *listener);
static void TCBeginHtmlDel(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlDel(MWLISTENER *listener);
static void TCBeginHtmlIns(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlIns(MWLISTENER *listener);
static void TCBeginHtmlFont(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlFont(MWLISTENER *listener);
static void TCBeginHtmlBig(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlBig(MWLISTENER *listener);
static void TCBeginHtmlSmall(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlSmall(MWLISTENER *listener);
static void TCBeginHtmlSub(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlSub(MWLISTENER *listener);
static void TCBeginHtmlSup(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlSup(MWLISTENER *listener);
static void TCBeginHtmlCite(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlCite(MWLISTENER *listener);
static void TCBeginHtmlCode(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlCode(MWLISTENER *listener);
static void TCBeginHtmlStrike(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlStrike(MWLISTENER *listener);
static void TCBeginHtmlStrong(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlStrong(MWLISTENER *listener);
static void TCBeginHtmlSpan(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlSpan(MWLISTENER *listener);
static void TCBeginHtmlTt(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlTt(MWLISTENER *listener);
static void TCBeginHtmlVar(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlVar(MWLISTENER *listener);
static void TCBeginHtmlAbbr(MWLISTENER *listener, pANTLR3_VECTOR attributes);
static void TCEndHtmlAbbr(MWLISTENER *listener);

static void * TCNew(void);
static void TCFree(void *tcontext);

static void TCPrintAttributes(pANTLR3_VECTOR attributes);
static void TCPrintIndent(MWLISTENER *listener);
static void TCIncreaseIndent(MWLISTENER *listener);
static void TCDecreaseIndent(MWLISTENER *listener);

typedef struct MWTRACINGCONTEXT_struct
{
    int fd;
    const char *charCode;
    int indent;
    void (*free)(void *);
} 
    MWTRACINGCONTEXT;

#define TC(listener) ((MWTRACINGCONTEXT*)((listener)->data))
#define S(string) (((string)->toUTF8(string))->chars)

//#define putchar(c)
//#define putwchar(c)
//#define printf(...)

const MWLISTENER mwParserTracingListener = {
    .newData                  = TCNew,
    .freeData                 = TCFree,
    .resetData                = NULL,
    .onWord                   = TCOnWord,
    .onSpecial                = TCOnSpecial,
    .onSpace                  = TCOnSpace,
    .onNewline                = TCOnNewline,
    .onBr                     = TCOnBr,
    .beginParagraph           = TCBeginParagraph,
    .endParagraph             = TCEndParagraph,
    .beginArticle             = TCBeginArticle,
    .endArticle               = TCEndArticle,
    .beginItalic              = TCBeginItalic,
    .endItalic                = TCEndItalic,
    .beginBold                = TCBeginBold,
    .endBold                  = TCEndBold,
    .beginPre                 = TCBeginPre,
    .endPre                   = TCEndPre,
    .beginTable               = TCBeginTable,
    .endTable                 = TCEndTable,
    .beginTableRow            = TCBeginTableRow,
    .endTableRow              = TCEndTableRow,
    .beginTableCell           = TCBeginTableCell,
    .endTableCell             = TCEndTableCell,
    .beginTableHeading        = TCBeginTableHeading,
    .endTableHeading          = TCEndTableHeading,
    .beginTableCaption        = TCBeginTableCaption,
    .endTableCaption          = TCEndTableCaption,
    .beginTableBody           = TCBeginTableBody,
    .endTableBody             = TCEndTableBody,
    .onNowiki                 = TCOnNowiki,
    .onHTMLEntity             = TCOnHTMLEntity,
    .onHorizontalRule         = TCOnHorizontalRule,
    .beginHeading             = TCBeginHeading,
    .endHeading               = TCEndHeading,
    .beginInternalLink        = TCBeginInternalLink,
    .endInternalLink          = TCEndInternalLink,
    .onInternalLink           = TCOnInternalLink,
    .beginExternalLink        = TCBeginExternalLink,
    .endExternalLink          = TCEndExternalLink,
    .onExternalLink           = TCOnExternalLink,
    .beginMediaLink           = TCBeginMediaLink,
    .endMediaLink             = TCEndMediaLink,
    .onMediaLink              = TCOnMediaLink,
    .onTagExtension           = TCOnTagExtension,
    .beginBulletList          = TCBeginBulletList,
    .endBulletList            = TCEndBulletList,
    .beginBulletListItem      = TCBeginBulletListItem,
    .endBulletListItem        = TCEndBulletListItem,
    .beginEnumerationList     = TCBeginEnumerationList,
    .endEnumerationList       = TCEndEnumerationList,
    .beginEnumerationItem     = TCBeginEnumerationItem,
    .endEnumerationItem       = TCEndEnumerationItem,
    .beginDefinitionList      = TCBeginDefinitionList,
    .endDefinitionList        = TCEndDefinitionList,
    .beginDefinedTermItem     = TCBeginDefinedTermItem,
    .endDefinedTermItem       = TCEndDefinedTermItem,
    .beginDefinitionItem      = TCBeginDefinitionItem,
    .endDefinitionItem        = TCEndDefinitionItem,
    .beginTableOfContents     = TCBeginTableOfContents,
    .endTableOfContents       = TCEndTableOfContents,
    .beginTableOfContentsItem = TCBeginTableOfContentsItem,
    .endTableOfContentsItem   = TCEndTableOfContentsItem,
    .beginHtmlDiv             = TCBeginHtmlDiv,
    .endHtmlDiv               = TCEndHtmlDiv,
    .beginHtmlBlockquote      = TCBeginHtmlBlockquote,
    .endHtmlBlockquote        = TCEndHtmlBlockquote,
    .beginHtmlCenter          = TCBeginHtmlCenter,
    .endHtmlCenter            = TCEndHtmlCenter,
    .beginHtmlU               = TCBeginHtmlU,
    .endHtmlU                 = TCEndHtmlU,
    .beginHtmlDel             = TCBeginHtmlDel,
    .endHtmlDel               = TCEndHtmlDel,
    .beginHtmlIns             = TCBeginHtmlIns,
    .endHtmlIns               = TCEndHtmlIns,
    .beginHtmlFont            = TCBeginHtmlFont,
    .endHtmlFont              = TCEndHtmlFont,
    .beginHtmlBig             = TCBeginHtmlBig,
    .endHtmlBig               = TCEndHtmlBig,
    .beginHtmlSmall           = TCBeginHtmlSmall,
    .endHtmlSmall             = TCEndHtmlSmall,
    .beginHtmlSub             = TCBeginHtmlSub,
    .endHtmlSub               = TCEndHtmlSub,
    .beginHtmlSup             = TCBeginHtmlSup,
    .endHtmlSup               = TCEndHtmlSup,
    .beginHtmlCite            = TCBeginHtmlCite,
    .endHtmlCite              = TCEndHtmlCite,
    .beginHtmlCode            = TCBeginHtmlCode,
    .endHtmlCode              = TCEndHtmlCode,
    .beginHtmlStrike          = TCBeginHtmlStrike,
    .endHtmlStrike            = TCEndHtmlStrike,
    .beginHtmlStrong          = TCBeginHtmlStrong,
    .endHtmlStrong            = TCEndHtmlStrong,
    .beginHtmlSpan            = TCBeginHtmlSpan,
    .endHtmlSpan              = TCEndHtmlSpan,
    .beginHtmlTt              = TCBeginHtmlTt,
    .endHtmlTt                = TCEndHtmlTt,
    .beginHtmlVar             = TCBeginHtmlVar,
    .endHtmlVar               = TCEndHtmlVar,
    .beginHtmlAbbr            = TCBeginHtmlAbbr,
    .endHtmlAbbr              = TCEndHtmlAbbr,

};


static void * TCNew()
{
    MWTRACINGCONTEXT *tc = ANTLR3_MALLOC(sizeof(*tc));

    if (tc == NULL) {
        return NULL;
    }
    tc->free = TCFree;
    tc->indent = 0;
    return tc;
}

static void TCFree(void *tcontext)
{
    MWTRACINGCONTEXT *tc = tcontext;
    ANTLR3_FREE(tc);
}

static void
TCOnWord(MWLISTENER *listener, pANTLR3_STRING word)
{
    TCPrintIndent(listener);
    printf("WORD[%s]\n", S(word));
}

static void
TCOnSpecial(MWLISTENER *listener, pANTLR3_STRING special)
{
    TCPrintIndent(listener);
    printf("SPECIAL[%s]\n", S(special));
}

static void
TCOnSpace(MWLISTENER *listener, pANTLR3_STRING space)
{
    TCPrintIndent(listener);
    printf("SPACE[%s]\n", S(space));
}

static void
TCOnNewline(MWLISTENER *listener)
{
    TCPrintIndent(listener);
    printf("NEWLINE\n");
}

static void
TCOnBr(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BR");
    TCPrintAttributes(attr);
    printf("\n");
}

static void
TCBeginParagraph(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN PARAGRAPH");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndParagraph(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END PARAGRAPH\n");
}

static void
TCBeginArticle(MWLISTENER *listener)
{
    TCPrintIndent(listener);
    printf("BEGIN ARTICLE\n");
    TCIncreaseIndent(listener);
}

static void
TCEndArticle(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END ARTICLE\n");
}

static void
TCBeginItalic(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN ITALIC");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndItalic(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END ITALIC\n");
}

static void
TCBeginBold(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN BOLD");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndBold(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END BOLD\n");
}

static void
TCBeginPre(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN PRE");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndPre(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END PRE\n");
}

static void
TCBeginHeading(MWLISTENER *listener, int level, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN HEADING[%d]", level);
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHeading(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END HEADING\n");
}

static void
TCBeginInternalLink(MWLISTENER *listener, pANTLR3_STRING linkTitle)
{
    TCPrintIndent(listener);
    printf("BEGIN INTERNAL LINK[%s]\n", linkTitle->chars);
    TCIncreaseIndent(listener);
}

static void
TCEndInternalLink(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END INTERNAL LINK\n");
}

static void
TCOnInternalLink(MWLISTENER *listener, pANTLR3_STRING linkTitle)
{
    TCPrintIndent(listener);
    printf("INTERNAL LINK[%s]\n", linkTitle->chars);
}

static void
TCBeginExternalLink(MWLISTENER *listener, pANTLR3_STRING linkUrl)
{
    TCPrintIndent(listener);
    printf("BEGIN EXTERNAL LINK[%s]\n", linkUrl->chars);
    TCIncreaseIndent(listener);
}

static void
TCEndExternalLink(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END EXTERNAL LINK\n");
}

static void
TCOnExternalLink(MWLISTENER *listener, pANTLR3_STRING linkUrl)
{
    TCPrintIndent(listener);
    printf("EXTERNAL LINK[%s]\n", linkUrl->chars);
}

static void
TCBeginMediaLink(MWLISTENER *listener, pANTLR3_STRING linkUrl, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN MEDIA LINK[%s]", linkUrl->chars);
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndMediaLink(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END MEDIA LINK\n");
}

static void
TCOnMediaLink(MWLISTENER *listener, pANTLR3_STRING linkUrl, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("MEDIA LINK[%s]", linkUrl->chars);
    TCPrintAttributes(attr);
    printf("\n");
}

static void
TCOnTagExtension(MWLISTENER *listener, const char *name, pANTLR3_STRING body, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN TAG EXTENSION [%s]", name);
    TCPrintAttributes(attr);
    printf("[%s]\n", body->chars);
    TCPrintIndent(listener);
    printf("END TAG EXTENSION [%s]\n", name);
}

static void
TCBeginBulletList(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN BULLET LIST");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndBulletList(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END BULLET LIST\n");
}

static void
TCBeginBulletListItem(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN BULLET LIST ITEM");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndBulletListItem(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END BULLET LIST ITEM\n");
}

static void
TCBeginEnumerationList(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN ENUMERATION LIST");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndEnumerationList(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END ENUMERATION LIST\n");
}

static void
TCBeginEnumerationItem(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN ENUMERATION LIST ITEM");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndEnumerationItem(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END ENUMERATION LIST ITEM\n");
}

static void
TCBeginDefinitionList(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN DEFINITION LIST");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndDefinitionList(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END DEFINITION LIST\n");
}

static void
TCBeginDefinedTermItem(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN DEFINED TERM ITEM");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndDefinedTermItem(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END DEFINED TERM ITEM\n");
}

static void
TCBeginDefinitionItem(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN DEFINITION ITEM");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndDefinitionItem(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END DEFINITION ITEM\n");
}

static void
TCBeginTableOfContents(MWLISTENER *listener)
{
    TCPrintIndent(listener);
    printf("BEGIN TABLE OF CONTENTS\n");
    TCIncreaseIndent(listener);
}

static void
TCEndTableOfContents(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END TABLE OF CONTENTS\n");
}

static void
TCBeginTableOfContentsItem(MWLISTENER *listener, int level)
{
    TCPrintIndent(listener);
    printf("BEGIN TABLE OF CONTENTS ITEM[%d]\n", level);
    TCIncreaseIndent(listener);
}

static void
TCEndTableOfContentsItem(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END TABLE OF CONTENTS ITEM\n");
}

static void
TCPrintAttributes(pANTLR3_VECTOR attributes)
{
    if (attributes != NULL) {
        int i;
        for (i = 0; i < attributes->count; i++) {
            MWKEYVALUE *p = attributes->get(attributes, i);
            printf(" %s=\"", p->key->chars);
            int j;
            for (j = 0; j < p->value->len; j++) {
                ANTLR3_UINT32 c = p->value->charAt(p->value, j);
                if (c == '"') {
                    putchar('\\');
                    putchar('"');
                } else {
                    putwchar(c);
                }
            }
            putchar('"');
        }
    }
}

static void
TCBeginTable(MWLISTENER *listener, pANTLR3_VECTOR attributes)
{
    TCPrintIndent(listener);
    printf("BEGIN TABLE");
    TCPrintAttributes(attributes);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndTable(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END TABLE\n");
}

static void
TCBeginTableRow(MWLISTENER *listener, pANTLR3_VECTOR attributes)
{
    TCPrintIndent(listener);
    printf("BEGIN ROW");
    TCPrintAttributes(attributes);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndTableRow(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END ROW\n");
}

static void
TCBeginTableCell(MWLISTENER *listener, pANTLR3_VECTOR attributes)
{
    TCPrintIndent(listener);
    printf("BEGIN CELL");
    TCPrintAttributes(attributes);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndTableCell(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END CELL\n");
}

static void
TCBeginTableHeading(MWLISTENER *listener, pANTLR3_VECTOR attributes)
{
    TCPrintIndent(listener);
    printf("BEGIN TABLE HEADING");
    TCPrintAttributes(attributes);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndTableHeading(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END TABLE HEADING\n");
}

static void
TCBeginTableCaption(MWLISTENER *listener, pANTLR3_VECTOR attributes)
{
    TCPrintIndent(listener);
    printf("BEGIN TABLE CAPTION");
    TCPrintAttributes(attributes);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndTableCaption(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END TABLE CAPTION\n");
}


static void
TCBeginTableBody(MWLISTENER *listener, pANTLR3_VECTOR attributes)
{
    TCPrintIndent(listener);
    printf("BEGIN TABLE BODY");
    TCPrintAttributes(attributes);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndTableBody(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END TABLE BODY\n");
}

static void
TCOnNowiki(MWLISTENER *listener, pANTLR3_STRING nowiki)
{
    TCPrintIndent(listener);
    printf("NOWIKI[%s]\n", S(nowiki));
    TCPrintIndent(listener);
    printf("END NOWIKI\n");
}

static void
TCOnHTMLEntity(MWLISTENER *listener, pANTLR3_STRING entity)
{
    TCPrintIndent(listener);
    printf("HTML_ENTITY[%s]\n", S(entity));
}

static void
TCOnHorizontalRule(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("HORIZONTAL_RULE");
    TCPrintAttributes(attr);
    printf("\n");
}

static void
TCBeginHtmlDiv(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN DIV");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlDiv(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END DIV\n");
}

static void
TCBeginHtmlBlockquote(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN BLOCKQUOTE");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlBlockquote(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END BLOCKQUOTE\n");
}

static void
TCBeginHtmlCenter(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN CENTER");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlCenter(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END CENTER\n");
}

static void
TCBeginHtmlU(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN U");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlU(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END U\n");
}

static void
TCBeginHtmlDel(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN DEL");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlDel(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END DEL\n");
}

static void
TCBeginHtmlIns(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN INS");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlIns(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END INS\n");
}

static void
TCBeginHtmlFont(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN FONT");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlFont(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END FONT\n");
}

static void
TCBeginHtmlBig(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN BIG");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlBig(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END BIG\n");
}

static void
TCBeginHtmlSmall(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN SMALL");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlSmall(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END SMALL\n");
}

static void
TCBeginHtmlSub(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN SUB");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlSub(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END SUB\n");
}

static void
TCBeginHtmlSup(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN SUP");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlSup(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END SUP\n");
}

static void
TCBeginHtmlCite(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN CITE");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlCite(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END CITE\n");
}

static void
TCBeginHtmlCode(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN CODE");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlCode(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END CODE\n");
}

static void
TCBeginHtmlStrike(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN STRIKE");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlStrike(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END STRIKE\n");
}

static void
TCBeginHtmlStrong(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN STRONG");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlStrong(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END STRONG\n");
}

static void
TCBeginHtmlSpan(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN SPAN");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlSpan(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END SPAN\n");
}

static void
TCBeginHtmlTt(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN TT");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlTt(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END TT\n");
}

static void
TCBeginHtmlVar(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN VAR");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlVar(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END VAR\n");
}

static void
TCBeginHtmlAbbr(MWLISTENER *listener, pANTLR3_VECTOR attr)
{
    TCPrintIndent(listener);
    printf("BEGIN ABBR");
    TCPrintAttributes(attr);
    printf("\n");
    TCIncreaseIndent(listener);
}

static void
TCEndHtmlAbbr(MWLISTENER *listener)
{
    TCDecreaseIndent(listener);
    TCPrintIndent(listener);
    printf("END ABBR\n");
}

static void
TCPrintIndent(MWLISTENER *listener)
{
    int i = TC(listener)->indent;
    while (i > 0) {
        putchar(' ');
        i--;
    }
}

static void
TCIncreaseIndent(MWLISTENER *listener)
{
    TC(listener)->indent += INDENT_SPACES;
}

static void
TCDecreaseIndent(MWLISTENER *listener)
{
    TC(listener)->indent -= INDENT_SPACES;
}



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

parser grammar mwParser;

options {
    language = C;
    k = 1;
    backtrack = false;
    tokenVocab = mwLexer;
}

@parser::preincludes {
#include <mwconfig.h>
}

@parser::includes {
#include <mwparsercontext.h>
#include <assert.h>
#include <mwkeyvalue.h>
}

@parser::members{
#define CX ((MWPARSERCONTEXT*)(ctx->pParser->super))
#define LISTENER (&CX->listener)
#define IE(code) if (!CX->inlinePrescan) { code } else { CX->onInlineTokenPrescan(CX, LT(-1)); }

#define D_(msg) (fputs(msg, stderr), fputc('\n', stderr), printParserInfo(PARSER), true)

#define HEADING_LEVEL user1
}

article:
    {
        CX->beginArticle(CX);
        /*
         * Used by table of contents production.
         */
        CX->startOfTokenStream = MARK();
    }
    block_level EOF
    {
        CX->endArticle(CX);
    }
    ;

block_level: (()=> NEWLINE)* ((~(EOF))=> block_elements)? (()=> NEWLINE)*
    ;

block_elements: block_element (()=> empty_lines)?  ((~(EOF))=> block_elements)?
    ;

empty_lines:
    (()=> NEWLINE)+
    {
        CX->onNonListBlockElement(CX);
    }
    ;

block_element: paragraph | table | wikitext_list | heading | pre | horizontal_rule | html_div | html_list | html_blockquote | html_center
    ;

html_div: 
    token = HTML_DIV_OPEN
    {
        CX->beginHtmlDiv(CX, $token->custom);
    }
    block_element_contents
    (HTML_DIV_CLOSE|EOF)
    {
        CX->endHtmlDiv(CX);
    }
    ;

html_blockquote: 
    token = HTML_BLOCKQUOTE_OPEN
    {
        CX->beginHtmlBlockquote(CX, $token->custom);
    }
    block_element_contents
    (HTML_BLOCKQUOTE_CLOSE|EOF)
    {
        CX->endHtmlBlockquote(CX);
    }
    ;

html_center: 
    token = HTML_CENTER_OPEN
    {
        CX->beginHtmlCenter(CX, $token->custom);
    }
    block_element_contents
    (HTML_CENTER_CLOSE|EOF)
    {
        CX->endHtmlCenter(CX);
    }
    ;


html_list: html_ul | html_ol | html_dl
    ;

html_ul:
    token = HTML_UL_OPEN
    {
        LISTENER->beginBulletList(LISTENER, $token->custom);
    }
    ((~(EOF|HTML_UL_CLOSE))=>
     (()=> html_ul_li
    |      block_element_contents))*
    (HTML_UL_CLOSE|EOF)
    {
        LISTENER->endBulletList(LISTENER);
    }
    ;

html_ol:
    token = HTML_OL_OPEN
    {
        LISTENER->beginEnumerationList(LISTENER, $token->custom);
    }
    ((~(EOF|HTML_OL_CLOSE))=>
     (()=> html_ol_li
    |      block_element_contents))*
    (HTML_OL_CLOSE|EOF)
    {
        LISTENER->endEnumerationList(LISTENER);
    }
    ;

html_dl:
    token = HTML_DL_OPEN
    {
        LISTENER->beginDefinitionList(LISTENER, $token->custom);
    }
    ((~(HTML_DL_CLOSE|EOF))=>
     (()=> html_dd
    | ()=> html_dt
    | block_element_contents))*
    (HTML_DL_CLOSE|EOF)
    {
        LISTENER->endDefinitionList(LISTENER);
    }
    ;

html_ul_li:
    token = HTML_UL_LI_OPEN
    {
        LISTENER->beginBulletListItem(LISTENER, $token->custom);
    }
    block_element_contents
    HTML_UL_LI_CLOSE?
    {
        LISTENER->endBulletListItem(LISTENER);
    }
    ;

html_ol_li:
    token = HTML_OL_LI_OPEN
    {
        LISTENER->beginEnumerationItem(LISTENER, $token->custom);
    }
    block_element_contents
    HTML_OL_LI_CLOSE?
    {
        LISTENER->endEnumerationItem(LISTENER);
    }
    ;

html_dt:
    token = HTML_DT_OPEN
    {
        LISTENER->beginDefinedTermItem(LISTENER, $token->custom);
    }
    block_element_contents
    HTML_DT_CLOSE?
    {
        LISTENER->endDefinedTermItem(LISTENER);
    }
    ;

html_dd:
    token = HTML_DD_OPEN
    {
        LISTENER->beginDefinitionItem(LISTENER, $token->custom);
    }
    block_element_contents
    HTML_DD_CLOSE?
    {
        LISTENER->endDefinitionItem(LISTENER);
    }
    ;


wikitext_list: (()=> list_element)+ {CX->onNonListBlockElement(CX);}
    ;

list_element: 
    e = LIST_ELEMENT 
    {
        CX->onListElement(CX, $e->getText($e));
    }
    (inline_text_line | table)
    ;

paragraph:
    (token = HTML_P_OPEN {CX->beginParagraph(CX, $token->custom); }
    | {CX->beginParagraph(CX, NULL); })
    inline_text
    HTML_P_CLOSE?
    {
       CX->endParagraph(CX);
    }
    ;

inline_text: 
    inline_text_line ((newline inline_element)=> newline inline_text_line)*
    ;

inline_text_line: 
    inline_prescan
    {
       CX->openFormats(CX);
    }
    actual_inline_text_line
    {
       CX->closeFormats(CX);
    }
    ;

inline_prescan
@init{ANTLR3_MARKER marker;}:
    {
        marker = MARK();
        CX->beginInlinePrescan(CX);
    }
    (()=> inline_element)+
    {
        CX->endInlinePrescan(CX);
        REWIND(marker);
    }
    ;

actual_inline_text_line: (()=> inline_element)+
    ;

inline_element: word|space|special|br|html_entity|link_element|format|nowiki|table_of_contents|html_inline_tag
    ;

special: (token = SPECIAL {IE(CX->onSpecial(CX, $token->getText($token));)} | apostrophes)
    ;

space: token = SPACE_TAB  {IE(CX->onSpace(CX, $token->getText($token));)}
    ;

word: token = (WORD|ASCII_WORD) {IE(CX->onWord(CX, $token->getText($token));)}
    ;

html_entity: token = HTML_ENTITY {IE(CX->onHTMLEntity(CX, $token->getText($token));)}
    ;

html_inline_tag: html_b|html_i|html_u|html_span|html_del|html_ins|html_font|html_big|html_small|html_sub|
                 html_sup|html_cite|html_code|html_strike|html_strong|html_tt|html_var|html_abbr
    ;

html_b:      token = HTML_B_OPEN      {IE(CX->beginHtmlB(CX, $token->custom);)}      | HTML_B_CLOSE      {IE(CX->endHtmlB(CX);)}     ;
html_del:    token = HTML_DEL_OPEN    {IE(CX->beginHtmlDel(CX, $token->custom);)}    | HTML_DEL_CLOSE    {IE(CX->endHtmlDel(CX);)}   ;
html_i:      token = HTML_I_OPEN      {IE(CX->beginHtmlI(CX, $token->custom);)}      | HTML_I_CLOSE      {IE(CX->endHtmlI(CX);)}     ;
html_u:      token = HTML_U_OPEN      {IE(CX->beginHtmlU(CX, $token->custom);)}      | HTML_U_CLOSE      {IE(CX->endHtmlU(CX);)}     ;
html_ins:    token = HTML_INS_OPEN    {IE(CX->beginHtmlIns(CX, $token->custom);)}    | HTML_INS_CLOSE    {IE(CX->endHtmlIns(CX);)}   ;
html_font:   token = HTML_FONT_OPEN   {IE(CX->beginHtmlFont(CX, $token->custom);)}   | HTML_FONT_CLOSE   {IE(CX->endHtmlFont(CX);)}  ;
html_big:    token = HTML_BIG_OPEN    {IE(CX->beginHtmlBig(CX, $token->custom);)}    | HTML_BIG_CLOSE    {IE(CX->endHtmlBig(CX);)}   ;
html_small:  token = HTML_SMALL_OPEN  {IE(CX->beginHtmlSmall(CX, $token->custom);)}  | HTML_SMALL_CLOSE  {IE(CX->endHtmlSmall(CX);)} ;
html_sub:    token = HTML_SUB_OPEN    {IE(CX->beginHtmlSub(CX, $token->custom);)}    | HTML_SUB_CLOSE    {IE(CX->endHtmlSub(CX);)}   ;
html_sup:    token = HTML_SUP_OPEN    {IE(CX->beginHtmlSup(CX, $token->custom);)}    | HTML_SUP_CLOSE    {IE(CX->endHtmlSup(CX);)}   ;
html_cite:   token = HTML_CITE_OPEN   {IE(CX->beginHtmlCite(CX, $token->custom);)}   | HTML_CITE_CLOSE   {IE(CX->endHtmlCite(CX);)}  ;
html_code:   token = HTML_CODE_OPEN   {IE(CX->beginHtmlCode(CX, $token->custom);)}   | HTML_CODE_CLOSE   {IE(CX->endHtmlCode(CX);)}  ;
html_strike: token = HTML_STRIKE_OPEN {IE(CX->beginHtmlStrike(CX, $token->custom);)} | HTML_STRIKE_CLOSE {IE(CX->endHtmlStrike(CX);)};
html_strong: token = HTML_STRONG_OPEN {IE(CX->beginHtmlStrong(CX, $token->custom);)} | HTML_STRONG_CLOSE {IE(CX->endHtmlStrong(CX);)};
html_span:   token = HTML_SPAN_OPEN   {IE(CX->beginHtmlSpan(CX, $token->custom);)}   | HTML_SPAN_CLOSE   {IE(CX->endHtmlSpan(CX);)}  ;
html_tt:     token = HTML_TT_OPEN     {IE(CX->beginHtmlTt(CX, $token->custom);)}     | HTML_TT_CLOSE     {IE(CX->endHtmlTt(CX);)}    ;
html_var:    token = HTML_VAR_OPEN    {IE(CX->beginHtmlVar(CX, $token->custom);)}    | HTML_VAR_CLOSE    {IE(CX->endHtmlVar(CX);)}   ;
html_abbr:   token = HTML_ABBR_OPEN   {IE(CX->beginHtmlAbbr(CX, $token->custom);)}   | HTML_ABBR_CLOSE   {IE(CX->endHtmlAbbr(CX);)}  ;

br: token = HTML_BR {IE(CX->onBr(CX, $token->custom);)}
    ;

newline: NEWLINE  
    {
       IE(CX->onNewline(CX);)
    }
    ;

nowiki : token = NOWIKI {IE(CX->onNowiki(CX, $token->getText($token));)}
    ;

format: ({!CX->inlinePrescan}?=> (bold | italic)  {CX->onConsumedApostrophes(CX);});
apostrophes
@init{
    int length = 0;
}: (apostrophe {length++;})+ { if(CX->inlinePrescan) { CX->onApostrophesPrescan(CX, length); CX->onInlineTokenPrescan(CX, LT(-1));} };

bold:   {CX->takeBold}?=> (begin_bold   | end_bold);
italic: {CX->takeItalic}?=> (begin_italic | end_italic);
apostrophe: {CX->inlinePrescan}?=> APOS
    |       {CX->takeApostrophe}?=> token = APOS {CX->onConsumedApostrophes(CX); CX->onSpecial(CX, $token->getText($token));} ;

begin_bold:   {!CX->inShortBold}?=>   APOS APOS APOS {CX->beginBold(CX, NULL);}  ;
end_bold:     { CX->inShortBold}?=>   APOS APOS APOS {CX->endBold(CX);}    ;
begin_italic: {!CX->inShortItalic}?=> APOS APOS      {CX->beginItalic(CX, NULL);};
end_italic:   { CX->inShortItalic}?=> APOS APOS      {CX->endItalic(CX);}  ;

pre:
    ( 
        {
            CX->beginPre(CX, NULL);
        }
        (()=> INDENT inline_text_line (NEWLINE|EOF))+
      |
        p = BEGIN_PRE
        {
           CX->beginPre(CX, $p->custom);
        }
        inline_text END_PRE
    )


    {
        CX->endPre(CX);
    }
    ;

table:
    begin_table 
    ((TABLE_CAPTION|TABLE_ROW_SEPARATOR|TABLE_CELL|
      TABLE_HEADING|HTML_CAPTION_OPEN|HTML_TR_OPEN|
      HTML_TD_OPEN|HTML_TH_OPEN|HTML_TBODY_OPEN)=> table_body)*
    end_table
    (()=> garbage_inline_text_line)?
    ;

table_body:
    ((TABLE_CAPTION|HTML_CAPTION_OPEN)=> table_captions)?
    ((~(END_TABLE|HTML_TABLE_CLOSE))=>
        {
            CX->beginTableBody(CX, NULL);
        }
        (
           (()=> HTML_TBODY_OPEN table_rows HTML_TBODY_CLOSE?)
           |
           table_rows
        )
        {
           CX->endTableBody(CX);
        }
    )?   
    ;

table_captions:
    (
      caption = TABLE_CAPTION table_caption_contents[$caption->custom] ((TABLE_CELL_INLINE)=> inline_table_caption)*
    )
    |
    (
      caption = HTML_CAPTION_OPEN table_caption_contents[$caption->custom] HTML_CAPTION_CLOSE?
    )
    ;

inline_table_caption: TABLE_CELL_INLINE table_caption_contents[NULL]
    ;

table_caption_contents[pANTLR3_VECTOR attrs]:
        {
            CX->beginTableCaption(CX, attrs);
        }
        block_element_contents
        {
            CX->endTableCaption(CX);
        }
    ;

begin_table:
    begin = (BEGIN_TABLE|HTML_TABLE_OPEN) (()=> NEWLINE)*
    (()=> garbage_inline_text_line (()=>NEWLINE)*)*
    block_element_contents
    {CX->beginTable(CX, $begin->custom);}
    ;

garbage_inline_text_line: inline_text_line
    ;

end_table: (END_TABLE | HTML_TABLE_CLOSE | EOF) {CX->endTable(CX);}
    ;

table_rows: ((~(TABLE_ROW_SEPARATOR|HTML_TR_OPEN))=> table_first_row)? ((TABLE_ROW_SEPARATOR|HTML_TR_OPEN)=> table_row)*
    ;

table_first_row: table_row_content[NULL]
    ;

table_row: 
    (row = TABLE_ROW_SEPARATOR table_row_content[$row->custom])
    |
    (row = HTML_TR_OPEN table_row_content[$row->custom] HTML_TR_CLOSE?)
    ;

table_row_content[pANTLR3_VECTOR attrs]:
        {
            CX->beginTableRow(CX, attrs);
        } 
        table_cells
        {
            CX->endTableRow(CX);
        }
    ;

table_cells: ((TABLE_CELL|TABLE_CELL_INLINE|TABLE_HEADING|TABLE_HEADING_INLINE|HTML_TD_OPEN|HTML_TH_OPEN)=> (table_cell|table_heading))*
    ;

table_cell: 
        (
           cell = (TABLE_CELL|TABLE_CELL_INLINE)   table_cell_common[$cell->custom]
        )
        |
        (
           cell = HTML_TD_OPEN                     table_cell_common[$cell->custom] HTML_TD_CLOSE?
        )
    ;

table_heading: 
        (
           h= (TABLE_HEADING|TABLE_HEADING_INLINE) table_heading_common[$h->custom]
        )
        |
        (
           h = HTML_TH_OPEN                        table_heading_common[$h->custom] HTML_TH_CLOSE?
        )
    ;

table_cell_common[pANTLR3_VECTOR attrs]:
        {
            CX->beginTableCell(CX, attrs);
        }
        block_element_contents
        {
            CX->endTableCell(CX);
        }
    ;

table_heading_common[pANTLR3_VECTOR attrs]:
        {
            CX->beginTableHeading(CX, attrs);
        }
        block_element_contents
        {
            CX->endTableHeading(CX);
        }
    ;

block_element_contents:
    (
        /* 
         * The first line of inline text is not a paragraph.
         */
        ()=> inline_text (()=> newline)*
    )?
    block_level
    ;

horizontal_rule: hr = HORIZONTAL_RULE { CX->onHorizontalRule(CX, $hr->custom); } (()=> garbage_inline_text_line)?
    ;

heading: begin_heading heading_contents end_heading
    ;

heading_contents: newline* inline_text ((NEWLINE)=> (()=>newline)+ (()=>inline_text)?)*
    ;


begin_heading: 
      (h = BEGIN_HEADING { CX->beginHeading(CX, $h->HEADING_LEVEL, NULL); })
    | (h = HTML_H1_OPEN  { CX->beginHeading(CX, 1, $h->custom); })
    | (h = HTML_H2_OPEN  { CX->beginHeading(CX, 2, $h->custom); })
    | (h = HTML_H3_OPEN  { CX->beginHeading(CX, 3, $h->custom); })
    | (h = HTML_H4_OPEN  { CX->beginHeading(CX, 4, $h->custom); })
    | (h = HTML_H5_OPEN  { CX->beginHeading(CX, 5, $h->custom); })
    | (h = HTML_H6_OPEN  { CX->beginHeading(CX, 6, $h->custom); })
    ;

end_heading:
    (  END_HEADING
    |((HTML_H1_CLOSE
    |  HTML_H2_CLOSE
    |  HTML_H3_CLOSE
    |  HTML_H4_CLOSE
    |  HTML_H5_CLOSE
    |  HTML_H6_CLOSE)|EOF))
    {
        CX->endHeading(CX);
    }   
    ;

table_of_contents:  TABLE_OF_CONTENTS
    (
       {CX->inlinePrescan || CX->haveGeneratedTableOfContents || BACKTRACKING > 0}?=> 
     | table_of_contents_scan
    )
    ;

table_of_contents_scan
@init{ ANTLR3_MARKER tocScanMark; }:
    {
        tocScanMark = MARK();
        REWIND(CX->startOfTokenStream);
        CX->beginTableOfContents(CX);
        CX->haveGeneratedTableOfContents = true;
    }
    ((table_of_contents_item | .))* EOF
    {
        CX->endTableOfContents(CX);
        REWIND(tocScanMark);
    }
    ;

table_of_contents_item:
    begin_table_of_contents_item 
    newline* inline_text ((NEWLINE)=> newline+ inline_text)*
    end_table_of_contents_item
    ;

begin_table_of_contents_item:
      (h = BEGIN_HEADING { CX->beginTableOfContentsItem(CX, h->HEADING_LEVEL); })
    | (h = HTML_H1_OPEN  { CX->beginTableOfContentsItem(CX, 1); })
    | (h = HTML_H2_OPEN  { CX->beginTableOfContentsItem(CX, 2); })
    | (h = HTML_H3_OPEN  { CX->beginTableOfContentsItem(CX, 3); })
    | (h = HTML_H4_OPEN  { CX->beginTableOfContentsItem(CX, 4); })
    | (h = HTML_H5_OPEN  { CX->beginTableOfContentsItem(CX, 5); })
    | (h = HTML_H6_OPEN  { CX->beginTableOfContentsItem(CX, 6); })
    ;

end_table_of_contents_item:
       (END_HEADING   { CX->endTableOfContentsItem(CX); })
    |(((HTML_H1_CLOSE { CX->endTableOfContentsItem(CX); })
    |  (HTML_H2_CLOSE { CX->endTableOfContentsItem(CX); })
    |  (HTML_H3_CLOSE { CX->endTableOfContentsItem(CX); })
    |  (HTML_H4_CLOSE { CX->endTableOfContentsItem(CX); })
    |  (HTML_H5_CLOSE { CX->endTableOfContentsItem(CX); })
    |  (HTML_H6_CLOSE { CX->endTableOfContentsItem(CX); }))|EOF)
    ;

link_element: internal_link | external_link | media_link
    ;

internal_link: complete_internal_link | begin_internal_link | end_internal_link
    ;

complete_internal_link: linkToken = INTERNAL_LINK
    {
        IE(CX->onInternalLink(CX, $linkToken->custom);)
    }
    ;

begin_internal_link:  linkToken = BEGIN_INTERNAL_LINK
    {
        IE(CX->beginInternalLink(CX, $linkToken->custom);)
    }
    ;

end_internal_link: END_INTERNAL_LINK
    {
        IE(CX->endInternalLink(CX);)
    }
    ;

external_link: complete_external_link | begin_external_link | end_external_link
    ;

complete_external_link: linkToken = EXTERNAL_LINK
    {
        IE(CX->onExternalLink(CX, $linkToken->custom);)
    }
    ;

begin_external_link:  linkToken = BEGIN_EXTERNAL_LINK
    {
        IE(CX->beginExternalLink(CX, $linkToken->custom);)
    }
    ;

end_external_link: END_EXTERNAL_LINK
    {
        IE(CX->endExternalLink(CX);)
    }
    ;

media_link: complete_media_link | begin_media_link | end_media_link
    ;

complete_media_link: linkToken = MEDIA_LINK
    {
        IE(CX->onMediaLink(CX, $linkToken->custom);)
    }
    ;

begin_media_link:  linkToken = BEGIN_MEDIA_LINK
    {
        IE(CX->beginMediaLink(CX, $linkToken->custom);)
    }
    ;

end_media_link: END_MEDIA_LINK
    {
        IE(CX->endMediaLink(CX);)
    }
    ;


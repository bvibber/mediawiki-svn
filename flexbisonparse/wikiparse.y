%{

/**
 **
 **  This file is part of the flex/bison-based parser for MediaWiki.
 **          This is the grammar - the input file for bison.
 **  See fb_defines.h on how to make it output debugging information.
 **
 ** This source file is licensed unter the GNU General Public License
 **               http://www.gnu.org/copyleft/gpl.html
 **                 Originally written 2004 by Timwi
 **/

#include <stdio.h>
#include "parsetree.h"
#include "fb_defines.h"
int yyerror() { printf ("\n\nSYNTAX ERROR.\n\n"); }

Node articlenode;
int i;

%}

/* This defines the type of yylval */
%union {
    Node node;
    char* str;
    int num;
    AttributeData ad;
}
%type <node> article attribute attributes block blockintbl blocks blocksintbl boldnoitalics identlistblock
             bulletlistblock bulletlistline identlistline comment heading italicsnobold italicsorbold linketc
             listblock listseries numberlistblock numberlistline oneormorenewlines
             oneormorenewlinessave paragraph paragraphintbl pipeseries preblock
             preline table tablecell tablecellcontents tablecells tablerow tablerows text
             textelement textelementnoboit textelementnobold textelementnoital textelementinlink
             textnoboit textnobold textnoital textinlink textorempty zeroormorenewlines
             zeroormorenewlinessave textintbl textelementintbl textintmpl textelementintmpl
             template templatevar tablecaption
             TEXT EXTENSION
%type <ad>   ATTRIBUTE
%type <num>  HEADING ENDHEADING EQUALS ATTRAPO ATTRQ
             TABLEBEGIN TABLECELL TABLEHEAD TABLEROW TABLECAPTION

%token  EXTENSION BEGINCOMMENT TEXT ENDCOMMENT OPENLINK OPENDBLSQBR CLOSEDBLSQBR PIPE
        NEWLINE PRELINE LISTBULLET LISTNUMBERED LISTIDENT HEADING ENDHEADING APO5 APO3 APO2 TABLEBEGIN
        TABLECELL TABLEHEAD TABLEROW TABLEEND TABLECAPTION ATTRIBUTE EQUALS ATTRAPO ATTRQ
        OPENPENTUPLECURLY CLOSEPENTUPLECURLY OPENTEMPLATEVAR CLOSETEMPLATEVAR OPENTEMPLATE
        CLOSETEMPLATE

%start article

%%
/* rules */

    /* TODO:
        - optimise zeroormorenewlinessave (no need for Newlines nodes)
        - find all 'memcpy's and add a 'sizeof (char)' wherever necessary

       UNATTENDED-TO CAVEATS:
        - a row beginning with TABLEBEGIN but not containing valid table mark-up
          (e.g. "{| Hah!" + NEWLINE) is turned into a paragraph of its own even
          if it and the next line are separated by only one newline (so they should
          all be one paragraph).
    */

article         :   /* empty */                        { debugf ("article#1 "); $$ = articlenode = newNode (Article); }
                |   oneormorenewlines                  { debugf ("article#2 "); $$ = articlenode = newNode (Article); }
                |   blocks          { debugf ("article#3 "); $$ = articlenode = nodeAddChild (newNode (Article), $1); }

blocks          :   block                   { debugf ("blocks#1 "); $$ = $1; }
                |   blocks block            { debugf ("blocks#2 "); $$ = nodeAddSibling ($1, $2); }

blocksintbl     :   blockintbl              { debugf ("blocksintbl#1 "); $$ = $1; }
                |   blocksintbl blockintbl  { debugf ("blocksintbl#2 "); $$ = nodeAddSibling ($1, $2); }

block           :   preblock                        { debugf ("block#1 "); $$ = processPreBlock ($1); }
                |   heading zeroormorenewlines      { debugf ("block#2 "); $$ = $1; }
                |   listblock zeroormorenewlines    { debugf ("block#3 "); $$ = $1; }
                |   paragraph zeroormorenewlines    { debugf ("block#4 "); $$ = $1; }
                |   table zeroormorenewlines        { debugf ("block#5 "); $$ = $1; }
                |   comment zeroormorenewlines      { debugf ("block#6 "); $$ = $1; }

blockintbl      :   preblock                            { debugf ("blockintbl#1 "); $$ = processPreBlock ($1); }
                |   heading zeroormorenewlines          { debugf ("blockintbl#2 "); $$ = $1; }
                |   listblock zeroormorenewlines        { debugf ("blockintbl#3 "); $$ = $1; }
                |   paragraphintbl zeroormorenewlines   { debugf ("blockintbl#4 "); $$ = $1; }
                |   table zeroormorenewlines            { debugf ("blockintbl#5 "); $$ = $1; }
                |   comment zeroormorenewlines          { debugf ("blockintbl#6 "); $$ = $1; }

heading         :   HEADING text ENDHEADING
                        { debugf ("heading#1 "); $$ = nodeAddChild (newNodeI (Heading, $1), $2); }
                |   HEADING text  /* for eof */
                        { debugf ("heading#2 "); $$ = nodeAddChild (newNode (Paragraph), makeTextBlock (convertHeadingToText ($1), $2)); }
                |   HEADING
                        { debugf ("heading#3 "); $$ = nodeAddChild (newNode (Paragraph), convertHeadingToText ($1)); }

preblock        :   preline             { debugf ("preblock#1 "); $$ = nodeAddChild (newNode (PreBlock), $1); }
                |   preblock preline    { debugf ("preblock#2 "); $$ = nodeAddChild ($1, $2); }

preline         :   PRELINE textorempty zeroormorenewlinessave
                        { debugf ("preline#1 "); $$ = nodeAddChild2 (newNode (PreLine), $2, $3); }

listblock       :   bulletlistblock             { debugf ("listblock#1 "); $$ = processListBlock ($1); }
                |   numberlistblock             { debugf ("listblock#2 "); $$ = processListBlock ($1); }
                |   identlistblock              { debugf ("listblock#3 "); $$ = processListBlock ($1); }

bulletlistblock :   bulletlistline                  { debugf ("bulletlistblock#1 "); $$ = nodeAddChild (newNode (ListBlock), $1); }
                |   bulletlistblock bulletlistline  { debugf ("bulletlistblock#2 "); $$ = nodeAddChild ($1, $2); }
numberlistblock :   numberlistline                  { debugf ("numberlistblock#1 "); $$ = nodeAddChild (newNode (ListBlock), $1); }
                |   numberlistblock numberlistline  { debugf ("numberlistblock#2 "); $$ = nodeAddChild ($1, $2); }
identlistblock  :   identlistline                   { debugf ("identlistblock#1 "); $$ = nodeAddChild (newNode (ListBlock), $1); }
                |   identlistblock identlistline    { debugf ("identlistblock#2 "); $$ = nodeAddChild ($1, $2); }

bulletlistline  :   LISTBULLET listseries textorempty NEWLINE
                        { debugf ("bulletlistline#1 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListBullet)), $3); }
                |   LISTBULLET listseries textorempty
                        { debugf ("bulletlistline#2 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListBullet)), $3); }
numberlistline  :   LISTNUMBERED listseries textorempty NEWLINE
                        { debugf ("numberlistline#1 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListNumbered)), $3); }
                |   LISTNUMBERED listseries textorempty
                        { debugf ("numberlistline#2 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListNumbered)), $3); }
identlistline  :   LISTIDENT listseries textorempty NEWLINE
                        { debugf ("identlistline#1 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListIdent)), $3); }
                |   LISTIDENT listseries textorempty
                        { debugf ("identlistline#2 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListIdent)), $3); }

listseries      :   /* empty */                 { debugf ("listseries#1 "); $$ = newNode (ListLine); }
                |   LISTBULLET
                        { debugf ("listseries#2 "); $$ = nodeAddChild (newNode (ListLine), newNode (ListBullet)); }
                |   LISTNUMBERED
                        { debugf ("listseries#3 "); $$ = nodeAddChild (newNode (ListLine), newNode (ListNumbered)); }
                |   LISTIDENT
                        { debugf ("listseries#4 "); $$ = nodeAddChild (newNode (ListLine), newNode (ListIdent)); }
                |   listseries LISTBULLET       { debugf ("listseries#5 "); $$ = nodeAddChild ($1, newNode (ListBullet)); }
                |   listseries LISTNUMBERED     { debugf ("listseries#6 "); $$ = nodeAddChild ($1, newNode (ListNumbered)); }
                |   listseries LISTIDENT     { debugf ("listseries#6 "); $$ = nodeAddChild ($1, newNode (ListIdent)); }

linketc         :   OPENDBLSQBR textinlink CLOSEDBLSQBR
                        { debugf ("linketc#1 "); $$ = nodeAddChild (newNodeI (LinkEtc, 0), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENDBLSQBR textinlink PIPE CLOSEDBLSQBR
                        { debugf ("linketc#2 "); $$ = nodeAddChild (newNodeI (LinkEtc, 1), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENDBLSQBR textinlink pipeseries CLOSEDBLSQBR
                        { debugf ("linketc#3 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 0), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENDBLSQBR textinlink pipeseries PIPE CLOSEDBLSQBR
                        { debugf ("linketc#4 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 1), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENLINK textinlink CLOSEDBLSQBR
                        { debugf ("linketc#5 "); $$ = nodeAddChild (newNodeI (LinkEtc, 2), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENLINK textinlink PIPE CLOSEDBLSQBR
                        { debugf ("linketc#6 "); $$ = nodeAddChild (newNodeI (LinkEtc, 3), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENLINK textinlink pipeseries CLOSEDBLSQBR
                        { debugf ("linketc#7 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 2), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENLINK textinlink pipeseries PIPE CLOSEDBLSQBR
                        { debugf ("linketc#8 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 3), nodeAddChild (newNode (LinkTarget), $2), $3); }
                    /* ... and now everything again with the CLOSEDBLSQBR missing,
                     * to take care of invalid mark-up. */
                |   OPENDBLSQBR textinlink
                        { debugf ("linketc#9 "); $$ = makeTextBlock (newNodeS (TextToken, "[["), $2); }
                |   OPENDBLSQBR textinlink PIPE
                        { debugf ("linketc#10 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[["), $2, newNodeS (TextToken, "|")); }
                |   OPENDBLSQBR textinlink pipeseries
                        { debugf ("linketc#11 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[["), $2, convertPipeSeriesToText ($3)); }
                |   OPENDBLSQBR textinlink pipeseries PIPE
                        { debugf ("linketc#12 "); $$ = makeTextBlock3 (newNodeS (TextToken, "[["), $2, convertPipeSeriesToText ($3), newNodeS (TextToken, "|")); }
                |   OPENLINK textinlink
                        { debugf ("linketc#13 "); $$ = makeTextBlock (newNodeS (TextToken, "[[:"), $2); }
                |   OPENLINK textinlink PIPE
                        { debugf ("linketc#14 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[[:"), $2, newNodeS (TextToken, "|")); }
                |   OPENLINK textinlink pipeseries
                        { debugf ("linketc#15 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[[:"), $2, convertPipeSeriesToText ($3)); }
                |   OPENLINK textinlink pipeseries PIPE
                        { debugf ("linketc#16 "); $$ = makeTextBlock3 (newNodeS (TextToken, "[[:"), $2, convertPipeSeriesToText ($3), newNodeS (TextToken, "|")); }

pipeseries      :   PIPE textinlink               { debugf ("pipeseries#1 "); $$ = nodeAddChild (newNode (LinkOption), $2); }
                |   pipeseries PIPE textinlink    { debugf ("pipeseries#2 "); $$ = nodeAddSibling ($1, nodeAddChild (newNode (LinkOption), $3)); }

textorempty     :   /* empty */             { debugf ("textorempty#1 "); $$ = newNodeS (TextToken, ""); }
                |   text                    { debugf ("textorempty#2 "); $$ = $1; }

italicsorbold   :   APO2 textnoital APO2
                        { debugf ("italicsorbold#1 "); $$ = nodeAddChild (newNode (Italics), $2); }
                |   APO2 textnoital APO3 textnoboit APO5
                        { debugf ("italicsorbold#2 "); $$ = nodeAddChild (newNode (Italics),
                                makeTextBlock ($2, nodeAddChild (newNode (Bold), $4))); }
                |   APO2 textnoital APO3 textnoboit
                        { debugf ("italicsorbold#3 "); $$ =
                        makeTextBlock2 (nodeAddChild (newNode (Italics), $2), newNodeS (TextToken, "'"), $4); }
                |   APO2 textnoital
                        { debugf ("italicsorbold#4 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2); }
                |   APO3 textnobold APO3
                        { debugf ("italicsorbold#5 "); $$ = nodeAddChild (newNode (Bold), $2); }
                |   APO3 textnobold APO2 textnoboit APO5
                        { debugf ("italicsorbold#6 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock ($2, nodeAddChild (newNode (Italics), $4))); }
                /* Peculiar case, especially for French l'''homme'' => l'<italics>homme</italics> */
                /* We have to use textnobold here, even though textnoital would be logical. */
                /* We use processNestedItalics to fix the weirdness produced by this. */
                |   APO3 textnobold APO2 textnoboit
                        { debugf ("italicsorbold#7 "); $$ = processNestedItalics (makeTextBlock2 (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2), $4)); }
                |   APO3 textnobold APO2
                        { debugf ("italicsorbold#8 "); $$ = processNestedItalics (makeTextBlock (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2))); }
                |   APO3 textnobold
                        { debugf ("italicsorbold#9 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2); }
                |   APO5 textnoboit APO5
                        { debugf ("italicsorbold#10 "); $$ = nodeAddChild (newNode (Italics),
                            nodeAddChild (newNode (Bold), $2)); }
                |   APO5 textnoboit APO3 textnoital APO2
                        { debugf ("italicsorbold#11 "); $$ = nodeAddChild (newNode (Italics),
                            makeTextBlock (nodeAddChild (newNode (Bold), $2), $4)); }
                |   APO5 textnoboit APO3 textnoital
                        { debugf ("italicsorbold#12 "); $$ = makeTextBlock2 (newNodeS (TextToken, "''"),
                            nodeAddChild (newNode (Bold), $2), $4); }
                |   APO5 textnoboit APO3
                        { debugf ("italicsorbold#13 "); $$ = makeTextBlock (newNodeS (TextToken, "''"),
                            nodeAddChild (newNode (Bold), $2)); }
                |   APO5 textnoboit APO2 textnobold APO3
                        { debugf ("italicsorbold#14 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock (nodeAddChild (newNode (Italics), $2), $4)); }
                |   APO5 textnoboit APO2 textnobold
                        { debugf ("italicsorbold#15 "); $$ = makeTextBlock2 (newNodeS (TextToken, "'''"),
                            nodeAddChild (newNode (Italics), $2), $4); }
                |   APO5 textnoboit APO2
                        { debugf ("italicsorbold#16 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"),
                            nodeAddChild (newNode (Italics), $2)); }
                |   APO5 textnoboit
                        { debugf ("italicsorbold#17 ");
                            $$ = makeTextBlock (newNodeS (TextToken, "'''''"), $2); }

italicsnobold   :   APO2 textnoboit APO2
                        { debugf ("italicsnobold#1 "); $$ = nodeAddChild (newNode (Italics), $2); }
                |   APO2 textnoboit
                        { debugf ("italicsnobold#2 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2); }

boldnoitalics   :   APO3 textnoboit APO3
                        { debugf ("boldnoitalics#1 "); $$ = nodeAddChild (newNode (Bold), $2); }
                |   APO3 textnoboit
                        { debugf ("boldnoitalics#2 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2); }

table           :   TABLEBEGIN attributes tablerows TABLEEND
                        { debugf ("table#1 "); $$ = nodeAddChild2 (newNode (Table), $2, $3); }
                |   TABLEBEGIN attributes tablerows
                        { debugf ("table#2 "); $$ = nodeAddChild2 (newNode (Table), $2, $3); }
                |   TABLEBEGIN attributes oneormorenewlines tablerows TABLEEND
                        { debugf ("table#3 "); $$ = nodeAddChild2 (newNode (Table), $2, $4); }
                |   TABLEBEGIN attributes oneormorenewlines tablerows
                        { debugf ("table#4 "); $$ = nodeAddChild2 (newNode (Table), $2, $4); }
                |   TABLEBEGIN tablerows TABLEEND
                        { debugf ("table#5 "); $$ = nodeAddChild (newNode (Table), $2); }
                |   TABLEBEGIN tablerows
                        { debugf ("table#6 "); $$ = nodeAddChild (newNode (Table), $2); }
                |   TABLEBEGIN oneormorenewlines tablerows TABLEEND
                        { debugf ("table#7 "); $$ = nodeAddChild (newNode (Table), $3); }
                |   TABLEBEGIN oneormorenewlines tablerows
                        { debugf ("table#8 "); $$ = nodeAddChild (newNode (Table), $3); }
                /* and now some invalid mark-up catering ... */
                |   TABLEBEGIN attributes zeroormorenewlines
                        { debugf ("table#9 "); $$ = nodeAddChild (newNode (Paragraph),
                            makeTextBlock (newNodeS (TextToken, addSpaces ("{|", $1)),
                                           convertAttributesToText ($2))); }
                |   TABLEBEGIN attributes text zeroormorenewlines
                        { debugf ("table#10 "); $$ = nodeAddChild (newNode (Paragraph),
                            makeTextBlock2 (newNodeS (TextToken, addSpaces ("{|", $1)),
                                            convertAttributesToText ($2), $3)); }
                |   TABLEBEGIN text zeroormorenewlines
                        { debugf ("table#11 "); $$ = nodeAddChild (newNode (Paragraph),
                            makeTextBlock (newNodeS (TextToken, addSpaces ("{|", $1)), $3)); }
                |   TABLEBEGIN oneormorenewlines
                        { debugf ("table#12 "); $$ = nodeAddChild (newNode (Paragraph),
                            newNodeS (TextToken, addSpaces ("{|", $1))); }

tablerows       :   tablerow                { debugf ("tablerows#1 "); $$ = $1; }
                |   tablerows tablerow      { debugf ("tablerows#2 "); $$ = nodeAddSibling ($1, $2); }

tablerow        :   TABLEROW attributes tablecells
                        { debugf ("tablerow#1 "); $$ = nodeAddChild2 (newNode (TableRow), $2, $3); }
                |   TABLEROW tablecells
                        { debugf ("tablerow#2 "); $$ = nodeAddChild (newNode (TableRow), $2); }
                |   TABLEROW attributes oneormorenewlines tablecells
                        { debugf ("tablerow#3 "); $$ = nodeAddChild2 (newNode (TableRow), $2, $4); }
                |   TABLEROW oneormorenewlines tablecells
                        { debugf ("tablerow#4 "); $$ = nodeAddChild (newNode (TableRow), $3); }
                /* It is possible for the first table row to have no TABLEROW token */
                |   tablecells
                        { debugf ("tablerow#5 "); $$ = nodeAddChild (newNode (TableRow), $1); }
                /* Some invalid mark-up catering... */
                |   TABLEROW attributes oneormorenewlines
                        { debugf ("tablerow#6 "); freeRecursivelyWithSiblings ($2); $$ = 0; }
                |   TABLEROW attributes
                        { debugf ("tablerow#7 "); freeRecursivelyWithSiblings ($2); $$ = 0; }
                |   TABLEROW oneormorenewlines
                        { debugf ("tablerow#8 "); $$ = 0; }
                |   TABLEROW
                        { debugf ("tablerow#9 "); $$ = 0; }
                |   tablecaption
                        { debugf ("tablerow#10 "); $$ = $1; }

tablecells      :   tablecell               { debugf ("tablecells#1 "); $$ = $1; }
                |   tablecells tablecell    { debugf ("tablecells#2 "); $$ = nodeAddSibling ($1, $2); }

tablecell       :   TABLECELL attributes PIPE tablecellcontents
                        { debugf ("tablecell#1 "); $$ = nodeAddChild2 (newNode (TableCell), $2, processTableCellContents ($4)); }
                |   TABLECELL tablecellcontents
                        { debugf ("tablecell#2 "); $$ = nodeAddChild (newNode (TableCell), processTableCellContents ($2)); }
                |   TABLECELL attributes PIPE oneormorenewlines
                        { debugf ("tablecell#3 "); $$ = nodeAddChild (newNode (TableCell), $2); }
                |   TABLECELL attributes PIPE
                        { debugf ("tablecell#4 "); $$ = nodeAddChild (newNode (TableCell), $2); }
                |   TABLECELL oneormorenewlines
                        { debugf ("tablecell#5 "); $$ = newNode (TableCell); }
                |   TABLECELL
                        { debugf ("tablecell#6 "); $$ = newNode (TableCell); }
                |   TABLEHEAD attributes PIPE tablecellcontents
                        { debugf ("tablecell#7 "); $$ = nodeAddChild2 (newNode (TableHead), $2, processTableCellContents ($4)); }
                |   TABLEHEAD tablecellcontents
                        { debugf ("tablecell#8 "); $$ = nodeAddChild (newNode (TableHead), processTableCellContents ($2)); }
                |   TABLEHEAD attributes PIPE oneormorenewlines
                        { debugf ("tablecell#9 "); $$ = nodeAddChild (newNode (TableHead), $2); }
                |   TABLEHEAD attributes PIPE
                        { debugf ("tablecell#10 "); $$ = nodeAddChild (newNode (TableHead), $2); }
                |   TABLEHEAD oneormorenewlines
                        { debugf ("tablecell#11 "); $$ = newNode (TableHead); }
                |   TABLEHEAD
                        { debugf ("tablecell#12 "); $$ = newNode (TableHead); }

tablecellcontents   :   blocksintbl
                            { debugf ("tablecellcontents#1 "); $$ = $1; }
                    |   oneormorenewlines blocksintbl
                            { debugf ("tablecellcontents#2 "); $$ = $2; }

tablecaption    :   TABLECAPTION attributes PIPE textintbl
                        { debugf ("tablecaption#1 "); $$ = nodeAddChild2 (newNode (TableCaption), $2, $4); }
                |   TABLECAPTION attributes textintbl
                        { debugf ("tablecaption#2 "); $$ = nodeAddChild (newNode (TableCaption), makeTextBlock (convertAttributesToText ($2), $3)); }
                |   TABLECAPTION textintbl
                        { debugf ("tablecaption#3 "); $$ = nodeAddChild (newNode (TableCaption), $2); }
                |   TABLECAPTION attributes PIPE
                        { debugf ("tablecaption#4 "); $$ = nodeAddChild (newNode (TableCaption), makeTextBlock (convertAttributesToText ($2), newNodeS (TextToken, "|"))); }
                |   TABLECAPTION attributes
                        { debugf ("tablecaption#5 "); $$ = nodeAddChild (newNode (TableCaption), convertAttributesToText ($2)); }
                |   TABLECAPTION
                        { debugf ("tablecaption#6 "); $$ = 0; }

/* In order to reduce the second one (ATTRIBUTE EQUALS TEXT) correctly, this rule must
 * be further up than textelement. */
attribute       :   ATTRIBUTE
                        { debugf ("attribute#1 "); $$ = newNodeA (0, $1, 0, 0); }
                |   ATTRIBUTE EQUALS TEXT
                        { debugf ("attribute#2 "); $$ = nodeAddChild (newNodeA (1, $1, $2, strtrimNC ($3)), $3); }
                |   ATTRIBUTE EQUALS ATTRAPO text ATTRAPO
                        { debugf ("attribute#3 "); $$ = nodeAddChild (newNodeA (2, $1, $2, $5), $4); }
                |   ATTRIBUTE EQUALS ATTRQ text ATTRQ
                        { debugf ("attribute#4 "); $$ = nodeAddChild (newNodeA (3, $1, $2, $5), $4); }
                |   ATTRIBUTE EQUALS ATTRQ ATTRQ
                        { debugf ("attribute#5 "); $$ = newNodeA (3, $1, $2, $4); }
                |   ATTRIBUTE EQUALS
                        { debugf ("attribute#6 "); $$ = newNodeA (1, $1, $2, 0); }

attributes      :   attribute                { debugf ("attributes#1 "); $$ = nodeAddChild (newNode (AttributeGroup), $1); }
                |   attributes attribute     { debugf ("attributes#2 "); $$ = nodeAddChild ($1, $2); }

text            :   textelement                     { debugf ("text#1 "); $$ = $1; }
                |   text textelement                { debugf ("text#2 "); $$ = makeTextBlock ($1, $2); }
textnoital      :   textelementnoital               { debugf ("textnoital#1 "); $$ = $1; }
                |   textnoital textelementnoital    { debugf ("textnoital#2 "); $$ = makeTextBlock ($1, $2); }
textnobold      :   textelementnobold               { debugf ("textnobold#1 "); $$ = $1; }
                |   textnobold textelementnobold    { debugf ("textnobold#2 "); $$ = makeTextBlock ($1, $2); }
textnoboit      :   textelementnoboit               { debugf ("textnoboit#1 "); $$ = $1; }
                |   textnoboit textelementnoboit    { debugf ("textnoboit#2 "); $$ = makeTextBlock ($1, $2); }
textintbl       :   textelementintbl                { debugf ("textintbl#1 "); $$ = $1; }
                |   textintbl textelementintbl      { debugf ("textintbl#2 "); $$ = makeTextBlock ($1, $2); }
textinlink      :   textelementinlink               { debugf ("textinlink#1 "); $$ = $1; }
                |   textinlink textelementinlink    { debugf ("textinlink#2 "); $$ = makeTextBlock ($1, $2); }
textintmpl      :   textelementintmpl               { debugf ("textintmpl#1 "); $$ = $1; }
                |   textintmpl textelementintmpl    { debugf ("textintmpl#2 "); $$ = makeTextBlock ($1, $2); }

textelement         :   TEXT                { debugf ("textelement#1 "); $$ = $1; }
                    |   EXTENSION           { debugf ("textelement#2 "); $$ = $1; }
                    |   PIPE                { debugf ("textelement#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   CLOSEDBLSQBR        { debugf ("textelement#4 "); $$ = newNodeS (TextToken, "]]"); }
                    |   APO2                { debugf ("textelement#5 "); $$ = newNodeS (TextToken, "''"); }
                    |   APO3                { debugf ("textelement#6 "); $$ = newNodeS (TextToken, "'''"); }
                    |   APO5                { debugf ("textelement#7 "); $$ = newNodeS (TextToken, "'''''"); }
                    |   EQUALS              { debugf ("textelement#8 "); $$ = newNodeS (TextToken, addSpaces ("=", $1)); }
                    |   TABLEBEGIN          { debugf ("textelement#9 "); $$ = newNodeS (TextToken, addSpaces ("    {|", $1)); }
                    |   TABLEEND            { debugf ("textelement#10 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW            { debugf ("textelement#11 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL           { debugf ("textelement#12 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD           { debugf ("textelement#13 "); $$ = convertTableHeadToText ($1); }
                    |   TABLECAPTION        { debugf ("textelement#14 "); $$ = convertTableCaptionToText ($1); }
                    |   ATTRIBUTE           { debugf ("textelement#15 "); $$ = convertAttributeDataToText ($1); }
                    |   CLOSEPENTUPLECURLY  { debugf ("textelement#16 "); $$ = newNodeS (TextToken, "}}}}}"); }
                    |   CLOSETEMPLATEVAR    { debugf ("textelement#17 "); $$ = newNodeS (TextToken, "}}}"); }
                    |   CLOSETEMPLATE       { debugf ("textelement#18 "); $$ = newNodeS (TextToken, "}}"); }
                    |   comment             { debugf ("textelement#19 "); $$ = $1; }
                    |   linketc             { debugf ("textelement#20 "); $$ = $1; }
                    |   italicsorbold       { debugf ("textelement#21 "); $$ = $1; }
                    |   template            { debugf ("textelement#22 "); $$ = $1; }
                    |   templatevar         { debugf ("textelement#23 "); $$ = $1; }

textelementnoital   :   TEXT                { debugf ("textelementnoital#1 "); $$ = $1; }
                    |   EXTENSION           { debugf ("textelementnoital#2 "); $$ = $1; }
                    |   PIPE                { debugf ("textelementnoital#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   CLOSEDBLSQBR        { debugf ("textelementnoital#4 "); $$ = newNodeS (TextToken, "]]"); }
                    |   TABLEBEGIN          { debugf ("textelementnoital#5 "); $$ = newNodeS (TextToken, addSpaces ("    {|", $1)); }
                    |   TABLEEND            { debugf ("textelementnoital#6 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW            { debugf ("textelementnoital#7 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL           { debugf ("textelementnoital#8 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD           { debugf ("textelementnoital#9 "); $$ = convertTableHeadToText ($1); }
                    |   TABLECAPTION        { debugf ("textelementnoital#10 "); $$ = convertTableCaptionToText ($1); }
                    |   ATTRIBUTE           { debugf ("textelementnoital#11 "); $$ = convertAttributeDataToText ($1); }
                    |   CLOSEPENTUPLECURLY  { debugf ("textelementnoital#12 "); $$ = newNodeS (TextToken, "}}}}}"); }
                    |   CLOSETEMPLATEVAR    { debugf ("textelementnoital#13 "); $$ = newNodeS (TextToken, "}}}"); }
                    |   CLOSETEMPLATE       { debugf ("textelementnoital#14 "); $$ = newNodeS (TextToken, "}}"); }
                    |   comment             { debugf ("textelementnoital#15 "); $$ = $1; }
                    |   linketc             { debugf ("textelementnoital#16 "); $$ = $1; }
                    |   boldnoitalics       { debugf ("textelementnoital#17 "); $$ = $1; }
                    |   template            { debugf ("textelementnoital#18 "); $$ = $1; }
                    |   templatevar         { debugf ("textelementnoital#19 "); $$ = $1; }

textelementnobold   :   TEXT                { debugf ("textelementnobold#1 "); $$ = $1; }
                    |   EXTENSION           { debugf ("textelementnobold#2 "); $$ = $1; }
                    |   PIPE                { debugf ("textelementnobold#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   CLOSEDBLSQBR        { debugf ("textelementnobold#4 "); $$ = newNodeS (TextToken, "]]"); }
                    |   TABLEBEGIN          { debugf ("textelementnobold#5 "); $$ = newNodeS (TextToken, addSpaces ("    {|", $1)); }
                    |   TABLEEND            { debugf ("textelementnobold#6 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW            { debugf ("textelementnobold#7 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL           { debugf ("textelementnobold#8 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD           { debugf ("textelementnobold#9 "); $$ = convertTableHeadToText ($1); }
                    |   TABLECAPTION        { debugf ("textelementnobold#10 "); $$ = convertTableCaptionToText ($1); }
                    |   ATTRIBUTE           { debugf ("textelementnobold#11 "); $$ = convertAttributeDataToText ($1); }
                    |   CLOSEPENTUPLECURLY  { debugf ("textelementnobold#12 "); $$ = newNodeS (TextToken, "}}}}}"); }
                    |   CLOSETEMPLATEVAR    { debugf ("textelementnobold#13 "); $$ = newNodeS (TextToken, "}}}"); }
                    |   CLOSETEMPLATE       { debugf ("textelementnobold#14 "); $$ = newNodeS (TextToken, "}}"); }
                    |   comment             { debugf ("textelementnobold#15 "); $$ = $1; }
                    |   linketc             { debugf ("textelementnobold#16 "); $$ = $1; }
                    |   italicsnobold       { debugf ("textelementnobold#17 "); $$ = $1; }
                    |   template            { debugf ("textelementnobold#18 "); $$ = $1; }
                    |   templatevar         { debugf ("textelementnobold#19 "); $$ = $1; }

textelementnoboit   :   TEXT                { debugf ("textelementnoboit#1 "); $$ = $1; }
                    |   EXTENSION           { debugf ("textelementnoboit#2 "); $$ = $1; }
                    |   PIPE                { debugf ("textelementnoboit#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   CLOSEDBLSQBR        { debugf ("textelementnoboit#4 "); $$ = newNodeS (TextToken, "]]"); }
                    |   TABLEBEGIN          { debugf ("textelementnoboit#5 "); $$ = newNodeS (TextToken, addSpaces ("    {|", $1)); }
                    |   TABLEEND            { debugf ("textelementnoboit#6 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW            { debugf ("textelementnoboit#7 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL           { debugf ("textelementnoboit#8 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD           { debugf ("textelementnoboit#9 "); $$ = convertTableHeadToText ($1); }
                    |   TABLECAPTION        { debugf ("textelementnoboit#10 "); $$ = convertTableCaptionToText ($1); }
                    |   ATTRIBUTE           { debugf ("textelementnoboit#11 "); $$ = convertAttributeDataToText ($1); }
                    |   CLOSEPENTUPLECURLY  { debugf ("textelementnobold#12 "); $$ = newNodeS (TextToken, "}}}}}"); }
                    |   CLOSETEMPLATEVAR    { debugf ("textelementnobold#13 "); $$ = newNodeS (TextToken, "}}}"); }
                    |   CLOSETEMPLATE       { debugf ("textelementnobold#14 "); $$ = newNodeS (TextToken, "}}"); }
                    |   comment             { debugf ("textelementnoboit#15 "); $$ = $1; }
                    |   linketc             { debugf ("textelementnoboit#16 "); $$ = $1; }
                    |   template            { debugf ("textelementnoboit#17 "); $$ = $1; }
                    |   templatevar         { debugf ("textelementnoboit#18 "); $$ = $1; }

textelementintbl    :   TEXT                { debugf ("textelementintbl#1 "); $$ = $1; }
                    |   EXTENSION           { debugf ("textelementintbl#2 "); $$ = $1; }
                    |   PIPE                { debugf ("textelementintbl#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   CLOSEDBLSQBR        { debugf ("textelementintbl#4 "); $$ = newNodeS (TextToken, "]]"); }
                    |   APO2                { debugf ("textelementintbl#5 "); $$ = newNodeS (TextToken, "''"); }
                    |   APO3                { debugf ("textelementintbl#6 "); $$ = newNodeS (TextToken, "'''"); }
                    |   APO5                { debugf ("textelementintbl#7 "); $$ = newNodeS (TextToken, "'''''"); }
                    |   EQUALS              { debugf ("textelementintbl#8 "); $$ = newNodeS (TextToken, addSpaces ("=", $1)); }
                    |   CLOSEPENTUPLECURLY  { debugf ("textelementintbl#9 "); $$ = newNodeS (TextToken, "}}}}}"); }
                    |   CLOSETEMPLATEVAR    { debugf ("textelementintbl#10 "); $$ = newNodeS (TextToken, "}}}"); }
                    |   CLOSETEMPLATE       { debugf ("textelementintbl#11 "); $$ = newNodeS (TextToken, "}}"); }
                    |   comment             { debugf ("textelementintbl#12 "); $$ = $1; }
                    |   linketc             { debugf ("textelementintbl#13 "); $$ = $1; }
                    |   italicsorbold       { debugf ("textelementintbl#14 "); $$ = $1; }
                    |   template            { debugf ("textelementintbl#15 "); $$ = $1; }
                    |   templatevar         { debugf ("textelementintbl#16 "); $$ = $1; }

textelementinlink   :   TEXT                { debugf ("textelementinlink#1 "); $$ = $1; }
                    |   EXTENSION           { debugf ("textelementinlink#2 "); $$ = $1; }
                    |   APO2                { debugf ("textelementinlink#3 "); $$ = newNodeS (TextToken, "''"); }
                    |   APO3                { debugf ("textelementinlink#4 "); $$ = newNodeS (TextToken, "'''"); }
                    |   APO5                { debugf ("textelementinlink#5 "); $$ = newNodeS (TextToken, "'''''"); }
                    |   EQUALS              { debugf ("textelementinlink#6 "); $$ = newNodeS (TextToken, addSpaces ("=", $1)); }
                    |   TABLEBEGIN          { debugf ("textelementinlink#7 "); $$ = newNodeS (TextToken, addSpaces ("    {|", $1)); }
                    |   TABLEEND            { debugf ("textelementinlink#8 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW            { debugf ("textelementinlink#9 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL           { debugf ("textelementinlink#10 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD           { debugf ("textelementinlink#11 "); $$ = convertTableHeadToText ($1); }
                    |   TABLECAPTION        { debugf ("textelementinlink#12 "); $$ = convertTableCaptionToText ($1); }
                    |   ATTRIBUTE           { debugf ("textelementinlink#13 "); $$ = convertAttributeDataToText ($1); }
                    |   CLOSEPENTUPLECURLY  { debugf ("textelementinlink#14 "); $$ = newNodeS (TextToken, "}}}}}"); }
                    |   CLOSETEMPLATEVAR    { debugf ("textelementinlink#15 "); $$ = newNodeS (TextToken, "}}}"); }
                    |   CLOSETEMPLATE       { debugf ("textelementinlink#16 "); $$ = newNodeS (TextToken, "}}"); }
                    |   comment             { debugf ("textelementinlink#17 "); $$ = $1; }
                    |   linketc             { debugf ("textelementinlink#18 "); $$ = $1; }
                    |   italicsorbold       { debugf ("textelementinlink#19 "); $$ = $1; }
                    |   template            { debugf ("textelementinlink#20 "); $$ = $1; }
                    |   templatevar         { debugf ("textelementinlink#21 "); $$ = $1; }

textelementintmpl   :   TEXT                { debugf ("textelementintmpl#1 "); $$ = $1; }
                    |   EXTENSION           { debugf ("textelementintmpl#2 "); $$ = $1; }
                    |   PIPE                { debugf ("textelementintmpl#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   CLOSEDBLSQBR        { debugf ("textelementintmpl#4 "); $$ = newNodeS (TextToken, "]]"); }
                    |   APO2                { debugf ("textelementintmpl#5 "); $$ = newNodeS (TextToken, "''"); }
                    |   APO3                { debugf ("textelementintmpl#6 "); $$ = newNodeS (TextToken, "'''"); }
                    |   APO5                { debugf ("textelementintmpl#7 "); $$ = newNodeS (TextToken, "'''''"); }
                    |   EQUALS              { debugf ("textelementintmpl#8 "); $$ = newNodeS (TextToken, addSpaces ("=", $1)); }
                    |   TABLEBEGIN          { debugf ("textelementintmpl#9 "); $$ = newNodeS (TextToken, addSpaces ("    {|", $1)); }
                    |   TABLEEND            { debugf ("textelementintmpl#10 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW            { debugf ("textelementintmpl#11 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL           { debugf ("textelementintmpl#12 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD           { debugf ("textelementintmpl#13 "); $$ = convertTableHeadToText ($1); }
                    |   TABLECAPTION        { debugf ("textelementintmpl#14 "); $$ = convertTableCaptionToText ($1); }
                    |   ATTRIBUTE           { debugf ("textelementintmpl#15 "); $$ = convertAttributeDataToText ($1); }
                    |   comment             { debugf ("textelementintmpl#16 "); $$ = $1; }
                    |   linketc             { debugf ("textelementintmpl#17 "); $$ = $1; }
                    |   italicsorbold       { debugf ("textelementintmpl#18 "); $$ = $1; }
                    |   template            { debugf ("textelementintmpl#19 "); $$ = $1; }
                    |   templatevar         { debugf ("textelementintmpl#20 "); $$ = $1; }

template            :   OPENTEMPLATE textintmpl CLOSETEMPLATE
                            { debugf ("template#1 "); $$ = nodeAddChild (newNode (Template), $2); }
                    |   OPENPENTUPLECURLY textintmpl CLOSETEMPLATEVAR textintmpl CLOSETEMPLATE
                            { debugf ("template#2 "); $$ = nodeAddChild (newNode (Template),
                                makeTextBlock (nodeAddChild (newNode (TemplateVar), $2), $4)); }
                    |   OPENTEMPLATE textintmpl OPENTEMPLATEVAR textintmpl CLOSEPENTUPLECURLY
                            { debugf ("template#3 "); $$ = nodeAddChild (newNode (Template),
                                makeTextBlock ($2, nodeAddChild (newNode (TemplateVar), $4))); }
                    /* cater for invalid mark-up... */
                    |   OPENTEMPLATE textintmpl
                            { debugf ("template#4 "); $$ = makeTextBlock (newNodeS (TextToken, "{{"), $2); }
                    |   OPENPENTUPLECURLY textintmpl CLOSETEMPLATEVAR textintmpl
                            { debugf ("template#5 "); $$ = makeTextBlock3 (newNodeS (TextToken, "{{{{{"),
                                $2, newNodeS (TextToken, "}}}"), $4); }
                    |   OPENTEMPLATE textintmpl OPENTEMPLATEVAR textintmpl
                            { debugf ("template#6 "); $$ = makeTextBlock3 (newNodeS (TextToken, "{{"),
                                $2, newNodeS (TextToken, "{{{"), $4); }

templatevar         :   OPENTEMPLATEVAR textintmpl CLOSETEMPLATEVAR
                            { debugf ("templatevar#1 "); $$ = nodeAddChild (newNode (TemplateVar), $2); }
                    |   OPENPENTUPLECURLY textintmpl CLOSEPENTUPLECURLY
                            { debugf ("templatevar#2 "); $$ =
                                nodeAddChild (newNode (Template), nodeAddChild (newNode (TemplateVar), $2)); }
                    /* cater for invalid mark-up... */
                    |   OPENTEMPLATEVAR textintmpl
                            { debugf ("templatevar#1 "); $$ = makeTextBlock (newNodeS (TextToken, "{{{"), $2); }
                    |   OPENPENTUPLECURLY textintmpl
                            { debugf ("templatevar#2 "); $$ = makeTextBlock (newNodeS (TextToken, "{{{{{"), $2); }

zeroormorenewlines  :   /* empty */                 { debugf ("zeroormorenewlines#1 "); $$ = 0; }
                    |   oneormorenewlines           { debugf ("zeroormorenewlines#2 "); $$ = 0; }
oneormorenewlines   :   NEWLINE                     { debugf ("oneormorenewlines#1 "); $$ = 0; }
                    |   oneormorenewlines NEWLINE   { debugf ("oneormorenewlines#2 "); $$ = 0; }

zeroormorenewlinessave  :   /* empty */                     { debugf ("zeroormorenewlinessave#1 "); $$ = 0; }
                        |   oneormorenewlinessave           { debugf ("zeroormorenewlinessave#2 "); $$ = $1; }
oneormorenewlinessave   :   NEWLINE                         { debugf ("oneormorenewlinessave#1 "); $$ = newNodeI (Newlines, 0); }
                        |   oneormorenewlinessave NEWLINE   { debugf ("oneormorenewlinessave#2 "); $1->data.num++; $$ = $1; }

paragraph       :   text NEWLINE
                        { debugf ("paragraph#1 "); $$ = nodeAddChild (newNode (Paragraph), $1); }
                |   paragraph text NEWLINE
                        { debugf ("paragraph#2 "); $$ = nodeAddChild2 ($1, newNodeS (TextToken, " "), $2); }
                /* for eof ... */
                |   text
                        { debugf ("paragraph#3 "); $$ = nodeAddChild (newNode (Paragraph), $1); }
                |   paragraph text
                        { debugf ("paragraph#4 "); $$ = nodeAddChild2 ($1, newNodeS (TextToken, " "), $2); }

/* This seemingly pointless inclusion of 'attributes' here that will all be converted to text
 * by way of convertAttributesToText() is necessary because, as a table cell begins, we simply
 * don't know whether there are attributes following or not. We parse them as attributes first,
 * but then convert them back to text if it turns out they're not. */
paragraphintbl  :   textintbl NEWLINE
                        { debugf ("paragraphintbl#1 "); $$ = nodeAddChild (newNode (Paragraph), $1); }
                |   attributes textintbl NEWLINE
                        { debugf ("paragraphintbl#2 "); $$ = nodeAddChild2 (newNode (Paragraph), convertAttributesToText ($1), $2); }
                |   attributes NEWLINE
                        { debugf ("paragraphintbl#3 "); $$ = nodeAddChild (newNode (Paragraph), convertAttributesToText ($1)); }
                |   paragraphintbl textintbl NEWLINE
                        { debugf ("paragraphintbl#4 "); $$ = nodeAddChild2 ($1, newNodeS (TextToken, " "), $2); }
                |   paragraphintbl attributes textintbl NEWLINE
                        { debugf ("paragraphintbl#5 "); $$ = nodeAddChild3 ($1, newNodeS (TextToken, " "), convertAttributesToText ($2), $3); }
                |   paragraphintbl attributes NEWLINE
                        { debugf ("paragraphintbl#6 "); $$ = nodeAddChild2 ($1, newNodeS (TextToken, " "), convertAttributesToText ($2)); }
                /* for eof ... */
                |   textintbl
                        { debugf ("paragraphintbl#7 "); $$ = nodeAddChild (newNode (Paragraph), $1); }
                |   attributes textintbl
                        { debugf ("paragraphintbl#8 "); $$ = nodeAddChild2 (newNode (Paragraph), convertAttributesToText ($1), $2); }
                |   attributes
                        { debugf ("paragraphintbl#9 "); $$ = nodeAddChild (newNode (Paragraph), convertAttributesToText ($1)); }
                |   paragraphintbl textintbl
                        { debugf ("paragraphintbl#10 "); $$ = nodeAddChild2 ($1, newNodeS (TextToken, " "), $2); }
                |   paragraphintbl attributes textintbl
                        { debugf ("paragraphintbl#11 "); $$ = nodeAddChild3 ($1, newNodeS (TextToken, " "), convertAttributesToText ($2), $3); }
                |   paragraphintbl attributes
                        { debugf ("paragraphintbl#12 "); $$ = nodeAddChild2 ($1, newNodeS (TextToken, " "), convertAttributesToText ($2)); }

comment         :   BEGINCOMMENT text ENDCOMMENT
                        { debugf ("comment#1 "); $$ = nodeAddChild (newNode (Comment), $2); }
                |   BEGINCOMMENT ENDCOMMENT
                        { debugf ("comment#2 "); $$ = newNode (Comment); }


%%

/* main() -- this is called whenever you invoke the parser from the command line. You probably
 * do that only to test it, which is why we are outputting some extra information. It reads
 * standard input and writes to standard output. */
int main() {
    int result;
//    printf ("Parsing... ");
    result = yyparse();
    if (!result) printf ( outputXML (articlenode, 1024) ) ;
//        printf ("\n\nXML output:\n\n%s\n\n", outputXML (articlenode, 1024));
    freeRecursively (articlenode);
    return result;
}

/* wikiparse_do_parse() -- this is the function that is actually called by PHP. It uses an
 * input string, and returns an output string. No stdin/stdout. */
const char* wikiparse_do_parse (const char* input)
{
    int result, i;
    char* ret = "<error />";

    /* yy_scan_string copies the string into an internal buffer. During lexing, this internal
     * buffer may be modified. We don't really need the string anymore, so we probably don't mind
     * if it's modified, so we might not need for it to be copied. There is yy_scan_buffer which
     * uses the string directly as a buffer, but for some bizarre reason it expects the buffer to
     * end with *two* NULs instead of just one. Thus yy_scan_string is the easiest way for now. */
    yy_scan_string (input);

    result = yyparse();
    if (!result)
    {
        /* Start with an output buffer twice the size of the input, but at least 1 KB. This should
         * normally be plenty. If it isn't, it will grow automatically. */
        i = 2*strlen (input);
        ret = outputXML (articlenode, i < 1024 ? 1024 : i);
        freeRecursively (articlenode);
    }
    return ret;
}

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
%type <node> article blocks block paragraph heading textorempty zeroormorenewlines preblock
             oneormorenewlines preline bulletlistline numberlistline listseries listblock
             zeroormorenewlinessave oneormorenewlinessave bulletlistblock numberlistblock
             textelement textelementnoboit textelementnobold textelementnoital italicsorbold
             textnoboit textnobold textnoital boldnoitalics italicsnobold linketc pipeseries
             text attribute attributes tablecells tablecell tablecellcontents tablerows
             tablerow table comment blocksnotbl blocknotbl textnoitaltbl textnoboldtbl
             textnoboittbl textnotbl textelementnotbl textelementnoboldtbl textelementnoitaltbl
             textelementnoboittbl paragraphnotbl linketcnotbl italorboldnotbl boldnoitalicstbl
             italicsnoboldtbl pipeseriesnotbl textelementnoblittbp textelementnobltbpp
             textelementnoittbpp textelementnotblpipe textelementnobitp textelementnobldp
             textelementnoitp textelementnopipe textnoboittblpipe textnoboldtblpipe
             textnoitaltblpipe textnotblpipe textnoboitpipe textnoboldpipe textnoitalpipe
             textnopipe italorboldnopipe italorboldnotblp boldnoitalicspp boldnoitaltblpp
             italicsnoboldpp italnoboldtblpp
             TEXT EXTENSION
%type <ad>   ATTRIBUTE
%type <num>  HEADING ENDHEADING TABLEBEGIN TABLECELL TABLEHEAD TABLEROW EQUALS ATTRAPO ATTRQ

%token  EXTENSION BEGINCOMMENT TEXT ENDCOMMENT OPENLINK OPENDBLSQBR CLOSEDBLSQBR PIPE
        NEWLINE PRELINE LISTBULLET LISTNUMBERED HEADING ENDHEADING APO5 APO3 APO2 TABLEBEGIN
        TABLECELL TABLEHEAD TABLEROW TABLEEND ATTRIBUTE EQUALS ATTRAPO ATTRQ
        // Not yet used:
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

blocksnotbl     :   blocknotbl              { debugf ("blocksnotbl#1 "); $$ = $1; }
                |   blocksnotbl blocknotbl  { debugf ("blocksnotbl#2 "); $$ = nodeAddSibling ($1, $2); }

block           :   preblock                        { debugf ("block#1 "); $$ = processPreBlock ($1); }
                |   heading zeroormorenewlines      { debugf ("block#2 "); $$ = $1; }
                |   listblock zeroormorenewlines    { debugf ("block#3 "); $$ = $1; }
                |   paragraph zeroormorenewlines    { debugf ("block#4 "); $$ = $1; }
                |   table zeroormorenewlines        { debugf ("block#5 "); $$ = $1; }
                |   comment zeroormorenewlines      { debugf ("block#6 "); $$ = $1; }

blocknotbl      :   preblock                            { debugf ("blocknotbl#1 "); $$ = processPreBlock ($1); }
                |   heading zeroormorenewlines          { debugf ("blocknotbl#2 "); $$ = $1; }
                |   listblock zeroormorenewlines        { debugf ("blocknotbl#3 "); $$ = $1; }
                |   paragraphnotbl zeroormorenewlines   { debugf ("blocknotbl#4 "); $$ = $1; }
                |   table zeroormorenewlines            { debugf ("blocknotbl#5 "); $$ = $1; }
                |   comment zeroormorenewlines          { debugf ("blocknotbl#6 "); $$ = $1; }

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

bulletlistblock :   bulletlistline                  { debugf ("bulletlistblock#1 "); $$ = nodeAddChild (newNode (ListBlock), $1); }
                |   bulletlistblock bulletlistline  { debugf ("bulletlistblock#2 "); $$ = nodeAddChild ($1, $2); }
numberlistblock :   numberlistline                  { debugf ("numberlistblock#1 "); $$ = nodeAddChild (newNode (ListBlock), $1); }
                |   numberlistblock numberlistline  { debugf ("numberlistblock#2 "); $$ = nodeAddChild ($1, $2); }

bulletlistline  :   LISTBULLET listseries textorempty NEWLINE
                        { debugf ("bulletlistline#1 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListBullet)), $3); }
                |   LISTBULLET listseries textorempty
                        { debugf ("bulletlistline#2 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListBullet)), $3); }
numberlistline  :   LISTNUMBERED listseries textorempty NEWLINE
                        { debugf ("numberlistline#1 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListNumbered)), $3); }
                |   LISTNUMBERED listseries textorempty
                        { debugf ("numberlistline#2 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListNumbered)), $3); }

listseries      :   /* empty */                 { debugf ("listseries#1 "); $$ = newNode (ListLine); }
                |   LISTBULLET
                        { debugf ("listseries#2 "); $$ = nodeAddChild (newNode (ListLine), newNode (ListBullet)); }
                |   LISTNUMBERED
                        { debugf ("listseries#3 "); $$ = nodeAddChild (newNode (ListLine), newNode (ListNumbered)); }
                |   listseries LISTBULLET       { debugf ("listseries#4 "); $$ = nodeAddChild ($1, newNode (ListBullet)); }
                |   listseries LISTNUMBERED     { debugf ("listseries#5 "); $$ = nodeAddChild ($1, newNode (ListNumbered)); }

linketc         :   OPENDBLSQBR textnopipe CLOSEDBLSQBR
                        { debugf ("linketc#1 "); $$ = nodeAddChild (newNodeI (LinkEtc, 0), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENDBLSQBR textnopipe PIPE CLOSEDBLSQBR
                        { debugf ("linketc#2 "); $$ = nodeAddChild (newNodeI (LinkEtc, 1), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENDBLSQBR textnopipe pipeseries CLOSEDBLSQBR
                        { debugf ("linketc#3 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 0), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENDBLSQBR textnopipe pipeseries PIPE CLOSEDBLSQBR
                        { debugf ("linketc#4 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 1), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENLINK textnopipe CLOSEDBLSQBR
                        { debugf ("linketc#5 "); $$ = nodeAddChild (newNodeI (LinkEtc, 2), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENLINK textnopipe PIPE CLOSEDBLSQBR
                        { debugf ("linketc#6 "); $$ = nodeAddChild (newNodeI (LinkEtc, 3), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENLINK textnopipe pipeseries CLOSEDBLSQBR
                        { debugf ("linketc#7 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 2), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENLINK textnopipe pipeseries PIPE CLOSEDBLSQBR
                        { debugf ("linketc#8 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 3), nodeAddChild (newNode (LinkTarget), $2), $3); }
                    /* ... and now everything again with the CLOSEDBLSQBR missing,
                     * to take care of invalid mark-up. */
                |   OPENDBLSQBR textnopipe
                        { debugf ("linketc#9 "); $$ = makeTextBlock (newNodeS (TextToken, "[["), $2); }
                |   OPENDBLSQBR textnopipe PIPE
                        { debugf ("linketc#10 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[["), $2, newNodeS (TextToken, "|")); }
                |   OPENDBLSQBR textnopipe pipeseries
                        { debugf ("linketc#11 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[["), $2, convertPipeSeriesToText ($3)); }
                |   OPENDBLSQBR textnopipe pipeseries PIPE
                        { debugf ("linketc#12 "); $$ = makeTextBlock3 (newNodeS (TextToken, "[["), $2, convertPipeSeriesToText ($3), newNodeS (TextToken, "|")); }
                |   OPENLINK textnopipe
                        { debugf ("linketc#13 "); $$ = makeTextBlock (newNodeS (TextToken, "[[:"), $2); }
                |   OPENLINK textnopipe PIPE
                        { debugf ("linketc#14 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[[:"), $2, newNodeS (TextToken, "|")); }
                |   OPENLINK textnopipe pipeseries
                        { debugf ("linketc#15 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[[:"), $2, convertPipeSeriesToText ($3)); }
                |   OPENLINK textnopipe pipeseries PIPE
                        { debugf ("linketc#16 "); $$ = makeTextBlock3 (newNodeS (TextToken, "[[:"), $2, convertPipeSeriesToText ($3), newNodeS (TextToken, "|")); }

linketcnotbl    :   OPENDBLSQBR textnotblpipe CLOSEDBLSQBR
                        { debugf ("linketcnotbl#1 "); $$ = nodeAddChild (newNodeI (LinkEtc, 0), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENDBLSQBR textnotblpipe PIPE CLOSEDBLSQBR
                        { debugf ("linketcnotbl#2 "); $$ = nodeAddChild (newNodeI (LinkEtc, 1), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENDBLSQBR textnotblpipe pipeseriesnotbl CLOSEDBLSQBR
                        { debugf ("linketcnotbl#3 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 0), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENDBLSQBR textnotblpipe pipeseriesnotbl PIPE CLOSEDBLSQBR
                        { debugf ("linketcnotbl#4 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 1), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENLINK textnotblpipe CLOSEDBLSQBR
                        { debugf ("linketcnotbl#5 "); $$ = nodeAddChild (newNodeI (LinkEtc, 2), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENLINK textnotblpipe PIPE CLOSEDBLSQBR
                        { debugf ("linketcnotbl#6 "); $$ = nodeAddChild (newNodeI (LinkEtc, 3), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENLINK textnotblpipe pipeseriesnotbl CLOSEDBLSQBR
                        { debugf ("linketcnotbl#7 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 2), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENLINK textnotblpipe pipeseriesnotbl PIPE CLOSEDBLSQBR
                        { debugf ("linketcnotbl#8 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 3), nodeAddChild (newNode (LinkTarget), $2), $3); }
                    /* ... and now everything again with the CLOSEDBLSQBR missing,
                     * to take care of invalid mark-up. */
                |   OPENDBLSQBR textnotblpipe
                        { debugf ("linketcnotbl#9 "); $$ = makeTextBlock (newNodeS (TextToken, "[["), $2); }
                |   OPENDBLSQBR textnotblpipe PIPE
                        { debugf ("linketcnotbl#10 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[["), $2, newNodeS (TextToken, "|")); }
                |   OPENDBLSQBR textnotblpipe pipeseriesnotbl
                        { debugf ("linketcnotbl#11 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[["), $2, convertPipeSeriesToText ($3)); }
                |   OPENDBLSQBR textnotblpipe pipeseriesnotbl PIPE
                        { debugf ("linketcnotbl#12 "); $$ = makeTextBlock3 (newNodeS (TextToken, "[["), $2, convertPipeSeriesToText ($3), newNodeS (TextToken, "|")); }
                |   OPENLINK textnotblpipe
                        { debugf ("linketcnotbl#13 "); $$ = makeTextBlock (newNodeS (TextToken, "[[:"), $2); }
                |   OPENLINK textnotblpipe PIPE
                        { debugf ("linketcnotbl#14 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[[:"), $2, newNodeS (TextToken, "|")); }
                |   OPENLINK textnotblpipe pipeseriesnotbl
                        { debugf ("linketcnotbl#15 "); $$ = makeTextBlock2 (newNodeS (TextToken, "[[:"), $2, convertPipeSeriesToText ($3)); }
                |   OPENLINK textnotblpipe pipeseriesnotbl PIPE
                        { debugf ("linketcnotbl#16 "); $$ = makeTextBlock3 (newNodeS (TextToken, "[[:"), $2, convertPipeSeriesToText ($3), newNodeS (TextToken, "|")); }

pipeseries      :   PIPE textnopipe               { debugf ("pipeseries#1 "); $$ = nodeAddChild (newNode (LinkOption), $2); }
                |   pipeseries PIPE textnopipe    { debugf ("pipeseries#2 "); $$ = nodeAddSibling ($1, nodeAddChild (newNode (LinkOption), $3)); }

pipeseriesnotbl :   PIPE textnotblpipe                  { debugf ("pipeseriesnotbl#1 "); $$ = nodeAddChild (newNode (LinkOption), $2); }
                |   pipeseriesnotbl PIPE textnotblpipe  { debugf ("pipeseriesnotbl#2 "); $$ = nodeAddSibling ($1, nodeAddChild (newNode (LinkOption), $3)); }

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

italorboldnotbl :   APO2 textnoitaltbl APO2
                        { debugf ("italorboldnotbl#1 "); $$ = nodeAddChild (newNode (Italics), $2); }
                |   APO2 textnoitaltbl APO3 textnoboittbl APO5
                        { debugf ("italorboldnotbl#2 "); $$ = nodeAddChild (newNode (Italics),
                                makeTextBlock ($2, nodeAddChild (newNode (Bold), $4))); }
                |   APO2 textnoitaltbl APO3 textnoboittbl
                        { debugf ("italorboldnotbl#3 "); $$ =
                        makeTextBlock2 (nodeAddChild (newNode (Italics), $2), newNodeS (TextToken, "'"), $4); }
                |   APO2 textnoitaltbl
                        { debugf ("italorboldnotbl#4 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2); }
                |   APO3 textnoboldtbl APO3
                        { debugf ("italorboldnotbl#5 "); $$ = nodeAddChild (newNode (Bold), $2); }
                |   APO3 textnoboldtbl APO2 textnoboittbl APO5
                        { debugf ("italorboldnotbl#6 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock ($2, nodeAddChild (newNode (Italics), $4))); }
                |   APO3 textnoboldtbl APO2 textnoboittbl
                        { debugf ("italorboldnotbl#7 "); $$ = processNestedItalics (makeTextBlock2 (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2), $4)); }
                |   APO3 textnoboldtbl APO2
                        { debugf ("italorboldnotbl#8 "); $$ = processNestedItalics (makeTextBlock (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2))); }
                |   APO3 textnoboldtbl
                        { debugf ("italorboldnotbl#9 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2); }
                |   APO5 textnoboittbl APO5
                        { debugf ("italorboldnotbl#10 "); $$ = nodeAddChild (newNode (Italics),
                            nodeAddChild (newNode (Bold), $2)); }
                |   APO5 textnoboittbl APO3 textnoitaltbl APO2
                        { debugf ("italorboldnotbl#11 "); $$ = nodeAddChild (newNode (Italics),
                            makeTextBlock (nodeAddChild (newNode (Bold), $2), $4)); }
                |   APO5 textnoboittbl APO3 textnoitaltbl
                        { debugf ("italorboldnotbl#12 "); $$ = makeTextBlock2 (newNodeS (TextToken, "''"),
                            nodeAddChild (newNode (Bold), $2), $4); }
                |   APO5 textnoboittbl APO3
                        { debugf ("italorboldnotbl#13 "); $$ = makeTextBlock (newNodeS (TextToken, "''"),
                            nodeAddChild (newNode (Bold), $2)); }
                |   APO5 textnoboittbl APO2 textnoboldtbl APO3
                        { debugf ("italorboldnotbl#14 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock (nodeAddChild (newNode (Italics), $2), $4)); }
                |   APO5 textnoboittbl APO2 textnoboldtbl
                        { debugf ("italorboldnotbl#15 "); $$ = makeTextBlock2 (newNodeS (TextToken, "'''"),
                            nodeAddChild (newNode (Italics), $2), $4); }
                |   APO5 textnoboittbl APO2
                        { debugf ("italorboldnotbl#16 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"),
                            nodeAddChild (newNode (Italics), $2)); }
                |   APO5 textnoboittbl
                        { debugf ("italorboldnotbl#17 ");
                            $$ = makeTextBlock (newNodeS (TextToken, "'''''"), $2); }

italorboldnopipe:   APO2 textnoitalpipe APO2
                        { debugf ("italicsorbold#1 "); $$ = nodeAddChild (newNode (Italics), $2); }
                |   APO2 textnoitalpipe APO3 textnoboitpipe APO5
                        { debugf ("italicsorbold#2 "); $$ = nodeAddChild (newNode (Italics),
                                makeTextBlock ($2, nodeAddChild (newNode (Bold), $4))); }
                |   APO2 textnoitalpipe APO3 textnoboitpipe
                        { debugf ("italicsorbold#3 "); $$ =
                        makeTextBlock2 (nodeAddChild (newNode (Italics), $2), newNodeS (TextToken, "'"), $4); }
                |   APO2 textnoitalpipe
                        { debugf ("italicsorbold#4 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2); }
                |   APO3 textnoboldpipe APO3
                        { debugf ("italicsorbold#5 "); $$ = nodeAddChild (newNode (Bold), $2); }
                |   APO3 textnoboldpipe APO2 textnoboitpipe APO5
                        { debugf ("italicsorbold#6 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock ($2, nodeAddChild (newNode (Italics), $4))); }
                |   APO3 textnoboldpipe APO2 textnoboitpipe
                        { debugf ("italicsorbold#7 "); $$ = processNestedItalics (makeTextBlock2 (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2), $4)); }
                |   APO3 textnoboldpipe APO2
                        { debugf ("italicsorbold#8 "); $$ = processNestedItalics (makeTextBlock (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2))); }
                |   APO3 textnoboldpipe
                        { debugf ("italicsorbold#9 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2); }
                |   APO5 textnoboitpipe APO5
                        { debugf ("italicsorbold#10 "); $$ = nodeAddChild (newNode (Italics),
                            nodeAddChild (newNode (Bold), $2)); }
                |   APO5 textnoboitpipe APO3 textnoitalpipe APO2
                        { debugf ("italicsorbold#11 "); $$ = nodeAddChild (newNode (Italics),
                            makeTextBlock (nodeAddChild (newNode (Bold), $2), $4)); }
                |   APO5 textnoboitpipe APO3 textnoitalpipe
                        { debugf ("italicsorbold#12 "); $$ = makeTextBlock2 (newNodeS (TextToken, "''"),
                            nodeAddChild (newNode (Bold), $2), $4); }
                |   APO5 textnoboitpipe APO3
                        { debugf ("italicsorbold#13 "); $$ = makeTextBlock (newNodeS (TextToken, "''"),
                            nodeAddChild (newNode (Bold), $2)); }
                |   APO5 textnoboitpipe APO2 textnoboldpipe APO3
                        { debugf ("italicsorbold#14 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock (nodeAddChild (newNode (Italics), $2), $4)); }
                |   APO5 textnoboitpipe APO2 textnoboldpipe
                        { debugf ("italicsorbold#15 "); $$ = makeTextBlock2 (newNodeS (TextToken, "'''"),
                            nodeAddChild (newNode (Italics), $2), $4); }
                |   APO5 textnoboitpipe APO2
                        { debugf ("italicsorbold#16 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"),
                            nodeAddChild (newNode (Italics), $2)); }
                |   APO5 textnoboitpipe
                        { debugf ("italicsorbold#17 ");
                            $$ = makeTextBlock (newNodeS (TextToken, "'''''"), $2); }

italorboldnotblp:   APO2 textnoitaltblpipe APO2
                        { debugf ("italorboldnotbl#1 "); $$ = nodeAddChild (newNode (Italics), $2); }
                |   APO2 textnoitaltblpipe APO3 textnoboittblpipe APO5
                        { debugf ("italorboldnotbl#2 "); $$ = nodeAddChild (newNode (Italics),
                                makeTextBlock ($2, nodeAddChild (newNode (Bold), $4))); }
                |   APO2 textnoitaltblpipe APO3 textnoboittblpipe
                        { debugf ("italorboldnotbl#3 "); $$ =
                        makeTextBlock2 (nodeAddChild (newNode (Italics), $2), newNodeS (TextToken, "'"), $4); }
                |   APO2 textnoitaltblpipe
                        { debugf ("italorboldnotbl#4 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2); }
                |   APO3 textnoboldtblpipe APO3
                        { debugf ("italorboldnotbl#5 "); $$ = nodeAddChild (newNode (Bold), $2); }
                |   APO3 textnoboldtblpipe APO2 textnoboittblpipe APO5
                        { debugf ("italorboldnotbl#6 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock ($2, nodeAddChild (newNode (Italics), $4))); }
                |   APO3 textnoboldtblpipe APO2 textnoboittblpipe
                        { debugf ("italorboldnotbl#7 "); $$ = processNestedItalics (makeTextBlock2 (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2), $4)); }
                |   APO3 textnoboldtblpipe APO2
                        { debugf ("italorboldnotbl#8 "); $$ = processNestedItalics (makeTextBlock (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2))); }
                |   APO3 textnoboldtblpipe
                        { debugf ("italorboldnotbl#9 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2); }
                |   APO5 textnoboittblpipe APO5
                        { debugf ("italorboldnotbl#10 "); $$ = nodeAddChild (newNode (Italics),
                            nodeAddChild (newNode (Bold), $2)); }
                |   APO5 textnoboittblpipe APO3 textnoitaltblpipe APO2
                        { debugf ("italorboldnotbl#11 "); $$ = nodeAddChild (newNode (Italics),
                            makeTextBlock (nodeAddChild (newNode (Bold), $2), $4)); }
                |   APO5 textnoboittblpipe APO3 textnoitaltblpipe
                        { debugf ("italorboldnotbl#12 "); $$ = makeTextBlock2 (newNodeS (TextToken, "''"),
                            nodeAddChild (newNode (Bold), $2), $4); }
                |   APO5 textnoboittblpipe APO3
                        { debugf ("italorboldnotbl#13 "); $$ = makeTextBlock (newNodeS (TextToken, "''"),
                            nodeAddChild (newNode (Bold), $2)); }
                |   APO5 textnoboittblpipe APO2 textnoboldtblpipe APO3
                        { debugf ("italorboldnotbl#14 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock (nodeAddChild (newNode (Italics), $2), $4)); }
                |   APO5 textnoboittblpipe APO2 textnoboldtblpipe
                        { debugf ("italorboldnotbl#15 "); $$ = makeTextBlock2 (newNodeS (TextToken, "'''"),
                            nodeAddChild (newNode (Italics), $2), $4); }
                |   APO5 textnoboittblpipe APO2
                        { debugf ("italorboldnotbl#16 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"),
                            nodeAddChild (newNode (Italics), $2)); }
                |   APO5 textnoboittblpipe
                        { debugf ("italorboldnotbl#17 ");
                            $$ = makeTextBlock (newNodeS (TextToken, "'''''"), $2); }

italicsnobold   :   APO2 textnoboit APO2
                        { debugf ("italicsnobold#1 "); $$ = nodeAddChild (newNode (Italics), $2); }
                |   APO2 textnoboit
                        { debugf ("italicsnobold#2 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2); }

boldnoitalics   :   APO3 textnoboit APO3
                        { debugf ("boldnoitalics#1 "); $$ = nodeAddChild (newNode (Bold), $2); }
                |   APO3 textnoboit
                        { debugf ("boldnoitalics#2 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2); }

italicsnoboldpp :   APO2 textnoboitpipe APO2
                        { debugf ("italicsnobold#1 "); $$ = nodeAddChild (newNode (Italics), $2); }
                |   APO2 textnoboitpipe
                        { debugf ("italicsnobold#2 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2); }

boldnoitalicspp :   APO3 textnoboitpipe APO3
                        { debugf ("boldnoitalics#1 "); $$ = nodeAddChild (newNode (Bold), $2); }
                |   APO3 textnoboitpipe
                        { debugf ("boldnoitalics#2 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2); }

italicsnoboldtbl:   APO2 textnoboittbl APO2
                        { debugf ("italicsnobold#1 "); $$ = nodeAddChild (newNode (Italics), $2); }
                |   APO2 textnoboittbl
                        { debugf ("italicsnobold#2 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2); }

boldnoitalicstbl:   APO3 textnoboittbl APO3
                        { debugf ("boldnoitalics#1 "); $$ = nodeAddChild (newNode (Bold), $2); }
                |   APO3 textnoboittbl
                        { debugf ("boldnoitalics#2 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2); }

italnoboldtblpp :   APO2 textnoboittblpipe APO2
                        { debugf ("italicsnobold#1 "); $$ = nodeAddChild (newNode (Italics), $2); }
                |   APO2 textnoboittblpipe
                        { debugf ("italicsnobold#2 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2); }

boldnoitaltblpp :   APO3 textnoboittblpipe APO3
                        { debugf ("boldnoitalics#1 "); $$ = nodeAddChild (newNode (Bold), $2); }
                |   APO3 textnoboittblpipe
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
                |   TABLEROW text zeroormorenewlines
                        { debugf ("tablerow#5 "); $$ = nodeAddChild (newNode (TableRow), nodeAddChild (newNode (TableCell), $2)); }
                |   TABLEROW attributes text zeroormorenewlines
                        { debugf ("tablerow#6 "); $$ = nodeAddChild (newNode (TableRow), nodeAddChild2 (newNode (TableCell), convertAttributesToText ($2), $3)); }
                |   TABLEROW zeroormorenewlines
                        { debugf ("tablerow#7 "); $$ = 0; }
                /* It is possible for the first table row to have no TABLEROW token */
                |   tablecells
                        { debugf ("tablerow#8 "); $$ = nodeAddChild (newNode (TableRow), $1); }

tablecells      :   tablecell               { debugf ("tablecells#1 "); $$ = $1; }
                |   tablecells tablecell    { debugf ("tablecells#2 "); $$ = nodeAddSibling ($1, $2); }

tablecell       :   TABLECELL attributes PIPE tablecellcontents
                        { debugf ("tablecell#1 "); $$ = nodeAddChild2 (newNode (TableCell), $2, processTableCellContents ($4)); }
                |   TABLECELL tablecellcontents
                        { debugf ("tablecell#2 "); $$ = nodeAddChild (newNode (TableCell), processTableCellContents ($2)); }

tablecellcontents   :   blocksnotbl
                            { debugf ("tablecellcontents#1 "); $$ = $1; }
                    |   oneormorenewlines blocksnotbl
                            { debugf ("tablecellcontents#2 "); $$ = $2; }

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
textnotbl       :   textelementnotbl                { debugf ("textnotbl#1 "); $$ = $1; }
                |   textnotbl textelementnotbl      { debugf ("textnotbl#2 "); $$ = makeTextBlock ($1, $2); }
textnoitaltbl   :   textelementnoitaltbl                { debugf ("textnoitaltbl#1 "); $$ = $1; }
                |   textnoitaltbl textelementnoitaltbl  { debugf ("textnoitaltbl#2 "); $$ = makeTextBlock ($1, $2); }
textnoboldtbl   :   textelementnoboldtbl                { debugf ("textnoboldtbl#1 "); $$ = $1; }
                |   textnoboldtbl textelementnoboldtbl  { debugf ("textnoboldtbl#2 "); $$ = makeTextBlock ($1, $2); }
textnoboittbl   :   textelementnoboittbl                { debugf ("textnoboittbl#1 "); $$ = $1; }
                |   textnoboittbl textelementnoboittbl  { debugf ("textnoboittbl#2 "); $$ = makeTextBlock ($1, $2); }
textnopipe          :   textelementnopipe                   { debugf ("textnopipe#1 "); $$ = $1; }
                    |   textnopipe textelementnopipe        { debugf ("textnopipe#2 "); $$ = makeTextBlock ($1, $2); }
textnoitalpipe      :   textelementnoitp                    { debugf ("textnoitalpipe#1 "); $$ = $1; }
                    |   textnoitalpipe textelementnoitp     { debugf ("textnoitalpipe#2 "); $$ = makeTextBlock ($1, $2); }
textnoboldpipe      :   textelementnobldp                   { debugf ("textnoboldpipe#1 "); $$ = $1; }
                    |   textnoboldpipe textelementnobldp    { debugf ("textnoboldpipe#2 "); $$ = makeTextBlock ($1, $2); }
textnoboitpipe      :   textelementnobitp                   { debugf ("textnoboitpipe#1 "); $$ = $1; }
                    |   textnoboitpipe textelementnobitp    { debugf ("textnoboitpipe#2 "); $$ = makeTextBlock ($1, $2); }
textnotblpipe       :   textelementnotblpipe                { debugf ("textnotblpipe#1 "); $$ = $1; }
                    |   textnotblpipe textelementnotblpipe  { debugf ("textnotblpipe#2 "); $$ = makeTextBlock ($1, $2); }
textnoitaltblpipe   :   textelementnoittbpp                     { debugf ("textnoitaltblpipe#1 "); $$ = $1; }
                    |   textnoitaltblpipe textelementnoittbpp   { debugf ("textnoitaltblpipe#2 "); $$ = makeTextBlock ($1, $2); }
textnoboldtblpipe   :   textelementnobltbpp                     { debugf ("textnoboldtblpipe#1 "); $$ = $1; }
                    |   textnoboldtblpipe textelementnobltbpp   { debugf ("textnoboldtblpipe#2 "); $$ = makeTextBlock ($1, $2); }
textnoboittblpipe   :   textelementnoblittbp                    { debugf ("textnoboittblpipe#1 "); $$ = $1; }
                    |   textnoboittblpipe textelementnoblittbp  { debugf ("textnoboittblpipe#2 "); $$ = makeTextBlock ($1, $2); }

textelement         :   TEXT            { debugf ("textelement#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelement#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelement#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   APO2            { debugf ("textelement#5 "); $$ = newNodeS (TextToken, "''"); }
                    |   APO3            { debugf ("textelement#6 "); $$ = newNodeS (TextToken, "'''"); }
                    |   APO5            { debugf ("textelement#7 "); $$ = newNodeS (TextToken, "'''''"); }
                    |   EQUALS          { debugf ("textelement#8 "); $$ = newNodeS (TextToken, addSpaces ("=", $1)); }
                    |   TABLEBEGIN      { debugf ("textelement#9 "); $$ = newNodeS (TextToken, addSpaces ("{|", $1)); }
                    |   TABLEEND        { debugf ("textelement#10 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW        { debugf ("textelement#11 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL       { debugf ("textelement#12 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD       { debugf ("textelement#13 "); $$ = convertTableHeadToText ($1); }
                    |   ATTRIBUTE       { debugf ("textelement#14 "); $$ = convertAttributeDataToText ($1); }
                    |   comment         { debugf ("textelement#15 "); $$ = $1; }
                    |   linketc         { debugf ("textelement#16 "); $$ = $1; }
                    |   italicsorbold   { debugf ("textelement#17 "); $$ = $1; }
                    |   CLOSEDBLSQBR    { debugf ("textelement#18 "); $$ = newNodeS (TextToken, "]]"); }

textelementnoital   :   TEXT            { debugf ("textelementnoital#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnoital#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnoital#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   TABLEBEGIN      { debugf ("textelementnoital#5 "); $$ = newNodeS (TextToken, addSpaces ("{|", $1)); }
                    |   TABLEEND        { debugf ("textelementnoital#6 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW        { debugf ("textelementnoital#7 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL       { debugf ("textelementnoital#8 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD       { debugf ("textelementnoital#9 "); $$ = convertTableHeadToText ($1); }
                    |   comment         { debugf ("textelementnoital#10 "); $$ = $1; }
                    |   linketc         { debugf ("textelementnoital#11 "); $$ = $1; }
                    |   boldnoitalics   { debugf ("textelementnoital#12 "); $$ = $1; }
                    |   CLOSEDBLSQBR    { debugf ("textelementnoital#13 "); $$ = newNodeS (TextToken, "]]"); }

textelementnobold   :   TEXT            { debugf ("textelementnobold#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnobold#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnobold#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   TABLEBEGIN      { debugf ("textelementnobold#5 "); $$ = newNodeS (TextToken, addSpaces ("{|", $1)); }
                    |   TABLEEND        { debugf ("textelementnobold#6 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW        { debugf ("textelementnobold#7 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL       { debugf ("textelementnobold#8 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD       { debugf ("textelementnobold#9 "); $$ = convertTableHeadToText ($1); }
                    |   comment         { debugf ("textelementnobold#10 "); $$ = $1; }
                    |   linketc         { debugf ("textelementnobold#11 "); $$ = $1; }
                    |   italicsnobold   { debugf ("textelementnobold#12 "); $$ = $1; }
                    |   CLOSEDBLSQBR    { debugf ("textelementnobold#13 "); $$ = newNodeS (TextToken, "]]"); }

textelementnoboit   :   TEXT            { debugf ("textelementnoboit#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnoboit#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnoboit#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   TABLEBEGIN      { debugf ("textelementnoboit#5 "); $$ = newNodeS (TextToken, addSpaces ("{|", $1)); }
                    |   TABLEEND        { debugf ("textelementnoboit#6 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW        { debugf ("textelementnoboit#7 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL       { debugf ("textelementnoboit#8 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD       { debugf ("textelementnoboit#9 "); $$ = convertTableHeadToText ($1); }
                    |   comment         { debugf ("textelementnoboit#10 "); $$ = $1; }
                    |   linketc         { debugf ("textelementnoboit#11 "); $$ = $1; }
                    |   CLOSEDBLSQBR    { debugf ("textelementnoboit#12 "); $$ = newNodeS (TextToken, "]]"); }

textelementnotbl    :   TEXT            { debugf ("textelementnotbl#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnotbl#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnotbl#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   APO2            { debugf ("textelementnotbl#5 "); $$ = newNodeS (TextToken, "''"); }
                    |   APO3            { debugf ("textelementnotbl#6 "); $$ = newNodeS (TextToken, "'''"); }
                    |   APO5            { debugf ("textelementnotbl#7 "); $$ = newNodeS (TextToken, "'''''"); }
                    |   EQUALS          { debugf ("textelementnotbl#8 "); $$ = newNodeS (TextToken, addSpaces ("=", $1)); }
                    |   comment         { debugf ("textelementnotbl#9 "); $$ = $1; }
                    |   linketcnotbl    { debugf ("textelementnotbl#10 "); $$ = $1; }
                    |   italorboldnotbl { debugf ("textelementnotbl#11 "); $$ = $1; }
                    |   CLOSEDBLSQBR    { debugf ("textelementnotbl#12 "); $$ = newNodeS (TextToken, "]]"); }

textelementnoitaltbl:   TEXT            { debugf ("textelementnoitaltbl#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnoitaltbl#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnoitaltbl#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   comment         { debugf ("textelementnoitaltbl#5 "); $$ = $1; }
                    |   linketcnotbl    { debugf ("textelementnoitaltbl#6 "); $$ = $1; }
                    |   boldnoitalicstbl{ debugf ("textelementnoitaltbl#7 "); $$ = $1; }
                    |   CLOSEDBLSQBR    { debugf ("textelementnoitaltbl#8 "); $$ = newNodeS (TextToken, "]]"); }

textelementnoboldtbl:   TEXT            { debugf ("textelementnoboldtbl#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnoboldtbl#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnoboldtbl#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   comment         { debugf ("textelementnoboldtbl#5 "); $$ = $1; }
                    |   linketcnotbl    { debugf ("textelementnoboldtbl#6 "); $$ = $1; }
                    |   italicsnoboldtbl{ debugf ("textelementnoboldtbl#7 "); $$ = $1; }
                    |   CLOSEDBLSQBR    { debugf ("textelementnoboldtbl#8 "); $$ = newNodeS (TextToken, "]]"); }

textelementnoboittbl:   TEXT            { debugf ("textelementnoboittbl#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnoboittbl#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnoboittbl#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   comment         { debugf ("textelementnoboittbl#5 "); $$ = $1; }
                    |   linketcnotbl    { debugf ("textelementnoboittbl#6 "); $$ = $1; }
                    |   CLOSEDBLSQBR    { debugf ("textelementnoboittbl#7 "); $$ = newNodeS (TextToken, "]]"); }

textelementnopipe   :   TEXT            { debugf ("textelementnopipe#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnopipe#2 "); $$ = $1; }
                    |   APO2            { debugf ("textelementnopipe#5 "); $$ = newNodeS (TextToken, "''"); }
                    |   APO3            { debugf ("textelementnopipe#6 "); $$ = newNodeS (TextToken, "'''"); }
                    |   APO5            { debugf ("textelementnopipe#7 "); $$ = newNodeS (TextToken, "'''''"); }
                    |   EQUALS          { debugf ("textelementnopipe#8 "); $$ = newNodeS (TextToken, addSpaces ("=", $1)); }
                    |   TABLEBEGIN      { debugf ("textelementnopipe#9 "); $$ = newNodeS (TextToken, addSpaces ("{|", $1)); }
                    |   TABLEEND        { debugf ("textelementnopipe#10 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW        { debugf ("textelementnopipe#11 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL       { debugf ("textelementnopipe#12 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD       { debugf ("textelementnopipe#13 "); $$ = convertTableHeadToText ($1); }
                    |   ATTRIBUTE       { debugf ("textelementnopipe#14 "); $$ = convertAttributeDataToText ($1); }
                    |   comment         { debugf ("textelementnopipe#15 "); $$ = $1; }
                    |   linketc         { debugf ("textelementnopipe#16 "); $$ = $1; }
                    |   italorboldnopipe{ debugf ("textelementnopipe#17 "); $$ = $1; }

textelementnoitp    :   TEXT            { debugf ("textelementnoitp#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnoitp#2 "); $$ = $1; }
                    |   TABLEBEGIN      { debugf ("textelementnoitp#5 "); $$ = newNodeS (TextToken, addSpaces ("{|", $1)); }
                    |   TABLEEND        { debugf ("textelementnoitp#6 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW        { debugf ("textelementnoitp#7 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL       { debugf ("textelementnoitp#8 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD       { debugf ("textelementnoitp#9 "); $$ = convertTableHeadToText ($1); }
                    |   comment         { debugf ("textelementnoitp#10 "); $$ = $1; }
                    |   linketc         { debugf ("textelementnoitp#11 "); $$ = $1; }
                    |   boldnoitalicspp { debugf ("textelementnoitp#12 "); $$ = $1; }

textelementnobldp   :   TEXT            { debugf ("textelementnobldp#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnobldp#2 "); $$ = $1; }
                    |   TABLEBEGIN      { debugf ("textelementnobldp#5 "); $$ = newNodeS (TextToken, addSpaces ("{|", $1)); }
                    |   TABLEEND        { debugf ("textelementnobldp#6 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW        { debugf ("textelementnobldp#7 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL       { debugf ("textelementnobldp#8 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD       { debugf ("textelementnobldp#9 "); $$ = convertTableHeadToText ($1); }
                    |   comment         { debugf ("textelementnobldp#10 "); $$ = $1; }
                    |   linketc         { debugf ("textelementnobldp#11 "); $$ = $1; }
                    |   italicsnoboldpp { debugf ("textelementnobldp#12 "); $$ = $1; }

textelementnobitp   :   TEXT            { debugf ("textelementnobitp#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnobitp#2 "); $$ = $1; }
                    |   TABLEBEGIN      { debugf ("textelementnobitp#5 "); $$ = newNodeS (TextToken, addSpaces ("{|", $1)); }
                    |   TABLEEND        { debugf ("textelementnobitp#6 "); $$ = newNodeS (TextToken, "|}"); }
                    |   TABLEROW        { debugf ("textelementnobitp#7 "); $$ = convertTableRowToText ($1); }
                    |   TABLECELL       { debugf ("textelementnobitp#8 "); $$ = convertTableCellToText ($1); }
                    |   TABLEHEAD       { debugf ("textelementnobitp#9 "); $$ = convertTableHeadToText ($1); }
                    |   comment         { debugf ("textelementnobitp#10 "); $$ = $1; }
                    |   linketc         { debugf ("textelementnobitp#11 "); $$ = $1; }

textelementnotblpipe:   TEXT            { debugf ("textelementnotblpipe#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnotblpipe#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnotblpipe#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   APO2            { debugf ("textelementnotblpipe#5 "); $$ = newNodeS (TextToken, "''"); }
                    |   APO3            { debugf ("textelementnotblpipe#6 "); $$ = newNodeS (TextToken, "'''"); }
                    |   APO5            { debugf ("textelementnotblpipe#7 "); $$ = newNodeS (TextToken, "'''''"); }
                    |   EQUALS          { debugf ("textelementnotblpipe#8 "); $$ = newNodeS (TextToken, addSpaces ("=", $1)); }
                    |   comment         { debugf ("textelementnotblpipe#9 "); $$ = $1; }
                    |   linketcnotbl    { debugf ("textelementnotblpipe#10 "); $$ = $1; }
                    |   italorboldnotblp{ debugf ("textelementnotblpipe#11 "); $$ = $1; }

textelementnoittbpp :   TEXT            { debugf ("textelementnoittbpp#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnoittbpp#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnoittbpp#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   comment         { debugf ("textelementnoittbpp#5 "); $$ = $1; }
                    |   linketcnotbl    { debugf ("textelementnoittbpp#6 "); $$ = $1; }
                    |   boldnoitaltblpp { debugf ("textelementnoittbpp#7 "); $$ = $1; }

textelementnobltbpp :   TEXT            { debugf ("textelementnobltbpp#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnobltbpp#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnobltbpp#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   comment         { debugf ("textelementnobltbpp#5 "); $$ = $1; }
                    |   linketcnotbl    { debugf ("textelementnobltbpp#6 "); $$ = $1; }
                    |   italnoboldtblpp { debugf ("textelementnobltbpp#7 "); $$ = $1; }

textelementnoblittbp:   TEXT            { debugf ("textelementnoblittbp#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnoblittbp#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnoblittbp#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   comment         { debugf ("textelementnoblittbp#5 "); $$ = $1; }
                    |   linketcnotbl    { debugf ("textelementnoblittbp#6 "); $$ = $1; }

zeroormorenewlines : /* empty */                { debugf ("zeroormorenewlines#1 "); $$ = 0; }
                |   oneormorenewlines           { debugf ("zeroormorenewlines#2 "); $$ = 0; }
oneormorenewlines : NEWLINE                     { debugf ("oneormorenewlines#1 "); $$ = 0; }
                |   oneormorenewlines NEWLINE   { debugf ("oneormorenewlines#2 "); $$ = 0; }

zeroormorenewlinessave : /* empty */            { debugf ("zeroormorenewlinessave#1 "); $$ = 0; }
                |   oneormorenewlinessave       { debugf ("zeroormorenewlinessave#2 "); $$ = $1; }
oneormorenewlinessave : NEWLINE                     { debugf ("oneormorenewlinessave#1 "); $$ = newNodeI (Newlines, 0); }
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
paragraphnotbl  :   textnotbl NEWLINE
                        { debugf ("paragraphnotbl#1 "); $$ = nodeAddChild (newNode (Paragraph), $1); }
                |   attributes textnotbl NEWLINE
                        { debugf ("paragraphnotbl#2 "); $$ = nodeAddChild2 (newNode (Paragraph), convertAttributesToText ($1), $2); }
                |   attributes NEWLINE
                        { debugf ("paragraphnotbl#3 "); $$ = nodeAddChild (newNode (Paragraph), convertAttributesToText ($1)); }
                |   paragraphnotbl textnotbl NEWLINE
                        { debugf ("paragraphnotbl#4 "); $$ = nodeAddChild2 ($1, newNodeS (TextToken, " "), $2); }
                |   paragraphnotbl attributes textnotbl NEWLINE
                        { debugf ("paragraphnotbl#5 "); $$ = nodeAddChild3 ($1, newNodeS (TextToken, " "), convertAttributesToText ($2), $3); }
                |   paragraphnotbl attributes NEWLINE
                        { debugf ("paragraphnotbl#6 "); $$ = nodeAddChild2 ($1, newNodeS (TextToken, " "), convertAttributesToText ($2)); }
                /* for eof ... */
                |   textnotbl
                        { debugf ("paragraphnotbl#7 "); $$ = nodeAddChild (newNode (Paragraph), $1); }
                |   attributes textnotbl
                        { debugf ("paragraphnotbl#8 "); $$ = nodeAddChild2 (newNode (Paragraph), convertAttributesToText ($1), $2); }
                |   attributes
                        { debugf ("paragraphnotbl#9 "); $$ = nodeAddChild (newNode (Paragraph), convertAttributesToText ($1)); }
                |   paragraphnotbl textnotbl
                        { debugf ("paragraphnotbl#10 "); $$ = nodeAddChild2 ($1, newNodeS (TextToken, " "), $2); }
                |   paragraphnotbl attributes textnotbl
                        { debugf ("paragraphnotbl#11 "); $$ = nodeAddChild3 ($1, newNodeS (TextToken, " "), convertAttributesToText ($2), $3); }
                |   paragraphnotbl attributes
                        { debugf ("paragraphnotbl#12 "); $$ = nodeAddChild2 ($1, newNodeS (TextToken, " "), convertAttributesToText ($2)); }

comment         :   BEGINCOMMENT text ENDCOMMENT
                        { debugf ("comment#1 "); $$ = nodeAddChild (newNode (Comment), $2); }
                |   BEGINCOMMENT ENDCOMMENT
                        { debugf ("comment#2 "); $$ = newNode (Comment); }


%%

/* programs */

int main() {
    int result;
    printf ("Parsing... ");
    result = yyparse();
    if (!result)
        printf ("\n\nXML output:\n\n%s\n\n", outputXML (articlenode, 1024));
    return result;
}

const char* wikiparse_do_parse (const char* input)
{
    int result, i;

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
        return outputXML (articlenode, i < 1024 ? 1024 : i);
 }
	return "<error />";
}

/* declarations */

%{
#include <stdio.h>
#include "parsetree.h"
int yyerror() { printf ("Syntax error.\n"); }

/* Change this line to "#define debugf printf" to output each reduction */
#define debugf(x)

Node articlenode;
%}

/* This defines the type of yylval */
%union {
    Node node;
    int num;
}
%type <node> article block paragraph heading textorempty zeroormorenewlines oneormorenewlines
             preblock preline bulletlistline numberlistline listseries text listblock
             zeroormorenewlinessave oneormorenewlinessave bulletlistblock numberlistblock
             textelement textelementnoboit textelementnobold textelementnoital italicsorbold
             textnoboit textnobold textnoital boldnoitalics italicsnobold linketc pipeseries
             TEXT EXTENSION PRELINE
%type <num>  HEADING ENDHEADING

%token  EXTENSION EMPTYCOMMENT BEGINCOMMENT TEXT ENDCOMMENT OPENLINK OPENDBLSQBR CLOSEDBLSQBR PIPE
        NEWLINE PRELINE LISTBULLET LISTNUMBERED HEADING ENDHEADING APO5 APO3 APO2
        // Not yet used:
        OPENPENTUPLECURLY CLOSEPENTUPLECURLY OPENTEMPLATEVAR CLOSETEMPLATEVAR OPENTEMPLATE
        CLOSETEMPLATE
%start article

%%
/* rules */

article         :   /* empty */                        { debugf ("article#1 "); $$ = articlenode = newNode (Article); }
                |   oneormorenewlines                  { debugf ("article#2 "); $$ = articlenode = newNode (Article); }
                |   block           { debugf ("article#3 "); $$ = articlenode = nodeAddChild (newNode (Article), $1); }
                |   article block                  { debugf ("article#4 "); $$ = articlenode = nodeAddChild ($1, $2); }

block           :   preblock                                    { debugf ("block#1 "); $$ = processPreBlock ($1); }
                |   heading zeroormorenewlines                  { debugf ("block#2 "); $$ = $1; }
                |   listblock zeroormorenewlines                { debugf ("block#3 "); $$ = $1; }
                |   paragraph zeroormorenewlines                { debugf ("block#4 "); $$ = $1; }

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
numberlistline  :   LISTNUMBERED listseries textorempty NEWLINE
                        { debugf ("numberlistline#1 "); $$ = nodeAddChild (nodePrependChild ($2, newNode (ListNumbered)), $3); }

listseries      :   /* empty */                 { debugf ("listseries#1 "); $$ = newNode (ListLine); }
                |   LISTBULLET
                        { debugf ("listseries#2 "); $$ = nodeAddChild (newNode (ListLine), newNode (ListBullet)); }
                |   LISTNUMBERED
                        { debugf ("listseries#3 "); $$ = nodeAddChild (newNode (ListLine), newNode (ListNumbered)); }
                |   listseries LISTBULLET       { debugf ("listseries#4 "); $$ = nodeAddChild ($1, newNode (ListBullet)); }
                |   listseries LISTNUMBERED     { debugf ("listseries#5 "); $$ = nodeAddChild ($1, newNode (ListNumbered)); }

linketc         :   OPENDBLSQBR text CLOSEDBLSQBR
                        { debugf ("linketc#1 "); $$ = nodeAddChild (newNodeI (LinkEtc, 0), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENDBLSQBR text PIPE CLOSEDBLSQBR
                        { debugf ("linketc#2 "); $$ = nodeAddChild (newNodeI (LinkEtc, 1), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENDBLSQBR text pipeseries CLOSEDBLSQBR
                        { debugf ("linketc#3 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 0), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENDBLSQBR text pipeseries PIPE CLOSEDBLSQBR
                        { debugf ("linketc#4 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 1), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENLINK text CLOSEDBLSQBR
                        { debugf ("linketc#5 "); $$ = nodeAddChild (newNodeI (LinkEtc, 2), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENLINK text PIPE CLOSEDBLSQBR
                        { debugf ("linketc#6 "); $$ = nodeAddChild (newNodeI (LinkEtc, 3), nodeAddChild (newNode (LinkTarget), $2)); }
                |   OPENLINK text pipeseries CLOSEDBLSQBR
                        { debugf ("linketc#7 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 2), nodeAddChild (newNode (LinkTarget), $2), $3); }
                |   OPENLINK text pipeseries PIPE CLOSEDBLSQBR
                        { debugf ("linketc#8 "); $$ = nodeAddChild2 (newNodeI (LinkEtc, 3), nodeAddChild (newNode (LinkTarget), $2), $3); }

pipeseries      :   PIPE text
                        { debugf ("pipeseries#1 "); $$ = nodeAddChild (newNode (LinkOption), $2); }
                |   PIPE text pipeseries
                        {   debugf ("pipeseries#2 ");
                            $$ = nodeAddChild (newNode (LinkOption), $2);
                            $$->nextSibling = $3;
                        }

textorempty     :   /* empty */             { debugf ("textorempty#1 "); $$ = newNodeS (TextToken, ""); }
                |   text                    { debugf ("textorempty#2 "); $$ = $1; }

italicsorbold   :   APO2 textnoital APO2
                        { debugf ("italicsorbold#1 "); $$ = nodeAddChild (newNode (Italics), $2);             }
                |   APO2 textnoital APO3 textnoboit APO5
                        { debugf ("italicsorbold#2 "); $$ = nodeAddChild (newNode (Italics),
                                makeTextBlock ($2, nodeAddChild (newNode (Bold), $4)));                 }
                |   APO2 textnoital APO3 textnoboit
                        { debugf ("italicsorbold#3 "); $$ =
                        makeTextBlock2 (nodeAddChild (newNode (Italics), $2), newNodeS (TextToken, "'"), $4);   }
                |   APO2 textnoital
                        { debugf ("italicsorbold#4 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2);   }
                |   APO3 textnobold APO3
                        { debugf ("italicsorbold#5 "); $$ = nodeAddChild (newNode (Bold), $2);                   }
                |   APO3 textnobold APO2 textnoboit APO5
                        { debugf ("italicsorbold#6 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock ($2, nodeAddChild (newNode (Italics), $4)));                  }
                /* Peculiar case, especially for French l'''homme'' => l'<italics>homme</italics> */
                /* We have to use textnobold here, even though textnoital would be logical. */
                /* We use processNestedItalics to fix the weirdness produced by this. */
                |   APO3 textnobold APO2 textnoboit
                        { debugf ("italicsorbold#7 "); $$ = processNestedItalics (makeTextBlock2 (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2), $4));               }
                |   APO3 textnobold APO2
                        { debugf ("italicsorbold#8 "); $$ = processNestedItalics (makeTextBlock (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2)));                   }
                |   APO3 textnobold
                        { debugf ("italicsorbold#9 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2);     }
                |   APO5 textnoboit APO3 textnoital APO2
                        { debugf ("italicsorbold#10 "); $$ = nodeAddChild (newNode (Italics),
                            makeTextBlock (nodeAddChild (newNode (Bold), $2), $4));                     }
                |   APO5 textnoboit APO2 textnobold APO3
                        { debugf ("italicsorbold#11 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock (nodeAddChild (newNode (Italics), $2), $4));                  }
                |   APO5 textnoboit APO3 textnoital
                        { debugf ("italicsorbold#12 "); $$ = makeTextBlock2 (newNodeS (TextToken, "''"),
                            nodeAddChild (newNode (Bold), $2), $4);                                     }
                |   APO5 textnoboit APO2 textnobold
                        { debugf ("italicsorbold#13 "); $$ = makeTextBlock2 (newNodeS (TextToken, "'''"),
                            nodeAddChild (newNode (Italics), $2), $4);                                  }
                |   APO5 textnoboit
                        { debugf ("italicsorbold#14 ");
                            $$ = makeTextBlock (newNodeS (TextToken, "'''''"), $2);                     }


italicsnobold   :   APO2 textnoboit APO2
                        { debugf ("italicsnobold#1 "); $$ = nodeAddChild (newNode (Italics), $2);              }
                |   APO2 textnoboit
                        { debugf ("italicsnobold#2 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2);    }

boldnoitalics   :   APO3 textnoboit APO3
                        { debugf ("boldnoitalics#1 "); $$ = nodeAddChild (newNode (Bold), $2);                   }
                |   APO3 textnoboit
                        { debugf ("boldnoitalics#2 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2);     }

/* In order to resolve a reduce/reduce conflict correctly, heading must come before textelement. */
heading         :   HEADING text ENDHEADING NEWLINE
                        { debugf ("heading#1 "); $$ = nodeAddChild (newNodeI (Heading, $1), $2); }
                |   HEADING text ENDHEADING  /* for eof */
                        { debugf ("heading#2 "); $$ = nodeAddChild (newNodeI (Heading, $1), $2); }
                |   HEADING text NEWLINE
                        { debugf ("heading#3 "); $$ = nodeAddChild (newNodeI (Heading, $1), $2); }
                |   HEADING text  /* for eof */
                        { debugf ("heading#4 "); $$ = nodeAddChild (newNodeI (Heading, $1), $2); }
                |   HEADING NEWLINE
                        { debugf ("heading#5 "); $$ = nodeAddChild (newNodeI (Heading, $1), newNodeS (TextToken, "?")); }
                |   HEADING
                        { debugf ("heading#6 "); $$ = nodeAddChild (newNodeI (Heading, $1), newNodeS (TextToken, "?")); }

text            :   textelement                     { debugf ("text#1 "); $$ = $1; }
                |   text textelement                { debugf ("text#2 "); $$ = makeTextBlock ($1, $2); }
textnoital      :   textelementnoital               { debugf ("textnoital#1 "); $$ = $1; }
                |   textnoital textelementnoital    { debugf ("textnoital#2 "); $$ = makeTextBlock ($1, $2); }
textnobold      :   textelementnobold               { debugf ("textnobold#1 "); $$ = $1; }
                |   textnobold textelementnobold    { debugf ("textnobold#2 "); $$ = makeTextBlock ($1, $2); }
textnoboit      :   textelementnoboit               { debugf ("textnoboit#1 "); $$ = $1; }
                |   textnoboit textelementnoboit    { debugf ("textnoboit#2 "); $$ = makeTextBlock ($1, $2); }

textelement         :   TEXT            { debugf ("textelement#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelement#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelement#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   ENDHEADING      { debugf ("textelement#4 "); $$ = processEndHeadingInText ($1); }
                    |   italicsorbold   { debugf ("textelement#5 "); $$ = $1; }
                    |   APO2            { debugf ("textelement#6 "); $$ = newNodeS (TextToken, "''"); }
                    |   APO3            { debugf ("textelement#7 "); $$ = newNodeS (TextToken, "'''"); }
                    |   APO5            { debugf ("textelement#8 "); $$ = newNodeS (TextToken, "'''''"); }
                    |   linketc         { debugf ("textelement#9 "); $$ = $1; }

textelementnoital   :   TEXT            { debugf ("textelementnoital#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnoital#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnoital#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   ENDHEADING      { debugf ("textelementnoital#4 "); $$ = processEndHeadingInText ($1); }
                    |   boldnoitalics   { debugf ("textelementnoital#5 "); $$ = $1; }

textelementnobold   :   TEXT            { debugf ("textelementnobold#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnobold#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnobold#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   ENDHEADING      { debugf ("textelementnobold#4 "); $$ = processEndHeadingInText ($1); }
                    |   italicsnobold   { debugf ("textelementnobold#5 "); $$ = $1; }

textelementnoboit   :   TEXT            { debugf ("textelementnoboit#1 "); $$ = $1; }
                    |   EXTENSION       { debugf ("textelementnoboit#2 "); $$ = $1; }
                    |   PIPE            { debugf ("textelementnoboit#3 "); $$ = newNodeS (TextToken, "|"); }
                    |   ENDHEADING      { debugf ("textelementnoboit#4 "); $$ = processEndHeadingInText ($1); }

paragraph       :   text NEWLINE
                        { debugf ("paragraph#1 "); $$ = nodeAddChild (newNode (Paragraph), $1); }
                |   text NEWLINE paragraph /* needs to be right-recursive due to eof */
                        { debugf ("paragraph#2 "); $$ = nodePrependChild (nodePrependChild ($3,
                                newNodeS (TextToken, " ")), $1); }
                |   text /* for eof */
                        { debugf ("paragraph#3 "); $$ = nodeAddChild (newNode (Paragraph), $1); }

zeroormorenewlines : /* empty */                { debugf ("zeroormorenewlines#1 "); $$ = 0; }
                |   oneormorenewlines           { debugf ("zeroormorenewlines#2 "); $$ = 0; }
oneormorenewlines : NEWLINE                     { debugf ("oneormorenewlines#1 "); $$ = 0; }
                |   oneormorenewlines NEWLINE   { debugf ("oneormorenewlines#2 "); $$ = 0; }

zeroormorenewlinessave : /* empty */            { debugf ("zeroormorenewlinessave#1 "); $$ = 0; }
                |   oneormorenewlinessave       { debugf ("zeroormorenewlinessave#2 "); $$ = $1; }
oneormorenewlinessave : NEWLINE                     { debugf ("oneormorenewlinessave#1 "); $$ = newNodeI (Newlines, 0); }
                |   oneormorenewlinessave NEWLINE   { debugf ("oneormorenewlinessave#2 "); $1->data.num++; $$ = $1; }


%%

/* programs */

int main() {
    int result;
    printf ("Parsing...\n");
    result = yyparse();
    if (!result)
    {
        printf ("\n");
        printf ("XML output:\n");
        printf ("\n");
        outputXML (articlenode);
        printf ("\n");
        printf ("\n");
    }
    return result;
}

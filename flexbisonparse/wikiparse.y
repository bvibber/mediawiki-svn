/* declarations */

%{
#include <stdio.h>
#include "parsetree.h"
int yyerror() { printf ("Syntax error.\n"); }

void debugf (char* s) { printf (s); }

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
             textnoboit textnobold textnoital boldnoitalics italicsnobold 
             TEXT EXTENSION PRELINE
%type <num>  HEADING ENDHEADING

%token  EXTENSION EMPTYCOMMENT BEGINCOMMENT TEXT ENDCOMMENT OPENLINK OPENDBLSQBR CLOSEDBLSQBR PIPE
        OPENPENTUPLECURLY CLOSEPENTUPLECURLY OPENTEMPLATEVAR CLOSETEMPLATEVAR OPENTEMPLATE
        CLOSETEMPLATE NEWLINE PRELINE LISTBULLET LISTNUMBERED HEADING ENDHEADING APO5 APO3 APO2
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
                        { debugf ("preline#1 "); $$ = nodeAddChild ( nodeAddChild (newNode (PreLine), $2), $3); }

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

textorempty     :   /* empty */             { debugf ("textorempty#1 "); $$ = newNodeS (TextToken, ""); }
                |   text                    { debugf ("textorempty#2 "); $$ = $1; }

italicsorbold   :   APO2 textnoital APO2
                        { debugf ("italicsorbold#1 "); $$ = nodeAddChild (newNode (Italics), $2);             }
                |   APO2 textnoital APO3 textnoboit APO5
                        { debugf ("italicsorbold#2 "); $$ = nodeAddChild (newNode (Italics),
                                makeTextBlock ($2, nodeAddChild (newNode (Bold), $4)));                 }
                |   APO2 textnoital
                        { debugf ("italicsorbold#3 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2);   }
                |   APO3 textnobold APO3
                        { debugf ("italicsorbold#4 "); $$ = nodeAddChild (newNode (Bold), $2);                   }
                |   APO3 textnobold APO2 textnoboit APO5
                        { debugf ("italicsorbold#5 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock ($2, nodeAddChild (newNode (Italics), $4)));                  }
                /* Peculiar case, especially for French l'''homme'' => l'<italics>homme</italics> */
                /* We have to use textnobold here, even though textnoital would be logical. */
                /* We use processNestedItalics to fix the weirdness produced by this. */
                |   APO3 textnobold APO2 textnoboit
                        { debugf ("italicsorbold#6 "); $$ = processNestedItalics (makeTextBlock2 (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2), $4));               }
                |   APO3 textnobold APO2
                        { debugf ("italicsorbold#7 "); $$ = processNestedItalics (makeTextBlock (newNodeS
                            (TextToken, "'"), nodeAddChild (newNode (Italics), $2)));               	}
                |   APO3 textnobold
                        { debugf ("italicsorbold#8 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2);     }
                |   APO5 textnoboit APO3 textnoital APO2
                        { debugf ("italicsorbold#9 "); $$ = nodeAddChild (newNode (Italics),
                            makeTextBlock (nodeAddChild (newNode (Bold), $2), $4));                     }
                |   APO5 textnoboit APO2 textnobold APO3
                        { debugf ("italicsorbold#10 "); $$ = nodeAddChild (newNode (Bold),
                            makeTextBlock (nodeAddChild (newNode (Italics), $2), $4));                  }
                |   APO5 textnoboit APO3 textnoital
                        { debugf ("italicsorbold#11 "); $$ = makeTextBlock2 (newNodeS (TextToken, "''"),
                            nodeAddChild (newNode (Bold), $2), $4);                                     }
                |   APO5 textnoboit APO2 textnobold
                        { debugf ("italicsorbold#12 "); $$ = makeTextBlock2 (newNodeS (TextToken, "'''"),
                            nodeAddChild (newNode (Italics), $2), $4);                                  }
                |   APO5 textnoboit
                        { debugf ("italicsorbold#13 ");
                            $$ = makeTextBlock (newNodeS (TextToken, "'''''"), $2);                     }


italicsnobold   :   APO2 textnoboit APO2
                        { debugf ("italicsnobold#1 "); $$ = nodeAddChild (newNode (Italics), $2);              }
                |   APO2 textnoboit
                        { debugf ("italicsnobold#2 "); $$ = makeTextBlock (newNodeS (TextToken, "''"), $2);    }

boldnoitalics   :   APO3 textnoboit APO3
                        { debugf ("boldnoitalics#1 "); $$ = nodeAddChild (newNode (Bold), $2);                   }
                |   APO3 textnoboit
                        { debugf ("boldnoitalics#2 "); $$ = makeTextBlock (newNodeS (TextToken, "'''"), $2);     }

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


/* heading must come after textelement in order to correctly resolve a reduce/reduce conflict: the
 * ENDHEADING token can appear in the middle of a heading, in which case it should be turned back
 * into '=' characters. The reason it's not *always* turned back into '=' characters is that that
 * there's *also* a shift/reduce conflict that causes bison to shift the NEWLINE after the
 * ENDHEADING first, and then it can only reduce the heading. */
heading         :   HEADING textorempty ENDHEADING NEWLINE
                        { debugf ("heading#1 "); $$ = nodeAddChild (newNodeI (Heading, $1), $2); }
                |   HEADING textorempty ENDHEADING  /* for eof */
                        { debugf ("heading#2 "); $$ = nodeAddChild (newNodeI (Heading, $1), $2); }

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

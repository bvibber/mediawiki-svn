#include "TOutput.h"

TOutput* TOutput::current = NULL ;

void TOutput::addHTML ( TUCS s )
    {
    body += s ;
    }
    
void TOutput::addWIKI ( TUCS s )
    {
    TParser p ;
    addHTML ( p.parse ( s ) ) ;
    }

TUCS TOutput::getPage ()
    {
    TUCS r ;
    r += "<HTML>\n" ;
    r += "<HEAD>\n" ;
    r += header ;
    r += "</HEAD>\n" ;
    r += "<BODY " + bodytags + ">\n" ;
    r += body ;
    r += "</BODY>\n" ;    
    r += "</HTML>\n" ;
    return r ;
    }
    
void TOutput::addHeader ( TUCS s )
    {
    header += s + "\n" ;
    }
    
void TOutput::addHeaderLink ( TUCS rel , TUCS href )
    {
    addHeader ( "<link rel=\"" + rel + "\" href=\"" + href + "\">" ) ;
    }


#include "TSpecialPages.h"

TSpecialPages::TSpecialPages ()
    {
    }
    
void TSpecialPages::render ( TUCS what , TArticle &art )
    {
    what.toupper () ;
    if ( what == "RANDOMPAGE" ) randompage ( art ) ;
    else unknownpage ( art ) ;
    }
    
void TSpecialPages::randompage ( TArticle &art )
    {
    DB->getRandomArticle ( art ) ;
    }
    
void TSpecialPages::unknownpage ( TArticle &art )
    {
    art.setSource ( "<h1>No such page!</h1>" ) ;
    }
    
    

#ifndef _SPECIALPAGES_H_
#define _SPECIALPAGES_H_

#include "main.h"

class TSpecialPages
    {
    public :
    TSpecialPages () ;
    void render ( TUCS what , TArticle &art ) ;

    private :
    void randompage ( TArticle &art ) ;
    void unknownpage ( TArticle &art ) ;
    } ;

#endif

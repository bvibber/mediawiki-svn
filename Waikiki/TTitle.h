#ifndef _TTITLE_H_
#define _TTITLE_H_

#include "main.h"

using namespace std ;

class TTitle
    {
    public :
    TTitle ( TUCS t = "" , uint source = FROM_TEXT ) ;
    
    TUCS getNiceTitle () ;
    TUCS getURL () ;
    TUCS getNamespace () ;
    TUCS getDBkey () ;
    
    int getNamespaceID () ;
    
    private :
    virtual void initFromText ( TUCS s ) ;
    
    // Variables
    TUCS ns , title ;
    } ;

#endif

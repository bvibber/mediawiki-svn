#ifndef _TOUTPUT_H_
#define _TOUTPUT_H_

#include "main.h"

using namespace std ;

class TOutput
    {
    public :
    void addHTML ( TUCS s ) ;
    void addWIKI ( TUCS s ) ;
    void addHeader ( TUCS s ) ;
    void addHeaderLink ( TUCS rel , TUCS href ) ;
    
    TUCS getPage () ;
    
    static TOutput *current ;
    VTUCS languageLinks ;
    
    private :
    TUCS bodytags , header ;
    TUCS body ;
    } ;


#endif

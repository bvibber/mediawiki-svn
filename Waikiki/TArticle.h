#ifndef _TARTICLE_H_
#define _TARTICLE_H_

#include "main.h"

using namespace std ;

class TTitle ;

class TArticle
    {
    public :
    TArticle () ;
    virtual ~TArticle () ;
    
    virtual void loadFromFile ( string filename ) ;
    virtual void setTitle ( TTitle t ) ;
    virtual void setSource ( TUCS t ) ;
    virtual TUCS getSource () ;
    virtual TTitle getTitle () ;
    
    private :
    TTitle *title ;
    TUCS source ;
    } ;

#endif

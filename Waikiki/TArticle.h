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
    
    virtual TArticle operator = ( const TArticle &x ) ;
    
    virtual void setTitle ( TTitle t ) ;
    virtual void setSource ( TUCS t ) ;
    virtual TUCS getSource () ;
    virtual TTitle getTitle () ;
    
    TUCS redirectedFrom ;
    bool allowRedirect ;
    
    uint id ;
    
    private :
    TTitle *title ;
    TUCS source ;
    } ;

#endif

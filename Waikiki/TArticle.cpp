#include "TArticle.h"

TArticle TArticle::operator = ( const TArticle &x )
    {
    source = x.source ;
    *title = *x.title ;
    return *this ;
    }

TArticle::TArticle ()
    {
    title = new TTitle () ;
    allowRedirect = true ;
    }

TArticle::~TArticle ()
    {
    delete title ;
    }
    
void TArticle::setTitle ( TTitle t )
    {
    *title = t ;
    }
    
void TArticle::setSource ( TUCS t )
    {
    source = t ;
    }
    
TTitle TArticle::getTitle ()
    {
    return *title ;
    }

TUCS TArticle::getSource ()
    {
    return source ;
    }
    

#include "TArticle.h"

TArticle::TArticle ()
    {
    title = new TTitle ;
    }

TArticle::~TArticle ()
    {
    delete title ;
    }
    
void TArticle::loadFromFile ( string filename )
    {
    ifstream in ( filename.c_str() , ios::in ) ;
    while ( !in.eof() )
        {
        char t[10000] ;
        in.getline ( t , sizeof ( t ) ) ;
        if ( !source.empty() ) source += '\n' ;
        source += t ;
        }
    in.close() ;
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
    

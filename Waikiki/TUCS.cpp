#include "TUCS.h"

// *****************************************************************************
// THE OPERATORS

TUCS TUCS::operator += ( const TUCS &x )
    {
    addtucs ( x ) ;
    return *this ;
    }

TUCS TUCS::operator += ( const char *x )
    {
    addtucs ( TUCS ( x ) ) ;
    return *this ;
    }
    
TUCS TUCS::operator + ( const TUCS &x )
    {
    TUCS temp ;
    temp = *this ;
    temp.addtucs ( x ) ;
    return temp ;
    }
    
uint & TUCS::operator [] ( uint x )
    {
    if ( x < length() ) return v[x] ;
    cerr << "Attempt to access a character at " << x ;
    cerr << ", which is beneath the current string length (" << length() << ")" << endl ;
//    system("PAUSE");	
    exit ( 0 ) ;
    }
    
bool TUCS::operator < ( TUCS &x )
    {
    return v < x.v ;
    }
    
bool TUCS::operator == ( TUCS &x )
    {
    return v == x.v ;
    }
    
// *****************************************************************************
// THE CONSTRUCTORS
    
TUCS::TUCS ( string s )
    {
    clear () ;
    for ( uint a = 0 ; a < s.length() ; a++ ) adduint ( (unsigned char) s[a] ) ;
    }
    
TUCS::TUCS ( const char *s )
    {
    clear () ;
    for ( uint a = 0 ; s[a] ; a++ ) adduint ( (unsigned char) s[a] ) ;
    }
    
TUCS::TUCS ( uint i )
    {
    clear () ;
    adduint ( i ) ;
    }

// *****************************************************************************
// THE PUBLIC METHODS

TUCS TUCS::substr ( uint from , uint len )
    {
    TUCS x ;
    if ( from + len > length() ) len = length() - from ;
    x.v.insert ( x.v.begin() , v.begin()+from , v.begin()+from+len ) ;
    return x ;
    }

TUCS TUCS::substr ( uint from )
    {
    TUCS x ;
    x.v.insert ( x.v.begin() , v.begin()+from , v.end()-1 ) ;
    return x ;
//    return substr ( from , length() ) ; // Better too much...
    }
        
bool TUCS::submatch ( uint from , TUCS &x )
    {
    uint *c1 = c_str() + from ;
    uint *c2 = x.c_str() ;
    while ( *c1 && *c2 && *c1 == *c2 ) { c1++ ; c2++ ; }
    if ( *c2 > 0 ) return false ;
//    if ( *c1 != *c2 ) return false ;
    return true ;
    }
        
void TUCS::explode ( TUCS &seq , VTUCS &r )
    {
    uint l = 0 ;
    uint sl = seq.length() ;
    uint a = find ( seq ) ;
    r.clear () ;
    while ( a < length() )
        {
        r.push_back ( substr ( l , a-l ) ) ;
        a += sl - 1 ;
        l = a+1 ;
        a = find ( seq , l ) ;
        }
    if ( l != a-1 || a == length() ) r.push_back ( substr ( l ) ) ;
    }

void TUCS::explode ( const char *seq , VTUCS &r )
    {
    TUCS s = seq ;
    explode ( s , r ) ;
    }

void TUCS::implode ( const TUCS &seq , const VTUCS &x )
    {
    uint a ;
    clear () ;
    for ( a = 0 ; a < x.size() ; a++ )
        {
        if ( a > 0 ) (*this) += seq ;
        (*this) += x[a] ;
        }
    }
    
string TUCS::getstring ()
    {
    uint a ;
    string s ;
    for ( a = 0 ; a < length() ; a++ )
        {
        uint b = (*this)[a] ;
        if ( b < 255 ) s += (unsigned char) b ;
        else
           {
           char t[10] ;
           sprintf ( t , "&#%d;" , b ) ;
           s += t ;
           }
        }
    return s ;
    }

bool TUCS::empty ()
    {
    return length() == 0 ;
    }
    
uint *TUCS::c_str()
    {
    return (uint*) &(*this)[0] ;
    }
    
#define ISBLANK(_c) (_c==32||_c==9)
    
void TUCS::trim ()
    {
    if ( empty() ) return ;
    int a , b ;
    for ( a = 0 ; a < length() && ISBLANK((*this)[a]) ; a++ ) ;
    for ( b = length() - 1 ; b >= a && ISBLANK((*this)[b]) ; b-- ) ;
    (*this) = substr ( a , b - a + 1 ) ;
    }

void TUCS::fromURL ()
    {
    if ( length() < 3 ) return ;
    uint *a ;
    for ( a = c_str() ; *a ; a++ )
        if ( *a == '+' )
           *a = ' ' ;
           
    for ( a = c_str() ; *(a+2) ; a++ )
        {
        if ( *a == '%' && isHex ( *(a+1) ) && isHex ( *(a+2) ) )
           {
           uint q1 = hex2dec ( *(a+1) ) ;
           uint q2 = hex2dec ( *(a+2) ) ;
           uint b = a - c_str() ;
           *a = q1 * 16 + q2 ;
           modify ( b+1 , 2 , "" ) ;
           a = c_str() ;
           }
        }
    replace ( "\r" , "" ) ;
    }
    
uint TUCS::hex2dec ( uint c )
    {
    if ( c >= '0' && c <= '9' ) return c - '0' ;
    if ( c >= 'A' && c <= 'F' ) return c - 'A' + 10 ;
    if ( c >= 'a' && c <= 'f' ) return c - 'a' + 10 ;
    }
    
TUCS TUCS::fromint ( int i )
    {
    char s[10] ;
    sprintf ( s , "%d" , i ) ;
    return TUCS ( s ) ;
    }
    
bool TUCS::isChar ( uint c )
    {
    if ( c >= 'a' && c <= 'z' ) return true ;
    if ( c >= 'A' && c <= 'Z' ) return true ;
    return false ;
    }

bool TUCS::isDigit ( uint c )
    {
    if ( c >= '0' && c <= '9' ) return true ;
    return false ;
    }

bool TUCS::isHex ( uint c )
    {
    if ( c >= '0' && c <= '9' ) return true ;
    if ( c >= 'A' && c <= 'F' ) return true ;
    if ( c >= 'a' && c <= 'f' ) return true ;
    return false ;
    }

// Removes the last character and returns it
uint TUCS::pop_back ()
    {
    if ( empty() ) return 0 ;
    v.pop_back () ;
    uint i = v[length()] ;
    v[length()] = 0 ;
    return i ;
    }
    
// Default value of count is -1, thus replacing all occurrences
uint TUCS::replace ( TUCS out , TUCS in , int count )
    {
    uint r = 0 , a = find ( out ) ;
    while ( a < length() && count != 0 )
        {
        modify ( a , out.length() , in ) ;
        a = find ( out , a+1 ) ;
        count-- ;
        r++ ;
        }
    return r ;
    }
    
uint TUCS::find ( TUCS what , uint start )
    {
    uint a , b , l = 0 ;
    uint sl = what.length() ;
    
    for ( a = start ; a + sl <= length() ; a++ )
        if ( submatch ( a , what ) )
           return a ;
           
    return length() ;
    }

void TUCS::modify ( uint from , uint len , TUCS repl )
    {
    if ( from + len > length() ) return ; // Failed
    v.erase ( v.begin() + from , v.begin() + from + len ) ;
    v.insert ( v.begin() + from , repl.v.begin() , repl.v.end()-1 ) ;
    }
    
void TUCS::toupper ()
    {
    if ( empty() ) return ;
    uint *a ;
    for ( a = c_str() ; *a ; a++ )
        {
        if ( *a >= 'a' && *a <= 'z' ) *a = *a - 'a' + 'A' ;
        else if ( *a == 'ä' ) *a = 'Ä' ;
        else if ( *a == 'ö' ) *a = 'Ö' ;
        else if ( *a == 'ü' ) *a = 'Ü' ;
        }
    }
    
// *****************************************************************************
// THE PRIVATE METHODS

uint TUCS::getuint ( uint x )
    {
    if ( x < length() ) return v[x] ;
    cerr << "Attempt to access a character at " << x ;
    cerr << ", which is beneath the current string length (" ;
    cerr << length() << ")" << endl ;
//    system("PAUSE");	
    exit ( 0 ) ;
    }

void TUCS::adduint ( uint i )
    {
    v[v.size()-1] = i ;
    v.push_back ( 0 ) ;
    }
    
uint TUCS::length ()
    {
    return v.size() - 1 ;
    }

void TUCS::clear ()
    {
    v.clear() ;
    v.push_back ( 0 ) ;
    }

void TUCS::addtucs ( const TUCS &x )
    {
    v.insert ( v.end()-1 , x.v.begin() , x.v.end()-1 ) ;
    }


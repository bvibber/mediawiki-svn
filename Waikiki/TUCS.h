/*
This is the TUCS class, which stands for Type of UniCode String.
It manages a zero-terminated string where each character consists of
the "uint" type, which is short for "unsigned integer".
It is based on the STL "vector <uint>" type, so the zero at the end
would not be necessary. However, the "c_str()" function returns a
uint pointer, which can run through the vector searching for the
zero element at the end.
*/

#ifndef _TUCS_H_
#define _TUCS_H_

#include <iostream>
#include <stdlib.h>
#include <vector>
#include <string>
#include <map>

using namespace std;

#define SINGLE_QUOTE 39

struct ltstr
{
  bool operator()(const char* s1, const char* s2) const
  {
    return strcmp(s1, s2) < 0;
  }
};


class TUCS ;
typedef unsigned int uint ;
typedef vector <TUCS> VTUCS ;
typedef map < char* , TUCS , ltstr > MTUCS ;



class TUCS // Type of UniCodeString
    {
    public :
    TUCS ( string s = "" ) ;
    TUCS ( const char *s ) ;
    TUCS ( uint i ) ;
    
    // Operators
    virtual TUCS operator += ( const char* x ) ;
    virtual TUCS operator += ( const TUCS &x ) ;
    virtual TUCS operator + ( const TUCS &x ) ;
    virtual uint & operator [] ( uint x ) ;
    virtual uint length () ;
    virtual bool operator < ( TUCS &x ) ;
    virtual bool operator == ( TUCS &x ) ;
//    virtual bool operator != ( TUCS &x ) { return ! ( *this == x ) ; } ;
    virtual bool operator <= ( TUCS &x ) { return *this < x || *this == x ; } ;
    virtual bool operator > ( TUCS &x ) { return ! ( *this <= x ) ; } ;
    virtual bool operator >= ( TUCS &x ) { return ! ( *this < x ) ; } ;
    
    // Input and output
    virtual string getstring () ;
    
    // Search, replace & other stunts
    virtual uint replace ( TUCS out , TUCS in , int count = -1 ) ;
    virtual uint find ( TUCS what , uint start = 0 ) ;
    virtual void modify ( uint from , uint len , TUCS repl ) ;
    virtual void explode ( TUCS &seq , VTUCS &r ) ;
    virtual void explode ( const char *seq , VTUCS &r ) ;
    virtual void implode ( const TUCS &seq , const VTUCS &x ) ;
    virtual TUCS substr ( uint from , uint len ) ;
    virtual TUCS substr ( uint from ) ;
    virtual void trim () ;
    virtual void toupper () ;
    virtual uint pop_back () ;
    
    static TUCS fromint ( int i ) ;

    virtual void clear () ;
    virtual uint *c_str () ;
    virtual bool empty () ;
    static bool isChar ( uint c ) ;
    
    // No-no
    private :
    virtual void adduint ( uint i ) ;
    virtual void addtucs ( const TUCS &x ) ;
    virtual uint getuint ( uint x ) ;
    virtual bool submatch ( uint from , TUCS &x ) ;

    vector <uint> v ;
    } ;

static bool operator != ( TUCS a , TUCS b ) { return ! ( a == b ) ; }
static TUCS operator + ( const char *a , TUCS b ) { return TUCS ( (const char*) a ) + b ; }
static bool operator == ( TUCS a , const char *b ) { TUCS c(b); return ( a == c ) ; }

#endif

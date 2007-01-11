/*
 * (c) 2006 by Magnus Manske
 * Released under the terms of the GNU public license (GPL)
*/
#include <wx/wxprec.h>
#ifndef WX_PRECOMP
   #include <wx/wx.h>
#endif

#include "ZenoFile.h"
#include "base.h"
#include <wx/wfstream.h>
#include <wx/zstream.h>
#include <wx/sstream.h>

#include <wx/arrimpl.cpp> // this is a magic incantation which must be done!
WX_DEFINE_OBJARRAY(ArrayOfZenoArticles);

#define NODATACACHE
#define NOARTICLECACHE

char *qunicode = NULL ;

wxChar CharToQ ( wxChar c )
{
    return qunicode[(int)c] ;
}

wxString String2Q ( wxString s )
{
    int a ;
    for ( a = 0 ; a < s.Length() ; a++ ) s[a] = qunicode[(int)s[a]] ;
    s = s.Lower() ;
    return s ;
}

wxString ArrayToString ( const wxArrayInt &array )
{
    wxString ret ( ' ' , array.GetCount() ) ;
    for ( int a = 0 ; a < array.GetCount() ; a++ )
        ret[a] = (wxUint16) array[a] ;
    return ret ;
}

wxArrayInt StringToArray ( wxString s )
{
    wxArrayInt ret ;
    for ( int a = 0 ; a < s.Length() ; a++ )
    {
        ret.Add ( s[a] ) ;
    }
    return ret ;
}

wxArrayInt ZenoToArray ( char *s )
{
    wxArrayInt ret ;
    for ( unsigned char *t = (unsigned char*) s ; *t ; t++ )
    {
        if ( *t > 2 )
        {
            ret.Add ( (unsigned char) *t ) ;
        } else if ( *t == 1 ) {
            wxUint16 l1 = (unsigned char) *(t+1) ;
            wxUint16 l2 = (unsigned char) *(t+2) ;
            wxUint16 l = ( l2 << 8 ) | l1 ;
            t += 2 ;
            ret.Add ( (wxChar) l ) ;
        } else {
            wxUint16 l1 = (unsigned char) *(t+1) ;
            wxUint16 l = l1 << 8 ;
			t += 2 ;
            ret.Add ( (wxChar) l ) ;
        }
    }
    return ret ;
}

wxString ZenoToString ( char *s )
{
    wxArrayInt array = ZenoToArray ( s ) ;
    return ArrayToString ( array ) ;
}


//________________________________________________________________________________________________

ZenoArticle::ZenoArticle ()
{
    ok = false ;
    rExtra = NULL ;
	data = NULL ;
    index = -1 ;
    load_qunicode() ;
}

ZenoArticle::~ZenoArticle ()
{
//    if ( rExtra ) delete rExtra ;
}

int ZenoArticle::Compare ( wxString s )
{
    wxArrayInt t = StringToArray ( s ) ;
	return Compare ( t ) ;
}

int ZenoArticle::Compare ( wxArrayInt t , bool anything_starting_with )
{
    if ( !ok ) return 1 ; // Paranoia
    
    int ret = -2 ; // Not set
    wxArrayInt orig = ZenoToArray ( rExtra ) ;
    
    if ( anything_starting_with )
    {
        while ( orig.GetCount() > t.GetCount() ) orig.RemoveAt ( orig.GetCount()-1 ) ;
    }
    
    orig.Add ( 0 ) ;
    t.Add ( 0 ) ;
    
    int p1 , p2 ;
    
    // Round 1
    p1 = 0 ;
    p2 = 0 ;
    while ( ret == -2 )
    {
        if ( !orig[p1] )
        {
            if ( !t[p2] ) break ;
            else { ret = -1 ; break ; }
        }
        else if ( !t[p2] ) { ret = 1 ; break ; }
        int i1 = (unsigned char) qunicode[orig[p1]] ;
        int i2 = (unsigned char) qunicode[t[p2]] ;
        int i = i1 - i2 ;
        if ( i ) { ret = i > 0 ? 1 : -1 ; break ; }
        p1++ ;
        p2++ ;
    }
//    wxMessageBox ( _T("Round 1 done") ) ;

    // Round 2
    p1 = 0 ;
    p2 = 0 ;
    while ( ret == -2 )
    {
        if ( !orig[p1] )
        {
            if ( !t[p2] ) { ret = 0 ; break ; }
            else { ret = -1 ; break ; }
        }
        if ( !t[p2] ) { ret = 1 ; break ; }
        int i1 = orig[p1] ;
        int i2 = t[p2] ;
        int i = i1 - i2 ;
        if ( i ) { ret = i > 0 ? 1 : -1 ; break ; }
        p1++ ;
        p2++ ;
    }
//    wxMessageBox ( _T("Round 2 done") ) ;
    
//    wxMessageBox ( ArrayToString(orig) + _T(" : ") + ArrayToString(t) , wxString::Format ( _T("COMPARE : %d") , ret ) ) ;
    
//    delete orig ;
//    delete t ;
    if ( ret == -2 ) ret = -1 ; // Emergency brake
//    return title.CmpNoCase ( s ) ;

    return ret ;
}

void ZenoArticle::load_qunicode()
{
    if ( qunicode ) return ;
    wxFile f ( ((MainApp*)wxTheApp)->frame->dirbase + _T("qunicode.txt") ) ;
    long length = f.Length() ;
    qunicode = new char[length] ;
    f.Read ( qunicode , length ) ;
}

char *ZenoArticle::GetBlob()
{
    if ( !ok ) return NULL ;
    char *data = zfile->GetBlob ( rFilePos , rFileLen ) ;
	zfile->SetCacheData ( index , data ) ;
    return data ;
}

wxString ZenoArticle::GetText()
{
    if ( !ok ) { wxMessageBox ( _T("!!2") ) ; return _T("") ; }
    if ( rCompression == 2 ) return GetTextFromZip() ;
    return GetTextFromPlain() ;
}

wxString ZenoArticle::GetTextFromPlain()
{
    if ( !ok ) return _T("") ;
    char *data = zfile->GetBlob ( rFilePos , rFileLen ) ;
    wxString ret ( data , wxConvLocal ) ;
    delete data ;
    return ret ;
}

wxString ZenoArticle::GetTextFromZip()
{
    if ( !ok ) return _T("") ;
    char *data = zfile->GetBlob ( rFilePos , rFileLen ) ;
    wxString fn = wxGetTempFileName ( _T("YAWR") ) ;
    wxString ret ;


    // Write temporary file
    if ( 1 )
    {
        wxFile file ( fn , wxFile::write ) ;
        file.Write ( data , rFileLen ) ;
        file.Close() ;
    }
    delete data ;
    
    // Read temporary file
    if ( 1 )
    {
        wxFFileInputStream in ( fn ) ;
        wxZlibInputStream zin ( in ) ;
        
        wxString fn2 = wxGetTempFileName ( _T("YAWR") ) ;
        wxFileOutputStream f2 ( fn2 ) ;
        zin.Read ( f2 ) ;
        f2.Close() ;
        
        wxFile f3 ( fn2 ) ;
        int len = f3.Length() ;
        char *n = new char[len+2] ;
        f3.Read ( n , len ) ;
        f3.Close() ;
        wxRemoveFile ( fn2 ) ; // Deleting temporary file
        n[len] = 0 ; // Paranoia
        ret = wxString ( n , wxConvISO8859_1 ) ; // Why oh why isn't that UTF-8???
    }


/*
    // Write temporary file
    if ( 1 )
    {
        wxFile file ( fn , wxFile::write ) ;
        file.Write ( data , rFileLen ) ;
        file.Close() ;
    }
    delete data ;
    
    // Read temporary file
    if ( 1 )
    {
        wxFFileInputStream in ( fn ) ;
        wxZlibInputStream zin ( in ) ;
        
        wxString fn2 = wxGetTempFileName ( _T("YAWR") ) ;
        wxFileOutputStream f2 ( fn2 ) ;
        zin.Read ( f2 ) ;
        f2.Close() ;
        
        wxFile f3 ( fn2 ) ;
        int len = f3.Length() ;
        char *n = new char[len+2] ;
        f3.Read ( n , len ) ;
        f3.Close() ;
        wxRemoveFile ( fn2 ) ; // Deleting temporary file
        n[len] = 0 ; // Paranoia
        ret = wxString ( n , wxConvISO8859_1 ) ; // Why oh why isn't that UTF-8???
    }
*/    
    wxRemoveFile ( fn ) ; // Deleting temporary file
    return ret ;
}

//________________________________________________________________________________________________


ZenoFile::ZenoFile ()
{
    m_success = false ;
    indexlist = NULL ;
}

unsigned long ZenoFile::ReadLong ( wxFile &f )
{
	unsigned long ret ;
	f.Read ( &ret , 4 ) ;
	ret = wxUINT32_SWAP_ON_BE ( ret ) ;
	return ret ;
}

wxUint16 ZenoFile::ReadWord ( wxFile &f )
{
	wxUint16 ret ;
	f.Read ( &ret , 2 ) ;
	ret = wxUINT16_SWAP_ON_BE ( ret ) ;
	return ret ;
}

bool ZenoFile::Open ( wxString filename )
{
    m_success = false ;
    if ( !wxFileExists ( filename ) ) return false ;
    m_filename = filename ;
    
    unsigned long dummy ;
    long l1 , l2 ;
    wxFile f ( filename ) ;
	rMagicNumber = ReadLong ( f ) ;
    rVersion = ReadLong ( f ) ;
    rCount = ReadLong ( f ) ;
    dummy = ReadLong ( f ) ;

    // rIndexPos
	l1 = ReadLong ( f ) ;
	l2 = ReadLong ( f ) ;
    rIndexPos = wxLongLong ( l2 , l1 ) ;

	rIndexLen = ReadLong ( f ) ;
	rFlags = ReadLong ( f ) ;
    
    // rIndexPtrPos
	l1 = ReadLong ( f ) ;
	l2 = ReadLong ( f ) ;
    rIndexPtrPos = wxLongLong ( l2 , l1 ) ;

    rIndexPtrLen  = ReadLong ( f ) ;
    rUnused[0] = ReadLong ( f ) ;
	rUnused[1] = ReadLong ( f ) ;
    rUnused[2] = ReadLong ( f ) ;
    rUnused[3] = ReadLong ( f ) ;
    
    if ( 1439867043 != rMagicNumber ) return false ;
    
//    wxMessageBox ( wxString::Format ( _T("%d / %d / %d") , rIndexPtrLen , rCount*4 , rIndexLen ) , rIndexPos.ToString() ) ;
    
    m_success = true ;

    ReadIndexList ( f ) ;
//    ReadIndex ( f ) ;

    return m_success ;
}

bool ZenoFile::Ok ()
{
    return m_success ;
}

/**
 * Workaround for 64-bit-seeking on 32 bit systems
 */
void ZenoFile::Seek ( wxFile &f , wxLongLong pos )
{
    wxLongLong chunk = 1024*1024*1024*1 ; // 1GB
	bool first = true ;
    while ( pos > chunk )
    {
		if ( first ) f.Seek ( chunk.ToLong() , wxFromStart) ;
        else f.Seek ( chunk.ToLong() , wxFromCurrent ) ;
        pos -= chunk ;
		first = false ;
    }
    if ( first ) f.Seek ( pos.ToLong() , wxFromStart ) ;
	else f.Seek ( pos.ToLong() , wxFromCurrent ) ;
}

void ZenoFile::ReadIndexList ( wxFile &f )
{
    if ( !Ok() ) return ;
    
    Seek ( f , rIndexPtrPos ) ;
    indexlist = new unsigned long [rCount+5] ;
    f.Read ( indexlist , rIndexPtrLen ) ;
	for ( unsigned long l = 0 ; l < rCount ; l++ ) indexlist[l] = wxUINT32_SWAP_ON_BE ( indexlist[l] ) ;
}

ZenoArticle ZenoFile::ReadSingleArticle ( unsigned long number )
{
    ZenoArticle art ;
    if ( !Ok() ) return art ;
	
	art = LookInCache ( number ) ;
	if ( art.ok ) return art ;
	
    wxFile f ( m_filename ) ;
    ReadSingleArticle ( number , f , art ) ;
	AddToCache ( art ) ;
}

void ZenoFile::ReadSingleArticle ( unsigned long number , wxFile &f , ZenoArticle &art )
{
	art.ok = false ;
    if ( number == 0 || number >= rCount ) return ;
	
	art = LookInCache ( number ) ;
	if ( !art.ok )
	{
		Log ( _T("Hard-looking for ") + art.title , _T("ZenoFile::ReadSingleArticle") ) ;
		unsigned long l = indexlist[number] ;
		wxLongLong pos = rIndexPos ;
		pos += l ;
		Seek ( f , pos ) ;
		ReadArticleData ( f , art ) ;
		art.index = number ;
		if ( art.ok ) AddToCache ( art ) ;
	}
	if ( art.ok ) Log ( art.title + _T(" loaded successful") , _T("ZenoFile::ReadSingleArticle") ) ;
	else Log ( wxString::Format ( _T("ARTICLE #%d NOT FOUND!") , number ) , _T("ZenoFile::ReadSingleArticle") ) ;
}

void ZenoFile::ReadIndex ()
{
    wxFile f ( m_filename ) ;
    ReadIndex ( f ) ;
}

void ZenoFile::ReadIndex ( wxFile &f )
{
    if ( !Ok() ) return ;
    
    if ( articles.GetCount() > 0 ) return ; // Already read this one

    articles.Alloc ( rCount+5 ) ;
    Seek ( f , rIndexPos ) ;

    wxStartTimer() ;
    ZenoArticle art ;
    for ( unsigned long count = 0 ; count < rCount ; count++ )
    {
        articles.Add ( art ) ;
        ReadArticleData ( f , articles[count] ) ;
    }
    
    long l = wxGetElapsedTime() ;
    wxMessageBox ( wxString::Format ( _T("%d ms") , l ) ) ;
}

unsigned char *ZenoFile::ReadLongFromBuffer ( unsigned char *pos , unsigned long &l )
{
    unsigned long *u = (unsigned long *) pos ;
    l = *u ;
    return pos+4 ;
}

unsigned char *ZenoFile::ReadWordFromBuffer ( unsigned char *pos , wxUint16 &l )
{
    wxUint16 *u = (wxUint16 *) pos ;
    l = *u ;
    return pos+2 ;
}

void ZenoFile::ReadArticleData ( wxFile &f , ZenoArticle &art )
{
    unsigned long l1 , l2 ;
    art.rExtraLen = 0 ;

    unsigned char *y , z[26] ;
    f.Read ( z , 26 ) ;
    y = z ;
    
    // rFilePos
    y = ReadLongFromBuffer ( y , l1 ) ;
    y = ReadLongFromBuffer ( y , l2 ) ;
    art.rFilePos = wxLongLong ( l2 , l1 ) ;
    
    y = ReadLongFromBuffer ( y , art.rFileLen ) ;
    art.rCompression = *y++ ;
    art.rMime = *y++ ;
    art.rSubtype = *y++ ;
    art.rSearchFlag = *y++ ;
    y = ReadLongFromBuffer ( y , art.rSubtypeParent ) ;
    y = ReadLongFromBuffer ( y , art.rLogicalNumber ) ;
    
    wxUint16 w ;
    y = ReadWordFromBuffer ( y , w ) ;
    art.rExtraLen = w ;


/*
    // rFilePos
	l1 = ReadLong ( f ) ;
	l2 = ReadLong ( f ) ;
    art.rFilePos = wxLongLong ( l2 , l1 ) ;

    art.rFileLen = ReadLong ( f ) ;
    f.Read ( &art.rCompression , 1 ) ;
    f.Read ( &art.rMime , 1 ) ;
    f.Read ( &art.rSubtype , 1 ) ;
    f.Read ( &art.rSearchFlag , 1 ) ;
    art.rSubtypeParent = ReadLong ( f ) ;
    art.rLogicalNumber = ReadLong ( f ) ;
    art.rExtraLen = ReadWord ( f ) ;
*/
    if ( art.rExtra ) delete art.rExtra ; // Clear last entry
    art.rExtra = new char[art.rExtraLen+5] ;
    f.Read ( art.rExtra , art.rExtraLen ) ;
    
    art.title = ZenoToString ( art.rExtra ) ;
//    if ( art.rSubtype == 0 && art.title.Length()==2 ) wxMessageBox ( _T("OH NO!") , wxString::Format ( _T("%d:%d") , art.rExtraLen , art.rExtra[0] ) ) ;
    
    art.zfile = this ;
    art.ok = true ;
}


/**
 * Binary search
 */
unsigned long ZenoFile::FindPageID ( wxString page , bool anything_starting_with )
{
    if ( !Ok() ) return 0 ;
	Log ( page , _T("ZenoFile::FindPageID") ) ;
	
	// Trying cache, if it is turned on
	int cc = LookInCache ( page ) ;
	if ( cc != -1 ) return cc ;
	
	// OK, we'll have to look for this the hard way
    wxArrayInt page_array = StringToArray ( page ) ;
    unsigned long min = 0 ;
    unsigned long max = rCount - 1 ;
    wxFile file ( m_filename ) ;
    
    wxString show ;
    unsigned long lastmid = 0 ;
    while ( max >= min )
    {
        unsigned long mid = ( min + max ) / 2 ;
        ZenoArticle art ;
        ReadSingleArticle ( mid , file , art ) ;
        if ( art.rSubtype > 0 )
        {
//            mid = art.rSubtypeParent ;
            mid -= art.rSubtype ;
            if ( min > mid ) min = mid ;
            ReadSingleArticle ( mid , file , art ) ;
        }
        if ( mid == lastmid ) break ; // Not found
        lastmid = mid ;
        
        int i = art.Compare ( page_array , anything_starting_with ) ;
        show += wxString::Format ( _T("%d:%d (%d) EQ %d : ") , min , max , mid , i ) + art.title + _T(" seek ") + page + _T("\n\r") ;
        
        if ( i == 0 ) return mid ;
        if ( min == max ) break ; // Not found
        if ( min+1 == max ) max = min ;
        else if ( i > 0 ) max = mid ;
        else if ( i < 0 ) min = mid ;
    }
    
    Log ( _T("NOT FOUND : ") + page + _T("\n") + show , _T("ZenoFile::FindPageID") ) ;
    return 0 ; // Not found
}

char *ZenoFile::GetBlob ( wxLongLong pos , unsigned long length )
{
    if ( !Ok() ) return 0 ;
	
    char *data = GetCacheData ( pos , length ) ;
	if ( data ) return data ;
	
    
    wxFile file ( m_filename ) ;
    Seek ( file , pos ) ;
	data = new char[length] ;
    file.Read ( data , length ) ;
    return data ;
}

void ZenoFile::Log ( wxString message , wxString function )
{
	((MainApp*)wxTheApp)->frame->Log ( message , function ) ;
}


unsigned long ZenoFile::GetFirstArticleStartingWith ( wxString start )
{
    // Trying shortcut
    unsigned long l , l2 , step ;
    l = FindPageID ( start ) ;
    if ( l > 0 ) return l ;
    
    wxArrayInt page_array = StringToArray ( start ) ;
    wxString s2 = String2Q ( start ) ;
    wxFile file ( m_filename ) ;
    ZenoArticle art ;
    
    //OK, the long way...
    l = rCount - 1 ;
    step = l / 2 ;
    while ( step > 0 )
    {
        l2 = l - step ;
        ReadSingleArticle ( l2 , file , art ) ;
        if ( art.rLogicalNumber > 0 )
        {
            l2 -= art.rLogicalNumber ;
            ReadSingleArticle ( l2 , file , art ) ;
        }

        wxString t2 = String2Q ( art.title ) ;
        int i = t2 < s2 ? 1 : 0 ;
        
//        int i = art.Compare ( page_array , true ) ;
//        wxMessageBox ( s2 + _T(":") + t2 , wxString::Format ( _T("%d") , i ) ) ;
        if ( i > 0 )
        {
            l2 = l ;
            step /= 2 ;
        }
        
        l = l2 ;
        if ( step > l ) step = l ;
    }
    return l ;



/*
    unsigned long l = FindPageID ( start , true ) , l2 ;
    if ( l == 0 ) return 0 ; // No entry starting with the start word
    wxArrayInt orig = StringToArray ( start ) ;
    start = String2Q ( start.Mid(0,3) ) ;
    
    // Backwards search until start doesn't match anymore
    int step = l / 4 ; // Just some starting size
    if ( step == 0 ) step = 1 ;
    while ( step > 0 ) 
    {
        l2 = SeekArticleRelative ( l , -step ) ;
        ZenoArticle art = ReadSingleArticle ( l2 ) ;
        wxString nt = String2Q ( art.title.Mid(0,3) ) ;
        if ( !art.ok || nt != start )
        {
            if ( step == 1 ) return l ;
            step /= 2 ; // Try again, with smaller steps
            l2 = l ;
        }
        l = l2 ;
    }
    return 0 ;*/
}

unsigned long ZenoFile::SeekArticleRelative ( unsigned long start , long diff )
{
    if ( diff == 0 ) return start ; // You're a funny guy
    long dir = diff > 0 ? 1 : -1 ;
    diff *= dir ;
    for ( start += dir ; diff > 0 ; start += dir )
    {
        ZenoArticle art = ReadSingleArticle ( start ) ;
        if ( !art.ok ) return start-dir ; // Paranoia
        if ( art.rLogicalNumber == 0 && !art.title.IsEmpty() ) diff-- ;
    }
    return start ;
}

wxArrayString ZenoFile::GetArticleTitles ( unsigned long start , unsigned long number )
{
    wxArrayString ret ;
    ZenoArticle art ;
    start-- ; // Assuming you'll never try 0!
    while ( number > 0 )
    {
        do {
            art = ReadSingleArticle ( ++start ) ;
        } while ( art.ok && ( art.rLogicalNumber != 0 || art.rExtraLen < 3 ) ) ;
        if ( art.ok ) ret.Add ( art.title ) ;
        else return ret ; // Paranoia
        number-- ;
    }
    return ret ;
}



// __________________________________________________________________________________________
// Caching stuff

int ZenoFile::LookInCache ( wxString page )
{
#ifdef NOARTICLECACHE
	return -1 ;
#else
    if ( !Ok() ) return -1 ;
	
	int a ;
	for ( a = 0 ; a < cache.GetCount() ; a++ )
	{
        int i = cache[a].Compare ( page ) ;
		if ( i == 0 ) { Log ( page , _T("ZenoFile::LookInCache") ) ; return cache[a].index ; }
	}
	return -1 ;
#endif
}

ZenoArticle ZenoFile::LookInCache ( unsigned long number )
{
	ZenoArticle art ;
#ifdef NOARTICLECACHE
	return art ;
#else
    if ( !Ok() ) return art ;
	
	int a ;
	for ( a = 0 ; a < cache.GetCount() ; a++ )
	{
		if ( cache[a].index == number ) { Log ( cache[a].title , _T("ZenoFile::LookInCache") ) ; return cache[a] ; }
	}
	return art ;	
#endif
}

void ZenoFile::AddToCache ( ZenoArticle art )
{
#ifndef NOARTICLECACHE
    if ( !Ok() ) return ;
	if ( LookInCache ( art.index ) . ok ) return ; // Already in cache
	cache.Add ( art ) ;
#endif
}

void ZenoFile::SetCacheData ( long number , char *data )
{
#ifdef NODATACACHE
	return ;  // Data caching deactivated; seems to have no significant effect except wasting memory
#else
    if ( !Ok() ) return ;
	
	int a ;
	for ( a = 0 ; a < cache.GetCount() ; a++ )
	{
		if ( cache[a].index == number ) break ;
	}
	if ( a == cache.GetCount() ) return ; // No such article in cache
	if ( cache[a].data ) return ; // Already cached
	if ( cache[a].rFileLen > 10*1024 ) return ; // Do not cache data over 10KB
	
	char *d = new char[cache[a].rFileLen] ;
	memcpy ( d , data , cache[a].rFileLen ) ;
	cache[a].data = d ;
	Log ( _T("Storing cache data") , _T("ZenoFile::SetCacheData")  ) ;
#endif
}

char *ZenoFile::GetCacheData ( wxLongLong pos , unsigned long length )
{
#ifdef NODATACACHE
	return NULL ; // Data caching deactivated; seems to have no significant effect except wasting memory
#else
    if ( !Ok() ) return NULL ;
	
	int a ;
	for ( a = 0 ; a < cache.GetCount() ; a++ )
	{
		if ( cache[a].rFilePos == pos && cache[a].rFileLen == length ) break ;
	}
	if ( a == cache.GetCount() ) return NULL ; // No such article in cache
	if ( !cache[a].data ) return NULL ;
	
	char *ret = new char[length] ;
	memcpy ( ret , cache[a].data , length ) ;
	Log ( _T("Returning cached data") , _T("ZenoFile::GetCacheData")  ) ;
	return ret ;
#endif
}


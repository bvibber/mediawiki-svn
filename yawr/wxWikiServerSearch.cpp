/*
 * (c) 2006 by Magnus Manske
 * Released under the terms of the GNU public license (GPL)
*/
#include <wx/wxprec.h>
#ifndef WX_PRECOMP
   #include <wx/wx.h>
#endif

#include "base.h"

class TSearchWordTree ;
class TSearchWordTreeTableLine ;

WX_DECLARE_OBJARRAY(TSearchWordTree*, ArrayOfTSearchWordTree);
WX_DECLARE_OBJARRAY(TSearchWordTreeTableLine, ArrayOfTSearchWordTreeTableLine);

enum
{
	TREE_NORMAL_LIST = 0 ,
	TREE_WORD ,
	TREE_AND ,
	TREE_OR
} ;

// CAUTON : The whole search section has no user input sanity checks; eg unmatching () in the search string might bring it down

class TSearchWordTreeTableLine
{
    public :
    unsigned long article_id ;
    unsigned long word_pos ;
    
    bool operator == ( const TSearchWordTreeTableLine &x )
    {
        return article_id == x.article_id && word_pos == x.word_pos ;
    }
    bool operator < ( const TSearchWordTreeTableLine &x )
    {
        return article_id < x.article_id || ( article_id == x.article_id && word_pos < x.word_pos ) ;
    }
} ;

int CMPFUNCtable ( TSearchWordTreeTableLine **first, TSearchWordTreeTableLine **second)
{
    if ( (*first)->article_id < (*second)->article_id ) return -1 ;
    if ( (*first)->article_id > (*second)->article_id ) return 1 ;
    if ( (*first)->word_pos < (*second)->word_pos ) return -1 ;
    if ( (*first)->word_pos > (*second)->word_pos ) return 1 ;
    return 0 ;
}


class TSearchWordTree
{
	public :
	TSearchWordTree ( TSearchWordTree *parent = NULL ) ;
	void Parse ( wxArrayString words ) ;
	int CheckKeyword ( wxString s ) ;
	int ScanKeyword ( int start , const wxArrayString &wordlist , TSearchWordTree &child ) ;
	wxString GetHTMLtree ( int depth = 0 ) ;
	bool IsUsingWildcards() ;
	wxArrayString Process ( ZenoFile *index , int depth = 0 ) ;
	bool StringHasWildcards ( wxString s ) ;
	void CreateSingleWordTable ( wxString word , ZenoFile *index ) ;
	void CreateSingleWildcardTable ( wxString word , ZenoFile *index ) ;
	void ProcessList () ;
	void ProcessAND () ;
	void ProcessOR () ;
	TSearchWordTree *GetRoot() ;
	void FilterTitleAgainstTable ( wxString word ) ;
	void Explode ( wxString query , wxArrayString &words , bool is_parameter = true ) ;
	bool DoesMatchTitle ( wxString word , wxString title ) ;
	void DumpTable ( wxString filename ) ;
//	static wxString unescape ( wxString s ) ;
	
	ArrayOfTSearchWordTree children ;
	TSearchWordTree *_parent ;
	int type ;
	bool title_only , fuzzy ;
	wxString word ;
	ArrayOfTSearchWordTreeTableLine table ;
} ;

#include <wx/arrimpl.cpp> // this is a magic incantation which must be done!
WX_DEFINE_OBJARRAY(ArrayOfTSearchWordTree);
WX_DEFINE_OBJARRAY(ArrayOfTSearchWordTreeTableLine);




TSearchWordTree::TSearchWordTree ( TSearchWordTree *parent )
{
	_parent = parent ;
	type = TREE_NORMAL_LIST ;
	title_only = false ;
	fuzzy = false ;
}

TSearchWordTree *TSearchWordTree::GetRoot()
{
    if ( _parent ) return _parent->GetRoot() ;
    else return this ;
}

void TSearchWordTree::Parse ( wxArrayString words )
{
	// Cleanup; unnecessary, but who knows?
	children.Clear() ;
	
	// Removing leading keywords
	while ( CheckKeyword ( words[0] ) > -1 ) words.RemoveAt ( 0 ) ;
	
	// Initialize child list
	TSearchWordTree *wt = new TSearchWordTree ( this ) ;
	int begin = ScanKeyword ( 0 , words , *wt ) ;
	children.Add ( wt ) ;
	type = TREE_WORD ;
	
	// Scan remaining
	for ( int a = begin ; a < words.GetCount() ; a++ )
	{
		int keyword ;
		keyword = CheckKeyword ( words[a] ) ;
		if ( keyword == -1 ) keyword = TREE_AND ; // Default AND
		else a++ ;
		
		wt = new TSearchWordTree ( this ) ;
		a = ScanKeyword ( a , words , *wt ) - 1 ;
		
		TSearchWordTree *wt2 = new TSearchWordTree ( this ) ; // New child
		
		for ( int b = 0 ; b < children.GetCount() ; b++ )
		{
			TSearchWordTree *s1 = children[b] ;
			wt2->children.Add ( s1 ) ;
		}
		wt2->children.Add ( wt ) ;
		wt2->type = keyword ;
		wt2->word = word ;
		type = TREE_NORMAL_LIST ;
		word.Empty() ;
		children.Empty() ;
		children.Add ( wt2 ) ;
	}
}

int TSearchWordTree::ScanKeyword ( int start , const wxArrayString &wordlist , TSearchWordTree &child )
{
	child = TSearchWordTree ( this ) ;
	
	// Simple word?
	if ( wordlist[start] != _T("(") )
	{
		child.type = TREE_WORD ;
		child.word = wordlist[start] ;
		return start + 1 ;
	}
	
	// OK, some () suff
	wxArrayString newwordlist ;
	int cnt = 1 ;
	for ( start++ ; start < wordlist.GetCount() && cnt > 0 ; start++ )
	{
		if ( wordlist[start] == _T("(") ) { cnt++ ; continue ; }
		if ( wordlist[start] == _T(")") ) { cnt-- ; continue ; }
		newwordlist.Add ( wordlist[start] ) ;
	}
	child.Parse ( newwordlist ) ;
	return start+1 ;
}

int TSearchWordTree::CheckKeyword ( wxString s )
{
	if ( s == _T("and") ) return TREE_AND ;
	if ( s == _T("or") ) return TREE_OR ;
	return -1 ; // No keyword
}

wxString TSearchWordTree::GetHTMLtree ( int depth )
{
	int a ;
	wxString ret ;
	if ( type == TREE_WORD ) ret = _T("{") + word + _T("}") ;
	for ( a = 0 ; a < children.GetCount() ; a++ )
	{
		if ( a > 0 )
		{
			if ( type == TREE_NORMAL_LIST ) ret += _T(" , ") ;
			if ( type == TREE_AND ) ret += _T(" AND ") ;
			if ( type == TREE_OR ) ret += _T(" OR ") ;
			if ( type == TREE_WORD ) ret += _T(" _ ") ;
		}
		if ( children[a]->children.GetCount() <= 1 ) ret += children[a]->GetHTMLtree ( depth + 1 ) ;
		else ret += _T(" [ ") + children[a]->GetHTMLtree ( depth + 1 ) + _T(" ] ") ;
	}
	return ret ;
}

bool TSearchWordTree::StringHasWildcards ( wxString s )
{
	int a ;
	wxString against = _T("abcdefghijklmnopqrstuvwxyz") ;
	for ( a = 0 ; a < s.Length() ; a++ )
	{
		wxChar c = s.GetChar(a) ;
		if ( against.Find ( c ) > -1 ) continue ;
/*		if ( c >= 'a' && c <= 'z' ) continue ;
		if ( c >= '0' && c <= '9' ) continue ;
		if ( c == 'Š' || c == 'š' || c == 'Ÿ' || c == '§' ) continue ;*/
		return true ; // Something else, assumed wildcard
	}
	return false ;
}

bool TSearchWordTree::IsUsingWildcards()
{
	int a ;
	if ( StringHasWildcards ( word ) ) return true ;
	
	for ( a = 0 ; a < children.GetCount() ; a++ )
		if ( children[a]->IsUsingWildcards() ) return true ;
	return false ;
}

wxArrayString TSearchWordTree::Process ( ZenoFile *index , int depth )
{
	int a ;
	wxArrayString ret ;
	
	// Process children first
	for ( a = 0 ; a < children.GetCount() ; a++ )
		children[a]->Process ( index , depth+1 ) ;

	if ( !word.IsEmpty() ) // Search entry
	{
        if ( IsUsingWildcards() ) CreateSingleWildcardTable ( word , index ) ;
        else CreateSingleWordTable ( word , index ) ;
//        DumpTable ( _T("C:\\") + word + _T(".txt") ) ;
	} else { // Group / AND / OR / NEAR / whatnot
        switch ( type )
	    {
            case TREE_WORD : ProcessList() ; break ; // This should never be the case, but it sometimes is. Bad programmer!
            case TREE_NORMAL_LIST : ProcessList() ; break ; // Calls OR to merge
            case TREE_AND : ProcessAND() ; break ;
            case TREE_OR : ProcessOR() ; break ;
            default : wxMessageBox ( _T("BAD TYPE!") ) ; return ret ;
        }
    }
    
    // Return is done by root element only
    if ( depth > 0 ) return ret ;
    
//    wxMessageBox ( wxString::Format ( _T("Processing %d table entries") , table.GetCount() ) ) ;
    wxArrayInt ids ;
    for ( a = 0 ; a < table.GetCount() ; a++ )
    {
        if ( ids.IsEmpty() ) ids.Add ( table[a].article_id ) ;
        else if ( ids.Last() != table[a].article_id ) ids.Add ( table[a].article_id ) ;
    }
    
//    wxMessageBox ( wxString::Format ( _T("Processing %d ids") , ids.GetCount() ) ) ;
    ZenoFile *main = ((MainApp*)wxTheApp)->frame->GetMainPointer() ;
    for ( a = 0 ; a < ids.GetCount() ; a++ )
    {
        ZenoArticle art = main->ReadSingleArticle ( ids[a] ) ;
        ret.Add ( art.title ) ;
    }
    return ret ;
}

void TSearchWordTree::ProcessList ()
{
    ProcessOR () ;
}

void TSearchWordTree::ProcessAND ()
{
    if ( children.GetCount() == 0 ) return ;
    if ( children.GetCount() == 1 )
    {
        table = children[0]->table ;
        children[0]->table.Clear() ;
        return ;
    }
    int a , b , cp ;
    for ( cp = 1 ; cp < children.GetCount() ; cp++ )
    {
        ArrayOfTSearchWordTreeTableLine *t1 , *t2 ;
        t1 = &children[0]->table ;
        t2 = &children[cp]->table ;
        a = 0 ;
        b = 0 ;
        table.Clear() ;
        while ( a < t1->GetCount() && b < t2->GetCount() )
        {
            if ( (*t1)[a].article_id < (*t2)[b].article_id ) { a++ ; continue ; }
            if ( (*t1)[a].article_id > (*t2)[b].article_id ) { b++ ; continue ; }
            if ( (*t1)[a] < (*t2)[b] ) { table.Add((*t1)[a]) ; a++ ; continue ; }
            if ( (*t2)[b] < (*t1)[a] ) { table.Add((*t2)[b]) ; b++ ; continue ; }
            table.Add((*t1)[a]) ; // Both equal ??
            a++; b++;
        }
        t2->Clear() ;
        *t1 = table ;
    }
    table = children[0]->table ;
    children[0]->table.Clear() ;
//    table.Sort ( CMPFUNCtable ) ;
//    wxMessageBox ( wxString::Format ( _T("AND operation : %d left") , table.GetCount() ) ) ;
}

void TSearchWordTree::ProcessOR ()
{
    if ( children.GetCount() == 0 ) return ; // No need to run this
    int a ;
    table.Clear() ;
    for ( a = 0 ; a < children.GetCount() ; a++ )
    {
        WX_APPEND_ARRAY ( table , children[a]->table ) ;
        children[a]->table.Clear() ;
    }
    if ( children.GetCount() > 1 ) table.Sort ( CMPFUNCtable ) ; // Otherwise, no sorting necessary
//    wxMessageBox ( wxString::Format ( _T("OR operation : %d left") , table.GetCount() ) ) ;
}

void TSearchWordTree::DumpTable ( wxString filename )
{
    unsigned long a , number = 0 ;
//    wxFile out ( filename , wxFile::write ) ;
    ZenoFile *main = ((MainApp*)wxTheApp)->frame->GetMainPointer() ;
    wxString s ;
    for ( a = 0 ; a < table.GetCount() ; a++ )
    {
        if ( number == table[a].article_id )
        {
            s = wxString::Format ( _T(", %d") , table[a].word_pos ) ;
//            out.Write ( s ) ;
            continue ;
        }
        number = table[a].article_id ;
        ZenoArticle art = main->ReadSingleArticle ( number ) ;
        if ( !art.ok ) s = _T("Couldn't access article\n") ;
        else s = _T("\n") + wxString::Format ( _T("%d: ") , number ) + art.title ;
//        out.Write ( s ) ;
        s = wxString::Format ( _T(", %d") , table[a].word_pos ) ;
//        out.Write ( s ) ;
    }
}


void TSearchWordTree::CreateSingleWordTable ( wxString word , ZenoFile *index )
{
    table.Clear () ;
    if ( word.IsEmpty() ) return ;
    int i = index->FindPageID ( _T("X/") + word ) ;
    if ( i <= 0 ) return ;
    ZenoArticle art = index->ReadSingleArticle ( i ) ;
    if ( !art.ok ) return ;
    char *data = index->GetBlob ( art.rFilePos , art.rFileLen ) ;
    if ( !data ) return ;
    
    // Table data is now in *data
    unsigned long *x = (unsigned long*) data ;
    unsigned long cnt = 0 ;
    while ( cnt < art.rFileLen )
    {
        TSearchWordTreeTableLine line ;
        line.article_id = *x++ ;
        line.word_pos = *x++ ;
        table.Add ( line ) ;
        cnt += 8 ;
    }
    delete data ;
    if ( GetRoot()->title_only ) FilterTitleAgainstTable ( word ) ;
}

void TSearchWordTree::CreateSingleWildcardTable ( wxString word , ZenoFile *index )
{
    table.Clear () ;
    if ( word.IsEmpty() ) return ;
    index->ReadIndex() ;
    
}


void TSearchWordTree::FilterTitleAgainstTable ( wxString word )
{
    unsigned long number = 0 ;
    bool ok = false ;
    ArrayOfTSearchWordTreeTableLine table2 ;
    ZenoFile *main = ((MainApp*)wxTheApp)->frame->GetMainPointer() ;
    for ( int a = 0 ; a < table.GetCount() ; a++ )
    {
        if ( number == table[a].article_id )
        {
            if ( ok ) table2.Add ( table[a] ) ;
            continue ;
        }
        // New article in list
        number = table[a].article_id ;
        ok = false ;
        ZenoArticle art = main->ReadSingleArticle ( number ) ;
        if ( !art.ok ) continue ;
        wxString t = art.title ; // Needs to be qunicode-treated!!!!!!!!!!
        if ( !DoesMatchTitle ( word , t ) ) continue ;
        ok = true ;
        table2.Add ( table[a] ) ;
    }
    table = table2 ;
}

void TSearchWordTree::Explode ( wxString query , wxArrayString &words , bool is_parameter )
{
	// Break parse string into words and ()
	wxString temp ;
	while ( !query.IsEmpty() )
	{
		wxString l = query.Left ( 1 ) ;
		query = query.Mid ( 1 ) ;
		bool doit = false ;
		if ( is_parameter && ( l == _T(" ") || l == _T("(") || l == _T(")") ) ) doit = true ;
		if ( doit )
		{
			if ( !temp.IsEmpty() ) words.Add ( String2Q ( temp ) ) ;
			temp.Empty() ;
			if ( is_parameter && l == _T(" ") ) continue ;
			words.Add ( String2Q ( l ) ) ;
		} else temp += l ;
	}
	if ( !temp.IsEmpty() ) words.Add ( String2Q ( temp ) ) ;
}

bool TSearchWordTree::DoesMatchTitle ( wxString word , wxString title )
{
    int a ;
    wxString spec = _T("<>^-_()[]{}/\\=!;: \"$%&,.?~#'") ;
    title = title.Lower() ;
    for ( a = 0 ; a < title.Length() ; a++ )
        title[a] = CharToQ ( title[a] ) ;
    for ( a = 0 ; a < spec.length() ; a++ )
    {
        wxString s = spec.Mid ( a , 1 ) ;
        title.Replace ( s , _T(" ") ) ;
    }
    title = title.Lower() ;
    title = _T(" ") + title + _T(" ") ;
    word = _T(" ") + word + _T(" ") ;
//    wxMessageBox ( title , word ) ;
    if ( -1 == title.Find ( word ) ) return false ;
    return true ;
}

//________________________________________________________________________________________________________________________


wxArrayString wxWikiServer::Search ( wxString query , wxString mode )
{
    // Fun pre-processing
	query = query.Lower() ;
	query.Replace ( _T("_") , _T(" ") ) ;
	query.Replace ( _T("&") , _T(" and ") ) ;
	query.Replace ( _T("|") , _T(" or ") ) ;
	query.Replace ( _T(" und ") , _T(" and ") ) ; // German 
	query.Replace ( _T(" oder ") , _T(" or ") ) ; // German
	query.Replace ( _T("%") , _T("{0;1}") ) ;
	query.Replace ( _T("@") , _T("{1;}") ) ;
	
	wxArrayString words ;
	TSearchWordTree root ;
	root.Explode ( query , words ) ;
	root.title_only = mode == _T("titles") ;
	root.Parse ( words ) ;
	bool wildcards = root.IsUsingWildcards() ;
	// MISSING : Load index if wildcards==true

	return root.Process ( frame->GetIndexPointer() ) ;
}

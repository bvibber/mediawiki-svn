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

WX_DECLARE_OBJARRAY(TSearchWordTree*, ArrayOfTSearchWordTree);

enum
{
	TREE_NORMAL_LIST = 0 ,
	TREE_WORD ,
	TREE_AND ,
	TREE_OR
} ;

// CAUTON : The whole search section has no user input sanity checks; eg unmatching () in the search string might bring it down

class TSearchWordTree
{
	public :
	TSearchWordTree ( TSearchWordTree *parent = NULL ) ;
	void Parse ( wxArrayString words ) ;
	int CheckKeyword ( wxString s ) ;
	int ScanKeyword ( int start , const wxArrayString &wordlist , TSearchWordTree &child ) ;
	wxString GetHTMLtree ( int depth = 0 ) ;
	bool IsUsingWildcards() ;
	wxString Process ( ZenoFile *index , int depth = 0 ) ;
	bool StringHasWildcards ( wxString s ) ;
	
	ArrayOfTSearchWordTree children ;
	TSearchWordTree *_parent ;
	int type ;
	wxString word ;
} ;

#include <wx/arrimpl.cpp> // this is a magic incantation which must be done!
WX_DEFINE_OBJARRAY(ArrayOfTSearchWordTree);




TSearchWordTree::TSearchWordTree ( TSearchWordTree *parent )
{
	_parent = parent ;
	type = TREE_NORMAL_LIST ;
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
	ret = word ;
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
	for ( a = 0 ; a < s.Length() ; a++ )
	{
		wxChar c = s.GetChar(a) ;
		if ( c >= 'a' && c <= 'z' ) continue ;
		if ( c >= '0' && c <= '9' ) continue ;
		if ( c == 'Š' || c == 'š' || c == 'Ÿ' || c == '§' ) continue ;
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

wxString TSearchWordTree::Process ( ZenoFile *index , int depth )
{
	int a ;
	
	// Process children first
	for ( a = 0 ; a < children.GetCount() ; a++ )
		children[a]->Process ( index , depth+1 ) ;

	if ( !word.IsEmpty() )
	{
		
	}
}


//________________________________________________________________________________________________________________________


wxString wxWikiServer::Search ( wxString query , wxString type )
{
	// Break parse string indo words and ()
	query = query.Lower() ;
	wxString temp ;
	wxArrayString words ;
	while ( !query.IsEmpty() )
	{
		wxString l = query.Left ( 1 ) ;
		query = query.Mid ( 1 ) ;
		if ( l == _T(" ") || l == _T("(") || l == _T(")") )
		{
			if ( !temp.IsEmpty() ) words.Add ( temp ) ;
			temp.Empty() ;
			if ( l == _T(" ") ) continue ;
			words.Add ( l ) ;
		} else temp += l ;
	}
	if ( !temp.IsEmpty() ) words.Add ( temp ) ;
	temp.Empty() ;
	// At this point, "query" is empty, and "words[]" contains words and "()"
	
	TSearchWordTree root ;
	root.Parse ( words ) ;
	bool wildcards = root.IsUsingWildcards() ;
	// MISSING : Load index if wildcards==true
	
	wxString ret ;
	ret = root.GetHTMLtree() ;
	ret += _T("<hr/>") ;
	ret += wildcards ? _T("Using wildcards<br/>") : _T("Not using wildcards<hr/>") ;
	ret += root.Process ( frame->GetIndexPointer() ) ;
	return ret ;
}

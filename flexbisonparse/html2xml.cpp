#include <iostream>
#include <string>
#include <vector>

using namespace std ;

const string table_tags = "tr|th|td|caption" ;
const string markup_tags = "div|b|i|u|s" ;
const string block_tags = "hr|br|p|pre|table" ;
const string list_tags = "ol|ul|li" ;
const string wiki_tags = "nowiki|gallery|wikihiero|math" ;

class html2xml ;

class html2xml_tag
	{
	private :
	friend class html2xml ;
	html2xml_tag () ;
	
	virtual string get_string () ;
	virtual string get_debug_string () ;
	virtual void invalidate_tag () ;
	
	bool is_tag , is_close_tag , is_self_closed ;
	string text , tag_name ;
	html2xml_tag *match ;
	} ;

class html2xml
	{
	public :
	html2xml () ;
	virtual ~html2xml () ;
	virtual void scan_string ( const string &s ) ;
	virtual void add_to_allowed_tags ( string s ) ;
	virtual void to_xml () ;
	virtual void show_debug () ;
	virtual string get_string () ;
	
	private :
	virtual html2xml_tag *get_html_tag ( const string &s , int &start ) ;
	virtual bool scan_past_spaces ( const string &s , int &start ) ;
	virtual bool scan_alpha ( const string &s , int &start ) ;
	virtual bool scan_attr ( const string &s , int &start ) ;
	virtual bool scan_attribute ( const string &s , int &start ) ;
	virtual void add_text_tag ( const string &s ) ;
	virtual string to_lower ( string s ) ;
	virtual void replace_all ( string &s , string what , string with ) ;
	virtual bool in_tag_list ( string tag , const vector <string> &tag_list ) ;
	virtual void fix_single_tags () ;
	virtual void open_tag ( int &a ) ;
	virtual void close_tag ( int &a ) ;
	virtual void match ( html2xml_tag *t1 , html2xml_tag *t2 ) ;
	virtual void add_close_tag () ;
	virtual void insert_tag ( int a , string tag , bool close = false ) ;
	virtual string stack_top () ;
	virtual void add_to_tags ( string s , vector <string> &v ) ;
	virtual bool try_closing ( int &a , int depth ) ;

	vector <string> allowed_tags , table_tags , table_tags2 , format_tags , list_tags ;
	vector <html2xml_tag*> parts ;
	vector <string> stack ;
	vector <int> si ;
	} ;

//*********************************************************************************************

html2xml_tag::html2xml_tag ()
	{
	is_tag = is_close_tag = is_self_closed = false ;
	match = NULL ;
	}

// Removes a broken/unmatched HTML tag by converting it to plain text
void html2xml_tag::invalidate_tag ()
	{
//	if ( match ) match->match = NULL ;
	match = NULL ;
	text = get_string() ;
	tag_name = "" ;
	is_tag = is_close_tag = is_self_closed = false ;
	}
	
string html2xml_tag::get_string ()
	{
	string ret ;
	ret.reserve ( text.length() * 2 ) ;
	if ( is_tag )
		{
		ret = match ? "<" : "&lt;" ;
		if ( is_close_tag ) ret += "/" ;
		ret += tag_name ;
		if ( text != "" ) ret += " " + text ;
		if ( is_self_closed ) ret += "/" ;
		ret += match ? ">" : "&gt" ;
		}
	else ret = text ;
	return ret ;
	}

string html2xml_tag::get_debug_string ()
	{
	string ret ;
	if ( is_tag )
		{
		ret = "TAG : " + tag_name + " (" + text + ")" ;
		}
	else ret = text ;
	return ret ;
	}

//*********************************************************************************************

html2xml::html2xml ()
	{
	add_to_tags ( "td|th" , table_tags ) ;
	add_to_tags ( "tr|caption" , table_tags2 ) ;
	add_to_tags ( "b|i|u|s" , format_tags ) ;
	add_to_tags ( "ol|ul" , list_tags ) ;
	}

html2xml::~html2xml ()
	{
	while ( parts.size() )
		{
		delete parts[parts.size()-1] ;
		parts.pop_back() ;
		}
	}

void html2xml::add_to_allowed_tags ( string s )
	{
	add_to_tags ( s , allowed_tags ) ;
	}
	
void html2xml::add_to_tags ( string s , vector <string> &v )
	{
	int a ;
	while ( s != "" )
		{
		for ( a = 0 ; a < s.length() && s[a] != '|' ; a++ ) ;
		string t ;
		if ( a < s.length() )
			{
			t = s.substr ( 0 , a ) ;
			s = s.substr ( a+1 , s.length() - (a+1) ) ;
			}
		else
			{
			t = s ;
			s = "" ;
			}
		if ( t != "" ) v.push_back ( t ) ;
		}
	}

bool html2xml::in_tag_list ( string tag , const vector <string> &tag_list )
	{
	int a ;
	for ( a = 0 ; a < tag_list.size() ; a++ )
		if ( tag == tag_list[a] )
			return true ;
	return false ;
	}

string html2xml::to_lower ( string s )
	{
	for ( int a = 0 ; a < s.length() ; a++ )
		if ( s[a] >= 'A' && s[a] <= 'Z' ) s[a] = s[a] - 'A' + 'a' ;
	return s ;
	}

void html2xml::add_text_tag ( const string &s )
	{
	if ( s == "" ) return ;
	html2xml_tag *tag = new html2xml_tag ;
	tag->text = s ;
	replace_all ( tag->text , "<" , "&lt;" ) ;
	replace_all ( tag->text , ">" , "&gt;" ) ;
	parts.push_back ( tag ) ;
	}

// Scans through blanks and spaces inside a potential HTML tag
bool html2xml::scan_past_spaces ( const string &s , int &start )
	{
	while ( start < s.length() && 
				(  s[start] == ' ' || 
					s[start] == '\n' ||
					s[start] == '\r' ||
					s[start] == '\t' ) )
		start++ ;
	if ( start >= s.length() ) return false ; // Reached the end of the string, which is not good
	return true ; // Everything OK, start now points to the first non-space
	}

// Scans alphabetic tag inside a potential HTML tag
bool html2xml::scan_alpha ( const string &s , int &start )
	{
	while ( start < s.length() && 
				(  ( s[start] >= 'a' && s[start] <= 'z' ) ||
					( s[start] >= 'A' && s[start] <= 'Z' ) ) )
		start++ ;
	if ( start >= s.length() ) return false ; // Reached the end of the string, which is not good
	return true ; // Everything OK, start now points to the first non-space
	}

// Scans a non-quoted attribute inside a potential HTML tag
bool html2xml::scan_attr ( const string &s , int &start )
	{
	while ( start < s.length() && 
				(  ( s[start] >= 'a' && s[start] <= 'z' ) ||
					( s[start] >= 'A' && s[start] <= 'Z' ) || 
					( s[start] >= '0' && s[start] <= '9' ) ||
					( s[start] == '#' || s[start] == '_' ) ) )
		start++ ;
	if ( start >= s.length() ) return false ; // Reached the end of the string, which is not good
	return true ; // Everything OK, start now points to the first non-space
	}


bool html2xml::scan_attribute ( const string &s , int &start )
	{
	int a = start , b ;
	char SQ = 39 , DQ = '"' ;
	if ( !scan_past_spaces ( s , a ) ) return false ;
	
	// key
	b = a ;
	if ( !scan_alpha ( s , a ) ) return false ;
	if ( a == b ) return false ; // No key
	if ( !scan_past_spaces ( s , a ) ) return false ;
	
	// key without value?
	if ( s[a] != '=' )
		{
		start = a ;
		return true ;
		}
	
	// value
	a++ ;
	if ( !scan_past_spaces ( s , a ) ) return false ;
	if ( s[a] == SQ || s[a] == DQ ) // Quoted
		{
		char c = s[a] ;
		for ( a++ ; a < s.length() && ( s[a] != c || s[a-1] == '\\' ) ; a++ ) ;
		a++ ;
		if ( a >= s.length() ) return false ;
		}
	else
		{
		b = a ;
		if ( !scan_attr ( s , a ) ) return false ;
		}
		
	start = a ;
	return true ;
	}

html2xml_tag *html2xml::get_html_tag ( const string &s , int &start )
	{
	int a = start , b ;
	html2xml_tag ret ;
	if ( s[a] != '<' ) return NULL ;
	a++ ;
	if ( !scan_past_spaces ( s , a ) ) return NULL ;

	if ( s[a] == '/' ) // Check for closing tag
		{
		ret.is_close_tag = true ;
		a++ ;
		if ( !scan_past_spaces ( s , a ) ) return NULL ;
		}

	// Scanning for tag
	b = a ;
	if ( !scan_alpha ( s , a ) ) return NULL ;
	if ( a == b ) return NULL ; // No alphabetical sequence
	ret.tag_name = to_lower ( s.substr ( b , a - b ) ) ;
	if ( !in_tag_list ( ret.tag_name , allowed_tags ) ) return NULL ; // Not an allowed tag
	if ( !scan_past_spaces ( s , a ) ) return NULL ;
	int after_tag = a ;

	// Scan attributes
	while ( scan_attribute ( s , a ) ) ;
	int after_attributes = a ;
	if ( !scan_past_spaces ( s , a ) ) return NULL ;
	
	// Self-closed tag
	if ( s[a] == '/' )
		{
		if ( ret.is_close_tag ) return NULL ; // self-closed close tag - not good...
		ret.is_self_closed = true ;
		a++ ;
		if ( !scan_past_spaces ( s , a ) ) return NULL ;
		}
	
	if ( s[a] != '>' ) return NULL ; // No close > but something else
	
	// It's a tag all right!
	ret.is_tag = true ;
	ret.text = s.substr ( after_tag , after_attributes - after_tag ) ;
	html2xml_tag *ret2 = new html2xml_tag ;
	*ret2 = ret ;
	start = a ;
	return ret2 ;
	}

void html2xml::replace_all ( string &s , string what , string with )
	{
	int a , wl = what.length() ;
	for ( a = 0 ; a + wl <= s.length() ; a++ )
		{
		if ( s.substr ( a , wl ) != what ) continue ;
		s = s.substr ( 0 , a ) + with + s.substr ( a + wl , s.length() - ( a + wl ) ) ;
		a += wl ;
		}
	}

void html2xml::scan_string ( const string &s )
	{
	int a ;
	string t ;
	t.reserve ( 1024 ) ;
	for ( a = 0 ; a < s.length() ; a++ )
		{
		html2xml_tag *nt = get_html_tag ( s , a ) ;
		if ( nt )
			{
			add_text_tag ( t ) ;
			parts.push_back ( nt ) ;
			t = "" ;
			}
		else t += s[a] ;
		}
	add_text_tag ( t ) ;
	}
	
void html2xml::show_debug ()
	{
	int a ;
	for ( a = 0 ; a < parts.size() ; a++ )
		{
		cout << parts[a]->get_debug_string() << endl ;
		}
	}

string html2xml::get_string ()
	{
	int a ;
	string ret ;
	for ( a = 0 ; a < parts.size() ; a++ )
		{
		ret += parts[a]->get_string() ;
		}
	return ret ;
	}

void html2xml::fix_single_tags ()
	{
	int a ;
	for ( a = 0 ; a < parts.size() ; a++ )
		{
		html2xml_tag *t = parts[a] ;
		if ( t->tag_name != "br" && t->tag_name != "hr" ) continue ;
		t->is_close_tag = false ;
		t->is_self_closed = true ;
		}
	for ( a = 0 ; a < parts.size() ; a++ )
		{
		if ( parts[a]->is_self_closed ) parts[a]->match = parts[a] ;
		}
	}

void html2xml::match ( html2xml_tag *t1 , html2xml_tag *t2 )
	{
	t1->match = t2 ;
	t2->match = t1 ;
	}

string html2xml::stack_top ()
	{
	if ( stack.size() == 0 ) return "" ;
	return stack[stack.size()-1] ;
	}
	
void html2xml::add_close_tag ()
	{
	html2xml_tag *t = new html2xml_tag ;
	t->is_tag = true ;
	t->is_close_tag = true ;
	t->tag_name = stack_top() ;
	parts.push_back ( t ) ;
	}

void html2xml::insert_tag ( int a , string tag , bool close )
	{
	html2xml_tag *t = new html2xml_tag ;
	t->is_tag = true ;
	t->tag_name = tag ;
	t->is_close_tag = close ;

	// Inserting; should probably be done though a member function of vector...
	parts.push_back ( NULL ) ;
	for ( int b = parts.size()-1 ; b > a ; b-- )
		parts[b] = parts[b-1] ;
	parts[a] = t ;
	}

// Is called for each opening tag
void html2xml::open_tag ( int &a )
	{
	html2xml_tag *t = parts[a] ;

	// Close self - e.g, "<p>...<p>" => "<p>...</p><p>"
	if ( stack_top() == t->tag_name )
		{
		insert_tag ( a-- , stack_top() , true ) ;
		return ;
		}
	
	// Open TD or TH without TR
	if ( !in_tag_list ( stack_top() , table_tags2 ) && in_tag_list ( t->tag_name , table_tags ) )
		{
		insert_tag ( a-- , "tr" ) ;
		return ;
		}

	// Open TR or CAPTION without TABLE
	if ( stack_top() != "table" && in_tag_list ( t->tag_name , table_tags2 ) )
		{
		insert_tag ( a-- , "table" ) ;
		return ;
		}

	// Open LI without UL or OL
	if ( t->tag_name == "li" && !in_tag_list ( stack_top() , list_tags ) )
		{
		insert_tag ( a-- , "ul" ) ; // UL is default
		return ;
		}

	stack.push_back ( t->tag_name ) ;
	si.push_back ( a ) ;
	}

bool html2xml::try_closing ( int &a , int depth )
	{
	if ( depth == 0 ) return false ;
	if ( try_closing ( a , depth - 1 ) ) return true ;
	if ( stack.size() < depth ) return false ;
	if ( stack[stack.size()-depth] == parts[a]->tag_name )
		{
		insert_tag ( a-- , stack_top() , true ) ;
		return true ;
		}
	return false ;
	}
	
// Is called for each closing tag
void html2xml::close_tag ( int &a )
	{
	html2xml_tag *t = parts[a] ;

	// Close TABLE without TR or CAPTION closed first
	if ( in_tag_list ( stack_top() , table_tags2 ) && t->tag_name == "table" )
		{
		insert_tag ( a-- , stack_top() , true ) ;
		return ;
		}

	// Close TABLE without TD or TH closed first
	if ( in_tag_list ( stack_top() , table_tags ) && t->tag_name == "table" )
		{
		insert_tag ( a-- , stack_top() , true ) ;
		return ;
		}

	// Close TR or CAPTION without closing TD/TH first
	if ( in_tag_list ( stack_top() , table_tags ) && in_tag_list ( t->tag_name , table_tags2 ) )
		{
		insert_tag ( a-- , stack_top() , true ) ;
		return ;
		}

	// Always close broken format tags
	if ( in_tag_list ( stack_top() , format_tags ) && t->tag_name != stack_top() )
		{
		insert_tag ( a-- , stack_top() , true ) ;
		return ;
		}

	if ( stack_top() == t->tag_name )
		{
		match ( parts[si[si.size()-1]] , parts[a] ) ;
		stack.pop_back() ;
		si.pop_back () ;
		}
	else try_closing ( a , 3 ) ; // Experimental, try closing down three levels
	}

void html2xml::to_xml ()
	{
	fix_single_tags () ;

	int a ;
	stack.clear () ;
	si.clear () ;
	for ( a = 0 ; a < parts.size() ; a++ )
		{
		html2xml_tag *t = parts[a] ;
		if ( t->is_tag ) // This is a HTML tag
			{
			if ( t->is_close_tag ) close_tag ( a ) ;
			else if ( !t->is_self_closed ) open_tag ( a ) ;
			}
		else // This is plain text, might need some tagging though
			{
			if ( stack_top() == "table" ) insert_tag ( a-- , "tr" ) ;
			else if ( stack_top() == "tr" ) insert_tag ( a-- , "td" ) ;
			else if ( in_tag_list ( stack_top() , list_tags ) ) insert_tag ( a-- , "li" ) ;
			}
		if ( a+1 == parts.size() && stack.size() > 0 ) // Need to close remaining tags
			add_close_tag () ;
		}
	}

//*********************************************************************************************

string get_xml ( const string &s )
	{
	html2xml hx ;
	hx.add_to_allowed_tags ( table_tags + "|" + markup_tags + "|" + block_tags + "|" + list_tags + "|" + wiki_tags ) ;
	hx.scan_string ( s ) ;
	hx.to_xml () ;
	return hx.get_string () ;
	}

// Testing with random strings
void test ()
	{
	int len = 10000 ;
	for ( int a = 0 ; a < 100 ; a++ ) // Number of test runs
		{
		cout << a << " : " ;
		string s ;
		s.reserve ( len + 10 ) ;
		for ( int b = 0 ; b < len ; b++ ) // Test string length
			{
			unsigned char x = rand() % 255 ;
			if ( x > 220 ) x = '<' ;
			if ( x > 200 ) x = '>' ;
			s += (char) x ;
			}
		get_xml ( s ) ;
		cout << " " ;
		}
	}

//*********************************************************************************************

//#define TEST

int main ( int argc , char *argv[] )
	{
	
#ifdef TEST
	test () ; // Test routine
#else
	// The real stuff!
	string s ;
	while ( !cin.eof() ) s += cin.get() ;
	cout << get_xml ( s ) << endl ;
#endif

	return 0 ;
	}

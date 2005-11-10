<?
# Copyright by Magnus Manske (2005)
# Released under GPL

# TODO :
# The ";" thingy

class wiki2xml
	{
	var $protocols = array ( "http" , "https" , "news" , "ftp" , "irc" , "mailto" ) ;
	var $errormessage = "ERROR!" ;
	var $compensate_markup_errors = false;
	var $auto_fill_templates = true ; # Will try and replace templates right inline, instead of using <template> tags; requires global $content_provider
	var $use_space_tag = true ; # Use <space/> instead of spaces before and after tags
	var $allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890 +-#:;.,%="\'\\' ;
	var $directhtmltags = array (
		"b" => "xhtml:b",
		"i" => "xhtml:i",
		"u" => "xhtml:u",
		"s" => "xhtml:s",
		"p" => "xhtml:p",
		"br" => "xhtml:br",
		"div" => "xhtml:div",
		"span" => "xhtml:span",
		"small" => "xhtml:small",
		"font" => "xhtml:font",
		"table" => "xhtml:table",
		"tr" => "xhtml:tr",
		"th" => "xhtml:th",
		"td" => "xhtml:td",
		"caption" => "xhtml:caption",
		) ;
		
	var $w ; # The wiki text
	var $wl ; # The wiki text length
	var $bold_italics ;
	var $tables = array () ; # List of open tables

	# Some often used functions
	function fitit ( &$a , &$xml , &$f , $atleastonce , $many )
		{
		$f = "p_{$f}" ;
		$cnt = 0 ;
		do {
			$matched = $this->$f ( $a , $xml ) ;
			if ( $matched && $many ) $again = true ;
			else $again = false ;
			if ( $matched ) $cnt++ ;
			} while ( $again ) ;
		if ( !$atleastonce ) return true ;
		if ( $cnt > 0 ) return true ;
		return false ;
		}

	function once ( &$a , &$xml , $f )
		{
		return $this->fitit ( $a , $xml , $f , true , false ) ;
		}
		
	function onceormore ( &$a , &$xml , $f )
		{
		return $this->fitit ( $a , $xml , $f , true , true ) ;
		}

	function many ( &$a , &$xml , $f )
		{
		return $this->fitit ( $a , $xml , $f , false , true ) ;
		}
	
	function nextis ( &$a , $t , $movecounter = true )
		{
		if ( substr ( $this->w , $a , strlen ( $t ) ) != $t ) return false ;
		if ( $movecounter ) $a += strlen ( $t ) ;
		return true ;
		}

	function nextchar ( &$a , &$x )
		{
		if ( $a >= $this->wl ) return false ;
		$x .= htmlspecialchars ( $this->w[$a] ) ;
		$a++ ;
		return true ;
		}
		
	function ischaracter ( $c )
		{
		if ( $c >= 'A' && $c <= 'Z' ) return true ;
		if ( $c >= 'a' && $c <= 'z' ) return true ;
		return false ;
		}
		
	function skipblanks ( &$a , $blank = " " )
		{
		while ( $a < $this->wl )
			{
			if ( $this->w[$a] != $blank ) return ;
			$a++ ;
			}
		}
		
	##############

	
	function p_internal_link_target ( &$a , &$xml , $closeit = "]]" )
		{
		return $this->p_internal_link_text ( $a , $xml , true , $closeit ) ;
		}
		
	function p_internal_link_text2 ( &$a , &$xml , $closeit )
		{
		return $this->p_internal_link_text ( $a , $xml , false , $closeit , false ) ;
		}
	
	function p_internal_link_text ( &$a , &$xml , $istarget = false , $closeit = "]]" , $mark = true )
		{
		$b = $a ;
		$x = "" ;
		if ( $b >= $this->wl ) return false ;
		while ( 1 )
			{
			$c = $this->w[$b] ;
			if ( $closeit != "}}" && $c == "\n" ) return false ;
			if ( $c == "|" ) break ;
			if ( $this->nextis ( $b , $closeit , false ) ) break ;
			if ( !$istarget )
				{
				if ( $c == "[" && $this->once ( $b , $x , "internal_link" ) ) continue ;
				if ( $c == "[" && $this->once ( $b , $x , "external_link" ) ) continue ;
				if ( $this->once ( $b , $x , "external_freelink" ) ) continue ;
				if ( $c == "{" && $this->once ( $b , $x , "template_variable" ) ) continue ;
				if ( $c == "{" && $this->once ( $b , $x , "template" ) ) continue ;
				if ( $c == "<" && $this->once ( $b , $x , "html" ) ) continue ;
				if ( $c == "'" && $this->p_bold ( $b , $x , "internal_link_text2" , $closeit ) ) { break ; }
				if ( $c == "'" && $this->p_italics ( $b , $x , "internal_link_text2" , $closeit ) ) { break ; }
				}
			else
				{
				if ( $c == "{" && $this->once ( $b , $x , "template" ) ) continue ;
				}
			#if ( !$this->nextchar ( $b , $x ) ) return false ;
			$x .= htmlspecialchars ( $c ) ;
			$b++ ;
			if ( $b >= $this->wl ) return false ;
			}
			
		$x = trim ( str_replace ( "\n" , "" , $x ) ) ;
		if ( $mark )
			{
			if ( $istarget ) $xml .= "<target>{$x}</target>" ;
			else $xml .= "<part>{$x}</part>" ;
				
			}
		else $xml .= $x ;
		$a = $b ;
		return true ;
		}

	function p_internal_link_trail ( &$a , &$xml )
		{
		$b = $a ;
		$x = "" ;
		while ( 1 )
			{
			$c = "" ;
			if ( !$this->nextchar ( $b , $c ) ) break ;
			if ( $this->ischaracter ( $c ) )
				{
				$x .= $c ;
				}
			else
				{
				$b-- ;
				break ;
				}
			}
		if ( $x == "" ) return false ; # No link trail
		$xml .= "<trail>{$x}</trail>" ;
		$a = $b ;
		return true ;
		}
	
	function p_internal_link ( &$a , &$xml )
		{
		$x = "" ;
		$b = $a ;
		if ( !$this->nextis ( $b , "[[" ) ) return false ;
		if ( !$this->p_internal_link_target ( $b , $x , "]]" ) ) return false ;
		while ( 1 )
			{
			if ( $this->nextis ( $b , "]]" ) ) break ;
			if ( !$this->nextis ( $b , "|" ) ) return false ;
			if ( !$this->p_internal_link_text ( $b , $x , false , "]]" ) ) return false ;
			}
		$this->p_internal_link_trail ( $b , $x ) ;
		$xml .= "<link>{$x}</link>" ;
		$a = $b ;
		return true ;
		}
		
	# Template and template variable, utilizing parts of the internal link methods
	function p_template ( &$a , &$xml )
		{
		$x = "" ;
		$b = $a ;
		if ( !$this->nextis ( $b , "{{" ) ) return false ;
		if ( !$this->p_internal_link_target ( $b , $x , "}}" ) ) return false ;
		$target = $x ;
		$variables = array () ;
		$vcount = 1 ;
		while ( 1 )
			{
			if ( $this->nextis ( $b , "}}" ) ) break ;
			if ( !$this->nextis ( $b , "|" ) ) return false ;
			$l1 = strlen ( $x ) ;
			if ( !$this->p_internal_link_text ( $b , $x , false , "}}" ) ) return false ;
			$v = substr ( $x , $l1 ) ;
			$v = str_replace ( "<part>" , "" , $v ) ;
			$v = str_replace ( "</part>" , "" , $v ) ;
			$v = explode ( "=" , $v ) ;
			if ( count ( $v ) < 2 ) $vk = $vcount ;
			else $vk = array_shift ( $v ) ;
			$vv = array_shift ( $v ) ;
			$variables[$vk] = $vv ;
			if ( !isset ( $variables[$vcount] ) ) $variables[$vcount] = $vv ;
			$vcount++ ;
			}
		
		if ( $this->auto_fill_templates ) { # Do not generate <template> sections, but rather replace the template call with the template text
			# Get template text
			global $content_provider ;
			$target = array_pop ( explode ( ">" , $target , 2 ) ) ;
			$target = array_shift ( explode ( "<" , $target , 2 ) ) ;
			$between = $content_provider->get_template_text ( $target ) ;
			
			# Replacing template variables. ATTENTION: Template variables within <nowiki> sections of templates will be replaced as well!
			foreach ( $variables AS $vk => $vv ) {
				$between = str_replace ( '{{{'.$vk.'}}}' , $vv , $between ) ;
			}
			
			# Change source (!)
			$w1 = substr ( $this->w , 0 , $a ) ;
			$w2 = substr ( $this->w , $b ) ;
			$this->w = $w1 . $between . $w2 ;
			$this->wl = strlen ( $this->w ) ;
		} else {
			$xml .= "<template>{$x}</template>" ;
			$a = $b ;
		}
		return true ;
		}
		
	function p_template_variable ( &$a , &$xml )
		{
		$x = "" ;
		$b = $a ;
		if ( !$this->nextis ( $b , "{{{" ) ) return false ;
		if ( !$this->p_internal_link_text ( $b , $x , false , "}}}" ) ) return false ;
		if ( !$this->nextis ( $b , "}}}" ) ) return false ;
		$xml .= "<templatevar>{$x}</templatevar>" ;
		$a = $b ;
		return true ;
		}
		
	# Bold / italics
	function p_bold ( &$a , &$xml , $recurse = "restofline" , $end = "" )
		{
		return $this->p_intwined ( $a , $xml , "bold" , "'''" , $recurse , $end ) ;
		}
	
	function p_italics ( &$a , &$xml , $recurse = "restofline" , $end = "" )
		{
		return $this->p_intwined ( $a , $xml , "italics" , "''" , $recurse , $end ) ;
		}
	
	function p_intwined ( &$a , &$xml , $tag , $markup , $recurse , $end )
		{
		$b = $a ;
		if ( !$this->nextis ( $b , $markup ) ) return false ;
		$id = substr ( ucfirst ( $tag ) , 0 , 1 ) ;
		$bi = $this->bold_italics ;
		$open = false ;
		if ( substr ( $this->bold_italics , -1 ) == $id )
			{
			$x = "</{$tag}>" ;
			$this->bold_italics = substr ( $this->bold_italics , 0 , -1 ) ;
			}
		else
			{
			$pos = strpos ( $this->bold_italics , $id ) ;
			if ( false !== $pos ) return false ; # Can't close a tag that ain't open
			$open = true ;
			$x = "<{$tag}>" ;
			$this->bold_italics .= $id ;
			}
		
		if ( $end == "" )
			{
			$res = $this->once ( $b , $x , $recurse ) ;
			}
		else
			{
			$r = "p_{$recurse}" ;
			$res = $this->$r ( $b , $x , $end ) ;
			}
		
		$this->bold_italics = $bi ;
		if ( !$res )
			{
			return false ;
			}
		$xml .= $x ;
		$a = $b ;
		return true ;
		}	
		
	function scanplaintext ( &$a , &$xml , $goodstop , $badstop )
		{
		$b = $a ;
		$x = "" ;
		while ( $b < $this->wl )
			{
			foreach ( $goodstop AS $s )
				if ( $this->nextis ( $b , $s , false ) ) break 2 ;
			foreach ( $badstop AS $s )
				if ( $this->nextis ( $b , $s , false ) ) return false ;
			$c = $this->w[$b] ;
			$x .= htmlspecialchars ( $c ) ;
			$b++ ;
			}
		if ( count ( $goodstop ) > 0 && $b >= $this->wl ) return false ; # Reached end; not good
		$a = $b ;
		$xml .= $x ;
		return true ;
		}
		
	# External link
	function p_external_freelink ( &$a , &$xml , $mark = true )
		{
		$protocol = "" ;
		$b = $a ;
		foreach ( $this->protocols AS $p )
			{
			if ( $this->nextis ( $b , $p . "://" ) )
				{
				$protocol = $p ;
				break ;
				}
			}
		if ( $protocol == "" ) return false ;
		$x = "{$protocol}://" ;
		while ( $b < $this->wl )
			{
			if ( $this->w[$b] == "\n" || $this->w[$b] == " " ) break ;
			if ( !$mark && $this->w[$b] == "]" ) break ;
			$x .= htmlspecialchars ( $this->w[$b] ) ;
			$b++ ;
			}
		if ( substr ( $x , -1 ) == "." || substr ( $x , -1 ) == "," )
			{
			$x = substr ( $x , 0 , -1 ) ;
			$b-- ;
			}
		$a = $b ;
		if ( $mark ) $xml .= "<link type='external' url='{$x}'/>" ;
		else $xml .= $x ;
		return true ;
		}

	function p_external_link ( &$a , &$xml , $mark = true )
		{
		$b = $a ;
		if ( !$this->nextis ( $b , "[" ) ) return false ;
		$url = "" ;
		if ( !$this->p_external_freelink ( $b , $url , false ) ) return false ;
		$this->skipblanks ( $b ) ;
		if ( !$this->scanplaintext ( $b , $x , array ( "]" ) , array ( "\n" ) ) ) return false ;
		$a = $b + 1 ;
		$xml .= "<link type='external' href='{$url}'>{$x}</link>" ;
		return true ;
		}
		
	# Heading
	function p_heading ( &$a , &$xml )
		{
		if ( !$this->nextis ( $a , "==" , false ) ) return false ;
		$b = $a ;
		$level = 0 ;
		$h = "" ;
		$x = "" ;
		while ( $this->nextis ( $b , "=" ) )
			{
			$level++ ;
			$h .= "=" ;
			}
		$this->skipblanks ( $b ) ;
		if ( !$this->once ( $b , $x , "restofline" ) ) return false ;
		if ( $this->compensate_markup_errors ) $x = trim ( $x ) ;
		else if ( $x != trim ( $x ) ) $xml .= "<error type='heading' reason='trailing blank'/>" ;
		if ( substr ( $x , -$level ) != $h ) return false ; # No match

		$x = trim ( substr ( $x , 0 , -$level ) ) ;
		$level -= 1 ;
		$a = $b ;
		$xml .= "<heading level='{$level}'>{$x}</heading>" ;
		return true ;
		}
	
	# Line
	# Often used function for parsing the rest of a text line
	function p_restofline ( &$a , &$xml , $closeit = array() )
		{
		$b = $a ;
		$x = "" ;
		$override = false ;
		while ( $b < $this->wl && !$override )
			{
			$c = $this->w[$b] ;
			if ( $c == "\n" ) { $b++ ; break ; }
			foreach ( $closeit AS $z )
				if ( $this->nextis ( $b , $z , false ) ) break ;
			if ( $c == "[" && $this->once ( $b , $x , "internal_link" ) ) continue ;
			if ( $c == "[" && $this->once ( $b , $x , "external_link" ) ) continue ;
			if ( $c == "{" && $this->once ( $b , $x , "template_variable" ) ) continue ;
			if ( $c == "{" && $this->once ( $b , $x , "template" ) ) continue ;
			if ( $c == "{" && $this->p_table ( $b , $x ) ) continue ;
			if ( $c == "<" && $this->once ( $b , $x , "html" ) ) continue ;
			if ( $c == "'" && $this->once ( $b , $x , "bold" ) ) { $override = true ; break ; }
			if ( $c == "'" && $this->once ( $b , $x , "italics" ) ) { $override = true ; break ; }
			if ( $this->once ( $b , $x , "external_freelink" ) ) continue ;
			
			# Just an ordinary character
			$x .= htmlspecialchars ( $c ) ;
			$b++ ;
			if ( $b >= $this->wl ) break ;
			}
		if ( !$override && $this->bold_italics != "" )
			{
			return false ;
			}
		$xml .= $x ;
		$a = $b ;
		return true ;
		}
	
	function p_line ( &$a , &$xml , $force )
		{
		if ( $a >= $this->wl ) return false ; # Already at the end of the text
		$c = $this->w[$a] ;
		if ( !$force )
			{
			if ( $c == '*' || $c == ':' || $c == '#' || $c == ';' || $c == ' ' || $c == "\n" ) return false ; # Not a suitable beginning
			if ( $this->nextis ( $a , "{|" , false ) ) return false ; # Table
			if ( count ( $this->tables ) > 0 && $this->nextis ( $a , "|" , false ) ) return false ; # Table
			if ( count ( $this->tables ) > 0 && $this->nextis ( $a , "!" , false ) ) return false ; # Table
			if ( $this->nextis ( $a , "==" , false ) ) return false ; # Heading
			if ( $this->nextis ( $a , "----" , false ) ) return false ; # <hr>
			}
		$this->bold_italics = "" ;
		return $this->once ( $a , $xml , "restofline" ) ;
		}
	
	function p_blankline ( &$a , &$xml )
		{
		if ( $this->nextis ( $a , "\n" ) ) return true ;
		return false ;
		}
	
	function p_block_lines ( &$a , &$xml , $force = false )
		{
		$x = "" ;
		$b = $a ;
		if ( !$this->p_line ( $b , $x , $force ) ) return false ;
		while ( $this->p_line ( $b , $x , false ) ) ;
		$this->many ( $b , $x , "blankline" ) ;	
		$xml .= "<paragraph>{$x}</paragraph>" ;
		$a = $b ;
		return true ;
		}
	


	# PRE block
	# Parses a line starting with ' '
	function p_preline ( &$a , &$xml )
		{
		if ( $a >= $this->wl ) return false ; # Already at the end of the text
		$c = $this->w[$a] ;
		if ( $c != ' ' ) return false ; # Not a preline
		$this->bold_italics = "" ;
		$this->skipblanks ( $a ) ;
		return $this->once ( $a , $xml , "restofline" ) ;
		}

	# Parses a block of lines each starting with ' '
	function p_block_pre ( &$a , &$xml )
		{
		$x = "" ;
		$b = $a ;
		if ( !$this->onceormore ( $b , $x , "preline" ) ) return false ;
		$this->many ( $b , $x , "blankline" ) ;	
		$xml .= "<pre>{$x}</pre>" ;
		$a = $b ;
		return true ;
		}
	
	# LIST block
	# Returns a list tag depending on the wiki markup
	function listtag ( $c , $open = true )
		{
		if ( !$open ) return "</list>" ;
		$r = "" ;
		if ( $c == '#' ) $r = "numbered" ;
		if ( $c == '*' ) $r = "bullet" ;
		if ( $c == ':' ) $r = "ident" ;
		if ( $c == ';' ) $r = "def" ;
		if ( $r != "" ) $r = " type='{$r}'" ;
		$r = "<list{$r}>" ;
		return $r ;
		}
	
	# Opens/closes list tags
	function fixlist ( $last , $cur )
		{
		$r = "" ;
		$olast = $last ;
		$ocur = $cur ;
		$ocommon = "" ;
		
		# Remove matching parts
		while ( $last != "" && $cur != "" && $last[0] == $cur[0] )
			{
			$ocommon = $cur[0] ;
			$cur = substr ( $cur , 1 ) ;
			$last = substr ( $last , 1 ) ;
			}
		
		# Close old tags
		$fixitemtag = false ;
		if ( $last != "" && $ocommon != "" ) $fixitemtag = true ;
		while ( $last != "" )
			{
			$r .= "</listitem>" . $this->listtag ( substr ( $last , -1 ) , false ) ;
			$last = substr ( $last , 0 , -1 ) ;
			}
		if ( $fixitemtag ) $r .= "</listitem><listitem>" ;
		
		# Open new tags
		while ( $cur != "" )
			{
			$r .= $this->listtag ( $cur[0] ) . "<listitem>" ;
			$cur = substr ( $cur , 1 ) ;
			}
		
		return $r ;
		}
	
	# Parses a single list line
	function p_list_line ( &$a , &$xml , &$last )
		{
		$cur = "" ;
		do {
			$lcur = $cur ;
			while ( $this->nextis ( $a , "*" ) ) $cur .= "*" ;
			while ( $this->nextis ( $a , "#" ) ) $cur .= "#" ;
			while ( $this->nextis ( $a , ":" ) ) $cur .= ":" ;
			while ( $this->nextis ( $a , ";" ) ) $cur .= ";" ;
			} while ( $cur != $lcur ) ;

		$unchanged = false ;
#		if ( substr ( $cur , 0 , strlen ( $last ) ) == $last ) $unchanged = true ;
		if ( $last == $cur ) $unchanged = true ;
		$xml .= $this->fixlist ( $last , $cur ) ;

		if ( $cur == "" ) return false ; # Not a list line
		$last = $cur ;
		$this->skipblanks ( $a ) ;
		
		if ( $unchanged ) $xml .= "</listitem><listitem>" ;
		if ( $cur == ";" ) # Definition
			{
			$b = $a ;
			while ( $b < $this->wl && $this->w[$b] != "\n" && $this->w[$b] != ':' ) $b++ ;
			if ( $b >= $this->wl || $this->w[$b] == "\n" )
				{
				$xml .= "<defkey>" ;
				$this->p_restofline ( $a , $xml ) ;
				$xml .= "</defkey>" ;
				}
			else
				{
				$xml .= "<defkey>" ;
				$this->w[$b] = "\n" ;
				$this->p_restofline ( $a , $xml ) ;
				$xml .= "</defkey>" ;
				$xml .= "<defval>" ;
				$this->p_restofline ( $a , $xml ) ;
				$xml .= "</defval>" ;
				}
			}
		else $this->p_restofline ( $a , $xml ) ;
		return true ;
		}
		
	# Checks for a list block ( those nasty things starting with '*', '#', or the like...
	function p_block_list ( &$a , &$xml )
		{
		$last = "" ;
		$found = false ;
		while ( $this->p_list_line ( $a , $xml , $last ) ) $found = true ;
		return $found ;
		}
	
	# HTML
	# This function detects a HTML tag, finds the matching close tag, 
	# parses everything in between, and returns everything as an extension.
	# Returns false otherwise.
	function p_html ( &$a , &$xml )
		{
		if ( !$this->nextis ( $a , "<" , false ) ) return false ;
		$b = $a ;
		$x = "" ;
		$tag = "" ;
		$closing = false ;
		$selfclosing = false ;
		if ( !$this->p_html_tag ( $b , $x , $tag , $closing , $selfclosing ) ) return false ;
		
		if ( isset ( $this->directhtmltags[$tag] ) )
			{
			$tag_open = "<" . $this->directhtmltags[$tag] . ">" ;
			$tag_close = "</" . $this->directhtmltags[$tag] . ">" ;
			}
		else
			{
			$tag_open = "<extension name='{$tag}'>" ;
			$tag_close = "</extension>" ;
			}
		
		# Is this tag self-closing?
		if ( $selfclosing )
			{
			$a = $b ;
			$xml .= $tag_open . $x . $tag_close ;
			return true ;
			}
		
		# Find the matching close tag
		# TODO : The simple open/close counter should be replaced with a
		#        stack to allow for tolerating half-broken HTML,
		#        such as unclosed <li> tags
		$begin = $b ;
		$cnt = 1 ;
		$tag2 = "" ;
		while ( $cnt > 0 && $b < $this->wl )
			{
			$x2 = "" ;
			$last = $b ;
			if ( !$this->p_html_tag ( $b , $x2 , $tag2 , $closing , $selfclosing ) )
				{
				$b++ ;
				continue ;
				}
			if ( $tag != $tag2 ) continue ;
			if ( $selfclosing ) continue ;
			if ( $closing ) $cnt-- ;
			else $cnt++ ;
			}

		if ( $cnt > 0 ) return false ; # Tag was never closed

		# What happens in between?
		$between = substr ( $this->w , $begin , $last - $begin ) ;
		if ( $tag != "pre" && $tag != "nowiki" && $tag != "math" ) 
			{
			# Parse the part in between the tags
			$subparser = new wiki2xml ;
			$between2 = $subparser->parse ( $between ) ;
			
			# Was the parsing correct?
			if ( $between2 != $this->errormessage )
				$between = $this->strip_single_paragraph ( $between2 ) ; # No <paragraph> for inline HTML tags
			else
				$between = htmlspecialchars ( $between ) ; # Incorrect markup, use safe wiki source instead
			}
		else $between = htmlspecialchars ( $between ) ; # No wiki parsing in here

		$a = $b ;
		$xml .= $tag_open . $x . $between . $tag_close ;
		return true ;
		}
	
	function strip_single_paragraph ( $s )
		{
		if ( substr_count ( $s , "paragraph>" ) == 2 &&
			 substr ( $s , 0 , 11 ) == "<paragraph>" &&
			 substr ( $s , -12 ) == "</paragraph>" )
			$s = substr ( $s , 11 , -12 ) ;
		return $s ;
		}
	
	# This function checks for and parses a HTML tag
	# Only to be called from p_html, as it returns only a partial extension tag!
	function p_html_tag ( &$a , &$xml , &$tag , &$closing , &$selfclosing )
		{
		if ( $this->w[$a] != '<' ) return false ;
		$b = $a + 1 ;
		$this->skipblanks ( $b ) ;
		$tag = "" ;
		$attrs = array () ;
		if ( !$this->scanplaintext ( $b , $tag , array ( " " , ">" ) , array ( "\n" ) ) ) return false ;
		
		$this->skipblanks ( $b ) ;
		if ( $b >= $this->wl ) return false ;
		
		$tag = trim ( strtolower ( $tag ) ) ;
		$closing = false ;
		$selfclosing = false ;
		
		# Is closing tag?
		if ( substr ( $tag , 0 , 1 ) == "/" )
			{
			$tag = substr ( $tag , 1 ) ;
			$closing = true ;
			$this->skipblanks ( $b ) ;
			if ( $b >= $this->wl ) return false ;
			}
		
		if ( substr ( $tag , -1 ) == "/" )
			{
			$tag = substr ( $tag , 0 , -1 ) ;
			$selfclosing = true ;
			}

		# Parsing arrtibutes
		$ob = $b ;
		while ( $this->w[$b] != '>' && $this->w[$b] != '/' ) $b++ ;
		$attrs = $this->preparse_attributes ( substr ( $this->w , $ob , $b - $ob + 1 ) ) ;
		
		# Is self closing?
		if ( $this->w[$b] == '/' )
			{
			$b++ ;
			$selfclosing = true ;
			}
		
		$this->skipblanks ( $b ) ;
		if ( $b >= $this->wl ) return false ;
		if ( $this->w[$b] != '>' ) return false ;
		
		$a = $b + 1 ;
		if ( count ( $attrs ) > 0 )
			{
			$xml .= "<attrs>" ;
			$xml .= implode ( "" , $attrs ) ;
			$xml .= "</attrs>" ;
			}
		return true ;
		}

	# This function replaces templates and separates HTML attributes.
	# It is used for both HTML tags and wiki tables
	function preparse_attributes ( $x )
		{
		# Creating a temporary new parser to run the attribute list in
		$np = new wiki2xml ;
		$np->w = $x ;
		$np->wl = strlen ( $x ) ;

		# Replacing templates
		$c = 0 ;
		while ( $this->auto_fill_templates && $np->w[$c] != '>' && $np->w[$c] != '/' && $c < $np->wl )
			{
			if ( $np->nextis ( $c , "{{" , false ) )
				{
				$xx = "" ;
				if ( $np->p_template ( $c , $xx ) ) continue ;
				else $c++ ;
				}
			else $c++ ;
			}

		$attrs = array () ;
		$c = 0 ;
			
		# Seeking attributes
		while ( $np->w[$c] != '>' )
			{
			$attr = "" ;
			if ( !$np->p_html_attr ( $c , $attr ) ) break ;
			if ( $attr != "" ) $attrs[] = $attr ;
			$np->skipblanks ( $c ) ;
			}		
		if ( substr ( $np->w , $c ) != ">" ) return "" ;

		return $attrs ;
		}

		
	# This function scans a single HTML tag attribute and returns it as <attr name='key'>value</attr>
	function p_html_attr ( &$a , &$xml )
		{
		$b = $a ;
		$this->skipblanks ( $b ) ;
		if ( $b >= $this->wl ) return false ;
		$name = "" ;
		if ( !$this->scanplaintext ( $b , $name , array ( " " , "=" , ">" , "/" ) , array ( "\n" ) ) ) return false ;
		
		$this->skipblanks ( $b ) ;
		if ( $b >= $this->wl ) return false ;
		$name = trim ( strtolower ( $name ) ) ;
		
		$value = "" ;
		if ( $this->w[$b] == "=" )
			{
			$b++ ;
			$this->skipblanks ( $b ) ;
			if ( $b >= $this->wl ) return false ;
			$q = "" ;
			$is_q = false ;
			if ( $this->w[$b] == '"' || $this->w[$b] == "'" )
				{
				$q = $this->w[$b] ;
				$b++ ;
				if ( $b >= $this->wl ) return false ;
				$is_q = true ;
				}
			while ( $b < $this->wl )
				{
				$c = $this->w[$b] ;
				if ( $c == $q )
					{
					$b++ ;
					if ( $is_q ) break ;
					return false ; # Broken attribute value
					}
				if ( $this->nextis ( $b , "\\{$q}" ) ) # Ignore escaped quotes
					{
					$value .= "\\{$q}" ;
					continue ;
					}
				if ( $c == "\n" ) return false ; # Line break before value end
				if ( !$is_q && ( $c == ' ' || $c == '>' || $c == '/' ) ) break ;
				$value .= htmlspecialchars ( $c ) ;
				$b++ ;
				}
			}
		if ( $name == "" ) return true ;
		
		$a = $b ;
		$xml .= "<attr name='{$name}'>{$value}</attr>" ;
		return true ;
		}
	
	# Horizontal ruler (<hr> / ----)
	function p_hr ( &$a , &$xml )
		{
		if ( !$this->nextis ( $a , "----" ) ) return false ;
		$this->skipblanks ( $a , "-" ) ;
		$this->skipblanks ( $a ) ;
		$xml .= "<hr/>" ;
		return true ;
		}
	
	# TABLE
	# Scans the rest of the line as HTML attributes and returns the usual <attrs><attr> string
	function scanattributes ( &$a )
		{
		$x = "" ;
		while ( $a < $this->wl )
			{
			if ( $this->w[$a] == "\n" ) break ;
			$x .= $this->w[$a] ;
			$a++ ;
			}
		$x .= ">" ;
		
		$attrs = $this->preparse_attributes ( $x ) ;
		
		$ret = "" ;
		if ( count ( $attrs ) > 0 )
			{
			$ret .= "<attrs>" ;
			$ret .= implode ( "" , $attrs ) ;
			$ret .= "</attrs>" ;
			}
		return $ret ;
		}
		
	# Finds the first of the given items; does *not* alter $a
	function scanahead ( $a , $matches )
		{
		while ( $a < $this->wl )
			{
			foreach ( $matches AS $x )
				{
				if ( $this->nextis ( $a , $x , false ) )
					{
					return $a ;
					}
				}
			$a++ ;
			}
		return -1 ; # Not found
		}
	

	# The main table parsing function
	function p_table ( &$a , &$xml )
		{
		if ( $a >= $this->wl ) return false ;
		$c = $this->w[$a] ;
		if ( $c == "{" && $this->nextis ( $a , "{|" , false ) )
			return $this->p_table_open ( $a , $xml ) ;
		
		if ( $c != "|" && $c != "!" ) return false ; # No possible table markup
		
		if ( count ( $this->tables ) == 0 ) return false ; # No tables open, nothing to do
		
		if ( $c == "|" && $this->nextis ( $a , "|}" , false ) ) return $this->p_table_close ( $a , $xml ) ;
		
		#if ( $this->nextis ( $a , "|" , false ) || $this->nextis ( $a , "!" , false ) )
		return $this->p_table_element ( $a , $xml , true ) ;
		}
		
	function lasttable ()
		{
		return $this->tables[count($this->tables)-1] ;
		}

	# Returns the attributes for table cells
	function tryfindparams ( &$a )
		{
		$n = strspn ( $this->w , $this->allowed , $a ) ; # PHP 4.3.0 and above
#		$n = strspn ( substr ( $this->w , $a ) , $this->allowed ) ; # PHP < 4.3.0
		if ( $n == 0 ) return "" ; # None found

		$b = $a + $n ;
		if ( $b >= $this->wl ) return "" ;
		if ( $this->w[$b] != "|" && $this->w[$b] != "!" ) return "" ;
		if ( $this->nextis ( $b , "||" , false ) ) return "" ; # Reached a ||, so return blank string
		if ( $this->nextis ( $b , "!!" , false ) ) return "" ; # Reached a ||, so return blank string
		$this->w[$b] = "\n" ;
		$ret = $this->scanattributes ( $a ) ;
		$this->w[$b] = "|" ;
		$a = $b + 1 ;
		return $ret ;
		}

	function p_table_element ( &$a , &$xml , $newline = false )
		{
		$b = $a ;
		$x = "" ;
		$lt = $this->lasttable() ;
		if ( $newline && $this->nextis ( $b , "|-" ) ) # Table row
			{
			$this->skipblanks ( $b , "-" ) ;
			$this->skipblanks ( $b ) ;
			
			$attrs = $this->scanattributes ( $b ) ;
			if ( $this->tables[count($this->tables)-1]->is_row_open ) $x .= "</tablerow>" ;
			else $this->tables[count($this->tables)-1]->is_row_open = true ;
			$x .= "<tablerow>{$attrs}" ;
			}
		else if ( $newline && $this->nextis ( $b , "|+" ) ) # Table caption
			{
			$this->skipblanks ( $b ) ;
			if ( $this->tables[count($this->tables)-1]->is_row_open ) $x .= "</tablerow>" ;
			$this->tables[count($this->tables)-1]->is_row_open = false ;
			if ( !$this->p_restofcell ( $b , $x ) ) return false ;
			$x = "<tablecaption>{$x}</tablecaption>" ;
			}
		else # TD or TH
			{
			$c = $this->w[$b] ;
			$b++ ;
			if ( $c == '|' ) $tag = "tablecell" ;
			else if ( $c == '!' ) $tag = "tablehead" ;
			else { $xml .= "<error function='p_table_element'/>" ; return false ; } # This would indeed be strange!
			$attrs = $this->tryfindparams ( $b ) ;
			if ( !$this->p_restofcell ( $b , $x ) ) return false ;
			
			if ( substr ( $x , 0 , 1 ) == "|" ) # Crude fix to compensate for MediaWiki "tolerant" parsing
				$x = substr ( $x , 1 ) ;
			$x = "<{$tag}>{$attrs}{$x}</{$tag}>" ;
			if ( !$lt->is_row_open )
				{
				$this->tables[count($this->tables)-1]->is_row_open = true ;
				$x = "<tablerow>{$x}" ;
				}
			}
		
		$a = $b ;
		$xml .= $x ;
		return true ;
		}

	# Finds the substring that composes the table cell,
	# then runs a new parser on it
	function p_restofcell ( &$a , &$xml )
		{
		# Get substring for cell
		$b = $a ;
		$sameline = true ;
		$x = "" ;
		$itables = 0 ;
		while ( $b < $this->wl )
			{
			$c = $this->w[$b] ;
			if ( $c == "<" && $this->once ( $b , $x , "html" ) ) continue ; # Up front to catch pre and nowiki
			if ( $c == "\n" ) { $sameline = false ; }
			if ( $c == "\n" && $this->nextis ( $b , "\n{|" ) ) { $itables++ ; continue ; }
			if ( $c == "\n" && $itables > 0 && $this->nextis ( $b , "\n|}" ) ) { $itables-- ; continue ; }
			
			if ( ( $c == "\n" && $this->nextis ( $b , "\n|" , false ) ) OR 
				 ( $c == "\n" && $this->nextis ( $b , "\n!" , false ) ) OR
				 ( $c == "|" && $sameline && $this->nextis ( $b , "||" , false ) ) OR
				 ( $c == "!" && $sameline && $this->nextis ( $b , "!!" , false ) ) )
				{
				if ( $itables == 0 ) break ;
				$b += 2 ;
				}
			
			if ( $c == "[" && $this->once ( $b , $x , "internal_link" ) ) continue ;
			if ( $c == "{" && $this->once ( $b , $x , "template_variable" ) ) continue ;
			if ( $c == "{" && $this->once ( $b , $x , "template" ) ) continue ;
			$b++ ;
			}
		
#		if ( $itables > 0 ) return false ;
		
		# Parse cell substring
		$s = substr ( $this->w , $a , $b - $a ) ;
		$p = new wiki2xml ;
		$x = $p->parse ( $s ) ;
		if ( $x == $this->errormessage ) return false ;

		$a = $b + 1 ;
		$xml .= $this->strip_single_paragraph ( $x ) ;
		return true ;
		}
	
	function p_table_close ( &$a , &$xml )
		{
		if ( count ( $this->tables ) == 0 ) return false ;
		$b = $a ;
		if ( !$this->nextis ( $b , "|}" ) ) return false ;
		$x = "" ;
		if ( $this->tables[count($this->tables)-1]->is_row_open ) $x .= "</tablerow>" ;
		unset ( $this->tables[count($this->tables)-1] ) ;
		$x .= "</table>" ;
		$xml .= $x ;
		$a = $b ;
		while ( $this->nextis ( $a , "\n" ) ) ;
		return true ;
		}
		
	function p_table_open ( &$a , &$xml )
		{
		$b = $a ;
		if ( !$this->nextis ( $b , "{|" ) ) return false ;
		
		$this->is_row_open = false ;
		
		$x = "<table>" ;
		$x .= $this->scanattributes ( $b ) ;
		while ( $this->nextis ( $b , "\n" ) ) ;
		
		# Add table to stack
		$nt->is_row_open = false ;
		$this->tables[count($this->tables)] = $nt ;
				
		# Try the rest of the article as another article
		$x2 = "" ;
		$tcount = count($this->tables) ;
		if ( !$this->p_article ( $b , $x2 ) || count($this->tables) >= $tcount )
			{
			unset ( $this->tables[count($this->tables)-1] ) ;
			return false ;
			}
		$x2 = $this->strip_single_paragraph ( $x2 ) ;
		
		$a = $b ;
		$xml .= $x . $x2 ;
		return true ;
		}
	
	#-----------------------------------
	# Parse the article
	function p_article ( &$a , &$xml )
		{
		$x = "" ;
		$b = $a ;
		while ( $b < $this->wl )
			{
			if ( $this->onceormore ( $b , $x , "heading" ) ) continue ;
			if ( $this->onceormore ( $b , $x , "block_lines" ) ) continue ;
			if ( $this->onceormore ( $b , $x , "block_pre" ) ) continue ;
			if ( $this->onceormore ( $b , $x , "block_list" ) ) continue ;
			if ( $this->onceormore ( $b , $x , "hr" ) ) continue ;
			if ( $this->onceormore ( $b , $x , "table" ) ) continue ;
			if ( $this->onceormore ( $b , $x , "blankline" ) ) continue ;
			if ( $this->p_block_lines ( $b , $x , true ) ) continue ;
			# The last resort!
			if ( !$this->compensate_markup_errors ) $xml .= "<error type='general' reason='no matching markup'/>" ;
			$xml .= htmlspecialchars ( $this->w[$b] ) ;
			}
		$a = $b ;
		$xml .= $x ;
		return true ;
		}
	
	# The only function to be called directly from outside the class
	function parse ( &$wiki )
		{
		$this->w = rtrim ( $wiki ) ;
		
		# Fix line endings
		$cc = count_chars ( $wiki , 0 ) ;
		if ( $cc[10] > 0 && $cc[13] == 0 )
			$this->w = str_replace ( "\r" , "\n" , $this->w ) ;
		$this->w = str_replace ( "\r" , "" , $this->w ) ;
		
		# Remove HTML comments
		$this->w = preg_replace( '?<!--.*-->?msU', '', $this->w);
		
		# Run the thing!
#		$this->tables = array () ;
		$this->wl = strlen ( $this->w ) ;
		$xml = "" ;
		$a = 0 ;
		if ( !$this->p_article ( $a , $xml ) ) return $this->errormessage ;

		# XML cleanup
		do {
			$lxml = $xml ;
			$xml = str_replace ( "  " , " " , $xml ) ;
			} while ( $lxml != $xml ) ;
		if ( $this->use_space_tag ) {
			$xml = str_replace ( "> " , "><space/>" , $xml ) ;
			$xml = str_replace ( " <" , "<space/><" , $xml ) ;
		}
		
		return $xml ;
		}
	
	}

?>
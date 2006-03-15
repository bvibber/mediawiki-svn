<?php

/**
 * This file contains the /element/ class needed by xml2tree.php
 * to create a tree which is then converted into DocBook XML
 */

class element {
	var $name = '';
	var $attrs = array ();
	var $children = array ();
	
	# Temporary variables for link tags
	var $link_target = "" ;
	var $link_trail = "" ;
	var $link_parts = array () ;
	
	# Variables only used by $tree root
	var $list = array () ;
	var $iter = 1 ;
	var $opentags = array () ;
	var $sect_counter = 0 ;
	
	/**
	 * Parse the children ... why won't anybody think of the children?
	 */
	function sub_parse(& $tree) {
		$ret = '' ;
		foreach ($this->children as $key => $child) {
			if (is_string($child)) {
				$ret .= $child ;
			} elseif ($child->name != 'ATTRS') {
				$sub = $child->parse ( $tree ) ;
				if ( $this->name == 'LINK' ) {
					if ( $child->name == 'TARGET' ) $this->link_target = $sub ;
					else if ( $child->name == 'PART' ) $this->link_parts[] = $sub ;
					else if ( $child->name == 'TRAIL' ) $this->link_trail = $sub ;
				}
				$ret .= $sub ;
			}
		}
		return $ret ;
	}
	
	function ensure_new ( $tag , &$tree , $opttag = "" ) {
		foreach ( $tree->opentags AS $o ) {
			if ( $o == $tag ) return "" ; # Already open
		}
		array_push ( $tree->opentags , $tag ) ;
		if ( $opttag == "" ) return "<{$tag}>\n" ;
		else return $opttag ;
	}
	
	function close_last ( $tag , &$tree ) {
		$found = false ;
		foreach ( $tree->opentags AS $o ) {
			if ( $o == $tag ) $found = true ;
		}
		if ( !$found ) return "" ; # Already closed
		$ret = "\n" ;
		while ( 1 ) {
			$o = array_pop ( $tree->opentags ) ;
			$ret .= "</{$o}>\n" ;
			if ( $o == $tag ) return $ret ;
		}
	}
	
	/* 
	 * Parse the tag
	 */
	function parse ( &$tree ) {
		global $content_provider ;
		$ret = '';
		$tag = $this->name ;
		$is_root = ( $tree->iter == 1 ) ;
		$tree->iter++ ;
		$close_emphasis = false ;
		
		if ( $tag == 'SPACE' ) {
			return ' ' ; # Speedup
		} else if ( $tag == 'ARTICLES' ) {
			$ret .= "<book>\n";
		} else if ( $tag == 'ARTICLE' ) {
			$ret .= "<article>\n";
			$header = "" ;
			if ( isset ( $this->attrs["TITLE"] ) ) {
				$title = $this->attrs["TITLE"] ;
				$header .= "<title>" . $title . "</title>\n" ;
			}
			if ( $header != "" ) {
				$ret .= "<artheader>\n" . $header . "</artheader>\n";
			}
		} else if ( $tag == 'HEADING' ) {
			$level = $tree->sect_counter ;
			$wanted = $this->attrs["LEVEL"] ;
			$ret .= $this->close_last ( "para" , $tree ) ;
			if ( $level >= $wanted ) {
				$ret .= $this->close_last ( "sect{$wanted}" , $tree ) ;
				$level = $wanted - 1 ;
			}
			while ( $level < $wanted ) {
				$level++ ;
				$ret .= $this->ensure_new ( "sect{$level}" , $tree ) ;
			}
			$tree->sect_counter = $wanted ;
			$ret .= "<title>" ;
		} else if ( $tag == 'PARAGRAPH' ) {
			$ret .= $this->close_last ( "para" , $tree ) ;
			$ret .= $this->ensure_new ( "para" , $tree ) ;
		} else if ( $tag == 'LIST' ) {
			$ret .= $this->close_last ( "para" , $tree ) ;
			$list_type = strtolower ( $this->attrs['TYPE'] ) ;
			if ( $list_type == 'bullet' ) $ret .= '<itemizedlist mark="opencircle">' ;
			if ( $list_type == 'numbered' ) $ret .= '<orderedlist numeration="arabic">' ;
		} else if ( $tag == 'LISTITEM' ) {
			$ret .= $this->close_last ( "para" , $tree ) ;
			$ret .= "<listitem>\n" ;
			$ret .= $this->ensure_new ( "para" , $tree ) ;
		} else if ( $tag == 'BOLD' || $tag == 'XHTML:STRONG' || $tag == 'XHTML:B' ) {
			$ret .= $this->ensure_new ( "para" , $tree ) ;
			$ret .= '<emphasis role="bold">' ;
			$close_emphasis = true ;
		} else if ( $tag == 'ITALICS' || $tag == 'XHTML:EM' || $tag == 'XHTML:I' ) {
			$ret .= $this->ensure_new ( "para" , $tree ) ;
			$ret .= '<emphasis>' ;
			$close_emphasis = true ;
		} else { # Normal text
			$ret .= $this->ensure_new ( "para" , $tree ) ;
		}
		
		# Get the sub-items
		$ret .= $this->sub_parse ( $tree ) ;
		
		$tree->iter-- ; # Unnecessary, since not really used
		
		if ( $tag == 'ARTICLES' ) {
			$ret .= "</book>";
		} else if ( $tag == 'LIST' ) {
			$ret .= $this->close_last ( "para" , $tree ) ;
			if ( $list_type == 'bullet' ) $ret .= "</itemizedlist>\n" ;
			if ( $list_type == 'numbered' ) $ret .= "</orderedlist>\n" ;
		} else if ( $tag == 'LISTITEM' ) {
			$ret .= $this->close_last ( "para" , $tree ) ;
			$ret .= "</listitem>\n" ;
		} else if ( $close_emphasis ) {
			$ret .= '</emphasis>' ;
		} else if ( $tag == 'HEADING' ) {
			$ret .= "</title>\n" ;
		} else if ( $tag == 'ARTICLE' ) {
			$ret .= $this->close_last ( "sect1" , $tree ) ;
			$ret .= $this->close_last ( "para" , $tree ) ;
			$ret .= "</article>";
		}
		
		return $ret;
	}
}

require_once ( "./xml2tree.php" ) ;



//_______________________________________________________________
/*
$infile = "Biology.xml" ;
$xml = @file_get_contents ( $infile ) ;

print htmlentities ( $xml ) . "<hr>" ;

$x2t = new xml2php ;
$tree = $x2t->scanString ( $xml ) ;

$odt = new xml2odt ;
$odt->parse ( $tree ) ;
*/
?>

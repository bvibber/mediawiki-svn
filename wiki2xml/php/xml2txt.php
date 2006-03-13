<?php

class element {
	var $name = '';
	var $attrs = array ();
	var $children = array ();
	
	var $link_target = "" ;
	var $link_trail = "" ;
	var $link_parts = array () ;
	
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

	/* 
	 * Parse the tag
	 */
	function parse ( &$tree ) {
		global $content_provider ;
		$ret = '';
		
		$tag = $this->name ;
		$attr = $this->attrs ;

#		$ret .= " [{$tag}] " ;

		if ( $tag == 'SPACE' ) $ret .= ' ' ;
		else if ( $tag == 'HEADING' ) $ret .= "\n\n";
		else if ( $tag == 'PARAGRAPH' ) $ret .= "\n";
		else if ( $tag == 'TABLECELL' ) $ret .= "\n";
		else if ( $tag == 'TABLECAPTION' ) $ret .= "\n";
		else if ( $tag == 'TEMPLATE' ) return "" ; # Ignore unresolved template

		if ( $tag == "LINK" ) {
			$this->sub_parse ( $tree ) ;
			$link = "" ;
			if ( count ( $this->link_parts ) > 0 ) $link = array_pop ( $this->link_parts ) ;
			if ( $link == "" ) $link = $this->link_target ;
			$link .= $this->link_trail ;
			
			$ns = $content_provider->get_namespace_id ( $this->link_target ) ;
			
			# Surroung image text with newlines
			if ( $ns == 6 )
				$link = "\n  " . $link . "\n" ;
			
			# Adding newline to interlanguage link
			if ( $ns == -9 )
				$link = "\n" . $link ;
			
			$ret .= $link ;
		} else {
			$ret .= $this->sub_parse ( $tree ) ;
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

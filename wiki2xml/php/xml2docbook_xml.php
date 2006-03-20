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
	var $opentags = array () ;
	var $sections = array () ;
	
	/**
	 * Parse the children ... why won't anybody think of the children?
	 */
	function sub_parse(& $tree) {
		$ret = '' ;
		$temp = "" ;
		foreach ($this->children as $key => $child) {
			if (is_string($child)) {
				$temp .= $child ;
			} elseif ($child->name != 'ATTRS') {
				$ret .= $this->add_temp_text ( $temp )  ;
				$sub = $child->parse ( $tree ) ;
				if ( $this->name == 'LINK' ) {
					if ( $child->name == 'TARGET' ) $this->link_target = $sub ;
					else if ( $child->name == 'PART' ) $this->link_parts[] = $sub ;
					else if ( $child->name == 'TRAIL' ) $this->link_trail = $sub ;
				}
				$ret .= $sub ;
			}
		}
		return $ret . $this->add_temp_text ( $temp ) ;
	}
	
	function fix_text ( $s ) {
		$s = html_entity_decode ( $s ) ;
		filter_named_entities ( $s ) ;
		$s = str_replace ( "&" , "&amp;" , $s ) ;
		$s = str_replace ( "<" , "&lt;" , $s ) ;
		$s = str_replace ( ">" , "&gt;" , $s ) ;
		return utf8_decode ( $s ) ;
	}
	
	function add_temp_text ( &$temp ) {
		$s = $temp ;
		$temp = "" ;
		return $this->fix_text ( $s ) ;
	}
	
	function add_new ( $tag , &$tree ) {
		return $this->ensure_new ( $tag , $tree , "<{$tag}>\n" ) ;
	}
	
	function ensure_new ( $tag , &$tree , $opttag = "" ) {
		if ( $opttag == "" ) { # Catching special case (currently, <section>)
			foreach ( $tree->opentags AS $o ) {
				if ( $o == $tag ) return "" ; # Already open
			}
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
		while ( count ( $tree->opentags ) > 0 ) {
			$o = array_pop ( $tree->opentags ) ;
			$ret .= "</{$o}>\n" ;
			if ( $o == $tag ) return $ret ;
		}
	}
	
	function handle_link ( &$tree ) {
		global $content_provider ;
		$ot = $tree->opentags ;
		$sub = $this->sub_parse ( $tree ) ;
		$tree->opentags = $ot ;
		$link = "" ;
		if ( isset ( $this->attrs['TYPE'] ) AND strtolower ( $this->attrs['TYPE'] ) == 'external' ) {
			$href = htmlentities ( $this->attrs['HREF'] ) ;
			if ( trim ( $sub ) == "" ) {
				$sub = $href ;
				$sub = explode ( '://' , $sub , 2 ) ;
				$sub = explode ( '/' , array_pop ( $sub ) , 2 ) ;
				$sub = array_shift ( $sub ) ;
			}
			$sub = $this->fix_text ( $sub ) ;
			$link = "<ulink url=\"{$href}\"><citetitle>{$sub}</citetitle></ulink>" ;
		} else {
			if ( count ( $this->link_parts ) > 0 ) {
				$link = array_pop ( $this->link_parts ) ;
				array_push ( $this->link_parts , $link ) ; # Compensating array_pop
			}
			$link_text = $link ;
			if ( $link == "" ) $link = $this->link_target ;
			$link .= $this->link_trail ;
			
			$ns = $content_provider->get_namespace_id ( $this->link_target ) ;
			
			
			if ( $ns == 6 ) { # Image
				$nstext = explode ( ":" , $this->link_target , 2 ) ;
				$target = array_pop ( $nstext ) ;
				$nstext = array_shift ( $nstext ) ;
				
				$text = array_pop ( $this->link_parts ) ;
				$is_thumb = false ;
				$align = '' ;
				$width = '' ;
				foreach ( $this->link_parts AS $s ) {
					$s = trim ( $s ) ;
					if ( $s == 'thumb' ) {
						$is_thumb = true ;
						if ( $align == '' ) $align = 'right' ;
						if ( $width == '' ) $width = '200px' ;
					}
				}
				
				$href = $content_provider->get_image_url ( $target ) ;
				
				$link = "<inlinemediaobject>\n<imageobject>\n<imagedata" ;
				$link .= " fileref=\"{$href}\"" ;
				if ( $align != '' ) $link .= " align='{$align}'" ;
				if ( $width != '' ) $link .= " width='$width'  depth='$width' scalefit='1'" ;
				$link .= "/>\n</imageobject>\n" ;
				$link .= "<textobject>\n" ;
				$link .= "<phrase>{$text}</phrase>\n" ;
				$link .= "</textobject>\n" ;
				$link .= "</inlinemediaobject>\n" ;
			} else if ( $ns == -9 ) { # Interlanguage link
				$sub = $this->link_target ;
				$nstext = explode ( ":" , $sub , 2 ) ;
				$name = array_pop ( $nstext ) ;
				$nstext = array_shift ( $nstext ) ;

				$href = "http://{$nstext}.wikipedia.org/wiki/" . urlencode ( $name ) ;
				$link = "<ulink url=\"{$href}\"><citetitle>{$sub}</citetitle></ulink>" ;
			} else if ( $ns == -8 ) { # Category link
				if ( $link_text == "!" || $link_text == '*' ) $link = "" ;
				else $link = " ({$link})" ;
				$link = "" . $this->link_target . $link . "" ;
			} else {
				if ( $content_provider->is_an_article ( $this->link_target ) ) {
					$lt = urlencode ( trim ( $this->link_target ) ) ;
					$lt = str_replace ( "+" , "_" , $lt ) ;
					$link = "<link linkend='{$lt}'>{$link}</link>" ;
				} else {
					#$link = "<link linkend='{$lt}'>{$link}</link>" ;
				}
			}
		}
		return $link ;
	}
	
	function make_tgroup ( &$tree ) {
		$num_rows = 0 ;
		$max_num_cols = 0 ;
		$caption = "" ;
		foreach ($this->children AS $key1 => $row) {
			if (is_string($row)) continue ;
			elseif ($row->name == 'TABLECAPTION') {
				$caption .= $row->parse ( $tree , "DOCAPTION" ) ;
				continue ;
			} elseif ($row->name != 'TABLEROW') continue ;
			$num_rows++ ;
			$num_cols = 0 ;
			foreach ( $row->children AS $key2 => $col ) {
				if (is_string($col)) continue ;
				if ($col->name != 'TABLECELL' && $col->name != 'TABLEHEAD') continue ;
				if ( isset ( $col->attrs['COLSPAN'] ) ) $num_cols += $col->attrs['COLSPAN'] ;
				else $num_cols++ ;
			}
			if ( $num_cols > $max_num_cols )
				$max_num_cols = $num_cols ;
		}
		return "<title>{$caption}</title><tgroup cols='{$max_num_cols}'>" ;
	}
	
	/* 
	 * Parse the tag
	 */
	function parse ( &$tree , $param = "" ) {
		global $content_provider ;
		$ret = '';
		$tag = $this->name ;
		$close_tag = "" ;
		
		if ( $tag == 'SPACE' ) {
			return ' ' ; # Speedup
		} else if ( $tag == 'ARTICLES' ) {
			# dummy, to prevent default action to be called
		} else if ( $tag == 'ARTICLE' ) {
			$title = isset ( $this->attrs["TITLE"] ) ? $this->attrs["TITLE"] : "Untiteled" ;
			$id = str_replace ( "+" , "_" , $title ) ;
			$ret .= "<article id='{$id}'>\n";
			$ret .= "<title>" . urldecode ( $title ) . "</title>\n" ;
		} else if ( $tag == 'LINK' ) {
			return $this->handle_link ( $tree ) ; # Shortcut
		} else if ( $tag == 'HEADING' ) {
			$level = count ( $tree->sections ) ;
			$wanted = $this->attrs["LEVEL"] ;
			$ret .= $this->close_last ( "para" , $tree ) ;
			while ( $level >= $wanted ) {
				$x = array_pop ( $tree->sections ) ;
				if ( $x == 1 ) {
					$ret .= $this->close_last ( "section" , $tree ) ;
				}
				$level-- ;
			}
			while ( $level < $wanted ) {
				$level++ ;
				if ( $level < $wanted ) {
					array_push ( $tree->sections , 0 ) ;
				} else {
					$ret .= $this->ensure_new ( "section" , $tree , "<section>" ) ;
					array_push ( $tree->sections , 1 ) ;
				}
			}
			$ret .= "<title>" ;
		} else if ( $tag == 'PARAGRAPH' || $tag == 'XHTML:P' ) { # Paragraph
			$ret .= $this->close_last ( "para" , $tree ) ;
			$ret .= $this->ensure_new ( "para" , $tree ) ;
		} else if ( $tag == 'LIST' ) { # List
			$ret .= $this->close_last ( "para" , $tree ) ;
			$list_type = strtolower ( $this->attrs['TYPE'] ) ;
			if ( $list_type == 'bullet' || $list_type == 'ident' ) $ret .= '<itemizedlist mark="opencircle">' ;
			else if ( $list_type == 'numbered' ) $ret .= '<orderedlist numeration="arabic">' ;
		} else if ( $tag == 'LISTITEM' ) { # List item
			$ret .= $this->close_last ( "para" , $tree ) ;
			$ret .= "<listitem>\n" ;
			$ret .= $this->ensure_new ( "para" , $tree ) ;
			
		} else if ( $tag == 'TABLE' ) { # Table
			$ret .= $this->add_new ( "table" , $tree ) ;
#			$ret .= "<title></title>" ;
			$ret .= $this->make_tgroup ( $tree ) ;
			$ret .= "<tbody>" ;
		} else if ( $tag == 'TABLEROW' ) { # Tablerow
			$ret .= $this->add_new ( "row" , $tree ) ;
		} else if ( $tag == 'TABLEHEAD' ) { # Tablehead !!!!!
			$ret .= $this->add_new ( "entry" , $tree ) ;
		} else if ( $tag == 'TABLECELL' ) { # Tablecell
			$ret .= $this->add_new ( "entry" , $tree ) ;
		} else if ( $tag == 'TABLECAPTION' ) { # Tablecaption
			if ( $param != "DOCAPTION" ) return "" ;
#			$ret .= $this->add_new ( "title" , $tree ) ;
			
		} else if ( $tag == 'BOLD' || $tag == 'XHTML:STRONG' || $tag == 'XHTML:B' ) { # <b> or '''
			$ret .= $this->ensure_new ( "para" , $tree ) ;
			$ret .= '<emphasis role="bold">' ;
			$close_tag = "emphasis" ;
		} else if ( $tag == 'ITALICS' || $tag == 'XHTML:EM' || $tag == 'XHTML:I' ) { # <i> or ''
			$ret .= $this->ensure_new ( "para" , $tree ) ;
			$ret .= '<emphasis>' ;
			$close_tag = "emphasis" ;
		} else if ( $tag == 'XHTML:SUB' ) { # <sub>
			$ret .= $this->ensure_new ( "para" , $tree ) ;
			$ret .= '<subscript>' ;
			$close_tag = "subscript" ;
		} else if ( $tag == 'XHTML:SUP' ) { # <sup>
			$ret .= $this->ensure_new ( "para" , $tree ) ;
			$ret .= '<superscript>' ;
			$close_tag = "superscript" ;
		} else if ( $tag == 'XHTML:SUP' ) { # <sup>
			$ret .= $this->ensure_new ( "para" , $tree ) ;
			$ret .= '<superscript>' ;
			$close_tag = "superscript" ;
		} else if ( $tag == 'PRELINE' OR $tag == 'XHTML:PRE' ) { # <pre>
			$ret .= $this->ensure_new ( "para" , $tree ) ;
			$ret .= '<programlisting>' ;
			$close_tag = "programlisting" ;
#		} else if ( substr ( $tag , 0 , 6 ) == 'XHTML:' ) { # Other HTML
#			$close_tag = strtolower ( substr ( $tag , 6 ) ) ;
#			$ret .= "<" . $close_tag ;
#			foreach ( $this->attrs AS $k => $v )
#				$ret .= " " . strtolower ( $k ) . "='{$v}'" ;
#			$ret .= ">" ;
		} else { # Default : normal text
			$ret .= $this->ensure_new ( "para" , $tree ) ;
		}
		
		# Get the sub-items
		if ( $tag != 'MAGIC_VARIABLE' && $tag != 'TEMPLATE' ) {
			$ret .= $this->sub_parse ( $tree ) ;
		}
		
		if ( $tag == 'LIST' ) {
			$ret .= $this->close_last ( "para" , $tree ) ;
			if ( $list_type == 'bullet' || $list_type == 'ident' ) $ret .= "</itemizedlist>\n" ;
			else if ( $list_type == 'numbered' ) $ret .= "</orderedlist>\n" ;
		} else if ( $tag == 'LISTITEM' ) {
			$ret .= $this->close_last ( "para" , $tree ) ;
			$ret .= "</listitem>\n" ;
		} else if ( $close_tag != "" ) {
			$ret .= "</{$close_tag}>" ;
		} else if ( $tag == 'HEADING' ) {
			$ret .= "</title>\n" ;
			
		} else if ( $tag == 'TABLE' ) { # Table
			$ret .= "</tbody>" ;
			$ret .= "</tgroup>" ;
			$ret .= $this->close_last ( "table" , $tree ) ;
		} else if ( $tag == 'TABLEROW' ) { # Tablerow
			$ret .= $this->close_last ( "row" , $tree ) ;
		} else if ( $tag == 'TABLEHEAD' ) { # Tablehead !!!!
			$ret .= $this->close_last ( "entry" , $tree ) ;
		} else if ( $tag == 'TABLECELL' ) { # Tablecell
			$ret .= $this->close_last ( "entry" , $tree ) ;
		} else if ( $tag == 'TABLECAPTION' ) { # Tablecaption
#			$ret .= $this->close_last ( "title" , $tree ) ;

		} else if ( $tag == 'ARTICLE' ) {
			$ret .= $this->close_last ( "section" , $tree ) ;
#			$ret .= $this->close_last ( "para" , $tree ) ;
			$ret .= "</article>";
		}
		
		return $ret;
	}
}

require_once ( "./xml2tree.php" ) ; # Uses the "element" class defined above

?>

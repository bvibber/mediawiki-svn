<?php

# Setting allowed XHTML construct list
global $xhtml_allowed ;
$xhtml_inline = "a,b,br,cite,code,em,i,img,small,strong,span,sub,sup,tt,var,";
$xhtml_block = "blockquote,div,dl,h1,h2,h3,h4,h5,h6,hr,ol,p,pre,table,ul,";
$xhtml_allowed = array ( # A => B means B allowed in A
	'' => $xhtml_block, # COMPLETE
	'p' => $xhtml_inline."table", # COMPLETE
	'table' => 'caption,col,colgroup,thead,tfoot,tbody,tr', # COMPLETE
	'tbody' => 'tr', # COMPLETE
	'tr' => 'td,th', # COMPLETE
	'td' => $xhtml_inline.$xhtml_block, # COMPLETE
	'th' => $xhtml_inline.$xhtml_block, # COMPLETE
	'caption' => $xhtml_inline, # COMPLETE
	'ul' => 'li', # COMPLETE
	'ol' => 'li', # COMPLETE
	'dl' => 'dt,dd', # COMPLETE
	'li' => $xhtml_inline.$xhtml_block, # COMPLETE
	'dt' => $xhtml_inline, # COMPLETE
	'dd' => $xhtml_inline.$xhtml_block, # COMPLETE
	'h1' => $xhtml_inline, # COMPLETE
	'h2' => $xhtml_inline, # COMPLETE
	'h3' => $xhtml_inline, # COMPLETE
	'h4' => $xhtml_inline, # COMPLETE
	'h5' => $xhtml_inline, # COMPLETE
	'h6' => $xhtml_inline, # COMPLETE
	'div' => $xhtml_inline.$xhtml_block, # COMPLETE
	'blockquote' => $xhtml_block, # COMPLETE
) ;

$xhtml_allowed['caption'] .= $xhtml_allowed['p'] ;
$xhtml_allowed['li'] .= $xhtml_allowed['p'] ;

foreach ( $xhtml_allowed As $k => $v ) {
	$xhtml_allowed[$k] = explode ( ',' , $v ) ;
}


# The class
class XML2XHTML {
	var $s = "" ;
	var $tags = array () ;
	var $ignore_counter = 0 ;
	var $links = array () ;

	function fix_text ( $s , $replace_amp = false ) {
/*		$s = html_entity_decode ( $s ) ;
		filter_named_entities ( $s ) ;
		$s = str_replace ( "&" , "&amp;" , $s ) ;
		$s = str_replace ( "<" , "&lt;" , $s ) ;
		$s = str_replace ( ">" , "&gt;" , $s ) ;
		return utf8_decode ( $s ) ;*/
		filter_named_entities ( $s ) ;
		if ( $replace_amp ) $s = str_replace ( "&" , "&amp;" , $s ) ;
		$s = str_replace ( "<" , "&lt;" , $s ) ;
		$s = str_replace ( ">" , "&gt;" , $s ) ;
		return $s ;
	}
	
	function add ( $t ) { # Can be altered, e.g. for direct output (echo)
		$this->s .= $t ; 
	}
	
	function is_allowed ( $tag , $base = "" ) {
		global $xhtml_allowed ;
		if ( $tag == "" ) return false ;
		if ( $base == "" ) {
			$o = $this->top_tag () ;
			$base = $o->tag ;
		}
		if ( !isset ( $xhtml_allowed[$base] ) ) return false ;
		return in_array ( $tag , $xhtml_allowed[$base] ) ;
	}
	
	function filter_evil_attributes ( $tag , &$attrs ) {
		if ( count ( $attrs ) == 0 ) return "" ;
		$ret = "" ;
		foreach ( $attrs AS $k => $v ) {
			$ret .= " " . strtolower ( $k ) . '="' . str_replace ( '"' , '\"' , $v ) . '"' ;
		}
		return $ret ;
	}

	function add_tag ( $tag , $attrs = array () , $bogus = false ) {
		$o->tag = $tag ;
		$o->really_open = $this->is_allowed ( $tag ) ;
		if ( $bogus ) $o->really_open = false ;
		$o->close_with_previous = false ;
		$this->tags[] = $o ;
		if ( $o->really_open ) $this->add ( "<{$tag}" . $this->filter_evil_attributes ( $tag , $attrs ) . ">" ) ;
	}
	
	function close_tag ( $tag ) {
		if ( count ( $this->tags ) == 0 ) die ( "CLOSING NON-OPEN TAG {$tag}" ) ;
		$x = array_pop ( $this->tags ) ;
		if ( $tag != $x->tag ) die ( "CLOSING {$tag} instead of {$x->tag}" ) ;
		if ( $x->really_open ) $this->add ( "</{$x->tag}>" ) ;
		
		# Auto-close previous?
		$o = $this->top_tag() ;
		if ( $o->close_with_previous ) {
			$this->close_tag ( $o->tag ) ;
		}
	}
	
	function insist_on ( $tag ) {
		global $xhtml_allowed ;
		$o = $this->top_tag () ;
		if ( $o->tag == $tag ) return ; # Everything OK
		
		foreach ( $xhtml_allowed AS $k => $v ) {
			if ( $o->tag != $k ) continue ;
			if ( in_array ( $tag , $v ) ) return ; # Everything OK
		}
		
		$o->tag = $tag ;
		$o->really_open = true ;
		$o->close_with_previous = true ;
		$this->tags[] = $o ;
		$this->add ( "<{$tag}>" ) ;
	}
	
	function top_tag () {
		if ( count ( $this->tags ) == 0 ) {
			$o->tag = "" ;
			$o->really_open = false ;
			$o->close_with_previous = false ;
			return $o ;
		}
		$x = array_pop ( $this->tags ) ;
		$this->tags[] = $x ;
		return $x ;
	}
	
	
	
	
	function tag_paragraph ( $open , &$attrs ) {
		if ( !isset ( $attrs['align'] ) ) $attrs['align'] = 'justify' ;
		if ( $open ) $this->add_tag ( "p" , $attrs ) ;
		else $this->close_tag ( "p" ) ;
	}
	
	function tag_space ( $open , &$attrs ) {
		if ( $open ) $this->add ( " " ) ;
	}
	
	# SIMPLE TAGS
	
	function simple_tag ( $open , $tag ) {
		if ( $open ) $this->add_tag ( $tag ) ;
		else $this->close_tag ( $tag ) ;
	}
	
	function tag_bold ( $open , &$attrs ) { $this->simple_tag ( $open , "b" ) ; }
	function tag_xhtml_b ( $open , &$attrs ) { $this->simple_tag ( $open , "b" ) ; }
	function tag_xhtml_strong ( $open , &$attrs ) { $this->simple_tag ( $open , "strong" ) ;}
	function tag_italics ( $open , &$attrs ) { $this->simple_tag ( $open , "i" ) ; }
	function tag_xhtml_i ( $open , &$attrs ) { $this->simple_tag ( $open , "i" ) ; }
	function tag_xhtml_em ( $open , &$attrs ) { $this->simple_tag ( $open , "em" ) ; }
	function tag_xhtml_ol ( $open , &$attrs ) { $this->simple_tag ( $open , "ol" ) ; }
	function tag_xhtml_ul ( $open , &$attrs ) { $this->simple_tag ( $open , "ul" ) ; }
	function tag_xhtml_dl ( $open , &$attrs ) { $this->simple_tag ( $open , "dl" ) ; }
	function tag_xhtml_li ( $open , &$attrs ) { $this->simple_tag ( $open , "li" ) ; }
	function tag_xhtml_dt ( $open , &$attrs ) { $this->simple_tag ( $open , "dt" ) ; }
	function tag_xhtml_dd ( $open , &$attrs ) { $this->simple_tag ( $open , "dd" ) ; }
	
	# MISC
	function tag_list ( $open , &$attrs ) {
		if ( !$open ) {
			$o = $this->top_tag () ;
			$this->close_tag ( $o->tag ) ;
			return ;
		}
		$type = $attrs['TYPE'] ;
		if ( $type == 'bullet' ) {
			$this->tag_xhtml_ul ( $open , $attrs ) ;
		} else if ( $type == 'numbered' ) {
			$this->tag_xhtml_ol ( $open , $attrs ) ;
		} else if ( $type == 'ident' ) {
			$this->tag_xhtml_dl ( $open , $attrs ) ;
		} else return ;
	}

	function tag_listitem ( $open , &$attrs ) {
		$o = $this->top_tag() ;
		if ( !$open ) {
			$this->close_tag ( $o->tag ) ;
			return ;
		}
		if ( $o->tag == 'dl' || $o->tag == 'dt' || $o->tag == 'dd' ) $this->tag_xhtml_dt ( $open , $attrs ) ;
		else $this->tag_xhtml_li ( $open , $attrs ) ;
	}
	
	# HTML
	function tag_xhtml_div ( $open , &$attrs ) {
		if ( $open ) $this->add_tag ( "div" , $attrs ) ;
		else $this->close_tag ( "div" ) ;
	}
	
	function tag_xhtml_span ( $open , &$attrs ) {
		if ( $open ) $this->add_tag ( "div" , $attrs ) ;
		else $this->close_tag ( "div" ) ;
	}

	# LINKS
	function make_internal_link ( &$o ) {
		global $content_provider , $xmlg ;
		$text = $o->text ;
		if ( $text == "" ) $text = $o->target ;
		$text .= $o->trail ;
		$ns = $content_provider->get_namespace_id ( $o->target ) ;
		
		if ( $ns == 6 ) { # Image
			$nstext = explode ( ":" , $o->target , 2 ) ;
			$target = array_pop ( $nstext ) ;
			$href = $content_provider->get_image_url ( $target ) ;

			list($i_width, $i_height, $i_type, $i_attr) = @getimagesize($href);
			if ( $i_width <= 0 ) { # Paranoia
				$i_width = 100 ;
				$i_height = 100 ;
			}

			$width = "" ;
			$align = "" ;
			$is_thumb = false ;
			foreach ( $o->parts AS $p ) {
				$p = strtolower ( trim ( $p ) ) ;
				if ( $p == 'thumb' ) {
					$is_thumb = true ;
					if ( $align == '' ) $align = 'right' ;
					if ( $width == '' ) $width = '200' ;
				} else if ( $p == 'right' || $p == 'center' || $p == 'left' ) {
					$align = $p ;
				} else if ( substr ( $p , -2 , 2 ) == 'px' ) {
					$width = trim ( substr ( $p , 0 , -2 ) ) ;
				}
			}

			if ( $width == '' ) {
				$size = "" ;
				$divwidth = "" ;
			} else {
				$height = ( $i_height * $width ) / $i_width ;
				$size = " width='{$width}' height='{$height}'" ;
				$divwidth = $width + 2 ;
				$divwidth = ";width={$divwidth}" ;
			}
			
			$s = "" ;
			$image_page = 'http://' . $xmlg["site_base_url"] . '/index.php?title=' . urlencode ( $o->target ) ;
			if ( $is_thumb ) $s .= '<div class="thumb tright"><div style="' . $divwidth . '">' ;
			else if ( $align != '' ) $s .= "<div style='float:{$align}{$divwidth}'>" ;
			$s .= '<a href="' . $image_page . '" title="' . $text . '" class="internal">' ;
			$s .= "<img src='{$href}'{$size} alt=\"{$text}\" longdesc=\"{$image_page}\"/>" ;
			$s .= '</a>' ;
			if ( $is_thumb ) {
				$s .= '<div class="thumbcaption">' ;
				$s .= '<div class="magnify" style="float:right">' ;
				$s .= '<a href="' . $image_page . '" class="internal" title="enlarge">' ;
				$s .= '<img src="http://en.wikipedia.org/skins-1.5/common/images/magnify-clip.png" width="15" height="11" alt="enlarge" />' ;
				$s .= '</a>' ;
				$s .= "</div>" ;
				$s .= $text ;
				$s .= "</div>" ;
			}
			if ( $is_thumb || $align != '' ) $s .= "</div>" ;
			if ( $is_thumb ) $s .= "</div>" ;
			$this->add ( $s ) ;

		} else if ( $ns == -8 ) { # Category link
			if ( !$xmlg['keep_categories'] ) return ;
		} else if ( $ns == -9 ) { # Interlanguage link
			if ( !$xmlg['keep_interlanguage'] ) return ;
		} else { # Internal link
			$this->add ( $text ) ; # For now
		}
	}
	
	function tag_link ( $open , &$attrs ) {
		if ( $open ) {
			$o->trail = "" ;
			$o->parts = array () ;
			$o->target = "" ;
			$o->type = 'internal' ;
			$o->href = "" ;
			$o->text = "" ;
			if ( isset ( $attrs['TYPE'] ) ) $o->type = $attrs['TYPE'] ;
			if ( isset ( $attrs['HREF'] ) ) $o->href = $attrs['HREF'] ;
			$o->s = $this->s ;
			$this->s = "" ;
			$this->links[] = $o ;
		} else {
			$o = array_pop ( $this->links ) ;
			$text = $this->s ;
			$this->s = $o->s ;
			if ( count ( $o->parts ) > 0 ) $o->text = array_pop ( $o->parts ) ;
			if ( $o->type == 'internal' ) {
				$this->make_internal_link ( $o ) ;
			} else {
				$this->add ( '<a href="' . $o->href . '">' . $text . '</a>' ) ;
			}
		}
	}

	function tag_target ( $open , &$attrs ) {
		if ( $open ) return ;
		$o = array_pop ( $this->links ) ;
		$o->target = $this->s ;
		$this->s = "" ;
		$this->links[] = $o ;
	}

	function tag_part ( $open , &$attrs ) {
		if ( $open ) return ;
		$o = array_pop ( $this->links ) ;
		$o->parts[] = $this->s ;
		$this->s = "" ;
		$this->links[] = $o ;
	}

	function tag_trail ( $open , &$attrs ) {
		if ( $open ) return ;
		$o = array_pop ( $this->links ) ;
		$o->trail = $this->s ;
		$this->s = "" ;
		$this->links[] = $o ;
	}

	
	# IGNORE TAGS
	function ignore ( $open ) {
		if ( $open ) $this->ignore_counter++ ;
		else $this->ignore_counter-- ;
	}
	
	function tag_template  ( $open , &$attrs ) { $this->ignore ( $open ) ; }
	function tag_templatevar  ( $open , &$attrs ) { $this->ignore ( $open ) ; }
	function tag_magic_variable  ( $open , &$attrs ) { $this->ignore ( $open ) ; }

	# HEADINGS
	function tag_heading ( $open , &$attrs , $level = "" ) {
		if ( $level == "" ) $level = $attrs['LEVEL'] ;
		if ( $level > 6 ) $level = 6 ; # Paranoia
		if ( $open ) {
			$this->add_tag ( "h{$level}" ) ;
		} else {
			$o = $this->top_tag() ;
			$this->close_tag ( $o->tag ) ;
		}
	}
	
	function tag_xhtml_h1 ( $open , &$attrs ) { $this->tag_heading ( $open , $attrs , '1' ) ; }
	function tag_xhtml_h2 ( $open , &$attrs ) { $this->tag_heading ( $open , $attrs , '2' ) ; }
	function tag_xhtml_h3 ( $open , &$attrs ) { $this->tag_heading ( $open , $attrs , '3' ) ; }
	function tag_xhtml_h4 ( $open , &$attrs ) { $this->tag_heading ( $open , $attrs , '4' ) ; }
	function tag_xhtml_h5 ( $open , &$attrs ) { $this->tag_heading ( $open , $attrs , '5' ) ; }
	function tag_xhtml_h6 ( $open , &$attrs ) { $this->tag_heading ( $open , $attrs , '6' ) ; }
	
	# TABLES
	function tag_table ( $open , &$attrs ) {
		$o = $this->top_tag() ;
		if ( $o->tag == "p" && $o->really_open ) {
			$this->close_tag ( 'p' ) ;
			$this->add_tag ( "p" , array() , true ) ;
		}
		if ( $open ) {
			$this->add_tag ( "table" , $attrs ) ;
		} else {
			$this->close_tag ( "table" ) ;
		}
	}

	function tag_tablecaption ( $open , &$attrs ) {
		if ( $open ) {
			$this->insist_on ( "table" ) ;
			$this->add_tag ( "caption" , $attrs ) ;
		} else {
			$this->close_tag ( "caption" ) ;
		}
	}

	function tag_tablerow ( $open , &$attrs ) {
		if ( $open ) {
			$this->insist_on ( "table" ) ;
			$this->add_tag ( "tr" , $attrs ) ;
		} else {
			$this->close_tag ( "tr" ) ;
		}
	}

	function tag_tablecell ( $open , &$attrs ) {
		if ( $open ) {
			$this->insist_on ( "tr" ) ;
			$this->add_tag ( "td" , $attrs ) ;
		} else {
			$this->close_tag ( "td" ) ;
		}
	}

	function tag_tablehead ( $open , &$attrs ) {
		if ( $open ) {
			$this->insist_on ( "tr" ) ;
			$this->add_tag ( "th" , $attrs ) ;
		} else {
			$this->close_tag ( "th" ) ;
		}
	}

	function tag_xhtml_table ( $open , &$attrs ) { $this->tag_table ( $open , $attrs ) ; }
	function tag_xhtml_tr ( $open , &$attrs ) { $this->tag_tablerow ( $open , $attrs ) ; }
	function tag_xhtml_td ( $open , &$attrs ) { $this->tag_tablecell ( $open , $attrs ) ; }
	function tag_xhtml_th ( $open , &$attrs ) { $this->tag_tablehead ( $open , $attrs ) ; }
	function tag_xhtml_caption ( $open , &$attrs ) { $this->tag_tablecaption ( $open , $attrs ) ; }



}


# Global functions for parsing

function XML2XHTML_START($parser, $name, $attrs) {
	global $xml2xhtml ;
	$name = strtolower ( $name ) ;
	$function = 'tag_' . str_replace ( ':' , '_' , $name ) ;
	if ( method_exists ( $xml2xhtml , $function ) ) {
		$xml2xhtml->$function ( true , $attrs ) ;
	} else {
	}
}

function XML2XHTML_END($parser, $name) {
	global $xml2xhtml ;
	$name = strtolower ( $name ) ;
	$function = 'tag_' . str_replace ( ':' , '_' , $name ) ;
	if ( method_exists ( $xml2xhtml , $function ) ) {
		$xml2xhtml->$function ( false , $attrs ) ;
	} else {
	}
}

function XML2XHTML_DATA ( $parser, $data ) {
	global $xml2xhtml ;
	if ( $xml2xhtml->ignore_counter > 0 ) return ;
	$xml2xhtml->s .= $xml2xhtml->fix_text ( $data ) ;
}

function convert_xml_xhtml ( &$xml ) {
	global $xml2xhtml ;
	$xml2xhtml = new XML2XHTML ;
	$xml_parser_handle = xml_parser_create();
	xml_set_element_handler($xml_parser_handle, "XML2XHTML_START", "XML2XHTML_END");
	xml_set_character_data_handler($xml_parser_handle, "XML2XHTML_DATA"); 
	xml_parse($xml_parser_handle, $xml) ; #, feof($parse_handle)
	
#	if (!($parse_handle = fopen($xml_filename, 'r'))) {
#		die("FEHLER: Datei $xml_filename nicht gefunden.");
#	}
	
#	while ($xml_data = fread($parse_handle, 4096)) {
#		if (!xml_parse($xml_parser_handle, $xml_data, feof($parse_handle))) {
#			die(sprintf('XML error: %s at line %d',
#			xml_error_string(xml_get_error_code($xml_parser_handle)),
#			xml_get_current_line_number($xml_parser_handle)));
#		}
#	}

	xml_parser_free($xml_parser_handle); 
}

?>
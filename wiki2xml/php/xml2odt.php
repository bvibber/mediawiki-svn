<?php

class TextStyle {
	var $name = "" ;
	var $bold = false ;
	var $italics = false ;
	var $count = 0 ;
}

class XML2ODT {
	var $tags ;
	var $textstyle_current ;
	var $textstyles = array () ;
	var $listcode = "" ;
	var $list_is_open = false ;
	var $list_list = array () ;
	var $list_item_name = array () ;
	
	function XML2ODT () {
		$this->textstyle_current = new TextStyle ;
		$this->textstyle_current->name = "T0" ;
		$this->textstyles['T0'] = $this->textstyle_current ;
		$this->tags = array () ;
	}
	
	function get_list_key () {
		return "" ;
	}
	
	function ensure_list_open () {
		if ( $this->list_is_open ) return "" ;
		$this->list_is_open = true ;
		if ( substr ( $this->listcode , -1 ) == '#' ) $o->type = 'numbered' ;
		else $o->type = 'bullet' ;
		$o->depth = strlen ( $this->listcode ) ;
		$o->number = count ( $this->list_list ) + 1 ;
		$this->list_list[] = $o ;
		while ( count ( $this->list_item_name ) <= $o->depth ) $this->list_item_name[] = "" ;
		$this->list_item_name[$o->depth] = 'PL' . $o->number ;
		return '<text:list text:style-name="L' . $o->number . '">' ;
	}
	
	function ensure_list_closed () {
		if ( !$this->list_is_open ) return "" ;
		$this->list_is_open = false ;
		$ret = "" ;
		$ot = $this->tags ;
		do {
			$x = array_pop ( $this->tags ) ;
			$ret .= "</{$x}>" ;
		} while ( $x != "text:list-item" && count ( $this->tags ) > 0 ) ;
		if ( $x != "text:list-item" ) {
			$ret = "" ;
			$this->tags = $ot ;
		}
		$ret .= "</text:list>" ;
		return $ret ;
	}
	
	function get_text_style ( $find ) {
		$found = "" ;
		foreach ( $this->textstyles AS $k => $ts ) {
			if ( $ts->bold != $find->bold ) continue ;
			if ( $ts->italics != $find->italics ) continue ;
			$this->textstyles[$k]->count++ ;
			return $ts ;
		}
		
		# Create new style
		$found = "T" . count ( $this->textstyles ) ;
		$find->name = $found ;
		$find->count = 1 ;
		$this->textstyles[$found] = $find ;
		return $find ;
	}

	function get_styles_xml () {
		$ret = '<office:automatic-styles>' ;
		
		# Text styles
		foreach ( $this->textstyles AS $ts ) {
			if ( $ts->count == 0 ) continue ; # Skip, style never used
			$ret .= '<style:style style:name="' . $ts->name . '" style:family="text">' ;
			$ret .= '<style:text-properties' ;
			if ( $ts->italics ) $ret .= ' fo:font-style="italic" style:font-style-asian="italic" style:font-style-complex="italic"' ;
			if ( $ts->bold ) $ret .= ' fo:font-weight="bold" style:font-weight-asian="bold" style:font-weight-complex="bold"' ;
			$ret .= '/>' ;
			$ret .= '</style:style>' ;
		}
		
		# List styles
		$cm = 0.3 ;
		foreach ( $this->list_list AS $list ) {
			$l = "L" . $list->number ;
			$p = "PL" . $list->number ;
			$ret .= '<style:style style:name="'.$p.'" style:family="paragraph" style:parent-style-name="Standard" style:list-style-name="'.$l.'">' ;
			if ( $list->depth > 1 ) {
				$off = $cm * $list->depth ;
				$ret .= '<style:paragraph-properties fo:margin-left="' .
				$off .
				'cm" fo:margin-right="0cm" fo:text-indent="0cm" style:auto-text-indent="false"/>' ;
			}
			$ret .= '</style:style>' ;
			$ret .= '<text:list-style style:name="' . $l . '">' ;
			$off = 0 ;
			for ( $depth = 1 ; $depth <= 10 ; $depth++ ) {
				$off += $cm ;
				if ( $list->type == 'numbered' ) {
					$ret .= '<text:list-level-style-number text:level="' .
							$depth .
							'" text:style-name="Numbering_20_Symbols" style:num-suffix="." style:num-format="1">' .
							'<style:list-level-properties text:space-before="' .
							$off . 'cm" text:min-label-width="' . $cm . 'cm"/>' .
							'</text:list-level-style-number>' ;
				} else  {
					$ret .= '<text:list-level-style-bullet text:level="' .
							$depth . 
							'" text:style-name="Bullet_20_Symbols" style:num-suffix="." text:bullet-char="*">' .
							'<style:list-level-properties text:space-before="' .
							$off . 'cm" text:min-label-width="' . $cm . 'cm"/>' .
							'<style:text-properties style:font-name="StarSymbol"/>' .
							'</text:list-level-style-bullet>' ;
				}
			}
			$ret .= '</text:list-style>' ;
		}
		
		$ret .= '</office:automatic-styles>' ;
		
		return $ret ;
	}
	
	function get_odt_start () {
		$ret = "" ;
		
		$ret .= '<office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0">' ;

		
		$ret .= '<office:scripts/>
<office:font-face-decls>
<style:font-face style:name="Tahoma1" svg:font-family="Tahoma"/>
<style:font-face style:name="Lucida Sans Unicode" svg:font-family="&apos;Lucida Sans Unicode&apos;" style:font-pitch="variable"/>
<style:font-face style:name="Tahoma" svg:font-family="Tahoma" style:font-pitch="variable"/>
<style:font-face style:name="Times New Roman" svg:font-family="&apos;Times New Roman&apos;" style:font-family-generic="roman" style:font-pitch="variable"/>
<style:font-face style:name="Arial" svg:font-family="Arial" style:font-family-generic="swiss" style:font-pitch="variable"/>
</office:font-face-decls>' ;

		$ret .= $this->get_styles_xml () ;
		return $ret ;
	}

}



class element {
	var $name = '';
	var $attrs = array ();
	var $children = array ();


	function sub_parse(& $tree, $tag = '') {
		$ret = '';

		foreach ($this->children as $key => $child) {
			if (is_string($child)) {
				$ret .= $child;
			} else {
				$ret .= $child->parse($tree);
			}
		}
		return $ret;
	}
	
	function push_tag ( $tag , $params = "" ) {
		global $xml2odt ;
		$n = "<" . $tag ;
		if ( $params != "" ) $n .= " " . $params ;
		$n .= ">" ;
		$xml2odt->tags[] = $tag ;
		return $n ;
	}


	function parse ( &$tree ) {
		global $xml2odt ;
		$ret = '';
		$tag = $this->name; # Shortcut

		$old_text_style = $xml2odt->textstyle_current ;
		$tag_count = count ( $xml2odt->tags ) ;

		# Open tag
		if ( $tag == "SPACE" ) {
			return " " ;
		} else if ( $tag == "BOLD" || $tag == "XHTML:B" || $tag == "XHTML:STRONG" ) {
			$xml2odt->textstyle_current->bold = true ;
			$xml2odt->textstyle_current = $xml2odt->get_text_style ( $xml2odt->textstyle_current ) ;
			$ret .= $this->push_tag ( "text:span" , "text:style-name=\"" . $xml2odt->textstyle_current->name . "\"" ) ;
		} else if ( $tag == "ITALICS" || $tag == "XHTML:I" || $tag == "XHTML:EM" ) {
			$xml2odt->textstyle_current->italics = true ;
			$xml2odt->textstyle_current = $xml2odt->get_text_style ( $xml2odt->textstyle_current ) ;
			$ret .= $this->push_tag ( "text:span" , "text:style-name=\"" . $xml2odt->textstyle_current->name . "\"" ) ;
		} else if ( $tag == "PARAGRAPH" || $tag == "XHTML:P" ) {
			$ret .= $this->push_tag ( "text:p" , 'text:style-name="Standard"' ) ;
		} else if ( $tag == "LIST" ) {
			$ret .= $xml2odt->ensure_list_closed () ;
			$type = strtolower ( $this->attrs['TYPE'] ) ;
			if ( $type == 'numbered' ) $xml2odt->listcode .= "#" ;
			else $xml2odt->listcode .= "*" ;
		} else if ( $tag == "LISTITEM" ) {
			$ret .= $xml2odt->ensure_list_open () ;
			$tag_count = count ( $xml2odt->tags ) ;
			$p = $xml2odt->list_item_name[strlen($xml2odt->listcode)] ;
			$ret .= $this->push_tag ( "text:list-item" ) ;
			$ret .= $this->push_tag ( "text:p" , 'text:style-name="' . $p . '"' ) ;
		}

		# Children
		$ret .= $this->sub_parse ( $tree ) ;

		# Close tag
		$xml2odt->textstyle_current = $old_text_style ;
		
		while ( $tag_count < count ( $xml2odt->tags ) ) {
			$x = array_pop ( $xml2odt->tags ) ;
			$ret .= "</{$x}>" ;
		}

		if ( $tag == "LIST" ) {
			$ret .= $xml2odt->ensure_list_closed () ;
			$xml2odt->listcode = substr ( $xml2odt->listcode , 0 , strlen ( $xml2odt->listcode ) - 1 ) ;
		}
		
		return $ret ;
	}
}

require_once ( "./xml2tree.php" ) ; # Uses the "element" class defined above

?>

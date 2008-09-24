<?php

class CodeDiffHighlighter {
	function render( $text ) {
		return '<pre class="mw-codereview-diff">' .
			$this->splitLines( $text ) .
			"</pre>\n";
	}
	
	function splitLines( $text ) {
		return implode( "\n",
			array_map( array( $this, 'colorLine' ),
				explode( "\n", $text ) ) );
	}
	
	function colorLine( $line ) {
		list( $element, $attribs ) = $this->tagForLine( $line );
		return Xml::element( $element, $attribs, $line );
	}
	
	function tagForLine( $line ) {
		static $default = array( 'span', array() );
		static $tags = array(
			'-' => array( 'del', array() ),
			'+' => array( 'ins', array() ),
			'@' => array( 'span', array( 'class' => 'meta' ) ),
			' ' => array( 'span', array() ),
			);
		$first = substr( $line, 0, 1 );
		if( isset( $tags[$first] ) )
			return $tags[$first];
		else
			return $default;
	}
	
}

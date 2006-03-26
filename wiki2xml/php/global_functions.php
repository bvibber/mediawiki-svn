<?php

# Add authors to global list
function add_authors ( $authors ) {
	global $wiki2xml_authors ;
	foreach ( $authors AS $author ) {
		if ( !in_array ( $author , $wiki2xml_authors ) ) {
			$wiki2xml_authors[] = $author ;
		}
	}
}

function add_author ( $author ) {
	add_authors ( array ( $author ) ) ;
	}

?>
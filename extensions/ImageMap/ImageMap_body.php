<?php

/**
 * Image map extension. 
 *
 * Syntax:
 * <imagemap>
 * Image:Foo.jpg | 100px | picture of a foo
 *
 * rect    0  0  50 50  [[Foo type A]]
 * circle  50 50 20     [[Foo type B]]
 * 
 * desc bottom-left
 * </imagemap>
 *
 * Coordinates are relative to the source image, not the thumbnail
 *
 */

require_once( dirname( __FILE__ ) . '/ImageMap.i18n.php' );
global $wgMessageCache;
foreach( efImageMapMessages() as $lang => $messages )
	$wgMessageCache->addMessages( $messages, $lang );

class ImageMap {
	static public $id = 0;

	const TOP_RIGHT = 0;
	const BOTTOM_RIGHT = 1;
	const BOTTOM_LEFT = 2;
	const TOP_LEFT = 3;
	const NONE = 4;

	static function render( $input, $params, $parser ) {
		global $wgScriptPath;

		$lines = explode( "\n", $input );

		$first = true;
		$lineNum = 0;
		$output = '';
		$links = array();
		$descType = self::BOTTOM_RIGHT;
		$defaultLinkAttribs = false;
		foreach ( $lines as $line ) {
			++$lineNum;

			$line = trim( $line );
			if ( $line == '' || $line[0] == '#' ) {
				continue;
			}

			if ( $first ) {
				$first = false;

				# The first line should have an image specification on it
				# Extract it and render the HTML
				$bits = explode( '|', $line, 2 );
				if ( count( $bits ) == 1 ) {
					$image = $bits[0];
					$options = '';
				} else {
					list( $image, $options ) = $bits;
				}
				$imageTitle = Title::newFromText( $image );
				if ( !$imageTitle || $imageTitle->getNamespace() != NS_IMAGE ) {
					return self::error( 'imagemap_no_image' );
				}
				$imageHTML = $parser->makeImage( $imageTitle, $options );

				$domDoc = DOMDocument::loadXML( $imageHTML );
				$xpath = new DOMXPath( $domDoc );
				$imgs = $xpath->query( '//img' );
				if ( !$imgs->length ) {
					return self::error( 'imagemap_invalid_image' );
				}
				$imageNode = $imgs->item(0);
				$thumbWidth = $imageNode->getAttribute('width');
				$thumbHeight = $imageNode->getAttribute('height');

				$imageObj = new Image( $imageTitle );
				# Add the linear dimensions to avoid inaccuracy in the scale 
				# factor when one is much larger than the other
				# (sx+sy)/(x+y) = s
				$denominator = $imageObj->getWidth() + $imageObj->getHeight();
				$numerator = $thumbWidth + $thumbHeight;
				if ( $denominator <= 0 || $numerator <= 0 ) {
					return self::error( 'imagemap_invalid_image' );
				}
				$scale = $numerator / $denominator;
				continue;
			}

			# Handle desc spec
			$cmd = strtok( $line, " \t" );
			if ( $cmd == 'desc' ) {
				$typesText = wfMsgForContent( 'imagemap_desc_types' );
				$types = array_map( 'trim', explode( ',', $typesText ) );
				$type = trim( strtok( '' ) );
				$descType = array_search( $type, $types );
				if ( $descType === false ) {
					return self::error( 'imagemap_invalid_desc', $typesText );
				}
				continue;
			}

			# Find the link
			$link = trim( strstr( $line, '[[' ) );
			if ( preg_match( '/^ \[\[  ([^|]*+)  \|  ([^\]]*+)  \]\] \w* $ /x', $link, $m ) ) {
				$title = Title::newFromText( $m[1] );
				$alt = trim( $m[2] );
			} elseif ( preg_match( '/^ \[\[  ([^\]]*+) \]\] \w* $ /x', $link, $m ) ) {
				$title = Title::newFromText( $m[1] );
				$alt = $title->getFullText();
			} else {
				return self::error( 'imagemap_no_link', $lineNum );
			}
			if ( !$title ) {
				return self::error( 'imagemap_invalid_title', $lineNum );
			}

			$shapeSpec = substr( $line, 0, -strlen( $link ) );

			# Tokenize shape spec
			$shape = strtok( $shapeSpec, " \t" );
			switch ( $shape ) {
				case 'default':
					$coords = array();
					break;
				case 'rect':
					$coords = self::tokenizeCoords( 4, $lineNum );
					if ( !is_array( $coords ) ) {
						return $coords;
					}
					break;
				case 'circle':
					$coords = self::tokenizeCoords( 3, $lineNum );
					if ( !is_array( $coords ) ) {
						return $coords;
					}
					break;
				case 'poly':
					$coord = strtok( " \t" );
					while ( $coord !== false ) {
						$coords[] = $coord;
						$coord = strtok( " \t" );
					}
					if ( !count( $coords ) ) {
						return self::error( 'imagemap_missing_coord', $lineNum );
					}
					break;
				default:
					return self::error( 'imagemap_unrecognised_shape', $lineNum );
			}

			# Scale the coords using the size of the source image
			foreach ( $coords as $i => $c ) {
				$coords[$i] *= $scale;
			}

			# Construct the area tag
			$attribs = array( 
				'href' => $title->escapeLocalURL(),
			);
			if ( $shape != 'default' ) {
				$attribs['shape'] = $shape;
			}
			if ( $coords ) {
				$attribs['coords'] = implode( ',', $coords );
			}
			if ( $alt != '' ) {
				if ( $shape != 'default' ) {
					$attribs['alt'] = $alt;
				}
				$attribs['title'] = $alt;
			} 
			if ( $shape == 'default' ) {
				$defaultLinkAttribs = $attribs;
			} else {
				$output .= Xml::element( 'area', $attribs ) . "\n";
			}
			$links[] = $title;
		}

		if ( $first ) {
			return self::error( 'imagemap_no_image' );
		}

		if ( $output == '' ) {
			return self::error( 'imagemap_no_areas' );
		}

		# Construct the map
		$mapName = "ImageMap_" . ++self::$id;
		$output = "<map name=\"$mapName\">\n$output</map>\n";
		
		# Alter the image tag
		$imageNode->setAttribute( 'usemap', "#$mapName" );
		
		# Add a surrounding div, remove the default link to the description page
		$anchor = $imageNode->parentNode;
		$parent = $anchor->parentNode;
		$div = $parent->insertBefore( new DOMElement( 'div' ), $anchor );
		$div->setAttribute( 'style', 'position: relative;' );
		if ( $defaultLinkAttribs ) {
			$defaultAnchor = $div->appendChild( new DOMElement( 'a' ) );
			foreach ( $defaultLinkAttribs as $name => $value ) {
				$defaultAnchor->setAttribute( $name, $value );
			}
			$imageParent = $defaultAnchor;
		} else {
			$imageParent = $div;
		}

		$imageParent->appendChild( $imageNode->cloneNode( true ) );
		$parent->removeChild( $anchor );

		# Determine whether a "magnify" link is present
		$xpath = new DOMXPath( $domDoc );
		$magnify = $xpath->query( '//div[@class="magnify"]' );
		if ( !$magnify->length && $descType != self::NONE ) {
			# Add image description link
			if ( $descType == self::TOP_LEFT || $descType == self::BOTTOM_LEFT ) {
				$descLeft = 0;
			} else {
				$descLeft = $thumbWidth - 20;
			}
			if ( $descType == self::TOP_LEFT || $descType == self::TOP_RIGHT ) {
				$descTop = 0;
			} else {
				$descTop = $thumbHeight - 20;
			}
			$descAnchor = $div->appendChild( new DOMElement( 'a' ) );
			$descAnchor->setAttribute( 'href', $imageTitle->escapeLocalURL() );
			$descAnchor->setAttribute( 'title', wfMsgForContent( 'imagemap_description' ) );
			$descAnchor->setAttribute( 'style', "position:absolute; top: {$descTop}px; left: {$descLeft}px;" );
			$descImg = $descAnchor->appendChild( new DOMElement( 'img' ) );
			$descImg->setAttribute( 'alt', wfMsgForContent( 'imagemap_description' ) );
			$descImg->setAttribute( 'src', "$wgScriptPath/extensions/ImageMap/desc-20.png" );
			$descImg->setAttribute( 'style', 'border: none;' );
		}

		# Output the result
		# We use saveXML() not saveHTML() because then we get XHTML-compliant output.
		# The disadvantage is that we have to strip out the DTD
		$output .= preg_replace( '/<\?xml[^?]*\?>/', '', $domDoc->saveXML() );

		# Register links
		$parser->mOutput->addImage( $imageTitle->getDBkey() );
		foreach ( $links as $title ) {
			$parser->mOutput->addLink( $title );
		}

		# Armour output against broken parser
		$output = str_replace( "\n", '', $output );
		return $output;
	}

	static function tokenizeCoords( $count, $lineNum ) {
		$coords = array();
		for ( $i = 0; $i < $count; $i++ ) {
			$coord = strtok( " \t" );
			if ( $coord === false ) {
				return self::error( 'imagemap_missing_coord', $lineNum );
			}
			if ( !is_numeric( $coord ) || $coord > 1e9 || $coord < 0 ) {
				return self::error( 'imagemap_invalid_coord', $lineNum );
			}
			$coords[$i] = $coord;
		}
		return $coords;
	}

	static function error( $name, $line = false ) {
		return wfMsgForContent( $name, $line );
	}
}

?>

<?php

/**
 * Click Extension
 *
 * dds a parser function to display an image with a link that leads to a page other than the image description page.
 *
 * @addtogroup Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:Click
 *
 * @author MinuteElectron <minuteelectron@googlemail.com>
 * @copyright Copyright © 2008 MinuteElectron.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

// If this is run directly from the web die as this is not a valid entry point.
if( !defined( 'MEDIAWIKI' ) ) die;

// Extension credits.
$wgExtensionCredits[ 'parserhook' ][] = array(
	'name'        => 'Click',
	'description' => 'Adds a parser function to display an image with a link that leads to a page other than the image description page.',
	'author'      => 'MinuteElectron',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:Click',
	'version'     => '1.2',
);

// Setup function.
$wgExtensionFunctions[] = 'efClickParserFunction_Setup';

// Register hook.
$wgHooks[ 'LanguageGetMagic' ][] = 'efClickParserFunction_Magic';

function efClickParserFunction_Setup() {
	global $wgParser;
	// Register parser function hook.
	$wgParser->setFunctionHook( 'Click', 'efClickParserFunction_Render' );
	// Return true so things don't break.
	return true;
}

function efClickParserFunction_Magic( &$magicWords, $langCode ) {
	// Register parser function magic word.
	$magicWords[ 'Click' ] = array( 0, 'Click' );
	// Return true so things don't break.
	return true;
}

function efClickParserFunction_Render( &$parser, $target = '', $image = '', $widthalt = '', $altwidth = '' ) {

	// Width and alt-text are interchangable.
	if( preg_match( '#^[0|1|2|3|4|5|6|7|8|9]*px$#', $widthalt ) ) {
		// First value width, second alt.
		$width = $widthalt;
		$alt = $altwidth;
	} elseif( preg_match( '#^[0|1|2|3|4|5|6|7|8|9]*px$#', $altwidth ) ) {
		// First value alt, second width.
		$alt = $widthalt;
		$width = $altwidth;
	} else {
		// First value alt, no width.
		$alt = $widthalt;
		$width = false;
	}

	// Escape quotation marks of alt attribute so that any character can be
	// used (and it looks alright), but we are safe from including arbitrary
	// HTML.
	$alt = str_replace( '"', '&quot;', $alt );

	// Open hyperlink, default to a on-wiki page, but if it doesn't exist and
	// is a valid external URL then use it.
	$targettitle = Title::newFromText( $target );
	if( is_object( $targettitle ) && $targettitle->exists() ) {
		// Internal link, open hyperlink and register internal link.
		$r = Xml::openElement( 'a', array( 'href' => $targettitle->getLocalUrl(), 'title' => $alt ) );
		$parser->mOutput->addLink( $targettitle );
	} else {
		// Internal page doesn't exist, test if external.
		global $wgUrlProtocols;
		$ext = false;
		foreach( $wgUrlProtocols as $protocol ) {
			if( strpos( $target, $protocol ) === 0 ) {
				$ext = true;
				break;
			}
		}
		if( $ext ) {
			// External link, open hyperlink with escaped href and register external link.
			$r = Xml::openElement( 'a', array( 'href' => htmlspecialchars( $target ), 'title' => $alt ) );
			$parser->mOutput->addExternalLink( $target );
		} elseif( is_object( $targettitle ) ) {
				// Valid internal link after all (but to non-existant page), open hyperlink and register internal link.
				$r = Xml::openElement( 'a', array( 'class' => 'new', 'href' => $targettitle->getLocalUrl( 'action=edit&redlink=1' ), 'title' => $alt ) );
				$parser->mOutput->addLink( $targettitle );
		}
	}

	// Add image element, or use alt text on it's own if image doesn't exist.
	$imagetitle = Title::newFromText( $image );
	if( is_object( $imagetitle ) ) $imageimage = Image::newFromTitle( $imagetitle );
	if( isset( $imageimage ) && is_object( $imagetitle ) && is_object( $imageimage ) && $imageimage->exists() ) {
		// Display image.
		if( !$width ) $width = $imageimage->getWidth();
		$thumbnail = $imageimage->transform( array( 'width' => $width ) );
		$r .= $thumbnail->toHtml( array( 'alt' => $alt, 'file-link' => false ) );
	} else {
		// Display alt text.
		$r .= $alt;
	}
	// Register image usage if it is a valid name, even if it doesn't exist (so it appears as a "wanted image").
	if( isset( $imageimage ) && is_object( $imagetitle ) && is_object( $imageimage ) ) {
			$parser->mOutput->addImage( $imagetitle->getDBkey() );
	}

	// Close hyperlink.
	if( is_object( $targettitle ) ) $r .= Xml::closeElement( 'a' );

	// Yes, this is HTML.
	// TODO: Find some way to make this inline (not start a new paragraph).
	return array( $r, 'noparse' => true, 'isHTML' => true );

}

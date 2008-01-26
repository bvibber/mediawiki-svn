<?php

/**
 * Display a category stepper box on pages that are in a set set of categories.
 *
 * @addtogroup Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:CategoryStepper
 *
 * @author MinuteElectron <minuteelectron@googlemail.com>
 * @copyright Copyright © 2007-2008 MinuteElectron.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

// If this is run directly from the web die as this is not a valid entry point.
if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Invalid entry point.' );
}
 
// Extension credits.
$wgExtensionCredits[ 'other' ][] = array(
	'name' => 'CategoryStepper',
	'description' => 'Display a category stepper box on pages that are in a set set of categories.',
	'author' => 'MinuteElectron',
	'url' => 'http://www.mediawiki.org/wiki/Extension:CategoryStepper',
	'version' => '1.2',
);

// Hook into OutputPageBeforeHTML to add content to the end of the content.
$wgHooks[ 'OutputPageBeforeHTML' ][] = 'wfCategoryStepper';

// Set extension messages file.
$wgExtensionMessagesFiles[ 'CategoryStepper' ] = dirname( __FILE__ ) . '/CategoryStepper.i18n.php';

// Default configuration variables.
// Array of categories that the stepper will be shown on followed by the title it will be given.
// e.g. $wgCategoryStepper[ 'Astronauts' ] = 'Astronauts Category';
$wgCategoryStepper = array();

/**
 * Generate the form to be displayed at the top of the edit page and insert it into the page.
 * @param $out OutputPage
 * @param $text Text
 * @return true
 */
function wfCategoryStepper( &$out, &$text ) {

	// Get various variables needed for this extension.
	global $wgCategoryStepper, $wgTitle, $wgArticlePath;

	// Load messages into the message cache.
	wfLoadExtensionMessages( 'CategoryStepper' );

	// Open a database connection.
	$dbr =& wfGetDB( DB_SLAVE );

	// Loop through all the categories.
	foreach( $wgCategoryStepper as $name => $title ) {
		
		// Check if the currnet page is in this category and if so render the box.
		// @todo Check if there is some function alternative to this.
		if( $dbr->fetchRow( $dbr->select( "categorylinks", "*", array( "cl_from" => $wgTitle->getArticleID(), "cl_to" => $name ) ) ) ) {

			$prev = false;
			$nextI = false;

			// Get an array of pages in this category.
			$res = $dbr->select( "categorylinks", "cl_from", array( "cl_to" => $name ), 'Database::select', array( 'ORDER BY' => "cl_sortkey" ) );
			while( $row = $dbr->fetchRow( $res ) ) {
				if( isset( $donext ) ) {
					$nextI = $row[ 'cl_from' ];
					break;
				} elseif( $row[ 'cl_from' ] == $wgTitle->getArticleID() ) {
					$prevI = $prev;
					$donext = true;
				}
				$prev = $row[ 'cl_from' ];
			}

			// Get the title of the element before this.
			if( $prevI ) {
				$row = $dbr->fetchRow( $dbr->select( "page", "page_title", array( "page_id" => $prevI ) ) );
				$previous = Xml::element( "a", array( "href" => str_replace( "$1", $row[ 'page_title' ], $wgArticlePath ) ), str_replace( "_", ' ', $row[ 'page_title' ] ) );
			} else {
				$previous = Xml::element( "span", array( "style" => "font-style:italic;" ), wfMsg( "categorystepper-start" ) );
			}

			// Get the title of the element after this.
			if( $nextI ) {
				$row = $dbr->fetchRow( $dbr->select( "page", "page_title", array( "page_id" => $nextI ) ) );
				$next = Xml::element( "a", array( "href" => str_replace( "$1", $row[ 'page_title' ], $wgArticlePath ) ), str_replace( "_", ' ', $row[ 'page_title' ] ) );
			} else {
				$next = Xml::element( "span", array( "style" => "font-style:italic;" ), wfMsg( "categorystepper-end" ) );
			}

			// Generate the table at the bottom of the page and add it to the page text.
			$text .=
				Xml::openElement( "table", array( "class" => 'categorystepper', 'style' => 'margin-left:auto;margin-right:auto;' ) ) .
					Xml::openElement( "tr" ) .
						Xml::openElement( "th", array( "colspan" => "3" ) ) .
							Xml::element( "a", array( "href" => str_replace( "$1", "Category:" . $name, $wgArticlePath ) ), $title ) .
						Xml::closeElement( "th" ) .
					Xml::closeElement( "tr" ) .
					Xml::openElement( "tr" ) .
						Xml::tags( "td", array(), $previous ) .
						Xml::tags( "td", array(), $wgTitle->getText() ) .
						Xml::tags( "td", array(), $next ) .
					Xml::closeElement( "tr" ) .
				Xml::closeElement( "table" );

		}

	}

	// Return true so things don't break.
	return true;

}

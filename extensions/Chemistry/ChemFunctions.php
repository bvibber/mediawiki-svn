<?php

/**
 * Header
 */

 /**

 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if (!defined('MEDIAWIKI')) die();

/** Chemform wikipedia extension.
 *
 *   Formats the text between the tags as a chemical formula,
 *	First: all numericals are put in subscript.
 *	Second:  and - are put in superscript
 *	Third: all numericals preceding a + or a - are converted from subscript to superscript.
 *
 * usage: <chemform searchfor="tosearchfor" noprocess nolink>formula</chemform>
 *  (all parameters are optional).
 * Parameters:
 *   searchfor: alternate (e.g. CAS-sorted) formula to search for (plain formula, e.g. "C12H22O11"
 *   noprocess: results in the text between the tags not to be processed, for 'difficult' formula's like:
 *	 "CuSO4 . 10 H2O" , where the 10 should not be subscripted.
 *   link: results in the text between the tags to be a link to special:chemicalsources.
 *	 N.B.  : use noprocess with the searchfor parameter, otherwise search results may (!) be garbage/broken links.
 *	 N.B.2 : the text between the tags is interpreted as HTML, not as wikitext!
 *
 * Written by Dirk Beetstra, Oct. 2, 2006.
 */

$wgExtensionFunctions[] = "wfChemFormExtension";

$wgExtensionCredits['Extension'][] = array(
	'name' => 'ChemFunctions.php',
	'description' => 'Tag for chemical formulae',
	'author' => 'Dirk Beetstra',
	'url' => 'http://meta.wikimedia.org/wiki/Chemistry/ChemFunctions.php'

);

function wfChemFormExtension() {
	global $wgParser;
	$wgParser->setHook( "chemform", "RenderChemForm" );
}

function RenderChemForm( $input, $argv, &$parser ) {
	global $wgServer, $wgScript, $wgChemFunctions_Messages, $wgMessageCache;

	require_once( 'ChemFunctions.i18n.php' );

	# add messages
	global $wgMessageCache, $wgChemFunctions_Messages;
	foreach( $wgChemFunctions_Messages as $key => $value ) {
		$wgMessageCache->addMessages( $wgChemFunctions_Messages[$key], $key );
	}

	$searchfor = false;
	if ( isset( $argv["query"] ) )
		$searchfor = $argv["query"];

	if ($searchfor) {
		$searchfor = str_replace(" ", "", $searchfor );
	} else {
		$searchfor = $input;
		$searchfor = preg_replace( "/<.*?>/", "", $searchfor );
		$searchfor = preg_replace( "/[\[\]]/", "", $searchfor );
		$searchfor = str_replace(" ", "", $searchfor );
	}

	$noprocess = false;
	if ( isset( $argv["noprocess"] ) )
		$noprocess = $argv["noprocess"];

	$showthis = $input;
	if (!$noprocess) {
		$showthis = $input;
		$showthis = preg_replace( "/<.*?>/", "", $showthis );								# Remove all tags
		$showthis = preg_replace("/[0-9]+/", "<sub>$0</sub>", $showthis);					# All numbers down
		$showthis = preg_replace("/[\+\-]/", "<sup>$0</sup>", $showthis);					# + and - up
		$showthis = preg_replace("/<\/sub><sup>/", "", $showthis);						   # </sub><sup> should not occur
		$showthis = preg_replace("/<sub>([0-9\+\-]+)<\/sup>/", "<sup>$1</sup>", $showthis);  # and <sub>whatever</sup> to <sup>..</sup>
	}

	global $removeHTMLtags;
	$output = "";

	$showthis = Sanitizer::removeHTMLtags( $showthis);
	$searchfor = Sanitizer::removeHTMLtags( $searchfor);

	$link = false;
	if ( isset( $argv["link"] ) )
		$link =  $argv["link"];

	if ( $link ) {
		$title = Title::makeTitle( NS_SPECIAL, 'Chemicalsources' );
		$output = "<a href = " . $title->getFullUrl() . "?Formula=" . $searchfor .  ">" . $showthis . "</a>";
	} else {
		$output = $showthis;
	}

	return $output;
}

#End of php.
?>

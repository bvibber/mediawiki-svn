<?php

/**
 * A MediaWiki extension that adds the following tags:
 * <chemform>: for the formatting of chemical formulae.
 *
 * The i18n file is required for operation!
 * Installation: copy this file and ChemFunctions.i18n.php into the extensions directory
 *   and add "require_once( "$IP/extensions/ChemFunctions.php" );" to localsettings.php (using the correct path)
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

if (!defined('MEDIAWIKI')) die();

$wgExtensionCredits['Extension'][] = array(
    'name' => 'ChemFunctions',
    'description' => 'Adds the <chemform> tag, for chemical formulae',
    'author' => 'Dirk Beetstra',
    'url' => 'http://meta.wikimedia.org/wiki/Chemistry/ChemFunctions.php'
);

/** Chemform wikipedia extension.
 *
 *   Formats the text between the tags as a chemical formula,
 *    First: all numericals are put in subscript.
 *    Second:  and - are put in superscript
 *    Third: all numericals preceding a + or a - are converted from subscript to superscript.
 *
 * usage: <chemform searchfor="formula to be searched for" noprocess link wikilink>formula</chemform>
 *  (all parameters are optional).
 * Parameters:
 *   query: alternate (e.g. CAS-sorted) formula to search for (plain formula, e.g. "C12H22O11")
 *   noprocess: results in the text between the tags not to be processed, for 'difficult' formula's like:
 *     "CuSO<sub>4</sub> . 10 H<sub>2</sub>O" , where the 10 should not be subscripted.
 *   link: results in the text between the tags to be a link to special:chemicalsources.
 *     N.B.  : use noprocess with the searchfor parameter, otherwise search results may (!) be garbage/broken links.
 *     N.B.2 : the text between the tags is interpreted as HTML, not as wikitext!
 *   wikilink: makes the text between the tags a wikilink.
 *
 *   The query takes the value of query, or link, or wikilink (in that order)
 *
 * Written by Dirk Beetstra, Oct. 2, 2006.
 */

$wgExtensionFunctions[] = "wfChemFormExtension";

function wfChemFormExtension() {
    global $wgParser;
    $wgParser->setHook( "chemform", "RenderChemForm" );
}

function RenderChemForm( $input, $argv ) {
    global $wgChemFunctions_Messages, $wgMessageCache;

    require_once( 'ChemFunctions.i18n.php' );

    # add messages
    global $wgMessageCache, $wgChemFunctions_Messages;
    foreach( array_keys($wgChemFunctions_Messages) as $key ) {
        $wgMessageCache->addMessages( $wgChemFunctions_Messages[$key], $key );
    }

    $link = false;
    $wikilink = false;
    $noprocess = false;
    $searchfor = "";

    if ( isset( $argv["noprocess"] ) )
        $noprocess = $argv["noprocess"];

    if ( isset( $argv["link"] ) )
        $link =  $argv["link"];

    if ( isset($argv["wikilink"] ) )
       $wikilink = $argv["wikilink"];

    if ( isset( $argv["query"] ) )
        $searchfor = $argv["query"];

    if (!$searchfor) {
        $searchfor = $input;
        $searchfor = preg_replace( "/<.*?>/", "", $searchfor );
        $searchfor = preg_replace( "/[\[\]]/", "", $searchfor );
    }
    $searchfor = str_replace(" ", "", $searchfor );

    if (!$searchfor )
        $searchfor = $link;
    if (!$searchfor )
        $searchfor = $wikilink;

    $showthis = $input;
    if (!$noprocess) {
        $showthis = $input;
        $showthis = preg_replace( "/<.*?>/", "", $showthis );                                # Remove all tags
        $showthis = htmlentities( Sanitizer::StripAllTags ( $showthis ) );                   # tagstripping
        $showthis = preg_replace("/[0-9]+/", "<sub>$0</sub>", $showthis);                    # All numbers down
        $showthis = preg_replace("/[\+\-]/", "<sup>$0</sup>", $showthis);                    # + and - up
        $showthis = preg_replace("/<\/sub><sup>/", "", $showthis);                           # </sub><sup> should not occur
        $showthis = preg_replace("/<sub>([0-9\+\-]+)<\/sup>/", "<sup>$1</sup>", $showthis);  # and <sub>whatever</sup> to <sup>..</sup>
    } else {
        $showthis = htmlentities( Sanitizer::StripAllTags ( $showthis ) );
    }

    $searchfor = htmlentities( Sanitizer::StripAllTags ( $searchfor ) );

    if (!$searchfor) {
       $searchfor = "Error, no text given";
    }
    if ( $link ) {
        $title = Title::makeTitleSafe( NS_SPECIAL, 'Chemicalsources' );
        $output = "<a href=\"" . $title->getFullUrl() . "?Formula=" . $searchfor .  "\">" . $showthis . "</a>";
    } elseif ( $wikilink) {
        $title = Title::makeTitleSafe( NS_MAIN, $searchfor );
        if ($title) {
            $revision = Revision::newFromTitle( $title );
            if ($revision ) {
               $output = "<a href=\"" . $title->getFullUrl() . "\">" . $showthis . "</a>";
            } else {
               $output = "<a href=\"" . $title->getFullUrl() . "?action=edit\" class=\"new\">" . $showthis . "</a>";
            }
        } else {
            $output = wfMsg('ChemFunctions_ChemFormInputError');
        }
    } else {
        $output = $showthis;
    }

    return $output;
}

#End of php.
?>

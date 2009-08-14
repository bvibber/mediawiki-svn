<?php

/**
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:Transliterator Documentation
 * @link http://en.wiktionary.org/wiki/User:Conrad.Irwin/Transliterator.php Original
 *
 * @author Conrad Irwin
 * @modifier Purodha Blissenbach
 * @copyright Copyright © 2009 Conrad.Irwin
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0
 *     or later
 * @version 1.0
 *     initial creation.
 * @version 1.0.1
 *     better i18n support, adjustable limits, minor formal adjustment.
 * @version 1.1.0
 *     addition of answer parameter
 * @version 1.2.0
 *     semi-case-sensitive by default, fix bugs with edge-detection and html-entities
 * @version 1.2.1
 *     added cache support
 */

/**
    Extension:Transliterator Copyright (C) 2009 Conrad.Irwin
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

error_reporting(E_ALL | E_WARNING | E_STRICT);
if ( !defined( 'MEDIAWIKI' ) )
{
    die( 'This file is a MediaWiki extension, not a valid entry point.' );
}

// adjustable parameters
$wgTransliteratorRuleCount = 255;	// maximum number of permitted rules per map.
$wgTransliteratorRuleSize  =  10;	// maximum number of characters in left side of a rule.

$wgExtensionCredits['parserhook'][] = array(
    'name' => 'Transliterator',
    'version' => '1.2.0',
    'descriptionmsg' => 'transliterator-desc',
    'author' => 'Conrad Irwin',
    'url' => 'http://www.mediawiki.org/wiki/Extension:Transliterator',
    'path' => __FILE__,
);

if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
    $wgHooks['ParserFirstCallInit'][] = 'efTransliterator_Setup';
} else {
    $wgExtensionFunctions[] = 'efTransliterator_Setup';
}
$wgExtensionMessagesFiles['Transliterator'] = dirname(__FILE__).'/Transliterator.i18n.php';
$wgHooks['LanguageGetMagic'][]       = 'efTransliterator_Magic';
$wgHooks['ArticleDeleteComplete'][]  = 'ExtTransliterator::purgeMap';
$wgHooks['NewRevisionFromEditComplete'][]  = 'ExtTransliterator::purgeMap';
$wgHooks['ArticlePurge'][]  = 'ExtTransliterator::purgeMap';

class ExtTransliterator {

    const DELIMITER = "\x1F"; // A character that will be inserted in places where the ^ and $ should match
    var $mPages = null;  // An Array of "transliterator:$mapname" => The database row for that template.
    var $mMaps = array();// An Array of "$mapname" => The map parsed from that page.

    /**
     * Split a word into letters (not bytes or codepoints) implicitly in NFC due to MediaWiki.
     */
    function letters( $word ) {
        global $utfCombiningClass;
        UtfNormal::loadData();

        $split = preg_split( '/(.)/u', $word, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );

        $i = 1;
        while ( $i < count( $split ) ) {
            if ( isset( $utfCombiningClass[$split[$i]] ) ) {
               $split[$i - 1] .= $split[$i]; 
               unset( $split[$i] );

            } else {
                $i++;

            }
        }

        return $split;
    }

    /**
     * Split a word into the NFD codepoints that make it up.
     */
    function codepoints( $word ) {
        $word = UtfNormal::toNFD( $word );
        return preg_split( '/(.)/u', $word, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
    }

    /**
     * Given a codepoints or letters array returns a list that contains 1 for every 
     * alphabetic character and accent, and 0 otherwise. This allows for edge-of-word
     * detection.
     */
    function alphamap( $letters ) {

        $output = Array();
        $count = count($letters);

        for ($i = 0; $i < $count; $i++) {
            $output[] =  preg_match( '/\pL/u', $letters[$i]) || isset( $utfCombiningClass[$letters[$i]] );
        }

        return $output;
    }

    /**
     * Get all the existing maps in one query, useful given that the default 
     * behaviour of failing silently is designed to allow it to be used by
     * templates that don't know if a map exists, so may try far too often.
     */
    function getExistingMapNames( $prefix ) {

        if ( ! is_null($this->mPages) )
            return $this->mPages;

        $dbr = wfGetDB( DB_SLAVE );
        $res = $dbr->select( 'page',
                    array( '*' ),
                    array(
                        'page_namespace' => NS_MEDIAWIKI,
                        'page_title LIKE \'' . $dbr->escapeLike( $prefix ) .'%\''
                    ),
                    __METHOD__
        );

        $this->mPages = Array();

        while ( $r = $res->fetchObject() ) {
            $this->mPages[$r->page_title] = $r;
        }

        return $this->mPages;
    }
    /**
     * Get a map function, either from the local cache or from the page,
     * TODO: I am uncomfortable with cache integration.
     */
    function getMap( $prefix, $name ) {
        global $wgMemc;

        $mappage = $prefix.$name;

        // Have we used it on thie page already?
        if ( isset( $this->mMaps[$mappage] ) ) 
            return $this->mMaps[$mappage];

        // Have we used it recently?
        $cached = $wgMemc->get( "extTransliterator:$name" );
        if ( $cached ) 
            return $this->mMaps[$mappage] = ($cached == "false" ? false : $cached);

        // Does it exist at all?
        $existing = $this->getExistingMapNames( $prefix );
        if (! isset( $existing[$mappage] ) ) 
            $map = false;

        else
            $map = $this->readMap( wfMsg( $mappage ), $mappage );

        $wgMemc->set( "extTransliterator:$name", ($map == false ? "false" : $map));
        return $this->mMaps[$mappage] = $map;
    }

    /**
     * Parse a map input syntax into a map.
     * 
     * Input syntax is a set of lines.
     *  All " " are ignored.
     *  Lines starting with # are ignored, remaining lines are split by =>
     *  HTML entities are decoded (essential for sanity when trying to add rules for combining codepoints)
     *
     * The map created is a set of "from" strings to "to" strings
     *  With extra "from" => true for all substrings of "from" strings
     *   So that the transliteration algorithm knows when it has found the longest match
     *
     * $map[''] is used as the default fall through for any characters not in the map
     * $map['__decompose__'] indicates that NFD should be used instead of characters
     * $map['__sensitive__'] indicates that the automatic first-letter upper-case fall-through should not be tried
     */
    function readMap( $input, $mappage ) {
	global $wgTransliteratorRuleCount, $wgTransliteratorRuleSize;

        $map = array();
        $decompose = false;

        // Split lines and remove whitespace at beginning and end
        $lines = preg_split( "/(^|\s*\n)(\s*(#[^\n]*)?\n)*\s*/", $input."\n" );

        $count = count( $lines );

        if ( $count > 0 && $lines[0] == "" ) {
            array_shift( $lines );
            $count--;
        }

        if ( $count > 0 && $lines[$count - 1] == "" ) {
            array_pop( $lines );
            $count--;
        }

        // The only content was comments
        if ( $count == 0 )
            return false;

        // The first line can contain flags
        $first_line = $lines[0];
        if ( strpos( $first_line, "=>") === FALSE ) {
            // Or, could just signify that the message was blank
            if ( $first_line == "<$mappage>")
                return false;
            else if ( preg_replace( '/<(decompose|sensitive)>/', '', $first_line ) != '') 
                return wfMsg( 'transliterator-error-syntax', $first_line, $mappage );

            if ( strpos( $first_line, "<decompose>" ) !== FALSE ) {
                $map['__decompose__'] = true;
                $decompose = true;
            } 
            if ( strpos( $first_line, "<sensitive>" ) !== FALSE ) {
                $map['__sensitive__'] = true;
            }
            array_shift( $lines );
            $count--;
        }

        if ( $count > $wgTransliteratorRuleCount )
            return wfMsgExt( 'transliterator-error-rulecount', array('parsemag'), $wgTransliteratorRuleCount, $mappage );

        foreach ( $lines as $line ) {

            $pair = preg_split( '/\s*=>\s*/', $line );

            if ( count( $pair ) != 2 ) 
                return wfMsg( "transliterator-error-syntax", $line, $mappage );

            $from = $pair[0];
            $to = html_entity_decode( $pair[1], ENT_QUOTES, 'UTF-8' );

            // Convert the ^ and $ selectors into the DELIMITER so that it can be used with a negligable chance of conflict
            // Leave single ^ and $'s alone incase someone wants to use them
            // Still permits the creation of the rule "^$=>" that will never match, but hey
            $fromlast = strlen( $from ) - 1;
            if ( $fromlast > 0 ) {
                if ( $from[0] == "^" && $fromlast > 0)
                    $from[0] = ExtTransliterator::DELIMITER;

                if ( $from[$fromlast] == "$")
                    $from[$fromlast] = ExtTransliterator::DELIMITER;
            }

            // Now we've looked at our syntax we can remove html escaping to reveal the true form
            $from = html_entity_decode( $from, ENT_QUOTES, 'UTF-8' );
            if ( $decompose ) { // Undo the NFCing of MediaWiki
                $from = UtfNormal::toNFD( $from );
            }

            // If $map[$from] is set we can skip the filling in of sub-strings as there is a longer rule
            if ( isset( $map[$from] ) ) {

                // Or a rule of the same length, i.e. the same rule.
                if ( is_string( $map[$from] ) && $to != $map[$from] ) 
                    return wfMsg("transliterator-error-ambiguous", $line, $mappage);
                
            } else if ( strlen( $from ) > 1 ){

                // Bail if the left hand side is too long (has performance implications otherwise)
                $fromlen = strlen( $from );
                if ( $fromlen > $wgTransliteratorRuleSize )
                    return wfMsgExt('transliterator-error-rulesize', array('parsemag'), $line, $mappage, $wgTransliteratorRuleSize );

                // Fill in the blanks, so that we know when to stop looking while transliterating
                for ( $i = 1; $i < $fromlen; $i++ ) {
                    $substr = substr( $from, 0, $i );

                    if (! isset( $map[$substr] ) ) 
                        $map[$substr] = true;
                }
            } // else we have the default rule

            $map[$from] = $to;
        }

        return $map;
    }

    /**
     * Transliterate a word by iteratively finding the longest substring from 
     * the start of the untransliterated string that we have a rule for, and
     * transliterating it. 
     */
    function transliterate( $word, $map )
    {
        if ( isset( $map["__decompose__"] ) ) {
            $letters = $this->codepoints( $word );
        } else {
            $letters =  $this->letters( $word );
        }

        $alphamap = $this->alphamap( $letters );

        $sensitive = isset( $map["__sensitive__"] ); // Are we in case-sensitive mode, or not
        $ucfirst = false;                            // We are in case-sensitive mode and the first character of the current match was upper-case originally
        $last_upper = null;                          // We have lower-cased the current letter, but we need to keep track of the original (dotted I for example)
        $withstart = false;                          // Have we inserted a start character into the current $current

        $output = "";               // The output
        $last_match = 0;            // The position of the last character matched, or the first character of the current run
        $last_trans = null;         // The transliteration of the last character matched, or null if the first character of the current run
        $i = 0;                     // The current position in the string
        $count = count($letters);   // The total number of characters in the string
        $current = "";              // The substring that we are currently trying to find the longest match for.

        while ( $last_match < $count ) {

            if ( $i < $count ) {

                // if this is the start of a word, first try the form with the start indicator
                if ( $withstart ) {
                    $withstart = false;
                } else if ( $alphamap[$i] && ($last_trans == null) && ( $i == 0 || !$alphamap[$i - 1] ) ) {
                    $current = ExtTransliterator::DELIMITER;
                    $withstart = true;
                }

                $next = $current.$letters[$i];

                // There may be a match longer than $current
                if ( isset( $map[$next] ) ) {

                    // In fact, $next is a match
                    if ( is_string( $map[$next] ) ) {
                        $last_match = $i;
                        $last_trans = $map[$next];
                    }

                    $i++;
                    $current = $next;
                    continue;
                }
            }

            // We had no match at all, pass through one character
            if ( is_null( $last_trans ) ) {

                // This was a fake character that we inserted
                if ( $withstart ) {
                    $current = "";
                    continue;

                // It was a real character that we were supposed to transliterate
                } else {

                    $last_letter = $letters[$last_match];
                    $last_lower = $sensitive ? $last_letter : mb_strtolower( $last_letter );

                    // If we are not being sensitive, we can try down-casing the previous letter
                    if ( $last_letter != $last_lower ) {
                        $ucfirst = true;
                        $letters[$last_match] = $last_lower;
                        $last_upper = $last_letter;

                    // Might be nice to output a ? if we don't understand
                    } else if ( isset( $map[''] ) ) {

                        if ( $ucfirst ) {
                            $output .= str_replace( '$1', $last_upper , $map[''] );
                            $ucfirst = false;
                        } else {
                            $output .= str_replace( '$1', $last_letter, $map[''] );
                        }
                        $i = ++$last_match;
                        $current = "";

                    // Or the input if it's likely to be correct enough
                    } else {

                        if ( $ucfirst ) {
                            $output .= $last_upper;
                            $ucfirst = false;
                        } else {
                            $output .= $last_letter;
                        }
                        $i = ++$last_match;
                        $current = "";
                    }
                }

            // Output the previous match
            } else {

                // If this match is at the end of a word, see whether we have a more specific rule
                if ( $alphamap[$i-1] && ( $i == $count || !$alphamap[$i] ) ) {
                    $try = $current . ExtTransliterator::DELIMITER;
                    if ( isset( $map[$try] ) && is_string( $map[$try] ) ) {
                        $last_trans = $map[$try];
                    }
                }

                if ( $ucfirst ) {
                    $output .= mb_strtoupper( mb_substr( $last_trans, 0, 1 ) ).mb_substr( $last_trans, 1 );
                    $ucfirst = false;
                } else {
                    $output .= $last_trans;
                }
                $i = ++$last_match;
                $last_trans = null;
                $current = "";

            }
        }
        return $output;
    }

    /**
     * {{#transliterate:<mapname>|<word>[|<format>[|<answer>[|<onerror>]]]}}
     *
     * Direct usage will generally be of the form {{#transilterate:<mapname>|<word>}} while
     * generic templates may find the latter three parameters invaluable for easy use.
     *
     * $mapname is the name of the transliteration map to find.
     * $word    is the string to transliterate (if the map was found)
     * $format  is a string containing $1 to be replaced by the transliteration if the map exists
     * $answer  allows for a user-specified transliteration to override the automatic one
     * $other   is an error messsage to display if $answer is blank and an invalid map is specified
     */
    function render( &$parser, $mapname = '', $word = '', $format = '$1', $answer = '', $other = '' ) {

        if ( trim( $format ) == '') { // Handle the case when people use {{#transliterate:<>|<>||<>}}
            $format = '$1';
        }

        if ( trim( $answer ) != '') { 
            return str_replace('$1', $answer, $format);
        }

        $prefix = wfMsg( 'transliterator-prefix' );
        $mappage = $prefix.$mapname;

        $map = $this->getMap( $prefix, $mapname );

        if ( !$map ) { // False if map was not found
            $output = $other;

        } else if ( is_string( $map ) ) { // An error message
            $output = '<span class="transliterator error"> '.$map.' </span>';

        } else { // A Map
            $trans = UtfNormal::toNFC( $this->transliterate( html_entity_decode( $word, ENT_QUOTES, 'UTF-8' ), $map ) );
            $output = str_replace( '$1', $trans, $format );
        }

        // Populate the dependency table so that we get re-rendered if the map changes.
        if ( isset( $this->mPages[$mappage] ) ) 
            $title = Title::newFromRow( $this->mPages[$mappage] );
        else
            $title = Title::newFromText( $mappage, NS_MEDIAWIKI );

        if ($title)
            $parser->mOutput->addTemplate( $title, $title->getArticleID(), null );

        return $output;
    }

    /**
     * Called on ArticlePurge, ArticleDeleteComplete and NewRevisionFromEditComplete in order to purge cache
     */
    static function purgeMap( &$article, $a=false, $b=false, $c=false, $d=false ) {
        global $wgMemc;
        $title = $article->getTitle();
        if ( $title->getNamespace() == NS_MEDIAWIKI ) {
            $text = $title->getText();
            $prefix = wfMsg( 'transliterator-prefix' );
            if ( strpos( $text, $prefix ) === 0 ) {
                $wgMemc->delete( str_replace( $prefix, '', $text ) );
            }
        }
        return true;
    }
}

function efTransliterator_Setup() {
    global $wgParser;

    $trans = new ExtTransliterator;
    $wgParser->setFunctionHook( 'transliterate', array( $trans, 'render' ) );
    return true;
}
 
function efTransliterator_Magic( &$magicWords, $langCode ) {
    wfLoadExtensionMessages('Transliterator');

    $magicWords['transliterate'] = array( 0, 'transliterate', wfMsg( 'transliterator-invoke' ) );
    return true;
}

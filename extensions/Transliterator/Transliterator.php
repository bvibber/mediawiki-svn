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

if ( !defined( 'MEDIAWIKI' ) )
{
    die( 'This file is a MediaWiki extension, not a valid entry point.' );
}

// adjustable parameters
$wgTransliteratorRuleCount = 255;	// maximum number of permitted rules per map.
$wgTransliteratorRuleSize  =  10;	// maximum number of characters in left side of a rule.

$wgExtensionCredits['parserhook'][] = array(
    'name' => 'Transliterator',
    'version' => '1.1.0',
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

class ExtTransliterator {

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
     * TODO: discuss whether memcache should be used in any of this.
     */
    function getMap( $prefix, $name ) {

        $mappage = $prefix.$name;

        if ( isset( $mMaps[$mappage] ) ) 
            return $mMaps[$mappage];

        $existing = $this->getExistingMapNames( $prefix );

        if (! isset( $existing[$mappage] ) ) 
            $mMaps[$mappage] = false;

        else
            $mMaps[$mappage] = $this->readMap( wfMsg( $mappage ), $mappage );

        return $mMaps[$mappage];
    }

    /**
     * Parse a map input syntax into a map.
     * 
     * Input syntax is a set of lines.
     *  All " " are ignored.
     *  Lines starting with # are ignored.
     *  HTML entities are decoded (essential for sanity when trying to add rules for combining codepoints)
     *  Remaining lines are split by "=>".
     *
     * The map created is a set of "from" strings to "to" strings
     *  With extra "from" => true for all substrings of "from" strings
     *   So that the transliteration algorithm knows when it has found the longest match
     *
     * $map[''] is used as the default fall through for any characters not in the map
     * $map['__decompose__'] indicates that NFD should be used instead of characters
     */
    function readMap( $input, $mappage ) {
	global $wgTransliteratorRuleCount, $wgTransliteratorRuleSize;

        $map = array();
        $decompose = false;

        // Split lines and remove comments and space 
        $lines = split( "\n", html_entity_decode( preg_replace( '/^\s*(#.*)?(\n|$)| */m', '', $input ), ENT_NOQUOTES, "UTF-8" ) );

        // If the last line was a comment then there will be an empty line at the end
        if ( $lines[count( $lines ) - 1] == "" ) {
            array_pop( $lines );
        }

        if ( $lines[0] == "<decompose>" ) {
            $map['__decompose__'] = true;
            array_shift( $lines );
            $decompose = true;
        } else if ( $lines[0] == "<$mappage>" ) {
            return false;
        }

        if ( count( $lines ) > $wgTransliteratorRuleCount )
            return wfMsgExt('transliterator-error-rulecount', array('parsemag'), $wgTransliteratorRuleCount, $mappage );

        foreach ( $lines as $line ) {

            $pair = split( "=>", $line );

            if ( count($pair) != 2 ) 
                return wfMsg("transliterator-error-syntax", $line, $mappage);

            if ($decompose) // Undo the NFCing of MediaWiki
                $from = UtfNormal::toNFD( $pair[0] );
            else // substrings by NFC code-point are a superset of substrings by letters
                $from = $pair[0];

            $to = $pair[1];

            if ( isset( $map[$from] ) ) {

                if ( is_string( $map[$from] ) ) 
                    return wfMsg("transliterator-error-ambiguous", $line, $mappage);
                
            } else if ( strlen( $from ) > 1 ){
                // Fill in the blanks, so that we know when to stop looking while transliterating
                $to_fill = strlen( $from );

                if ( $to_fill > $wgTransliteratorRuleSize )
                    return wfMsgExt('transliterator-error-rulesize', array('parsemag'), $line, $mappage, $wgTransliteratorRuleSize );
                
                for ( $i = 1; $i < $to_fill; $i++ ) {
                    $substr = substr( $from, 0, $i );

                    if (! isset( $map[$substr] ) ) 
                        $map[$substr] = true;
                }
            }

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
        $word = "^" . str_replace( " ", "$ ^", $word ) . "$";
        if ( isset( $map["__decompose__"] ) ) {
            $letters = $this->codepoints( $word );
        } else {
            $letters =  $this->letters( $word );
        }

        $output = "";               // The output
        $last_match = 0;            // The position of the last character matched, or the first character of the current run
        $last_trans = null;         // The transliteration of the last character matched, or null if the first character of the current run
        $i = 0;                     // The current position in the string
        $count = count($letters);   // The total number of characters in the string
        $current = "";              // The substring that we are currently trying to find the longest match for.

        while ( $i < $count ) {

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

            // No more matching, go back to the last match and start from the character after
            } else {

                // We had no match at all, pass through one character
                if ( is_null( $last_trans ) ) {

                    // Might be nice to output a ? if we don't understand
                    if ( isset( $map[''] ) ) 
                        $output .= $map[''];
                    // Or the input if it's likely to be correct enough
                    else
                        $output .= $letters[$last_match];

                    $i = ++$last_match;

                // Output the previous match
                } else {

                    $output .= $last_trans;
                    $i = ++$last_match;
                    $last_trans = null;

                }
                $current = "";
            }
        }
        if (! is_null( $last_trans ))
            $output .= $last_trans;

        // Remove the beginnng and end markers
        return preg_replace('/^\^|\$$|\$(\s+)\^|\$(\s+)|(\s+)\^/',"$1", $output);
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
            $title = Title::newFromText( $mappage, NS_MEDIAWIKI );
            $output = $other;

        } else if ( is_string( $map ) ) { // An error message
            $title = Title::newFromRow( $this->mPages[$mappage] );
            $output = '<span class="transliterator error"> '.$map.' </span>';

        } else { // A Map
            $title = Title::newFromRow( $this->mPages[$mappage] );
            $output = UtfNormal::toNFC( $this->transliterate( $word, $map ) );
            $output = str_replace('$1', $output, $format);

        }
        // Populate the dependency table so that we get re-rendered if the map changes.
        if ($title)
            $parser->mOutput->addTemplate( $title, $title->getArticleID(), null );

        return $output;
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

    $magicWords['transliterate'] = array( 0, 'transliterate', wfMsg('transliterator-invoke') );
    return true;
}

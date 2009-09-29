<?php

/**
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:Transliterator Documentation
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
 * @version 1.2.2
 *     use new magic word i18n system
 * @version 1.3.1
 *     made ^ act more like $ (i.e. ^μπ => doesn't prevent μ => from matching), fix bug with cache refresh
 * @version 1.3.2 
 *     cache getExistingMapNames query - still not sure caching is optimal.
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

if ( !defined( 'MEDIAWIKI' ) ) {
    die( 'This file is a MediaWiki extension, not a valid entry point.' );
}

// adjustable parameters
$wgTransliteratorRuleCount = 255;	// maximum number of permitted rules per map.
$wgTransliteratorRuleSize  =  10;	// maximum number of characters in left side of a rule.

$wgExtensionCredits['parserhook'][] = array(
    'name' => 'Transliterator',
    'version' => '1.2.2',
    'descriptionmsg' => 'transliterator-desc',
    'author' => 'Conrad Irwin',
    'url' => 'http://www.mediawiki.org/wiki/Extension:Transliterator',
    'path' => __FILE__,
);

$wgHooks['ParserFirstCallInit'][] = 'ExtTransliterator::setup';
$wgExtensionMessagesFiles['Transliterator'] = dirname(__FILE__).'/Transliterator.i18n.php';
$wgHooks['ArticleDeleteComplete'][]  = 'ExtTransliterator::purgeArticle';
$wgHooks['NewRevisionFromEditComplete'][]  = 'ExtTransliterator::purgeArticle';
$wgHooks['ArticlePurge'][]  = 'ExtTransliterator::purgeArticle';
$wgHooks['ArticleUndelete'][]  = 'ExtTransliterator::purgeTitle';
$wgHooks['TitleMoveComplete'][] = 'ExtTransliterator::purgeNewtitle';

class ExtTransliterator {

    const FIRST = "\x1F"; // A character that will be appended when ^ should match at the start
    const LAST = "\x1E"; // A character that will be appended when $ should match at the end
    const CACHE_PREFIX = "extTransliterator.2:"; // The prefix to use for cache items (the number should be incremented when the map format changes)
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
        global $wgMemc;

        // Have we used it on this page already?
        if ( ! is_null($this->mPages) )
            return $this->mPages;

        // Have we used it recently?
        $cached = $wgMemc->get( self::CACHE_PREFIX . "__map_names__" );
        if ( $cached )
            return $this->mPages = $cached;

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
            $this->mPages[$r->page_title] = $r->page_id;
        }

        $wgMemc->set( self::CACHE_PREFIX . "__map_names__", $this->mPages );
        return $this->mPages;
    }
    /**
     * Get a map function, either from the local cache or from the page,
     */
    function getMap( $prefix, $mappage ) {
        global $wgMemc;

        // Have we used it on this page already?
        if ( isset( $this->mMaps[$mappage] ) ) {
            return $this->mMaps[$mappage];
        }

        // Does it exist at all?
        $existing = $this->getExistingMapNames( $prefix );
        if ( isset( $existing[$mappage] ) ) {

            // Have we used it recently?
            $map = $wgMemc->get( self::CACHE_PREFIX . $mappage );
            if (! $map ) {

                $map = $this->readMap( wfMsg( $mappage ), $mappage );

                if ( $map )
                    $wgMemc->set( self::CACHE_PREFIX . $mappage, $map);
            }

        } else {
            $map = false;
        }

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
            $to = Sanitizer::decodeCharReferences( $pair[1], ENT_QUOTES, 'UTF-8' );

            // Convert the ^ and $ selectors into special characters for matching
            // Leave single ^ and $'s alone incase someone wants to use them
            // Still permits the creation of the rule "^$=>" that will never match, but hey
            $fromlast = strlen( $from ) - 1;
            if ( $fromlast > 0 ) {
                if ( $from[0] == "^" ) {
                    $from = substr( $from, 1 ) . self::FIRST;
                    $fromlast--;
                }

                if ( $from[$fromlast] == "$")
                    $from[$fromlast] = self::LAST;
            }

            // Now we've looked at our syntax we can remove html escaping to reveal the true form
            $from = Sanitizer::decodeCharReferences( $from, ENT_QUOTES, 'UTF-8' );
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

        $output = "";               // The output
        $last_match = 0;            // The position of the last character matched, or the first character of the current run
        $last_trans = null;         // The transliteration of the last character matched, or null if the first character of the current run
        $i = 0;                     // The current position in the string
        $count = count($letters);   // The total number of characters in the string
        $current = "";              // The substring that we are currently trying to find the longest match for.
        $current_start = 0;         // The position that $current starts at

        while ( $last_match < $count ) {

            if ( $i < $count ) {

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


            // If this match is at the end of a word, see whether we have a more specific rule
            if ( $alphamap[$i-1] && ( $i == $count || !$alphamap[$i] ) ) {
                $try = $current . self::LAST;
                if ( isset( $map[$try] ) ) {
                    if ( is_string( $map[$try] ) ) {
                        $last_trans = $map[$try];
                    }
                    if ( isset( $map[$try . self::FIRST] ) ) {
                        $current = $try;
                    }
                }
            }

            // If this match is at the start of a word, see whether we have a more specific rule
            if ( ( $current_start == 0 || !$alphamap[$current_start-1]) && $alphamap[$current_start] ) {
                $try = $current . self::FIRST;
                if ( isset( $map[$try] ) && is_string( $map[$try] ) ) {
                    $last_trans = $map[$try];
                }
            }

            // We had no match at all, pass through one character
            if ( is_null( $last_trans ) ) {

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
                    $i = $current_start = ++$last_match;
                    $current = "";

                // Or the input if it's likely to be correct enough
                } else {

                    if ( $ucfirst ) {
                        $output .= $last_upper;
                        $ucfirst = false;
                    } else {
                        $output .= $last_letter;
                    }
                    $i = $current_start = ++$last_match;
                    $current = "";
                }

            // Output the previous match
            } else {

                if ( $ucfirst ) {
                    $output .= mb_strtoupper( mb_substr( $last_trans, 0, 1 ) ).mb_substr( $last_trans, 1 );
                    $ucfirst = false;
                } else {
                    $output .= $last_trans;
                }
                $i = $current_start = ++$last_match;
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
        $title = Title::newFromText( $prefix . $mapname, NS_MEDIAWIKI );

        if (! $title ) {
            return $other == '' ? str_replace("$1", "{{#transliterate:$mapname|$word}}", $format) : $other;
        }

        $mappage = $title->getDBkey();

        $map = $this->getMap( $prefix, $mappage );

        if ( !$map ) { // False if map was not found
            $output = $other;

        } else if ( is_string( $map ) ) { // An error message
            $output = '<span class="transliterator error"> '.$map.' </span>';

        } else { // A Map
            $trans = UtfNormal::toNFC( $this->transliterate( Sanitizer::decodeCharReferences( $word ), $map ) );
            $output = str_replace( '$1', $trans, $format );
        }

        // Populate the dependency table so that we get re-rendered if the map changes.
        if ( isset( $this->mPages[$mappage] ) )
            $parser->mOutput->addTemplate( $title, $this->mPages[$mappage], null );

        else
            $parser->mOutput->addTemplate( $title, $title->getArticleID(), null );

        return $output;
    }

    /**
     * Called on ArticlePurge, ArticleDeleteComplete and NewRevisionFromEditComplete in order to purge cache
     */
    static function purgeArticle( &$article, $a=false, $b=false, $c=false, $d=false ) {
        return self::purgeTitle( $article->getTitle() );
    }

    /**
     * Called on TitleMoveComplete
     */
    static function purgeNewTitle ( &$title, &$newtitle, $a=false, $b=false, $c=false ) {
        return self::purgeTitle( $newtitle );
    }

    /**
     * Called on ArticleUndelete (and by other purge hook handlers)
     */
    static function purgeTitle( &$title, $a=false ) {
        global $wgMemc;
        if ( $title->getNamespace() == NS_MEDIAWIKI ) {
            $text = $title->getText();
            $prefix = wfMsg( 'transliterator-prefix' );
            if ( strpos( $text, $prefix ) === 0 ) {
                $wgMemc->delete( self::CACHE_PREFIX . $title->getDBkey() );
                $wgMemc->delete( self::CACHE_PREFIX . "__map_names__" );
            }
        }
        return true;

    }

    /**
     * Called on first use to create singleton
     */
    static function setup( &$parser ) {
        $trans = new ExtTransliterator;
        $parser->setFunctionHook( 'transliterate', array( $trans, 'render' ) );
        return true;
    }
}


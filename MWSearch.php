<?php
/*
 * Copyright 2004, 2005 Kate Turner, Brion Vibber.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * $Id$
 */

# To use this, add something like the following to LocalSettings:
# 
#  $wgSearchType = 'LuceneSearch';
#  $wgLuceneHost = '192.168.0.1';
#  $wgLucenePort = 8123;
#
#  require_once("extensions/MWSearch/MWSearch.php");
#
# To load-balance with from multiple servers:
#
#  $wgLuceneHost = array( "192.168.0.1", "192.168.0.2" );
#
# The MWDaemon search daemon needs to be running on the specified host(s)
# - it's in the 'lucene-search' and 'mwsearch' modules in CVS.
##########

$wgLuceneDisableSuggestions = false;
$wgLuceneDisableTitleMatches = false;

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (defined('MEDIAWIKI')) {
$wgExtensionFunctions[] = "wfLuceneSearch";

function wfLuceneSearch() {

require_once( 'SearchEngine.php' );

class LuceneSearch extends SearchEngine {
	/**
	 * Perform a full text search query and return a result set.
	 *
	 * @param string $term - Raw search term
	 * @return LuceneSearchSet
	 * @access public
	 */
	function searchText( $term ) {
		return LuceneSearchSet::newFromQuery( 'search',
				$term, $this->namespaces, $this->limit, $this->offset );
	}

	/**
	 * Perform a title-only search query and return a result set.
	 *
	 * @param string $term - Raw search term
	 * @return LuceneSearchSet
	 * @access public
	 */
	function searchTitle( $term ) {
		return null;
		
		// this stuff's a little broken atm
		global $wgLuceneDisableTitleMatches;
		if( $wgLuceneDisableTitleMatches ) {
			return null;
		} else {
			return LuceneSearchSet::newFromQuery( 'titlematch',
				$term, $this->namespaces, $this->limit, $this->offset );
		}
	}
}

class LuceneResult {
	/**
	 * Read an input line from a socket and return a score & title pair.
	 * Will return FALSE if no more data or I/O error.
	 *
	 * @param resource $sock
	 * @return array (float, Title)
	 * @access private
	 */
	function LuceneResult( $line ) {
		list( $score, $namespace, $title ) = split( ' ', $line );
		
		$score     = FloatVal( $score );
		$namespace = IntVal( $namespace );
		$title     = urldecode( $title );
		
		global $wgUseLatin1;
		if( $wgUseLatin1 ) {
			global $wgContLang, $wgInputEncoding;
			$title = $wgContLang->iconv( 'utf-8', $wgInputEncoding, $title );
		}
		
		$this->mTitle = Title::makeTitle( $namespace, $title );
		$this->mScore = $score;
	}
	
	function getTitle() {
		return $this->mTitle;
	}
	
	function getScore() {
		return $this->mScore;
	}
}

class LuceneSearchSet extends SearchResultSet {
	/**
	 * Contact the MWDaemon search server and return a wrapper
	 * object with the set of results. Results may be cached.
	 *
	 * @param string $method The protocol verb to use
	 * @param string $query
	 * @param int $limit
	 * @return array
	 * @access public
	 * @static
	 */
	function newFromQuery( $method, $query, $namespaces = array(), $limit = 10, $offset = 0 ) {
		$fname = 'LuceneSearchSet::newFromQuery';
		wfProfileIn( $fname );
		
		global $wgLuceneHost, $wgLucenePort, $wgDBname, $wgMemc;
		
		if( is_array( $wgLuceneHost ) ) {
			$pick = mt_rand( 0, count( $wgLuceneHost ) - 1 );
			$host = $wgLuceneHost[$pick];
		} else {
			$host = $wgLuceneHost;
		}
		
		global $wgUseLatin1, $wgContLang, $wgInputEncoding;
		$enctext = rawurlencode( trim( $wgUseLatin1
			? $wgContLang->iconv( $wgInputEncoding, 'utf-8', $query )
			: $query ) );
		$searchUrl = "http://$host:$wgLucenePort/$method/$wgDBname/$enctext?" .
			wfArrayToCGI( array(
				'namespaces' => implode( ',', $namespaces ),
				'offset'     => $offset,
				'limit'      => $limit,
			) );
		
		
		// Cache results for fifteen minutes; they'll be read again
		// on reloads and paging.
		$key = "$wgDBname:lucene:" . md5( $searchUrl );
		$expiry = 60 * 15;
		$resultSet = $wgMemc->get( $key );
		if( is_object( $resultSet ) ) {
			wfDebug( "$fname: got cached lucene results for key $key\n" );
			wfProfileOut( $fname );
			return $resultSet;
		}

		wfDebug( "Fetching search data from $searchUrl\n" );
		$inputLines = @file( $searchUrl );
		if( $inputLines === false ) {
			// Network error or server error
			wfProfileOut( $fname );
			return false;
		} else {
			$resultLines = array_map( 'trim', $inputLines );
		}

		$suggestion = null;
		$totalHits = null;
		
		if( $method == 'search' ) {
			# This method outputs a summary line first.
			$totalHits = array_shift( $resultLines );
			if( $totalHits === false ) {
				# I/O error? this shouldn't happen
				wfDebug( "Couldn't read summary line...\n" );
			} else {
				$totalHits = IntVal( $totalHits );
				wfDebug( "total [$totalHits] hits\n" );
				if( $totalHits == 0 ) {
					# No results, but we got a suggestion...
					$suggestion = urldecode( array_shift( $resultLines ) );
					wfDebug( "no results; suggest: [$suggestion]\n" );
				}
			}
		}
		
		$resultSet = new LuceneSearchSet( $query, $resultLines, $totalHits, $suggestion );
		
		wfDebug( "$fname: caching lucene results for key $key\n" );
		$wgMemc->add( $key, $resultSet, $expiry );
		
		wfProfileOut( $fname );
		return $resultSet;
	}
	
	/**
	 * Private constructor. Use LuceneSearchSet::newFromQuery().
	 *
	 * @param string $query
	 * @param array $lines
	 * @param int $totalHits
	 * @param string $suggestion
	 * @access private
	 */
	function LuceneSearchSet( $query, $lines, $totalHits = null, $suggestion = null ) {
		$this->mQuery      = $query;
		$this->mTotalHits  = $totalHits;
		$this->mSuggestion = $suggestion;
		$this->mResults    = $lines;
	}
	
	function numRows() {
		return count( $this->mResults );
	}
	
	function termMatches() {
		$resq = trim( preg_replace( "/[ |\\[\\]()\"{}+]+/", " ", $this->mQuery ) );
		$terms = array_map( array( &$this, 'regexQuote' ),
			explode( ' ', $resq ) );
		return $terms;
	}
	
	/**
	 * Stupid hack around PHP's limited lambda support
	 * @access private
	 */
	function regexQuote( $term ) {
		return preg_quote( $term, '/' );
	}
	
	function hasResults() {
		return count( $this->mResults ) > 0;
	}
	
	/**
	 * Some search modes return a total hit count for the query
	 * in the entire article database. This may include pages
	 * in namespaces that would not be matched on the given
	 * settings.
	 *
	 * @return int
	 * @access public
	 */
	function getTotalHits() {
		return $this->mTotalHits;
	}
	
	/**
	 * Some search modes return a suggested alternate term if there are
	 * no exact hits. Returns true if there is one on this set.
	 *
	 * @return bool
	 * @access public
	 */
	function hasSuggestion() {
		return is_string( $this->mSuggestion ) && $this->mSuggestion != '';
	}
	
	/**
	 * Some search modes return a suggested alternate term if there are
	 * no exact hits. Check hasSuggestion() first.
	 *
	 * @return string
	 * @access public
	 */
	function getSuggestion() {
		return $this->mSuggestion;
	}
	
	/**
	 * Fetches next search result, or false.
	 * @return LuceneResult
	 * @access public
	 * @abstract
	 */
	function next() {
		$bits = each( $this->mResults );
		if( $bits === false ) {
			return false;
		} else {
			return new LuceneResult( $bits[1] );
		}
	}
}

} # End of extension function
} # End of invocation guard
?>

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
 * $Id: LuceneSearch.php 14800 2006-06-17 16:53:39Z robchurch $
 */

if (!defined('MEDIAWIKI')) {
	die( "This file is part of MediaWiki, it is not a valid entry point\n" );
}

global $IP;
require_once($IP.'/includes/SearchEngine.php');

# Add messages
global $wgMessageCache, $wgLuceneSearchMessages;
foreach( $wgLuceneSearchMessages as $lang => $messages ) {
	$wgMessageCache->addMessages( $messages, $lang );
}

class LuceneSearch extends SpecialPage
{
	var $namespaces;

	function LuceneSearch() {
		SpecialPage::SpecialPage('Search');
	}

	function makelink($term, $offset, $limit, $case='ignore') {
		global $wgRequest, $wgScript;
		if( $case == 'exact')
			$fulltext = htmlspecialchars(wfMsg('searchexactcase'));
		else
			$fulltext = htmlspecialchars(wfMsg('powersearch'));
		$link = $wgScript.'?title=Special:Search&amp;search='.
			urlencode($term).'&amp;fulltext='.$fulltext;
		foreach(SearchEngine::searchableNamespaces() as $ns => $name)
			if ($wgRequest->getCheck('ns' . $ns))
				$link .= '&amp;ns'.$ns.'=1';
		$link .= '&amp;offset='.$offset.'&amp;limit='.$limit;
		return $link;
	}

	function setHeaders() {
		global $wgRequest;
		if( $wgRequest->getVal( 'gen' ) == 'titlematch' ) {
			# NOP; avoid initializing the message cache
		} else {
			return parent::setHeaders();
		}
	}

	/**
	 * Callback for formatting of near-match title list.
	 *
	 * @param LuceneSearchSet $result
	 * @return string
	 * @access private
	 */
	function formatNearMatch( $result ) {
		$title = $result->getTitle();
		return wfMsg( 'searchnearmatch',
			$this->mSkin->makeKnownLinkObj( $title ) );
	}

	function execute($par) {
		global $wgRequest, $wgOut, $wgTitle, $wgContLang, $wgUser,
			$wgScriptPath,
			$wgLuceneDisableTitleMatches, $wgLuceneDisableSuggestions,
			$wgUser, $wgSitename, $wgLuceneSearchJS;
		global $wgGoToEdit;

		$fname = 'LuceneSearch::execute';
		wfProfileIn( $fname );
		$this->setHeaders();
		$wgOut->addHTML('<!-- titlens = '. $wgTitle->getNamespace() . '-->');
				
		// check custom namespaces in request
		foreach(SearchEngine::searchableNamespaces() as $ns => $name)
			if ($wgRequest->getCheck('ns' . $ns))
				$this->namespaces[] = $ns;
		
		// if still undefined fallback to default
		if (count($this->namespaces) == 0) {
			// user
			foreach(SearchEngine::searchableNamespaces() as $ns => $name) {
				if($wgUser->getOption('searchNs' . $ns)) {
					$this->namespaces[] = $ns;
				}
			}
			// system-wide default
			if (count($this->namespaces) == 0) {
				global $wgNamespacesToBeSearchedDefault;
				foreach( $wgNamespacesToBeSearchedDefault as $ns => $searchit ) {
					if( $searchit ) {
						$this->namespaces[] = $ns;
					}
				}
			}
		}

		// figure out the query
		$bits = split('/', $wgRequest->getVal('title'), 2);
		if(!empty($bits[1]))
			$q = str_replace('_', ' ', $bits[1]);
		else
			$q = $wgRequest->getText('search');
		list( $limit, $offset ) = $wgRequest->getLimitOffset( LS_PER_PAGE, 'searchlimit' );

		$this->mSkin =& $wgUser->getSkin();
		
		// set a descriptive HTML title
		$wgOut->setHTMLTitle(wfMsg('searchtitle',$q,$wgSitename));

		/* TODO: find a better place ("For more information about searching ... ") */ 
		$wgOut->addWikiText(wfMsg('searchresulttext')); 
		$wgOut->addHTML($this->showShortDialog($q));		

		if ($q === false || strlen($q) == 0) {
			// No search active. Put input focus in the search box.
			$wgOut->addHTML( $this->makeFocusJS() );
		} else {
			if (!($wgRequest->getText('fulltext'))) {
				$t = SearchEngine::getNearMatch($q);
				if(!is_null($t)) {
					$wgOut->redirect($t->getFullURL());
					wfProfileOut( $fname );
					return;
				}
			}			
			
			$case = 'ignore';
			# Replace localized namespace prefixes (from lucene-search 2.0)
			global $wgLuceneSearchVersion;
			if($wgLuceneSearchVersion >= 2){
				$searchq = $this->replacePrefixes($q);
				if($wgRequest->getText('fulltext') == wfMsg('searchexactcase'))
					$case = 'exact';
			} else
				$searchq = $q;

			global $wgDisableTextSearch;
			if( !$wgDisableTextSearch ) {
				$results = LuceneSearchSet::newFromQuery( 'search', $searchq, $this->namespaces, $limit, $offset, $case );
			}

			if( $wgDisableTextSearch || $results === false ) {
				if ( $wgDisableTextSearch ) {
					$wgOut->addHTML(wfMsg('searchdisabled'));
				} else {
					$wgOut->addWikiText(wfMsg('lucenefallback'));
				}
				$wgOut->addHTML(wfMsg('googlesearch',
					htmlspecialchars($q),
					'utf-8',
                                        htmlspecialchars( wfMsg( 'search' ) ) ) );
				wfProfileOut( $fname );
				return;
			}
			
			// Display did you mean... suggestions
			if( $results->hasSuggestion() ) {
				$suggestion = $results->getSuggestion();
				$o = ' ' . wfMsg('searchdidyoumean',
						$this->makeLink( $suggestion, $offset, $limit, $case ),
						htmlspecialchars( $suggestion ) );					
				$wgOut->addHTML( '<div id="lsearchDidYouMean">'.$o.'</div>' );
			}
			
			# Generate the edit URL for "Go" action
			$t = Title::newFromText($q);
			
			if(!$wgRequest->getText('go') || is_null($t)) {
				$editurl = ''; # hrm...
			} else {
				wfRunHooks( 'SpecialSearchNogomatch', array( &$t ) );
				# If the feature is enabled, go straight to the edit page
				if ($wgGoToEdit) {
					$wgOut->redirect($t->getFullURL('action=edit'));
					return;
				}				
				//TODO: find a better place for this (You can create ... ) 
				if( $t->quickUserCan( 'create' ) && $t->quickUserCan( 'edit' ) ) {
					$wgOut->addWikiText( wfMsg( 'noexactmatch', $t->getPrefixedText() ) );
				} else {
					$wgOut->addWikiText( wfMsg( 'noexactmatch-nocreate', $t->getPrefixedText() ) );
				}
			}
						
			// RESULTS			 
			$wgOut->addHTML('<hr />');
			
			// display results from sister projects in floating div
			if( $results->hasInterwiki() ){
				$parts = explode("\n",wfMsg('searchsisterdef'));
				$sisterDef = array(); // interwiki -> message
				foreach($parts as $p){
					$s = explode('|',$p,2);
					if(count($s) != 2)
						continue; // syntax error
					$def = trim($s[1]);
					$def = str_replace("$1",$q,$def); // $1 - query 
					$def = str_replace("$2",urlencode($q),$def); // $2 - urlencoded query 
					$def = $wgOut->parse($def); 
					$sisterDef[$s[0]] = $def; 
					
				}
				$sisterTable = "<div id=\"lsearchSister\">\n";
				$sisterTable .= '<span id="lsearchSisterCaption">'.wfMsg('searchsister',$wgSitename).'</span>';
				foreach($results->getInterwikiOrder() as $iwPrefix){
					$sd = '';
					if(array_key_exists($iwPrefix,$sisterDef)) 
						$sd = $sisterDef[$iwPrefix];
					$sisterTable .= '<div class="lsearchSisterProject">'.$sd.'</div><ul id="lsearchSisterResults">';
					$sisterTable .= implode( "\n", $results->iterateInterwiki(
						$iwPrefix, array( &$this, 'showInterwikiHit') ) );
					$sisterTable .= '</ul>';					
				}
				$sisterTable .= '</div>';
				$wgOut->addHtml($sisterTable);				
			}
			// display main results
			if( !$results->hasResults() ) {
				# Pass search terms back in a few different formats
				# $1: Plain search terms
				# $2: Search terms with s/ /_/
				# $3: URL-encoded search terms
				$tmsg = array( htmlspecialchars( $q ), htmlspecialchars( str_replace( ' ', '_', $q ) ), wfUrlEncode( $q ) );
				$wgOut->addHtml( wfMsgWikiHtml( 'searchnoresults', $tmsg[0], $tmsg[1], $tmsg[2] ) );
			} else {
				#$showresults = min($limit, count($results)-$numresults);
				$i = $offset;
				$resq = trim(preg_replace("/[ |\\[\\]()\"{}+]+/", " ", $q));
				$contextWords = implode("|",
					array_map( array( &$this, 'regexQuote' ),
						$wgContLang->convertForSearchResult(explode(" ", $resq))));

				$top = wfMsg('searchnumber', $offset + 1,
					min($results->getTotalHits(), $offset+$limit), $results->getTotalHits(), $this->mSkin->makeLink($q,$q));
				//$qu = Title::newFromText($q)->getEditURL();
				//$top .= " for <a href=\"{$qu}\">{$q}</a>";
				//$top = "<div align=right>".$top."&nbsp;&nbsp;</div>";
				$out = "<ul>";
				$numchunks = ceil($results->getTotalHits() / $limit);
				$whichchunk = $offset / $limit;
				$prevnext = "";
				if ($whichchunk > 0)
					$prevnext .= '<a href="'.
						$this->makelink($q, $offset-$limit, $limit, $case).'">'.
						wfMsg('searchprev').'</a> ';
				$first = max($whichchunk - 11, 0);
				$last = min($numchunks, $whichchunk + 11);
				//$wgOut->addWikiText("whichchunk=$whichchunk numchunks=$numchunks first=$first last=$last num=".count($chunks)." limit=$limit offset=$offset results=".count($results)."\n\n");
				if ($last - $first > 1) {
					for($i = $first; $i < $last; $i++) {
						if ($i === $whichchunk)
							$prevnext .= '<strong>'.($i+1).'</strong> ';
						else
							$prevnext .= '<a href="'.
								$this->makelink($q, $limit*$i,
								$limit, $case).'">'.($i+1).'</a> ';
					}
				}
				if ($whichchunk < $last-1)
					$prevnext .= '<a href="'.
						$this->makelink($q, $offset + $limit, $limit, $case).'">'.
						wfMsg('searchnext').'</a> ';
				$prevnext = '<div style="text-align: center;">'.$prevnext.'</div>';
				$top .= $prevnext;
				$out .= implode( "\n", $results->iterateResults(
					array( &$this, 'showHit'), $contextWords ) );
				$out .= '</ul>';
			}
			$wgOut->addHTML('<div>');			
			if( isset( $top ) ) $wgOut->addHTML( $top );
			if( isset( $out ) ) $wgOut->addHTML( $out );
			if( isset( $prevnext ) ) $wgOut->addHTML('<hr />' . $prevnext);
			$wgOut->addHTML('</div>');
			$wgOut->addHTML($this->showFullDialog($q));
		}
		$wgOut->setRobotpolicy('noindex,nofollow');
                $wgOut->setArticleRelated(false);
		wfProfileOut( $fname );
	}

	/**
	 * Replaces localized namespace prefixes with the standard ones
	 * defined in lucene-search daemon global configuration
	 *
	 * Small parser that extracts prefixes (e.g. help from 'help:editing'), 
	 * but ignores those that are within quotes (i.e. in a phrase). It
	 * replaces those with prefixes defined in messages searchall (all keyword) 
	 * and searchincategory (incategory keyword), and in wgLuceneSearchNSPrefixes.
	 * 
	 *
	 * @param string query 
	 * @return string rewritten query
	 * @access private
	 */
	function replacePrefixes( $query ) {
		global $wgContLang;
		$fname = 'LuceneSearch::replacePrefixes';
		wfProfileIn($fname);
		$qlen = strlen($query);
		$start = 0; $len = 0; // token start pos and length
		$rewritten = ''; // rewritten query
		$rindex = 0; // point to last rewritten character
		$inquotes = false;
		
		// quick check, most of the time we don't need any rewriting
		if(strpos($query,':')===false){ 
			wfProfileOut($fname);
			return $query;
		}

		$allkeywords = explode("\n",wfMsg('searchall'));
		$incatkeywords = explode("\n",wfMsg('searchincategory'));
		$aliaspairs = explode("\n",wfMsg('searchaliases'));
		$aliases = array(); // alias => indexes
		foreach($aliaspairs as $ap){
			$parts = explode('|',$ap);
			if(count($parts) == 2){
				$namespaces = explode(',',$parts[1]);
				$rewrite = array();
				foreach($namespaces as $ns){
					$index = $wgContLang->getNsIndex($ns);
					if($index !== false){
						$rewrite[] = $index;
					}
				}
				$aliases[$parts[0]] = $rewrite;
			}
		}
		for($i = 0 ; $i < $qlen ; $i++){
			$c = $query[$i];

			// ignore chars in quotes
			if($inquotes && $c!='"'); 
			// check if $c is valid prefix character
			else if(($c >= 'a' && $c <= 'z') ||
				 ($c >= 'A' && $c <= 'Z') ||
				 $c == '_' || $c == '-' || $c ==','){
				if($len == 0){
					$start = $i; // begin of token
					$len = 1;
				} else
					$len++;	
			// check for utf-8 chars
			} else if(($c >= "\xc0" && $c <= "\xff")){ 
				$utf8len = 1;
				for($j = $i+1; $j < $qlen; $j++){ // fetch extra utf-8 bytes
					if($query[$j] >= "\x80" && $query[$j] <= "\xbf")
						$utf8len++;
					else
						break;
				}
				if($len == 0){
					$start = $i;
					$len = $utf8len;
				} else
					$len += $utf8len;
				$i = $j - 1;  // we consumed the chars
			// check for end of prefix (i.e. semicolon)
			} else if($c == ':' && $len !=0){
				$rewrite = array(); // here we collect namespaces 
				$prefixes = explode(',',substr($query,$start,$len));
				// iterate thru comma-separated list of prefixes
				foreach($prefixes as $prefix){
					$index = $wgContLang->getNsIndex($prefix);
					
					// check for special prefixes all/incategory
					if(in_array($prefix,$allkeywords)){
						$rewrite = 'all';
						break;
					} else if(in_array($prefix,$incatkeywords)){
						$rewrite = 'incategory';
						break;
					// check for localized names of namespacessearch query
					} else if($index !== false)
						$rewrite[] = $index;
					// check aliases
					else if(isset($aliases[$prefix]))
						$rewrite = array_merge($rewrite,$aliases[$prefix]);
					
				}
				$translated = null;
				if($rewrite === 'all' || $rewrite === 'incategory')
					$translated = $rewrite;
				else if(count($rewrite) != 0)
					$translated = '['.implode(',',array_unique($rewrite)).']';

				if(isset($translated)){
					// append text before the prefix, and then the prefix
					$rewritten .= substr($query,$rindex,$start-$rindex);
					$rewritten .= $translated . ':';
					$rindex = $i+1;
				}
				
				$len = 0;
			} else{ // end of token
				if($c == '"') // get in/out of quotes
					$inquotes = !$inquotes;
				
				$len = 0;
			}
				
		}
		// add rest of the original query that doesn't need rewritting
		$rewritten .= substr($query,$rindex,$qlen-$rindex);
		wfProfileOut($fname);
		return $rewritten;
	}

	/**
	 * Stupid hack around PHP's limited lambda support
	 * @access private
	 */
	function regexQuote( $term ) {
		return '\b' . preg_quote( $term, '/' ) . '\b';
	}

	/**
	 * Show single interwiki result
	 *
	 * @param LuceneResult $result
	 * @return string
	 */
	function showInterwikiHit($result){
		$fname = 'LuceneSearch::showInterwikiHit';
		wfProfileIn($fname);
		global $wgUser, $wgLang, $wgContLang, $wgTitle;

		$t = $result->getTitle();
		if(is_null($t)) {
			wfProfileOut($fname);
			return "<!-- Broken link in search result -->\n";
		}
		$titleText = $result->getTitleText();
		if($titleText == '')
			$titleText = $t->getText(); 
		$link = $this->mSkin->makeKnownLinkObj($t, $titleText);
		$redirectTitle = $result->getRedirectTitle();
		$redirect = "";
		if(!is_null($redirectTitle)){
			$rlink = $this->mSkin->makeKnownLinkObj($redirectTitle, $result->getRedirectText());
			$redirect = ' <span class="lsearchSisterRedirect">'.wfMsg('searchredirect',$rlink).'</span>';
		}
				
		wfProfileOut($fname);
		return '<li class="lsearchSisterResult">'.$link.$redirect."</li>\n";					
	}
	
	/**
	 * Show one result, context lines from lsearch daemon
	 * Supported from lucene-search v2.1
	 *
	 * @param LuceneResult $result
	 * @param array $terms
	 * @return unknown
	 */
	function showHit($result, $terms){
		global $wgLuceneSearchVersion;
		if($wgLuceneSearchVersion < 2.1) // backward compatibility
			return showHitPre2($result,$terms);
			
		$fname = 'LuceneSearch::showHit';
		wfProfileIn($fname);
		global $wgUser, $wgLang, $wgContLang, $wgTitle;

		$t = $result->getTitle();
		if(is_null($t)) {
			wfProfileOut($fname);
			return "<!-- Broken link in search result -->\n";
		}
		
		$link = $this->mSkin->makeKnownLinkObj($t, $result->getTitleText());
		$text = $result->getText();
		$redirectTitle = $result->getRedirectTitle();
		$redirect = "";
		if(!is_null($redirectTitle)){
			$rlink = $this->mSkin->makeKnownLinkObj($redirectTitle, $result->getRedirectText());
			$redirect = ' <span class="lsearchRedirect">'.wfMsg('searchredirect',$rlink).'</span>';
		}
		$sectionTitle = $result->getSectionTitle();
		$section = '';
		if(!is_null($sectionTitle)){
			$slink = $this->mSkin->makeKnownLinkObj($sectionTitle, $result->getSectionText());
			$section = ' <span class="lsearchSection">'.wfMsg('searchsection',$slink).'</span>';
		}
		
		wfProfileOut($fname);
		return '<li class="lsearchList"><div class="lsearchTitle">'
			.$link.$redirect.$section.'</div><div class="lsearchResult">'.$text."</div></li>\n";					
	}
	
	
	/**
	 * Show one result, extract context lines from DB
	 * Compatibility version for lucene-search prior to 2.1
	 *
	 * @param LuceneResult $result
	 * @param array $terms
	 * @return unknown
	 */
	function showHitPre2($result, $terms) {
		$fname = 'LuceneSearch::showHitPre2';
		wfProfileIn($fname);
		global $wgUser, $wgLang, $wgContLang, $wgTitle, $wgOut, $wgDisableSearchContext;

		$t = $result->getTitle();
		if(is_null($t)) {
			wfProfileOut($fname);
			return "<!-- Broken link in search result -->\n";
		}
		
		//$contextlines = $wgUser->getOption('contextlines');
		$contextlines = 2;
		$contextchars = $wgUser->getOption('contextchars');
		if ('' == $contextchars)
			$contextchars = 50;
		if ( intval($contextchars) > 5000 )
			$contextchars = 5000;

		$link = $this->mSkin->makeKnownLinkObj($t, '');

		if ( !$wgDisableSearchContext ) {
			$rev = Revision::newFromTitle($t);
			if ($rev === null) {
				wfProfileOut( $fname );
				return "<!--Broken link in search results: ".$t->getDBKey()."-->\n";
			}

			$text = $rev->getText();
			$size = wfMsgHtml( 'lucene-resultsize',
				$this->mSkin->formatSize( strlen( $text ) ),
				str_word_count( $text ) );
			$text = $this->removeWiki($text);
			$date = $wgContLang->timeanddate($rev->getTimestamp());
		} else {
			$text = '';
			$date = '';
		}

		$lines = explode("\n", $text);

		$max = intval($contextchars) + 1;
		$pat1 = "/(.*)($terms)(.{0,$max})/i";

		$lineno = 0;

		$extract = '';
		wfProfileIn($fname.'-extract');
		foreach ($lines as $line) {
			if (0 == $contextlines)
				break;
			++$lineno;
			if (!preg_match($pat1, $line, $m))
				continue;
			--$contextlines;
			$pre = $wgContLang->truncate($m[1], -$contextchars, '...');

			if (count($m) < 3)
				$post = '';
			else
				$post = $wgContLang->truncate($m[3], $contextchars, '...');

			$found = $m[2];

			$line = htmlspecialchars($pre . $found . $post);
			$pat2 = '/([^ ]*(' . $terms . ")[^ ]*)/i";
			$line = preg_replace($pat2,
			  "<span class='searchmatch'>\\1</span>", $line);

			$extract .= "<br /><small>{$line}</small>\n";
		}
		wfProfileOut($fname.'-extract');
		wfProfileOut($fname);
		if (!$wgDisableSearchContext) { $date = $wgContLang->timeanddate($rev->getTimestamp()); }
		else { $date = ''; }
		$percent = sprintf( '%2.1f', $result->getScore() * 100 );
		$score = wfMsg( 'lucene-searchscore', $wgLang->formatNum( $percent ) );
		//$url = $t->getFullURL();
		return '<li style="padding-bottom: 1em;">'.$link.$extract.'<br />'
			.'<span style="color: green; font-size: small;">'
			."$score - $size - $date</span></li>\n";
	}	
	
	/* Basic wikitext removal */
	function removeWiki($text) {
		//$text = preg_replace("/'{2,5}/", "", $text);
		$text = preg_replace("/\[[a-z]+:\/\/[^ ]+ ([^]]+)\]/", "\\2", $text);
		//$text = preg_replace("/\[\[([^]|]+)\]\]/", "\\1", $text);
		//$text = preg_replace("/\[\[([^]]+\|)?([^|]]+)\]\]/", "\\2", $text);
		$text = preg_replace("/\\{\\|(.*?)\\|\\}/", "", $text);
		$text = preg_replace("/\\[\\[[A-Za-z_-]+:([^|]+?)\\]\\]/", "", $text);
		$text = preg_replace("/\\[\\[([^|]+?)\\]\\]/", "\\1", $text);
		$text = preg_replace("/\\[\\[([^|]+\\|)(.*?)\\]\\]/", "\\2", $text);
		$text = preg_replace("/<\/?[^>]+>/", "", $text);
		$text = preg_replace("/'''''/", "", $text);
		$text = preg_replace("/('''|<\/?[iIuUbB]>)/", "", $text);
		$text = preg_replace("/''/", "", $text);

		return $text;
	}

	function showShortDialog($term) {
		global $wgScript, $wgLuceneDisableSuggestions;
		global $wgLuceneSearchExactCase;
		
		$action = "$wgScript";
		$searchButton = '<input type="submit" name="fulltext" value="' .
			htmlspecialchars(wfMsg('powersearch')) . "\" />\n";
		$exactSearch = "";
		
		// if needed another exact-search button
		if($wgLuceneSearchExactCase){ 
			$exactSearch = '<input type="submit" name="fulltext" value="' .
			htmlspecialchars(wfMsg('searchexactcase')) . "\" />\n";
		}
		
		$autocomplete = '';
		if(!$wgLuceneDisableSuggestions)
			$autocomplete = 'autocomplete="off"';
		
		$searchField = '<div id="lsearchShortForm"><input type="text" id="lsearchbox" '
			. ' value="'. htmlspecialchars($term) . '"'
			. $autocomplete 
			. " name=\"search\" />\n"
			. $searchButton
			. $exactSearch .'</div>';

		$ret = $searchField;
		return
		  '<form id="search" method="get" '
          . "action=\"$action\"><input type='hidden' name='title' value='Special:Search' />\n<div>{$ret}</div></form>\n";
	}

	function showFullDialog($term) {
		global $wgContLang, $wgLuceneSearchExactCase;
		$namespaces = '';
		foreach(SearchEngine::searchableNamespaces() as $ns => $name) {
			$checked = in_array($ns, $this->namespaces)
			           ? ' checked="checked"' : '';
			$name = str_replace('_', ' ', $name);
			if('' == $name) {
				$name = wfMsg('blanknamespace');
			}
			$namespaces .= " <label><input type='checkbox' value=\"1\" name=\"" .
			               "ns{$ns}\"{$checked} />{$name}</label>\n";
		}

		$searchField = "<input type='text' name=\"search\" value=\"" .
					   htmlspecialchars($term) ."\" size=\"16\" />\n";

		$searchButton = '<input type="submit" name="fulltext" value="' .
						htmlspecialchars(wfMsg('powersearch')) . "\" />\n";
		
		$exactSearch = "";
		if($wgLuceneSearchExactCase){
			$exactSearch = '<input type="submit" name="fulltext" value="' .
			htmlspecialchars(wfMsg('searchexactcase')) . "\" />\n";
		}
			

		$redirect = ''; # What's this for?
		$ret = wfMsg('lucenepowersearchtext',
			$namespaces, $redirect, $searchField,
			'', '', '', '', '', # Dummy placeholders
			$searchButton, $exactSearch);

		$title = Title::makeTitle(NS_SPECIAL, 'Search');
		$action = $title->escapeLocalURL();
		return "<br /><br />\n<form id=\"powersearch\" method=\"get\" " .
		"action=\"$action\">\n{$ret}\n</form>\n";
	}
	
	function makeFocusJS() {
		return "<script type='text/javascript'>" .
			"document.getElementById('lsearchbox').focus();" .
			"</script>";
	}
}

class LuceneResult {
	/**
	 * Construct a result object from single result line
	 * 
	 * @param array $lines
	 * @param string $format how the line is formatted (result,interwiki)
	 * @return array (float, Title)
	 * @access private
	 */
	function LuceneResult( $lines, $format = "result" ) {
		global $wgContLang;
		
		$score = null;
		$interwiki = null;
		$namespace = null;
		$title = null;
		
		$line = $lines['line'];
		wfDebug( "Lucene line: '$line'\n" );
		
		if( $format != "interwiki" )
			list( $score, $namespace, $title ) = explode( ' ', $line );
		else
			list( $score, $interwiki, $namespace, $title ) = explode( ' ', $line );

		$score     = floatval( $score );
		$namespace = intval( $namespace );
		$title     = urldecode( $title );
		$nsText    = $wgContLang->getNsText($namespace);

		$this->mInterwiki = '';
		// make title
		if( is_null($interwiki)){
			$this->mTitle = Title::makeTitle( $namespace, $title );
		} else{
			$interwiki = urldecode( $interwiki );
			// there might be a better way to make an interwiki link			
			$t = $interwiki.':'.$nsText.':'.str_replace( '_', ' ', $title );
			$this->mTitle = Title::newFromText( $t );
			$this->mInterwiki = $interwiki;
		}
		
		$this->mScore = $score;
		
		// highlighting tags
		list( $this->mHighlightTitle, $dummy ) = $this->extractHighlight($lines,$nsText,"#h.title");
		
		list( $this->mHighlightText, $dummy ) = $this->extractHighlight($lines,$nsText,"#h.text",true);
		
		list( $this->mHighlightRedirect, $redirect ) = $this->extractHighlight($lines,$nsText,"#h.redirect");
		$this->mRedirectTitle = null;
		if( !is_null($redirect)){
			if($interwiki != ''){
				$t = $interwiki.':'.$nsText.':'.str_replace( '_', ' ', $redirect );
				$this->mRedirectTitle = Title::newFromText( $t );
			} else
				$this->mRedirectTitle = Title::makeTitle($namespace,$redirect);
		}
			
		list( $this->mHighlightSection, $section) = $this->extractHighlight($lines,'',"#h.section");
		$this->mSectionTitle = null;
		if( !is_null($section)){
			$t = $nsText.':'.str_replace( '_', ' ', $title ).'#'.$section;
			$this->mSectionTitle = Title::newFromText($t);
		} 
	}
	
	/**
	 * Get the pair [snippet, link title] for highlighted text
	 *
	 * @param string $lines
	 * @param string $nsText textual form of namespace
	 * @param string $type
	 * @param boolean $useFinalSeparator
	 * @return array
	 */
	function extractHighlight($lines, $nsText, $type, $useFinalSeparator=false){
		if(!array_key_exists($type,$lines))
			return array("",null);
		$ret = "";
		$original = null;
		foreach($lines[$type] as $h){
			list($s,$o) = $this->extractSnippet($h,$useFinalSeparator);
			$ret .= $s;
			$original = $o;
		}
		if($nsText!='')
			$ret = $nsText.':'.$ret;
		return array($ret,$original);
	}
	
	/**
	 * Construct a highlight snippet from related result lines
	 *
	 * @param string $line
	 * @param boolean $useFinalSeparator if "..." is to be appended to the end of snippet
	 * @access protected
	 */
	function extractSnippet($line, $useFinalSeparator){
		$parts = explode(" ",$line);
		if(count($parts)!=4 && count($parts)!=5){
			wfDebug("Bad result line:".$line."\n");
		}
		$splits = $this->stripBracketsSplit($parts[0]);
		$highlight = $this->stripBracketsSplit($parts[1]);
		$suffix = urldecode($this->stripBrackets($parts[2]));
		$text = urldecode($parts[3]);
		$original = null;
		if(count($parts) > 4)
			$original = urldecode($parts[4]);
		
		$splits[] = strlen($text);
		$start = 0;
		$snippet = "";
		$hi = 0;
		
		foreach($splits as $sp){
			$sp = intval($sp);
			// highlight words!
			while($hi < count($highlight) && intval($highlight[$hi]) < $sp){
				$s = intval($highlight[$hi]);
				$e = intval($highlight[$hi+1]);
				$snippet .= substr($text,$start,$s-$start)."<span class=\"lsearchMatch\">".substr($text,$s,$e-$s)."</span>";
				$start = $e;
				$hi += 2;
			}
			// copy till split point
			$snippet .= substr($text,$start,$sp-$start);
			if($sp == strlen($text) && $suffix != '')
				$snippet .= $suffix;
			else if($useFinalSeparator)
				$snippet .= " <b>...</b> ";
			
			$start = $sp;						
		}
		return array($snippet,$original);
	}
	
	
	/**
	 * @access private
	 */
	function stripBrackets($str){
		if($str == '[]')
			return '';
		return substr($str,1,strlen($str)-2);
	}
	
	/**
	 * @access private
	 * @return array
	 */
	function stripBracketsSplit($str){
		$strip = $this->stripBrackets($str);
		if($strip == '')
			return array();
		else
			return explode(",",$strip);
	}

	function getTitle() {
		return $this->mTitle;
	}

	function getScore() {
		return $this->mScore;
	}
	
	function getTitleText(){
		return $this->mHighlightTitle;
	}
	
	function getText() {
		return $this->mHighlightText;
	}
	
	function getRedirectText() {
		return $this->mHighlightRedirect;
	}
	
	function getRedirectTitle(){
		return $this->mRedirectTitle;
	}
	
	function getSectionText(){
		return $this->mHighlightSection;
	}
	
	function getSectionTitle(){
		return $this->mSectionTitle;
	}
	
	function getInterwikiPrefix(){
		return $this->mInterwiki;
	}
}

class LuceneSearchSet {
	/**
	 * Contact the lsearch server and return a wrapper
	 * object with the set of results. Results may be cached.
	 *
	 * @param string $method The protocol verb to use
	 * @param string $query
	 * @param int $limit
	 * @return array
	 * @access public
	 * @static
	 */
	function newFromQuery( $method, $query, $namespaces = array(), $limit = 10, $offset = 0, $case = 'ignore' ) {
		$fname = 'LuceneSearchSet::newFromQuery';
		wfProfileIn( $fname );

		global $wgLuceneHost, $wgLucenePort, $wgLuceneSearchVersion;
		global $wgDBname, $wgMemc, $wgLuceneCacheExpiry, $wgLuceneDaemonTimeout;

		$enctext = rawurlencode( trim( $query ) );
		$searchPath = "/$method/$wgDBname/$enctext?" .
			wfArrayToCGI( array(
				'namespaces' => implode( ',', $namespaces ),
				'offset'     => $offset,
				'limit'      => $limit,
				'case'       => $case,
				'iwlimit'	 => LS_INTERWIKI_PAGE, // TODO: customize via user prefs 
			) );

		global $wgOut;
		$wgOut->addHtml( "<!-- querying $searchPath -->\n" );

		if($wgLuceneCacheExpiry != 0){
			// Cache results; they'll be read again on reloads and back paging 
			$key = wfMemcKey( 'lucene', $wgLuceneSearchVersion, md5( $searchPath ) );
		
			$resultSet = $wgMemc->get( $key );
			if( is_object( $resultSet ) ) {
				wfDebug( "$fname: got cached lucene results for key $key\n" );
				wfProfileOut( $fname );
				return $resultSet;
			}	
		}

		if( is_array( $wgLuceneHost ) ) {
			$hosts = $wgLuceneHost;
		} else {
			$hosts = array( $wgLuceneHost );
		}
		$remaining = count( $hosts );
		$pick = mt_rand( 0, count( $hosts ) - 1 );
		$data = false;

		while( $data === false && $remaining-- > 0 ) {
			// Start at a random position in the list, and rotate through
			// until we find a host that works or run out of hosts.
			$pick = ($pick + 1) % count( $hosts );
			$host = $hosts[$pick];
			$searchUrl = "http://$host:$wgLucenePort$searchPath";
			
			wfDebug( "Fetching search data from $searchUrl\n" );
			wfSuppressWarnings();
			wfProfileIn( $fname.'-contact-'.$host );
			$data = wfGetHTTP( $searchUrl, $wgLuceneDaemonTimeout );
			wfProfileOut( $fname.'-contact-'.$host );
			wfRestoreWarnings();
			
			if( $data === false ) {
				wfDebug( "Failed on $searchUrl!\n" );
			}
		}

		if( $data === false || $data === '' ) {
			// Network error or server error
			wfProfileOut( $fname );
			return false;
		} else {
			$inputLines = explode( "\n", trim( $data ) );
			$resultLines = array_map( 'trim', $inputLines );
		}

		$suggestion = null;
		$interwiki = null;
		$interwikiOrder = null;
		$totalHits = null;

		if( $method == 'search' ) {
			# This method outputs a summary line first.
			$totalHits = array_shift( $resultLines );
			if( $totalHits === false ) {
				# I/O error? this shouldn't happen
				wfDebug( "Couldn't read summary line...\n" );
			} else {
				$totalHits = intval( $totalHits );
				wfDebug( "total [$totalHits] hits\n" );
				if($wgLuceneSearchVersion >= 2.1){
					# second line is suggestion
					$s = array_shift($resultLines);
					if(LuceneSearchSet::startsWith($s,'#suggest ')){
						$suggestion = urldecode(substr($s,strpos($s,' ')+1));
					}
					# third line is interwiki stuff 
					$iwHeading = array_shift($resultLines);
					$iwCount = substr($iwHeading,strpos($iwHeading,' ')+1);
					if($iwCount > 0){						
						$iwMap = array();
						$interwikiOrder = array();
						$l = '';
						$lastKey = '';
						// sort lines by interwiki prefix
						while( ($l = array_shift($resultLines)) != "#results" ){
							if( $l[0]=='#' ){
								$iwMap[$lastKey][] = $l;
								continue; // highlight info for the last result 
							}
							$parts = explode(" ",$l);
							$key = $parts[1]; // iw prefix
							if(!array_key_exists($key,$iwMap)){
								$iwMap[$key] = array();
								$interwikiOrder[] = $key;
							}
							$iwMap[$key][] = $l;
							$lastKey = $key;
						}
						// make interwiki map: iw -> lucenesearchset 
						$interwiki = array();
						foreach($iwMap as $key=>$lines){
							$interwiki[$key] = new LuceneSearchSet($lines,count($lines));
						}
					}
				}
			}
		}

		$resultSet = new LuceneSearchSet( $resultLines, $totalHits, $suggestion, $interwiki, $interwikiOrder );
		
		if($wgLuceneCacheExpiry != 0){
			wfDebug( $fname.": caching lucene results for key $key\n" );
			$wgMemc->add( $key, $resultSet, $wgLuceneCacheExpiry );
		}

		wfProfileOut( $fname );
		return $resultSet;
	}
	/**
	 * An efficient string startswith function (which PHP interestingly lack)
	 * 
	 * @param string $source
	 * @param string $prefix 
	 * @access private
	 */
	function startsWith($source, $prefix){
   		return strncmp($source, $prefix, strlen($prefix)) == 0;
	}

	/**
	 * Private constructor. Use LuceneSearchSet::newFromQuery().
	 *
	 * @param array $lines
	 * @param int $totalHits
	 * @param string $suggestion
	 * @param array $interwiki
	 * @param array $interwikiOrder
	 * @access private
	 */
	function LuceneSearchSet( $lines, $totalHits = null, $suggestion = null, $interwiki = null, $interwikiOrder = null ) {
		$this->mTotalHits  = $totalHits;
		$this->mSuggestion = $suggestion;
		$this->mResults    = $lines;
		$this->mInterwiki  = $interwiki;
		$this->mInterwikiOrder = $interwikiOrder;
	}

	function hasResults() {
		return count( $this->mResults ) > 0;
	}
	
	function hasInterwiki() {
		return count($this->mInterwiki) > 0;
	}
	
	function getInterwikiOrder(){
		return $this->mInterwikiOrder;
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
	 * Iterate over a custom resultset
	 *
	 * @param array $resultset
	 * @param callback $callback
	 * @param mixed $userdata
	 * @param string $format how results are formated (result,interwiki) 
	 * @access private
	 * @return array
	 */
	function iterate($resultset, $callback, $userdata, $format){
		$out = array();		
		$resCount = 0;
		for($i=0 ; $i<count($resultset); $i+=1, $resCount+=1){
			$lines = array(); // collect lines that belong to one result
			$lines['line'] = $resultset[$i];
			while(($i+1)<count($resultset) && $this->startsWith($resultset[$i+1],'#h')){
				$i+=1;
				$parts = explode(" ",$resultset[$i],2);
				if(!array_key_exists($parts[0],$lines))
					$lines[$parts[0]] = array();
				$lines[$parts[0]][] = $parts[1];
			}
			$out[$resCount] = call_user_func( $callback, new LuceneResult( $lines, $format ), $userdata );
		}		
		return $out;
	}

	/**
	 * Iterate over all returned results, passing LuceneResult objects
	 * to a given callback for processing.
	 *
	 * @param callback $callback
	 * @param mixed $userdata Optional data to pass to the callback
	 * @return array
	 * @access public
	 */
	function iterateResults( $callback, $userdata = null ) {
		return $this->iterate($this->mResults, $callback, $userdata, "result");
	}
	
	/**
	 * Iterate over interwiki results
	 * 
	 * @param string $interwikiPrefix which interwiki to iterate
	 * @param callback $callback
	 * @param mixed $userdata
	 * @return array
	 * @access public
	 */
	function iterateInterwiki( $interwikiPrefix, $callback, $userdata = null) {
		$target = $this->mInterwiki[$interwikiPrefix]; 
		return $target->iterate($target->mResults, $callback, $userdata, "interwiki");
	}
}

<?php

# To use this, add something like the following to LocalSettings:
# 
#  $wgLuceneHost = "192.168.0.1";
#  $wgLucenePort = 8123;
#
#  require_once("../extensions/LuceneSearch.php");
#
# The MWDaemon search daemon needs to be running on the specified host
# - it's in the 'lucene-search' module in CVS.
##########

$wgLuceneDisableSuggestions = false;
$wgLuceneDisableTitleMatches = false;

# Not a valid entry point, skip unless MEDIAWIKI is defined
require_once("SearchEngine.php");

if (defined('MEDIAWIKI')) {
$wgExtensionFunctions[] = "wfLuceneSearch";

if (class_exists("Revision"))
	$wgLSuseold = false;
else
	$wgLSuseold = true;

define('LS_PER_PAGE', 10);

function wfLuceneSearch() {

global $IP;
require_once("$IP/includes/SpecialPage.php");

class LuceneSearch extends SpecialPage
{
	var $namespaces;

	function LuceneSearch() {
		SpecialPage::SpecialPage("Search");
	}

	function makelink($term, $offset, $limit) {
		global $wgRequest;
		global $wgScriptPath;
		$link = "$wgScriptPath?title=Special:Search&amp;search=".
			urlencode($term)."&amp;fulltext=Search";
		foreach(SearchEngine::searchableNamespaces() as $ns => $name)
			if ($wgRequest->getCheck("ns" . $ns))
				$link .= "&amp;ns".$ns."=1";
		$link .= "&amp;offset=$offset&amp;limit=$limit";
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
		
	function execute($par) {
		global $wgRequest, $wgOut, $wgTitle, $wgContLang, $wgUser,
			$wgScriptPath, $wgLSuseold, $wgInputEncoding,
			$wgLuceneDisableTitleMatches, $wgLuceneDisableSuggestions,
			$wgUser;
		global $wgGoToEdit;

		$fname = 'LuceneSearch::execute';
		wfProfileIn( $fname );
		$this->setHeaders();

		foreach(SearchEngine::searchableNamespaces() as $ns => $name)
			if ($wgRequest->getCheck("ns" . $ns))
				$this->namespaces[] = $ns;

		if (count($this->namespaces) == 0) {
			foreach(SearchEngine::searchableNamespaces() as $ns => $name) {
				if($wgUser->getOption('searchNs' . $ns)) {
					$this->namespaces[] = $ns;
				}
			}
			if (count($this->namespaces) == 0)
				$this->namespaces = array(0);
		}

		$bits = split("/", $wgRequest->getVal("title"), 2);
		if(!empty($bits[1]))
			$q = str_replace("_", " ", $bits[1]);
		else
			$q = $wgRequest->getText('search');
		$limit = $wgRequest->getInt('limit');
		$offset = $wgRequest->getInt('offset');

		if( $wgRequest->getVal( 'gen' ) == 'titlematch' ) {
			$this->sendTitlePrefixes( $q, $limit );
			wfProfileOut( $fname );
			return;
		}

		if (!$wgLuceneDisableSuggestions)
			$wgOut->addHTML($this->makeSuggestJS());
		$wgOut->addLink(array(
			"rel" => "stylesheet",
			"type" => "text/css",
			"media" => "screen,projection",
			"href" => $wgScriptPath . '/extensions/lucenesearch.css'
			)
		);

		$wgOut->addWikiText(wfMsg('searchresulttext'));
		$wgOut->addHTML($this->showShortDialog($q));

		if ($q !== false && strlen($q) > 0) {
			if (!($wgRequest->getText('fulltext'))) {
				$t = SearchEngine::getNearMatch($q);
				if(!is_null($t)) {
					$wgOut->redirect($t->getFullURL());
					return;
				}
			}

			# No match, generate an edit URL
			$t = Title::newFromText($q);
			if(!$wgRequest->getText("go") || is_null($t)) {
				$editurl = ''; # hrm...
			} else {
				# If the feature is enabled, go straight to the edit page
				if ($wgGoToEdit) {
					$wgOut->redirect($t->getFullURL('action=edit'));
					return;
				} else {
					$editurl = $t->escapeLocalURL('action=edit');
				}
				# FIXME: HTML in wiki message
				$wgOut->addHTML('<p>' . wfMsg('nogomatch', $editurl, 
					htmlspecialchars($q)) . "</p>\n");
			}

			$maxresults = $offset + $limit;
			if ($maxresults < 10)
				$maxresults = 10;
			global $wgDisableTextSearch;
			$searchfailed = $wgDisableTextSearch;
			if (!$searchfailed) {
				$r = $this->doLuceneSearch($q, $maxresults);
			}
			if (isset( $r ) ) {
				$numresults = $r[0];
				$results = $r[1];
			} else {
				$searchfailed = true;
				$numresults = 0;
				$results = array();
			}

			if ($searchfailed) {
				global $wgInputEncoding;
				$wgOut->addHTML(wfMsg('searchdisabled'));
				$wgOut->addHTML(wfMsg('googlesearch',
					htmlspecialchars($q),
					htmlspecialchars($wgInputEncoding)));
				return;
			}


			$wgOut->setSubtitle(wfMsg('searchquery', htmlspecialchars($q)));


			if (is_string( $results ) ) {
				$suggestion = trim( $results );
			}
			if ($numresults == -1 && strlen($suggestion) > 0) {
				$o = " " . wfMsg("searchdidyoumean", 
						$this->makelink($suggestion, $offset, $limit),
						htmlspecialchars($suggestion));
				$wgOut->addHTML("<div style='text-align: center'>".$o."</div>");
			}

			$nmtext = "";
			if ($offset == 0 && !$wgLuceneDisableTitleMatches) {
				$titles = $this->doTitleMatches($q);
				if (count($titles) > 0) {
					$sk =& $wgUser->getSkin();
					$nmtext = "<p>".wfMsg('searchnearmatches')."</p>";
					$i = 0;
					$nmtext .= "<ul>";
					foreach ($titles as $title) {
						if (++$i > 5) break;
						$nmtext .= wfMsg('searchnearmatch',
							$sk->makeKnownLinkObj($title, ''));
					}
					$nmtext .= "</ul>";
					$nmtext .= "<hr/>";
				}
			}
	
			$wgOut->addHTML($nmtext);

			if ($numresults < 1) {
				$o = wfMsg("searchnoresults");
				$wgOut->addHTML($o);
			} else {
				if ($limit == 0 || $limit > 100)
					$limit = LS_PER_PAGE;
				
				$showresults = min($limit, count($results)-$numresults);
				$i = $offset;
				$resq = trim(preg_replace("/[] \\|[()\"{}]+/", " ", $q));
				$contextWords = implode("|", 
				$wgContLang->convertForSearchResult(split(" ", $resq)));

				$top = wfMsg("searchnumber", $offset + 1, 
					min($numresults, $offset+$limit), $numresults);
				$out = "<ul>";
				$chunks = array_chunk($results, $limit);
				$numchunks = ceil($numresults / $limit);
				$whichchunk = $offset / $limit;
				$prevnext = "";
				if ($whichchunk > 0)
					$prevnext .= "<a href=\"".
						$this->makelink($q, $offset-$limit, $limit)."\">".
						wfMsg("searchprev")."</a> ";
				$first = max($whichchunk - 11, 0);
				$last = min($numchunks, $whichchunk + 11);
				//$wgOut->addWikiText("whichchunk=$whichchunk numchunks=$numchunks first=$first last=$last num=".count($chunks)." limit=$limit offset=$offset results=".count($results)."\n\n");
				if ($last - $first > 1) {
					for($i = $first; $i < $last; $i++) {
						if ($i === $whichchunk)
							$prevnext .= "<strong>".($i+1)."</strong> ";
						else
							$prevnext .= "<a href=\"".
								$this->makelink($q, $limit*$i, 
								$limit)."\">".($i+1)."</a> ";
					}
				}
				if ($whichchunk < $last-1)
					$prevnext .= "<a href=\"".
						$this->makelink($q, $offset + $limit, $limit)."\">".
						wfMsg("searchnext")."</a> ";
				$prevnext = "<div style='text-align: center'>$prevnext</div>";
				$top .= $prevnext;
				if ($chunks && count($chunks) > 0) {
					foreach ($chunks[$whichchunk] as $result) {
						$out .= $this->showHit($result[0], $result[1], $contextWords);
					}
				}
				$out .= "</ul>";
			}
			$wgOut->addHTML("<hr/>");
			if( isset( $top ) ) $wgOut->addHTML( $top );
			if( isset( $out ) ) $wgOut->addHTML( $out );
			if( isset( $prevnext ) ) $wgOut->addHTML("<hr/>" . $prevnext);
			$wgOut->addHTML($this->showFullDialog($q));
		}
		$wgOut->setRobotpolicy('noindex,nofollow');
		wfProfileOut( $fname );
	}
	
	/**
	 * Send a list of titles starting with the given prefix.
	 * These are read by JavaScript code via an XmlHttpRequest
	 * and displayed in a drop-down box for selection.
	 *
	 * @param string $query
	 * @param int $limit
	 * @return void - side effects only
	 * @access private
	 */
	function sendTitlePrefixes( $query, $limit ) {
		global $wgOut, $wgInputEncoding;
		$wgOut->disable();
		
		if( $limit < 1 || $limit > 50 )
			$limit = 20;
		header("Content-Type: text/plain; charset=$wgInputEncoding");
		if( strlen( $query ) < 1 ) {
			return;
		}

		$results = $this->doTitlePrefixSearch( $query, $limit );
		if( $results && count( $results ) > 0 ) {
			foreach( $results as $result ) {
				echo $result->getPrefixedUrl() . "\n";
			}
		}
	}

	function showHit($score, $t, $terms) {
		$fname = 'LuceneSearch::showHit';
		wfProfileIn($fname);
		global $wgUser, $wgContLang, $wgLSuseold;

		if(is_null($t)) {
			wfProfileOut($fname);
			return "<!-- Broken link in search result -->\n";
		}
		$sk =& $wgUser->getSkin();

		//$contextlines = $wgUser->getOption('contextlines');
		$contextlines = 2;
		$contextchars = $wgUser->getOption('contextchars');
		if ('' == $contextchars) 
			$contextchars = 50;

		$link = $sk->makeKnownLinkObj($t, '');

		$rev = $wgLSuseold ? new Article($t) : Revision::newFromTitle($t);
		if ($rev === null)
			return "<!--Broken link in search results: ".$t->getDBKey()."-->\n";
		
		$text = $wgLSuseold ? $rev->getContent(false) : $rev->getText();
				$size = wfMsg('searchsize', sprintf("%.1f", strlen($text) / 1024), str_word_count($text));
		$text = $this->removeWiki($text);
	
		$lines = explode("\n", $text);

		$max = IntVal($contextchars) + 1;
		$pat1 = "/(.*)($terms)(.{0,$max})/i";

		$lineno = 0;

		$extract = '';
		wfProfileIn("$fname-extract");
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
		wfProfileOut("$fname-extract");
		wfProfileOut($fname);
		$date = $wgContLang->timeanddate($rev->getTimestamp());
		$percent = sprintf("%2.1f%%", $score * 100);
		//$score = wfMsg("searchscore", $percent);
		$url = $t->getFullURL();
		return "<li style='padding-bottom: 1em'>{$link}{$extract}<br/>"
			."<span style='color: green; font-size: small'>"
			."$url - $size - $date</span></li>\n";
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

	/**
	 * Read an input line from a socket and convert it to local encoding.
	 * Trailing newline is trimmed.
	 * Will return FALSE if no more data or I/O error.
	 *
	 * @param resource $sock
	 * @return string
	 * @access private
	 */
	function inputLine( $sock ) {
		$result = @socket_read( $sock, 1024, PHP_NORMAL_READ );
		if( $result ) {
			global $wgInputEncoding;
			$result = chop( $result ); # Trim newline
			if( $wgInputEncoding != 'utf-8' ) {
				global $wgContLang;
				$result = $wgContLang->iconv( 'utf-8', $wgInputEncoding, $result );
			}
		}
		return $result;
	}

	/**
	 * Read an input line from a socket and return a score & title pair.
	 * Will return FALSE if no more data or I/O error.
	 *
	 * @param resource $sock
	 * @return array (float, Title)
	 * @access private
	 */
	function readResult( $sock ) {
		$result = @socket_read( $sock, 1024, PHP_NORMAL_READ );
		if( !$result ) {
			return false;
		}
		$result = chop( $result ); # Trim newline
		list( $score, $namespace, $title ) = split( ' ', $result );
		
		$score = FloatVal( $score );
		$namespace = IntVal( $namespace );
		$title = urldecode( $title );
		
		global $wgInputEncoding;
		if( $wgInputEncoding != 'utf-8' ) {
			global $wgContLang;
			$title = $wgContLang->iconv( 'utf-8', $wgInputEncoding, $title );
		}
		
		$fulltitle = Title::makeTitle( $namespace, $title );
		return array( $score, $fulltitle );
	}
	
	/**
	 * Write given lines of text out to a socket.
	 * Text is converted to UTF-8 if internal encoding is different,
	 * URL-encoded, and a newline is automatically appended.
	 *
	 * @param resource $sock
	 * @param array $lines
	 * @return int Number of bytes written
	 * @access private
	 */
	function sendLines( $sock, $lines ) {
		global $wgInputEncoding;
		if( $wgInputEncoding != 'utf-8' ) {
			global $wgContLang;
			foreach( $lines as $i => $text ) {
				$lines[$i] = $wgContLang->iconv( $wgInputEncoding, 'utf-8', $text );
			}
		}
		return socket_write( $sock,
			implode( "\n",
				array_map( 'urlencode',
					$lines ) )
			. "\n" );
	}
	
	/**
	 * @param string $method The protocol verb to use
	 * @param string $query
	 * @param int $limit
	 * @return array
	 * @access private
	 */
	function queryLuceneServer( $method, $query, $limit = 65536 ) {
		$fname = 'LuceneSearch::queryLuceneServer';
		wfProfileIn( $fname );
		
		global $wgLuceneHost, $wgLucenePort, $wgDBname;
		$sock = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
		@$conn = socket_connect( $sock, $wgLuceneHost, $wgLucenePort );
		if( $conn === false ) {
			wfProfileOut( $fname );
			return false;
		}
		$this->sendLines( $sock, array(
			$wgDBname,
			$method,
			$query ) );
		
		if( $method == 'SEARCH' ) {
			# This method outputs a summary line first.
			$numresults = $this->inputLine( $sock );
			if( $numresults === false ) {
				# I/O error? this shouldn't happen
				wfDebug( "Couldn't read summary line...\n" );
				wfProfileOut( $fname );
				return array( 0, array() );
			}
			$numresults = IntVal( $numresults );
			wfDebug("total [$numresults] hits\n");
			if( $numresults == 0 ) {
				# No results, but we got a suggestion...
				$suggestion = urldecode( $this->inputLine( $sock ) );
				wfdebug("no results; suggest: [$suggestion]\n");
				wfProfileOut( $fname );
				return array( -1, $suggestion );
			}
		} else {
			$numresults = null;
		}
		
		$results = array();
		while( ( $result = $this->readResult( $sock ) ) !== false
				&& count( $results ) < $limit ) {
			
			if( !in_array( $result[1]->getNamespace(), $this->namespaces ) ) {
				continue;
			}
			if( $method == 'SEARCH' ) { # quick hack
				$results[] = $result;
			} else {
				$results[] = $result[1];
			}
		}
		wfProfileOut( $fname );
		return array( $numresults, $results );
	}
	
	function doTitlePrefixSearch($query, $limit) {
		list( $numresults, $results ) = $this->queryLuceneServer( 'TITLEPREFIX', $query, $limit );
		return $results;
	}

	function doTitleMatches($query) {
		list( $numresults, $results ) = $this->queryLuceneServer( 'TITLEMATCH', $query, 10 );
		return $results;
	}

	function doLuceneSearch($query, $max) {
		return $this->queryLuceneServer( 'SEARCH', $query, $max );
	}

	function showShortDialog($term) {
		global $wgScript;

		$action = "$wgScript";
                $searchButton = '<input type="submit" name="fulltext" value="' .
                  htmlspecialchars(wfMsg('powersearch')) . "\" />\n";
                $searchField = "<div><input type='text' id='lsearchbox' onkeyup=\"resultType()\" "
			. "style='margin-left: 25%; width: 50%; ' value=\""
                        . htmlspecialchars($term) ."\""
			. " autocomplete=\"off\" name=\"search\" />\n"
			. "<span id='loadStatus'></span>"
			. $searchButton
			. "<div id='results'></div></div>";

		$ret = $searchField /*. $searchButton*/;
                return 
		  "<form id=\"search\" method=\"get\" "
                  . "action=\"$action\"><input type='hidden' name='title' value='Special:Search'>\n<div>{$ret}</div>\n</form>\n";
	}

	function showFullDialog($term) {
		global $wgContLang;
		$namespaces = '';
		foreach(SearchEngine::searchableNamespaces() as $ns => $name) {
			$checked = in_array($ns, $this->namespaces)
				? ' checked="checked"'
                                : '';
                        $name = str_replace('_', ' ', $name);
                        if('' == $name) {
                                $name = wfMsg('blanknamespace');
                        }
                        $namespaces .= " <label><input type='checkbox' value=\"1\" name=\"" .
                          "ns{$ns}\"{$checked} />{$name}</label>\n";
                }

                $searchField = "<input type='text' name=\"search\" value=\"" .
                        htmlspecialchars($term) ."\" width=\"80\" />\n";

                $searchButton = '<input type="submit" name="fulltext" value="' .
                  htmlspecialchars(wfMsg('powersearch')) . "\" />\n";

				$redirect = ''; # What's this for?
                $ret = wfMsg('lucenepowersearchtext',
                        $namespaces, $redirect, $searchField,
                        '', '', '', '', '', # Dummy placeholders
                        $searchButton);

                $title = Title::makeTitle(NS_SPECIAL, 'Search');
                $action = $title->escapeLocalURL();
                return "<br /><br />\n<form id=\"powersearch\" method=\"get\" " .
                  "action=\"$action\">\n{$ret}\n</form>\n";
	}

	function makeSuggestJS() {
		global $wgScript, $wgArticlePath;
		return <<<___EOF___
<script type="text/javascript"><!--

var xmlHttp = (window.XMLHttpRequest) ? new XMLHttpRequest : new ActiveXObject("Microsoft.XMLHTTP");
var searchCache = {};
var searchStr;
var searchTimeout;

function getResults()
{
  var encStr = escape(searchStr.replace(/ /g, '_'));
  xmlHttp.open("GET", "$wgScript?title=Special:Search&gen=titlematch&ns0=0&limit=10&search=" 
    + encStr, true);

  xmlHttp.onreadystatechange = parseResults;
  xmlHttp.send(null);
}

function parseResults()
{
  if (xmlHttp.readyState > 3)
  {
    document.getElementById("loadStatus").innerHTML = "";
    var resultArr = xmlHttp.responseText.split("\\n");
    searchCache[searchStr.toLowerCase()] = resultArr;
    showResults(resultArr);
  }
}

function showResults(resultArr)
{
  var returnStr = "";
  var resultsEl = document.getElementById("results");

  if (resultArr.length < 2)
    resultsEl.innerHTML = "No results";
  else
  {
    resultsEl.innerHTML = "";

    for (var i=0; i < resultArr.length; i++)
    {
      var linkEl = document.createElement("a");
      linkEl.href = "$wgArticlePath".replace(/\\$1/, resultArr[i]);
      var textEl = document.createTextNode(decodeURIComponent(resultArr[i]).replace(/_/g, ' '));
      linkEl.appendChild(textEl);
      resultsEl.appendChild(linkEl);
    }
  }

  resultsEl.style.display = "block";
}

function resultType()
{
  searchStr = document.getElementById("lsearchbox").value;
  if (searchTimeout) clearTimeout(searchTimeout);

  if (searchStr != "")
  {
    if (searchCache[searchStr.toLowerCase()])
      showResults(searchCache[searchStr.toLowerCase()])
    else
      searchTimeout = setTimeout(getResults, 500);
  }
  else
  {
    document.getElementById("results").style.display = "none";
  }
}
//--></script>
___EOF___;
	}
}

global $wgMessageCache;
SpecialPage::addPage(new LuceneSearch);
$wgMessageCache->addMessage("searchnumber", "<strong>Results $1-$2 of $3</strong>");
$wgMessageCache->addMessage("searchprev", "&#x00AB; <span style='font-size: small'>Prev</span>");
$wgMessageCache->addMessage("searchnext", "<span style='font-size: small'>Next</span> &#x00BB;");
$wgMessageCache->addMessage("searchscore", "Relevancy: $1");
$wgMessageCache->addMessage("searchsize", "$1k ($2 words)");
$wgMessageCache->addMessage("searchdidyoumean", "Did you mean: \"<a href=\"$1\">$2</a>\"?");
$wgMessageCache->addMessage("searchnoresults", "Sorry, there were no exact matches to your query.");
$wgMessageCache->addMessage("searchnearmatches", "<b>These pages have similar titles to your query:</b>\n");
$wgMessageCache->addMessage("searchnearmatch", "<li>$1</li>\n");
$wgMessageCache->addMessage("lucenepowersearchtext", "
Search in namespaces:\n
$1\n
Search for $3 $9");

} # End of extension function
} # End of invocation guard
?>

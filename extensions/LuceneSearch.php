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
		$link = "$wgScriptPath?title=Special:Search&amp;search=".
			urlencode($term)."&amp;fulltext=Search";
		foreach(SearchEngine::searchableNamespaces() as $ns => $name)
			if ($wgRequest->getCheck("ns" . $ns))
				$link .= "&amp;ns".$ns."=1";
		$link .= "&amp;offset=$offset&amp;limit=$limit";
		return $link;
	}
		
	function execute($par) {
		global $wgRequest, $wgOut, $wgTitle, $wgContLang, $wgUser,
			$wgLuceneCSSPath, $wgLSuseold, $wgOutputEncoding,
			$wgLuceneDisableTitleMatches, $wgLuceneDisableSuggestions,
			$wgUser;
		global $wgGoToEdit;

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

		if ($wgRequest->getText('gen') == 'titlematch') {
			$limit = $wgRequest->getInt("limit");
			if ($limit < 1 || $limit > 50)
				$limit = 20;
			header("Content-Type: text/plain; charset=$wgOutputEncoding");
			if (strlen($q) < 1)
				wfAbruptExit();

			$results = $this->doTitlePrefixSearch($q, $limit);
			if ($results && count($results) > 0)
				foreach ($results as $result) {
					echo $result->getPrefixedText() . "\n";
				}
			wfAbruptExit();
		}

		if (!$wgLuceneDisableSuggestions)
			$wgOut->addHTML($this->makeSuggestJS());
		$wgOut->addLink(array(
			"rel" => "stylesheet",
			"type" => "text/css",
			"media" => "screen,projection",
			"href" => $wgLuceneCSSPath
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
			$limit = $wgRequest->getInt('limit');
			$offset = $wgRequest->getInt('offset');

			$maxresults = $offset + $limit;
			if ($maxresults < 10)
				$maxresults = 10;
			global $wgDisableTextSearch;
			$searchfailed = $wgDisableTextSearch;
			if (!$searchfailed)
				$r = $this->doLuceneSearch($q, $maxresults);
			if ($r === null)
				$searchfailed = true;

			if ($searchfailed) {
				global $wgInputEncoding;
				$wgOut->addHTML(wfMsg('searchdisabled'));
				$wgOut->addHTML(wfMsg('googlesearch',
					htmlspecialchars($q),
					htmlspecialchars($wgInputEncoding)));
				return;
			}

			$numresults = $r[0];
			$results = $r[1];

			$wgOut->setSubtitle(wfMsg('searchquery', htmlspecialchars($q)));


			$suggestion = trim($results);
			if ($numresults == -1 && strlen($suggestion) > 0) {
				$o .= " " . wfMsg("searchdidyoumean", 
						$this->makelink($suggestion, $offset, $limit),
						htmlspecialchars($suggestion));
			}
			$wgOut->addHTML("<div style='text-align: center'>".$o."</div>");

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
			$wgOut->addHTML("<hr/>" . $top . $out);
			$wgOut->addHTML("<hr/>" . $prevnext);
			$wgOut->addHTML($this->showFullDialog($q));
		}
		$wgOut->setRobotpolicy('noindex,nofollow');
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

	function doTitlePrefixSearch($query, $limit) {
		global $wgLuceneHost, $wgLucenePort;
		wfDebug("title prefix search: $query\n");
		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		@$conn = socket_connect($sock, $wgLuceneHost, $wgLucenePort);
		if ($conn === FALSE)
			return null;
		$query = iconv($wgOutputEncoding, "UTF-8", $query);
		@socket_write($sock, $wgDBname . "\n");
		@socket_write($sock, "TITLEPREFIX\n" . urlencode($query) . "\n");
		$results = array();
		while (($result = @socket_read($sock, 1024, PHP_NORMAL_READ)) != FALSE
                       && count($results) <= $limit) {
			$result = chop($result);
			$result = iconv("UTF-8", $wgOutputEncoding, $result);
			wfdebug("result: $result\n");
			list($score, $namespace, $title) = split(" ", $result);
			if (!in_array($namespace, $this->namespaces)) {
				continue;
			}
			$fulltitle = Title::makeTitle($namespace, $title);
			if ($fulltitle === null) {
				continue;
			}
			$results[] = $fulltitle;
		}
		return $results;
	}

	function doTitleMatches($query) {
		global $wgLuceneHost, $wgLucenePort;
		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$conn = @socket_connect($sock, $wgLuceneHost, $wgLucenePort);
		$query = iconv($wgOutputEncoding, "UTF-8", $query);
		@@socket_write($sock, $wgDBname . "\n");
		@socket_write($sock, "TITLEMATCH\n" . urlencode($query) . "\n");
		$results = array();
		while (($result = @socket_read($sock, 1024, PHP_NORMAL_READ)) != FALSE) {
			$result = chop($result);
			$result = iconv("UTF-8", $wgOutputEncoding, $result);
			list($score, $namespace, $title) = split(" ", $result);
			if (!in_array($namespace, $this->namespaces)) {
				continue;
			}
			$fulltitle = Title::makeTitle($namespace, $title);
			if ($fulltitle === null) {
				continue;
			}
			$results[] = $fulltitle;
		}
		return $results;
	}

	function doLuceneSearch($query, $max) {
		global $wgLuceneHost, $wgLucenePort, $wgDBname;
		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		@$conn = socket_connect($sock, $wgLuceneHost, $wgLucenePort);
		if ($conn === FALSE)
			return null;
		$query = iconv($wgOutputEncoding, "UTF-8", $query);
		if (@socket_write($sock, $wgDBname . "\n") === FALSE ||
                    @socket_write($sock, "SEARCH\n" . urlencode($query) . "\n") === FALSE)
			return null;
		$results = array();

		$numresults = @socket_read($sock, 1024, PHP_NORMAL_READ);
		wfDebug("total [$numresults] hits\n");
		if ($numresults === FALSE)
			return array();
		$numresults = chop($numresults);

		if ($numresults == 0) {
			$suggestion = @socket_read($sock, 1024, PHP_NORMAL_READ);
			wfdebug("no results; suggest: [$suggestion]\n");
			return array(-1, urldecode($suggestion));
		}

		while (($result = @socket_read($sock, 1024, PHP_NORMAL_READ)) != FALSE
		       && count($results) <= $max) {
			$result = chop($result);
			$result = iconv("UTF-8", $wgOutputEncoding, $result);
			list($score, $namespace, $title) = split(" ", $result);
			wfdebug("result: $namespace $title\n");
			if (!in_array($namespace, $this->namespaces)) {
				--$numresults;
				continue;
			}
			$fulltitle = Title::makeTitle($namespace, $title);
			if ($fulltitle === null) {
				wfDebug("broken link: $namespace $title");
				continue;
			}
			$results[] = array($score, $fulltitle);
		}
		socket_close($sock);
		return array($numresults, $results);
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
		global $wgScript;
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
      linkEl.href = "$wgScript?title=" + resultArr[i];
      var textEl = document.createTextNode(resultArr[i].replace(/_/g, ' '));
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

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

# Not a valid entry point, skip unless MEDIAWIKI is defined
require_once("SearchEngine.php");

if (defined('MEDIAWIKI')) {
$wgExtensionFunctions[] = "wfLuceneSearch";

define('LS_PER_PAGE', 20);

function wfLuceneSearch() {
global $IP;
require_once( "$IP/includes/SpecialPage.php" );

class LuceneSearch extends SpecialPage
{
	var $namespaces;

	function LuceneSearch() {
		SpecialPage::SpecialPage("Search");
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgTitle, $wgContLang;

		$this->setHeaders();

		foreach(SearchEngine::searchableNamespaces() as $ns => $name)
			if ($wgRequest->getCheck("ns" . $ns))
				$this->namespaces[] = $ns;

		if (count($this->namespaces) == 0)
			$this->namespaces = array(0);

		$q = $wgRequest->getText( 'search' );
		$r = $this->doLuceneSearch($q);
		$numresults = $r[0];
		$results = $r[1];

		list($limit, $offset) = $wgRequest->getLimitOffset(LS_PER_PAGE, 'searchlimit');
                if($numresults || $offset) {
                        $prevnext = wfViewPrevNext($offset, $limit,
                                'Special:Search',
                                wfArrayToCGI(
                                        array('search' => $q)));
                }

		$showresults = min($limit, count($results)-$numresults);
		$wgOut->setSubtitle(wfMsg('searchquery', htmlspecialchars($q)));
		$i = $offset;
		$resq = trim(preg_replace("/[] \\|[()\"{}]+/", " ", $q));
		$contextWords = implode("|", $wgContLang->convertForSearchResult(split(" ", $resq)));

		$top = wfMsg("searchnumber", $offset + 1, min($numresults, $limit), $numresults);
		$top .= $prevnext;
		$out = "<ol start=".($offset + 1).">";
		$chunks = array_chunk($results, $limit);
		$numchunks = $numresults / $limit;
		$whichchunk = ($offset / $limit);
		foreach ($chunks[$whichchunk] as $result) {
			$out .= $this->showHit($result, $contextWords);
		}
		$out .= "</ol>";
		$wgOut->addWikiText(wfMsg('searchresulttext'));
		$wgOut->addHTML("<hr/>" . $top . $out);
		$wgOut->addHTML("<hr/>" . $prevnext);
		$wgOut->addHTML($this->showFullDialog());
		$wgOut->setRobotpolicy('noindex,nofollow');
	}

        function showHit($t, $terms) {
                $fname = 'LuceneSearch::showHit';
                wfProfileIn( $fname );
                global $wgUser, $wgContLang;

                if(is_null($t)) {
                        wfProfileOut( $fname );
                        return "<!-- Broken link in search result -->\n";
                }
                $sk =& $wgUser->getSkin();

                $contextlines = $wgUser->getOption( 'contextlines' );
                if ( '' == $contextlines ) 
			$contextlines = 5;
                $contextchars = $wgUser->getOption( 'contextchars' );
                if ( '' == $contextchars ) 
			$contextchars = 50;

                $link = $sk->makeKnownLinkObj($t, '');
		$rev = Revision::newFromTitle($t);
		if ($rev === null)
			return "<b>Broken link in search results: ".$t->getDBKey()."</b>";

		$text = $rev->getText();
                $size = wfMsg('nbytes', strlen($text));

                $lines = explode("\n", $text);

                $max = IntVal( $contextchars ) + 1;
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
                        $pat2 = '/(' . $terms . ")/i";
                        $line = preg_replace($pat2,
                          "<span class='searchmatch'>\\1</span>", $line);

                        $extract .= "<br /><small>{$lineno}: {$line}</small>\n";
                }
                wfProfileOut( "$fname-extract" );
                wfProfileOut( $fname );
                return "<li style='padding-bottom: 1em'>{$link} ({$size}){$extract}</li>\n";
        }

	function doLuceneSearch( $query ) {
		global $wgLuceneHost, $wgLucenePort;
		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$conn = socket_connect($sock, $wgLuceneHost, $wgLucenePort);
		socket_write($sock, urlencode($query) . "\n");
		$results = array();

		$numresults = @socket_read($sock, 1024, PHP_NORMAL_READ);
		wfDebug("total [$numresults] hits\n");
		if ($numresults === FALSE)
			return array();
		$numresults = chop($numresults);

		while (($result = @socket_read($sock, 1024, PHP_NORMAL_READ)) != FALSE) {
			$result = chop($result);
			list($namespace, $title) = split(" ", $result);
			if (!in_array($namespace, $this->namespaces))
				continue;
			$fulltitle = Title::makeTitle($namespace, $title);
			if ($fulltitle === null) {
				wfDebug("broken link: $namespace $title");
				continue;
			}
			$results[] = $fulltitle;
		}
		socket_close($sock);
		return array($numresults, $results);
	}

	function showFullDialog() {
		global $wgContLang;
		$namespaces = '';
		foreach(SearchEngine::searchableNamespaces() as $ns => $name) {
			$checked = in_array($ns, $this->namespaces)
				? ' checked="checked"'
                                : '';
                        $name = str_replace( '_', ' ', $name );
                        if('' == $name) {
                                $name = wfMsg('blanknamespace');
                        }
                        $namespaces .= " <label><input type='checkbox' value=\"1\" name=\"" .
                          "ns{$ns}\"{$checked} />{$name}</label>\n";
                }

                $searchField = "<input type='text' name=\"search\" value=\"" .
                        htmlspecialchars( $term ) ."\" width=\"80\" />\n";

                $searchButton = '<input type="submit" name="searchx" value="' .
                  htmlspecialchars(wfMsg('powersearch')) . "\" />\n";

                $ret = wfMsg('lucenepowersearchtext',
                        $namespaces, $redirect, $searchField,
                        '', '', '', '', '', # Dummy placeholders
                        $searchButton );

                $title = Title::makeTitle( NS_SPECIAL, 'Search' );
                $action = $title->escapeLocalURL();
                return "<br /><br />\n<form id=\"powersearch\" method=\"get\" " .
                  "action=\"$action\">\n{$ret}\n</form>\n";
	}
}

global $wgMessageCache;
SpecialPage::addPage( new LuceneSearch );
$wgMessageCache->addMessage("searchnumber", "<strong>Results $1-$2 of $3</strong> &mdash; ");
$wgMessageCache->addMessage("lucenepowersearchtext", "
Search in namespaces:\n
$1\n
Search for $3 $9");

} # End of extension function
} # End of invocation guard
?>

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
if (defined('MEDIAWIKI')) {
$wgExtensionFunctions[] = "wfLuceneSearch";

define('LS_PER_PAGE', 10);

function wfLuceneSearch() {
global $IP;
require_once( "$IP/includes/SpecialPage.php" );

class LuceneSearch extends SpecialPage
{
	function LuceneSearch() {
		SpecialPage::SpecialPage("Search");
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgTitle;

		$this->setHeaders();

		$q = $wgRequest->getText( 'search' );
		$ok = htmlspecialchars( wfMsg( "ok" ) );

		$r = $this->doLuceneSearch($q);
		$numresults = $r[0];
		$results = $r[1];
		$showresults = min(LS_PER_PAGE, count($results));
		$wgOut->setSubtitle(wfMsg('searchquery', htmlspecialchars($q)));
		$wgOut->addWikiText(wfMsg('searchnumber', $showresults, $numresults));
		$i = 0;
		foreach ($results as $result) {
			if ($i++ > LS_PER_PAGE)
				break;
			$wgOut->addWikiText("*[[:" . $result->getPrefixedText() . "]]<br/>");
		}
	}

	function doLuceneSearch( $query ) {
		global $wgLuceneHost, $wgLucenePort;
		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$conn = socket_connect($sock, $wgLuceneHost, $wgLucenePort);
		socket_write($sock, urlencode($query) . "\n");
		$results = array();

		$numresults = @socket_read($sock, 1024, PHP_NORMAL_READ);
		if ($numresults === FALSE)
			return array();
		$numresults = chop($numresults);

		while (($result = @socket_read($sock, 1024, PHP_NORMAL_READ)) != FALSE) {
			$result = chop($result);
			list($namespace, $title) = split(" ", $result);
			$fulltitle = Title::makeTitle($namespace, $title);
			$results[] = $fulltitle;
		}
		socket_close($sock);
		return array($numresults, $results);
	}
}

global $wgMessageCache;
SpecialPage::addPage( new LuceneSearch );
$wgMessageCache->addMessage("searchnumber", "Showing \$1 of \$2 results\n\n");

} # End of extension function
} # End of invocation guard
?>

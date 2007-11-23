<?php

if ( ! defined( 'MEDIAWIKI' ) )
	die();


class SpecialWhoIsWatching extends SpecialPage
{
	/**
	 * Constructor
	 */
	public function __construct() {
		SpecialPage::SpecialPage( 'SpecialWhoIsWatching' );
	}

	/**
	 * @see SpecialPage::getDescription
	 */
	function getDescription() {
		return wfMsg( 'specialwhoiswatching' );
	}

	function execute($par) {
		global $wgRequest, $wgOut, $wgCanonicalNamespaceNames;
		wfLoadExtensionMessages( 'SpecialWhoIsWatching' );

		$this->setHeaders();
		$wgOut->setPagetitle(wfMsg('specialwhoiswatching'));

		$title = $wgRequest->getVal('page');
		if (!isset($title)) {
			$wgOut->addWikiText(wfMsg('specialwhoiswatchingusage'));
			return;
		}

		$ns = $wgRequest->getVal('ns');
		$ns = str_replace(' ', '_', $ns);
		if ($ns == '')
			$ns = NS_MAIN;
		else {
			foreach ( $wgCanonicalNamespaceNames as $i => $text ) {
				if (preg_match("/$ns/i", $text)) {
					$ns = $i;
					break;
				}
			}
		}

		$pageTitle = Title::makeTitle($ns, $title);
		$wiki_title = $pageTitle->getPrefixedText();
		$wiki_path = $pageTitle->getPrefixedDBkey();
		$wgOut->addWikiText("== ".sprintf(wfMsg('specialwhoiswatchingthepage'), "[[$wiki_path|$wiki_title]] =="));

		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'watchlist', 'wl_user', array('wl_namespace'=>$ns, 'wl_title'=>$title), __METHOD__);
		for ( $row = $dbr->fetchObject($res); $row; $row = $dbr->fetchObject($res)) {
			$u = User::newFromID($row->wl_user);
			$wgOut->addWikiText("[[:User:" . $u->getName() . "|" . $u->getName() . "]]");
		}
	}
}

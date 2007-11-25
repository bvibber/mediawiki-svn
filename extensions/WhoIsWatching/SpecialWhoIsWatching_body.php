<?php

class WhoIsWatching extends SpecialPage
{
    function WhoIsWatching() {
        SpecialPage::SpecialPage( 'WhoIsWatching' );
        self::loadMessages();
    }

    function loadMessages() {
        static $messagesLoaded = false;
        global $wgMessageCache;
        if ( !$messagesLoaded ) {
            $messagesLoaded = true;

            require( dirname( __FILE__ ) . '/SpecialWhoIsWatching.i18n.php' );
            foreach ( $allMessages as $lang => $langMessages ) {
                $wgMessageCache->addMessages( $langMessages, $lang );
            }
        }
        return true;
    }

    function execute($par) {
        global $wgRequest, $wgOut, $wgCanonicalNamespaceNames;

        $this->setHeaders();
        $wgOut->setPagetitle(wfMsg('whoiswatching'));

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

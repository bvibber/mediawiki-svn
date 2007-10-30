<?php

class SpecialWhoIsWatching extends SpecialPage
{
    function SpecialWhoIsWatching() {
        SpecialPage::SpecialPage("SpecialWhoIsWatching");
        self::loadMessages();
        return true;
    }

    function loadMessages() {
        static $messagesLoaded = false;
        global $wgMessageCache;
        if ($messagesLoaded) return;
            $messagesLoaded = true;

        
        $allMessages = array(
            'en' => array(
                'specialwhoiswatching'        => 'Who Is Watching a wiki page',
                'specialwhoiswatchingthepage' => 'Who is watching %s',
                'specialwhoiswatchingusage'   => 'This special page cannot be used on its own. Please use the 
[[MediaWiki:Number_of_watching_users_pageview]] to define an entry point to this special page.',
            )
        );
        
        foreach ( $allMessages as $lang => $langMessages ) {
            $wgMessageCache->addMessages( $langMessages, $lang );
        }
        return true;
    }

    function execute($par) {
        global $wgRequest, $wgOut, $wgCanonicalNamespaceNames;

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
            $wgOut->addWikiText("[[:user:" . $u->getName() . "|" . $u->getRealName() . "]]");
        }
    }
}

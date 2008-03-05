<?php

/**
* Allows sysops to unlock a page and all subpages of that page for anonymous editing
* via MediaWiki:Unlockedpages
*/

if(!defined('MEDIAWIKI')) {
	echo('This file is an extension to the MediaWiki software and cannot be used standalone');
	die(1);
}

$wgExtensionCredits['other'][] = array(
'name' => "EditSubpages",
'description' => "Allows sysops to unlock a page and all subpages of that page
for anonymous editing via [[MediaWiki:Unlockedpages]]",
'descriptionmsg' => 'editsubpages-desc',
'author' => "Ryan Schmidt",
'url' => "http://www.mediawiki.org/wiki/Extension:EditSubpages",
'version' => "1.2.1",
);

$wgHooks['UserGetRights'][] = 'EditSubpages';
$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['EditSubpages'] = $dir .'EditSubpages.i18n.php';
$wgGroupPermissions['*']['edit'] = false; //what's the point if they can edit to begin with?

function EditSubpages(&$user, &$aRights) {
	global $wgTitle;
	$pagename = $wgTitle->getText(); //name of page w/ spaces, not underscores
	if(!in_array('edit', $aRights)) {
		if($wgTitle->getNamespace == NS_MAIN) {
			$ns = ''; //for easier testing
		} else {
			$ns = $wgTitle->getNsText(); //namespace
		}
		if(!$wgTitle->isTalkPage()) {
			$nstalk = $wgTitle->getTalkNsText();
		} else {
			$nstalk = '';
		}
		if($ns == '') {
			$text = $pagename;
		} else {
			$text = $ns . ":" . $pagename;
		}
		if($nstalk == '') {
			$talktext = $pagename;
		} else {
			$talktext = $nstalk . ":" . $pagename;
		}
		
		$pages = explode ("\n", wfMsg ('unlockedpages')); //grabs MediaWiki:Unlockedpages
		foreach($pages as $value) {
			if( strpos( $value, '*' ) === false || strpos( $value, '*' ) !== 0 )
				continue; // "*" doesn't start the line, so treat it as a comment (aka skip over it)
			$value = trim( trim( trim( trim( $value ), "*[]" ) ), "*[]" );
			if ( $value == $text || strpos( $text, $value . '/' ) === 0 ) {
				$aRights = array_merge(  $aRights, array('edit', 'createpage', 'createtalk' ) );
				$aRights = array_unique($aRights);
				break;
			}
			$title = Title::newFromText($value);
			if(!$title->isTalkPage()) {
				$talk = $title->getTalkPage();
				$talkpage = $talk->getPrefixedText();
				if($talkpage == $talktext || $talkpage == $text || strpos( $talktext, $talkpage . '/' ) === 0 || strpos( $text, $talkpage . '/' ) === 0 ) {
					$aRights = array_merge($aRights, array('edit', 'createpage', 'createtalk'));
					$aRights = array_unique($aRights);
					break;
				}
			}
		}
	}
	return true; //Never EVER change this line! Needs to return true to continue hook processing
}
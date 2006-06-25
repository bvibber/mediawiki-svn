<?php

/**
 * Special page used to wipe the OBJECTCACHE table
 * I use it on test wikis when I'm fiddling about with things en masse that could be cached
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @licence Public domain
 */
 
if( defined( 'MEDIAWIKI' ) ) {
	
	require_once( 'SpecialPage.php' );
	$wgExtensionFunctions[] = 'efPurgeCache';
	$wgExtensionCredits['specialpage'][] = array( 'name' => 'PurgeCache', 'author' => 'Rob Church' );
	
	$wgAvailableRights[] = 'purgecache';
	$wgGroupPermissions['developer']['purgecache'] = true;
	
	function efPurgeCache() {
		global $wgMessageCache;
		$wgMessageCache->addMessage( 'purgecache', 'Purge cache' );
		SpecialPage::addPage( new PurgeCache() );
	}
	
	class PurgeCache extends SpecialPage {
	
		function PurgeCache() {
			SpecialPage::SpecialPage( 'PurgeCache', 'purgecache' );
		}
		
		function execute() {
			global $wgUser, $wgRequest, $wgOut;
			$this->setHeaders();
			if( $wgUser->isAllowed( 'purgecache' ) ) {
				if( $wgRequest->getCheck( 'purge' ) && $wgRequest->wasPosted() ) {
					$dbw =& wfGetDB( DB_MASTER );
					$dbw->delete( 'objectcache', '*', 'PurgeCache::execute' );
					$wgOut->addWikiText( 'The cache has been purged.' );
				} else {
					$wgOut->addWikiText( 'This will purge the cache tables.' );
					$wgOut->addHtml( $this->makeForm() );
				}
			} else {
				$wgOut->permissionRequired( 'purgecache' );
			}
		}
		
		function makeForm() {
			$self = $this->getTitle();
			$form  = wfOpenElement( 'form', array( 'method' => 'post', 'action' => $self->getLocalUrl() ) );
			$form .= wfElement( 'input', array( 'type' => 'submit', 'name' => 'purge', 'value' => 'Purge' ) );
			$form .= wfCloseElement( 'form' );
			return $form;
		}
	
	}
	
} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

?>
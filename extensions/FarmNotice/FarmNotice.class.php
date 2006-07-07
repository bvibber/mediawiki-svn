<?php

/**
 * Main class file for the FarmNotice extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */

class FarmNotice {

	var $sourceUrl = '';
	var $isSource = false;
	
	function __construct() {
		global $wgFarmNoticeIsSource, $wgFarmNoticeSourceUrl;
		$this->sourceUrl = $wgFarmNoticeSourceUrl;
		$this->isSource = $wgFarmNoticeIsSource;
		$this->initialiseHooks();
	}
	
	function initialiseHooks() {
		global $wgHooks;
		$wgHooks['SiteNoticeAfter'][] = array( &$this, 'showNotice' );
		if( $this->isSource )
			$wgHooks['ArticleSaveComplete'][] = array( &$this, 'invalidateCaches' );
	}
	
	function showNotice( &$notice ) {
		$extra = $this->fetchNotice();
		if( $extra )
			$notice = $extra . $notice;
	}
	
	function fetchNotice() {
		global $wgOut, $wgContLanguageCode;
		$msg = wfMsgForContent( 'farmnotice' );
		if( $msg == '&lt;farmnotice&gt;' || $msg == '-' ) {
			# If we're the source stop here; there is no message
			if( $this->isSource ) {
				return false;
			} else {
				# Check cache
				global $wgMemc;
				$key = $this->getCacheKey( $wgContLanguageCode );
				$msg = $wgMemc->get( $key );
				if( $msg ) {
					return $msg;
				} else {
					# Go and fetch it from the source
					$msg = Http::get( $this->getSourceUrl( $wgContLanguageCode ) );
					if( $msg ) {
						# Parse it and cache it
						$msg = $wgOut->parse( $msg );
						$wgMemc->set( $key, $msg, 900 );
						return $msg;
					} else {
						# Cache a blank to stop pointless checks
						$wgMemc->set( $key, '', 900 );
						return false;
					}
				}
			}
		} else {
			# Allow local wikis to override
			# This also handles the case where we're the source
			return $wgOut->parse( $msg );
		} 
	}
	
	function invalidateCaches( &$title ) {
		if( $this->isSource ) {
			if( $title->getNamespace() == NS_MEDIAWIKI && preg_match( '/^Farmnotice/?(.*)?/', $title->getText(), $matches ) ) {
				global $wgMemc;
				$lang = isset( $matches[1] ) ? $matches[1] : 'en';
				$wgMemc->delete( $this->getCacheKey( $lang ) );
			}
		}
	}
	
	function getSourceUrl( $lang ) {
		$msg = $lang == 'en' ? 'MediaWiki:Farmnotice' : 'MediaWiki:Farmnotice/' . $lang;
		return $this->sourceUrl . '?title=' . $msg . '&action=raw';
	}
	
	function getCacheKey( $lang ) {
		return "all:farmnotice:{$lang}";
	}

}

?>
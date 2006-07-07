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
		global $wgMemc, $wgOut, $wgContLanguageCode;
		# Attempt the local cache first
		$lkey = $this->getLocalCacheKey();
		$msg = $wgMemc->get( $lkey );
		if( $msg ) {
			# Hit!
			return $msg;
		} else {
			# No local cache, so check there isn't an uncached local version
			$msg = wfMsgForContent( 'farmnotice' );
			if( $msg == '&lt;farmnotice&gt;' || $msg == '-' ) {
				# No local override
				# If we're the source, stop here; there is no message
				if( $this->isSource ) {
					return false;
				} else {
					# Check cache
					global $wgMemc;
					$key = $this->getGlobalCacheKey( $wgContLanguageCode );
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
				# Cache the local version
				$msg = $wgOut->parse( $msg );
				$wgMemc->set( $lkey, $msg, 900 );
				return $msg;
			} 
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
	
	function getGlobalCacheKey( $lang ) {
		return "all:farmnotice:{$lang}";
	}
	
	function getLocalCacheKey() {
		global $wgDBname;
		return "{$wgDBname}:farmnotice";
	}

}

?>
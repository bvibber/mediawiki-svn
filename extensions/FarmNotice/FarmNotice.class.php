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
		$wgHooks['ArticleSaveComplete'][] = array( &$this, 'invalidateCaches' );
	}
	
	function showNotice( &$notice ) {
		$extra = $this->fetchNotice();
		if( $extra )
			$notice = $extra . $notice;
	}
	
	function fetchNotice() {
		$msg = $this->fetchLocal();
		if( $msg ) {
			# We found it in the local cache; stop here
			return $msg;
		} else {
			if( !$this->isSource ) {
				# Check the global cache et al.
				$msg = $this->fetchGlobal();
				return $msg ? $msg : false;
			} else {
				# There's nothing to find
				return false;
			}
		}
	}
	
	/**
	 * Attempt to fetch the message from the local cache, in case it's overridden;
	 * if that doesn't work, check for an uncached local override and parse it and cache it
	 */
	function fetchLocal() {
		global $wgMemc;
		$msg = $wgMemc->get( $this->getLocalCacheKey() );
		if( $msg ) {
			# Found a parsed local override in shared RAM cache
			wfDebugLog( 'farmnotice', 'Retrieved message from local override cache' );
			return $msg;
		} else {
			$msg = wfMsgForContent( 'farmnotice' );
			if( $msg != '&lt;farmnotice&gt;' || $msg == '-' ) {
				# There's a local override we need to use; cache it
				wfDebugLog( 'farmnotice', 'Retrieved message from local override' );
				global $wgOut;
				$msg = $wgOut->parse( $msg );
				$wgMemc->set( $this->getLocalCacheKey(), $msg, 900 );
				return $msg;
			} else {
				# There's no override
				wfDebugLog( 'farmnotice', 'Failed local override' );
				return false;
			}
		}
	}
	
	/**
	 * Attempt to fetch the message from the global cache for this language
	 * If it's not there, attempt to fetch it from the source, and parse it and cache
	 */
	function fetchGlobal() {
		global $wgMemc, $wgContLanguageCode;
		$msg = $wgMemc->get( $this->getGlobalCacheKey( $wgContLanguageCode ) );
		if( $msg ) {
			# Cache hit
			wfDebugLog( 'farmnotice', 'Retrieved message from global cache' );
			return $msg == '#NONE#' ? '' : $msg; # We store #NONE# 'cause blank strings become false somewhere
		} else {
			# Attempt to fetch from remote
			$msg = Http::get( $this->getSourceUrl( $wgContLanguageCode ) );
			if( $msg ) {
				# Parse it and cache it
				wfDebugLog( 'farmnotice', 'Retrieved message from global source' );
				global $wgOut;
				$msg = $wgOut->parse( $msg );
				$wgMemc->set( $this->getGlobalCacheKey( $wgContLanguageCode ), $msg, 900 );
				return $msg;
			} else {
				# Nothing, but *cache a blank* to avoid needless HTTP hits
				wfDebugLog( 'farmnotice', 'No global source for message' );
				$wgMemc->set( $this->getGlobalCacheKey( $wgContLanguageCode ), '#NONE#', 900 );
			}
		}
	}
	
	/**
	 * If a farm notice page is edited, clear the appropriate cache; if we're the source,
	 * then we need to clear the global cache for the relevant language, otherwise, we need
	 * to clear the local override cache
	 *
	 * @param $title Title of the page that was edited
	 */
	function invalidateCaches( &$title ) {
		if( $title->getNamespace() == NS_MEDIAWIKI && preg_match( '/^Farmnotice/?(.*)?/', $title->getText(), $matches ) ) {
			global $wgMemc;
			if( $this->isSource ) {
				# Clear the global cache for this language
				wfDebugLog( 'farmnotice', 'Invalidating global cache' );
				$wgMemc->delete( $this->getCacheKey( isset( $matches[1] ) ? $matches[1] : 'en' ) );
			} else {
				# Clear the local override cache
				wfDebugLog( 'farmnotice', 'Invalidating local override cache' );
				$wgMemc->delete( $this->getLocalCacheKey() );
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
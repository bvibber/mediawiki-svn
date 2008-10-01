<?php

/**
 * Class representing a remote site (e.g. any wiki except the current, local or across the world)
 * This class and related functions should be regarded as EXPERIMENTAL! They are not finished, and are full of debugging calls.
 * 
 * @ingroup RemoteSite
 */
abstract class RemoteSite {

	// Basic information about target wiki, from siteinfo
	var $mMainpage = 'Main Page';
	var $mBase = '';
	var $mSitename = '';
	var $mVersion = '';
	var $mRevision = '';
	var $mCase = 'first-letter';
	var $mLicense = '';
	var $mLanguage = 'en';
	
	// Object to get wiki-related data from
	var $mInterwikiObject = null;
	
	var $mCaching = true; //Set to false to disable caching, for debugging and used in LocalSite (as caching handled elsewhere)

	/**
	 * Fetch a RemoteSite object for a given interwiki prefix
	 * 
	 * @param $iwprefix The interwiki prefix (e.g. wikipedia:) for the site we want to access 
	 * @return RemoteSite An instance of RemoteSite, or null if we cannot find the scriptpath for it
	 */
	public static function getForInterwiki( $iwprefix ){
		global $wgLocalInterwiki;
		wfDebug('Fetching RemoteSite for ' . $iwprefix . "\n");
		$iwObj = Interwiki::fetch( $iwprefix );
		if( !$iwObj ){
			wfDebug( 'Failed to get Interwiki object' );
			return false;
		}
		if( $iwObj->mType != 'wiki_mw' ){ // Not supporting anything else except other mw as of the moment
			wfDebug( "Unsupported foreign site type " . $iwObj->mType . " \n" );
			return false;
		}
		if( $iwprefix == $wgLocalInterwiki ){ // Is local
			return LocalSite::get( $iwObj );
		}
		if( $iwObj->isLocal() ){ // Use local access (superfast)
			return LocalSite::get( $iwObj );
		}
		if( $iwObj->getWikiname() ){ // Use Database access (fast-ish)
			wfDebug("Passing onto DB \n");
			return DBSite::get( $iwObj );
		}elseif( $iwObj->getScriptURL() ){ // Use API (slow, HTTP)
			wfDebug("Passing onto API \n");
			return APISite::get( $iwObj );
		}else{
			return false;
		}
	}
	
	/**
	 * Gets whether or not a page exists
	 * 
	 * @param string title The string representation of a title, NOT a title object
	 * @return boolean Whether the page exists
	 */
	public function checkPageExistance( $title ){
		global $wgMemc;
		$title = str_replace( " ", "_", $title );
		$key = $this->memcKey( 'exists', $title );
		$memc = $wgMemc->get( $key );
		if( !$memc || !$this->mCaching ){
			wfDebug("Have to query wiki for existance of $title, not in cache (or cache disabled) :-(\n");
			$exists = $this->checkPageExistanceInternal( $title );
			$wgMemc->add( $key, ( $exists ? 'yes' : 'no' ), 3600 );
		}else{
			wfDebug("Existance of $title is in cache, :-)\n");
			$exists = ( $memc == 'yes' );
		}
		
		return $exists;
	}
	
	/**
	 * Fetches the siteinfo into member variables
	 * 
	 * @return boolean True on success, False on failure
	 */
	public function fetchSiteinfo(){
		if( $this->mBase ){
			// Already fetched, ignore
			return true;
		}else{
			global $wgMemc;
			$key = $this->memcKey( 'siteinfo' );
			$data = $wgMemc->get( $key );
			if( !$this->mCaching || !$data ){
				wfDebug("Siteinfo for " . $this->mInterwikiObject->mPrefix . " not cached (or not caching) :-( \n");
				$data = $this->fetchSiteinfoInternal();
				$wgMemc->add( $key, $data, 86400 );
			}else{
				wfDebug("Managed to get " . $this->mInterwikiObject->mPrefix . " siteinfo from cache :-) \n");
			}
			if( !$data ){
				wfDebug("Failed getting data");
				return false;
			}
			
			$this->mMainpage = $data['mainpage'];
			$this->mBase = $data['base'];
			$this->mSitename = $data['sitename'];
			$this->mVersion = substr( $data['generator'], strpos( $data['generator'], ' ' ) + 1 );
			if( isset( $data['rev'] ) ){
				$this->mRevision = $data['rev'];
			}
			$this->mCase = $data['case'];
			$this->mLicense = $data['rights'];
			$this->mLanguage = $data['lang'];
			return true;
		}
	}
	
	/**
	 * Actually query the target wiki for the siteinfo
	 * 
	 * @return array Associative array of values, with keys based on general siteinfo from API
	 */
	protected abstract function fetchSiteinfoInternal();
	
	/**
	 * Actually query the target wiki for the siteinfo
	 *
	 * @param $title string The page to check existance of
	 * @return boolean Whether the page exists or not
	 */
	protected abstract function checkPageExistanceInternal( $title );
	
	/**
	 * Get's a key for use in caching responses.
	 * 
	 * @param $type string The type of result we are caching (e.g. exists or siteinfo)
	 * @param $item string The item we are caching (e.g. the page or user name)
	 * @return string A key ready for passing to $wgMemc functions
	 */
	protected function memcKey($type, $item = ''){
		return wfMemcKey('remotesite', get_class($this), $this->mInterwikiObject->mPrefix, $type, $item);
	}
}

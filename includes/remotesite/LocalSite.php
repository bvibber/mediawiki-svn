<?php

/**
 * The current site, used if for some reason we want to use these APIs to access ourselves
 * This means that we dont have to check if interwiki links etc are pointing locally, as the RemoteSite will auto point them here
 * @ingroup RemoteSite
 */
class LocalSite extends RemoteSite {

	/**
	 * Fetch an APISite object for a given URL
	 * @param $url A scriptpath url, with $1 instead of a script name (e.g. http://en.wikipedia.org/w/$1)
	 * @return APISite An instance of APISite, or null if we cannot find the scriptpath for it
	 */
	public static function get( $iwObj ){
		global $wgLocalInterwiki;
		$site = new LocalSite();
		$site->mInterwikiObject = $iwObj;
		$site->mCaching = 0;
		return $site;
	}

	protected function fetchSiteinfoInternal(){
		global $wgSitename, $wgVersion, $IP, $wgCapitalLinks, $wgRightsText, $wgLanguageCode;
		$data = array();
		$mainPage = Title :: newFromText(wfMsgForContent('mainpage'));
		$data['mainpage'] = $mainPage->getPrefixedText();
		$data['base'] = $mainPage->getFullUrl();
		$data['sitename'] = $wgSitename;
		$data['generator'] = "MediaWiki $wgVersion";
		$svn = SpecialVersion::getSvnRevision( $IP );
		if( $svn ) {
			$data['rev'] = $svn;
		}else{
			$data['rev'] = '0';
		}
		$data['case'] = $wgCapitalLinks ? 'first-letter' : 'case-sensitive';
		$data['rights'] = $wgRightsText;
		$data['lang'] = $wgLanguageCode;
		
		return $data;
	}

	protected function checkPageExistanceInternal( $title ){
		$title = Title::newFromText( $title );
		return $title->exists();
	}

}
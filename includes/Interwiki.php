<?php
/**
 * @file
 * Interwiki table entry
 */

/**
 * The interwiki class
 * All information is loaded on creation when called by Interwiki::fetch( $prefix ).
 * All work is done on slave, because this should *never* change (except during schema updates etc, which arent wiki-related)
 */
class Interwiki {

	// Cache - removed in LRU order when it hits limit
	protected static $smCache = array();
	const CACHE_LIMIT = 100; // 0 means unlimited, any other value is max number of entries.

	public $mPrefix, $mURL, $mScriptURL, $mWikiname, $mType, $mLocal, $mTrans;

	function __construct( $prefix = null, $url = '', $scripturl = '', $wikiname = '', $type = '', $local = 0, $trans = 0 )
	{
		$this->mPrefix = $prefix;
		$this->mURL = $url;
		$this->mScriptURL = $scripturl;
		$this->mWikiname = $wikiname;
		$this->mType = $type;
		$this->mLocal = $local;
		$this->mTrans = $trans;
	}

	/**
	 * Fetch an Interwiki object
	 * 
	 * @return Interwiki Object, or null if not valid
	 * @param $prefix string Interwiki prefix to use
	 */
	static public function fetch( $prefix ) {
		if( isset( self::$smCache[$prefix] ) ){
			return self::$smCache[$prefix];
		}
		global $wgInterwikiCache;
		if ($wgInterwikiCache) {
			return Interwiki::getInterwikiCached( $key );
		}
		$iw = new Interwiki;
		if(! $iw->load( $prefix ) ){
			return false;
		}
		if( self::CACHE_LIMIT && count( self::$smCache ) >= self::CACHE_LIMIT ){
			array_shift( self::$smCache );
		}
		self::$smCache[$prefix] = &$iw;
		return $iw;
	}
	
	/**
	 * Fetch interwiki prefix data from local cache in constant database.
	 *
	 * @note More logic is explained in DefaultSettings.
	 *
	 * @param $key \type{\string} Database key
	 * @return \type{\string} URL of interwiki site
	 */
	protected static function getInterwikiCached( $key ) {
		global $wgInterwikiCache, $wgInterwikiScopes, $wgInterwikiFallbackSite;
		static $db, $site;

		if (!$db)
			$db=dba_open($wgInterwikiCache,'r','cdb');
		/* Resolve site name */
		if ($wgInterwikiScopes>=3 and !$site) {
			$site = dba_fetch('__sites:' . wfWikiID(), $db);
			if ($site=="")
				$site = $wgInterwikiFallbackSite;
		}
		$value = dba_fetch( wfMemcKey( $key ), $db);
		if ($value=='' and $wgInterwikiScopes>=3) {
			/* try site-level */
			$value = dba_fetch("_{$site}:{$key}", $db);
		}
		if ($value=='' and $wgInterwikiScopes>=2) {
			/* try globals */
			$value = dba_fetch("__global:{$key}", $db);
		}
		if ($value=='undef')
			$value='';
		$s = new Interwiki( $key );
		if ( $value != '' ) {
			list( $local, $url ) = explode( ' ', $value, 2 );
			$s->mURL = $url;
			$s->mLocal = (int)$local;
		}else{
			return false;
		}
		if( self::CACHE_LIMIT && count( self::$smCache ) >= self::CACHE_LIMIT ){
			array_shift( self::$smCache );
		}
		self::$smCache[$prefix] = &$s;
		return $s;
	}

	/**
	 * Get the DB object
	 *
	 * @return Database
	 */
	function &getDB(){
		$db = wfGetDB( DB_SLAVE );
		return $db;
	}

	/**
	 * Load interwiki from the DB
	 *
	 * @param $prefix The interwiki prefix
	 * @return bool The prefix is valid
	 *
	 */
	function load( $prefix ) {
		global $wgMemc;
		$key = wfMemcKey( 'interwiki', $prefix );
		$mc = $wgMemc->get( $key );
		if( $mc && is_array( $mc ) && $this->loadFromArray( $mc ) ){ // is_array is hack for old keys
			wfDebug("Succeeded\n");
			return true;
		}
		
		$db =& $this->getDB();
		$res = $db->resultObject( $db->select( 'interwiki', '*', array( 'iw_prefix' => $prefix ),
			__METHOD__ ) );
		if ( $this->loadFromResult( $res ) ) {
			$mc = array( 'url' => $this->mURL, 'scripturl' => $this->mScriptURL, 'wikiname' => $this->mWikiname, 'type' => $this->mType, 'local' => $this->mLocal, 'trans' => $this->mTrans );
			$wgMemc->add( $key, $mc );
			return true;
		}
		return false;
	}

	/**
	 * Fill in member variables from an array (e.g. memcached result)
	 *
	 * @return bool Whether everything was there
	 * @param $res ResultWrapper Row from the interwiki table
	 */
	function loadFromArray( $mc ) {
		if( isset( $mc['url'] ) && isset( $mc['scripturl'] ) && isset( $mc['wikiname'] ) && isset( $mc['type'] ) && isset( $mc['local'] ) && isset( $mc['trans'] ) ){
			$this->mURL = $mc['url'];
			$this->mScriptURL = $mc['scripturl'];
			$this->mWikiname = $mc['wikiname'];
			$this->mType = $mc['type'];
			$this->mLocal = $mc['local'];
			$this->mTrans = $mc['trans'];
			return true;
		}
		return false;
	}
	
	/**
	 * Fill in member variables from a result wrapper
	 *
	 * @return bool Whether there was a row there
	 * @param $res ResultWrapper Row from the interwiki table
	 */
	function loadFromResult( ResultWrapper $res ) {
		$ret = false;
		if ( 0 != $res->numRows() ) {
			# Get first entry
			$row = $res->fetchObject();
			$this->initFromRow( $row );
			$ret = true;
		}
		$res->free();
		return $ret;
	}

	/**
	 * Given a database row from the interwiki table, initialize
	 * member variables
	 *
	 * @param $row ResultWrapper A row from the interwiki table
	 */
	function initFromRow( $row ) {
		$this->mPrefix = $row->iw_prefix;
		$this->mURL = $row->iw_url;
		$this->mScriptURL = $row->iw_scripturl;
		$this->mWikiname = $row->iw_wikiname;
		$this->mType = $row->iw_type;
		$this->mLocal = $row->iw_local;
		$this->mTrans = $row->iw_trans;
	}
	
	/** 
	 * Get the URL for a particular title (or with $1 if no title given)
	 * 
	 * @param $title string What text to put for the article name
	 * @return string The URL
	 */
	function getURL( $title = null ){
		$url = $this->mURL;
		if( $title != null ){
			$space = $this->getSpacesCharacter();
			$title = str_replace( " ", $space, $title );
			$title = str_replace( "_", $space, $title );
			$url = str_replace( "$1", $title, $url );
		}
		return $url;
	}
	
	/** 
	 * Get the script URL for a particular script (or with $1 if no script given)
	 * 
	 * @param $title string What script you want (e.g. api.php, index.php, thumb.php)
	 * @return string The URL
	 */
	function getScriptURL( $script = null ){
		$url = $this->mScriptURL;
		if( $script != null ){
			$url = str_replace( "$1", $script, $url );
		}
		return $url;
	}
	
	/**
	 * Get the Wikiname of the wiki (name used in wfGetDB and related things)
	 * 
	 * @return string The Wikiname
	 */
	function getWikiname(){
		return $this->mWikiname;
	}
	
	/**
	 * Get whether the interwiki is local (e.g. this wiki)
	 * 
	 * @return boolean Whether the interwiki is local
	 */
	function isLocal(){
		return $this->mLocal;
	}
	
	/**
	 * Get whether users can transclude from this interwiki (also dependant on other config settings)
	 * 
	 * @return boolean Whether it is transcludable
	 */
	function isTranscludable(){
		return $this->mTrans;
	}
	
	/**
	 * Type of remote site
	 * gen_us
	 * 		Generic site (no special features like RemoteSite access), which uses underscores for spaces
	 * gen_plus
	 * 		Generic site (no special features like RemoteSite access), which uses plus signs for spaces (things like google)
	 * wiki_mw
	 * 		A MediaWiki wiki (full features, supports everything, uses underscores for spaces)
	*/
	function getType(){
		return $this->mType;
	}
	
	/** 
	 * Get what character/string to use for escaping spaces. Based off the type entry
	 * 
	 * @return string The character to use for spaces
	 */
	function getSpacesCharacter(){
		if( $this->mType == 'gen_plus' ){
			return '+';
		}else if( $this->mType == 'gen_us' || $this->mType == 'wiki_mw' ){
			return '_';
		}else{
			return '%20';
		}
	}

}

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

	// Cache - removes oldest entry when it hits limit
	protected static $smCache = array();
	const CACHE_LIMIT = 100; // 0 means unlimited, any other value is max number of entries.

	protected $mPrefix, $mURL, $mAPI, $mWikiID, $mLocal, $mTrans;

	public function __construct( $prefix = null, $url = '', $api = '', $wikiid = '', $local = 0, $trans = 0 ) {
		$this->mPrefix = $prefix;
		$this->mURL = $url;
		$this->mAPI = $api;
		$this->mWikiID = $wikiid;
		$this->mLocal = $local;
		$this->mTrans = $trans;
	}

	/**
	 * Check whether an interwiki prefix exists
	 *
	 * @param $prefix String: interwiki prefix to use
	 * @return Boolean: whether it exists
	 */
	static public function isValidInterwiki( $prefix ) {
		$result = self::fetch( $prefix );
		return (bool)$result;
	}

	/**
	 * Fetch an Interwiki object
	 *
	 * @param $prefix String: interwiki prefix to use
	 * @return Interwiki Object, or null if not valid
	 */
	static public function fetch( $prefix ) {
		global $wgContLang;
		if( $prefix == '' ) {
			return null;
		}
		$prefix = $wgContLang->lc( $prefix );
		if( isset( self::$smCache[$prefix] ) ) {
			return self::$smCache[$prefix];
		}
		global $wgInterwikiCache;
		if( $wgInterwikiCache ) {
			$iw = Interwiki::getInterwikiCached( $prefix );
		} else {
			$iw = Interwiki::load( $prefix );
			if( !$iw ) {
				$iw = false;
			}
		}
		if( self::CACHE_LIMIT && count( self::$smCache ) >= self::CACHE_LIMIT ) {
			reset( self::$smCache );
			unset( self::$smCache[ key( self::$smCache ) ] );
		}
		self::$smCache[$prefix] = $iw;
		return $iw;
	}

	/**
	 * Fetch interwiki prefix data from local cache in constant database.
	 *
	 * @note More logic is explained in DefaultSettings.
	 *
	 * @param $prefix String: interwiki prefix
	 * @return Interwiki object
	 */
	protected static function getInterwikiCached( $prefix ) {
		$value = self::getInterwikiCacheEntry( $prefix );

		$s = new Interwiki( $prefix );
		if ( $value != '' ) {
			// Split values
			list( $local, $url ) = explode( ' ', $value, 2 );
			$s->mURL = $url;
			$s->mLocal = (int)$local;
		} else {
			$s = false;
		}
		return $s;
	}

	/**
	 * Get entry from interwiki cache
	 *
	 * @note More logic is explained in DefaultSettings.
	 *
	 * @param $prefix String: database key
	 * @return String: the entry
	 */
	protected static function getInterwikiCacheEntry( $prefix ) {
		global $wgInterwikiCache, $wgInterwikiScopes, $wgInterwikiFallbackSite;
		static $db, $site;

		wfDebug( __METHOD__ . "( $prefix )\n" );
		if( !$db ) {
			$db = CdbReader::open( $wgInterwikiCache );
		}
		/* Resolve site name */
		if( $wgInterwikiScopes>=3 && !$site ) {
			$site = $db->get( '__sites:' . wfWikiID() );
			if ( $site == '' ) {
				$site = $wgInterwikiFallbackSite;
			}
		}

		$value = $db->get( wfMemcKey( $prefix ) );
		// Site level
		if ( $value == '' && $wgInterwikiScopes >= 3 ) {
			$value = $db->get( "_{$site}:{$prefix}" );
		}
		// Global Level
		if ( $value == '' && $wgInterwikiScopes >= 2 ) {
			$value = $db->get( "__global:{$prefix}" );
		}
		if ( $value == 'undef' )
			$value = '';

		return $value;
	}

	/**
	 * Load the interwiki, trying first memcached then the DB
	 *
	 * @param $prefix The interwiki prefix
	 * @return Boolean: the prefix is valid
	 */
	protected static function load( $prefix ) {
		global $wgMemc, $wgInterwikiExpiry;
		$key = wfMemcKey( 'interwiki', $prefix );
		$mc = $wgMemc->get( $key );
		$iw = false;
		if( $mc && is_array( $mc ) ) { // is_array is hack for old keys
			$iw = Interwiki::loadFromArray( $mc );
			if( $iw ) {
				return $iw;
			}
		}

		$db = wfGetDB( DB_SLAVE );

		$row = $db->fetchRow( $db->select( 'interwiki', '*', array( 'iw_prefix' => $prefix ),
			__METHOD__ ) );
		$iw = Interwiki::loadFromArray( $row );
		if ( $iw ) {
			$mc = array( 'iw_url' => $iw->mURL, 'iw_api' => $iw->mAPI, 'iw_local' => $iw->mLocal, 'iw_trans' => $iw->mTrans );
			$wgMemc->add( $key, $mc, $wgInterwikiExpiry );
			return $iw;
		}

		return false;
	}

	/**
	 * Fill in member variables from an array (e.g. memcached result, Database::fetchRow, etc)
	 *
	 * @param $mc Associative array: row from the interwiki table
	 * @return Boolean: whether everything was there
	 */
	protected static function loadFromArray( $mc ) {
		if( isset( $mc['iw_url'] ) && isset( $mc['iw_local'] ) && isset( $mc['iw_trans'] ) ) {
			$iw = new Interwiki();
			$iw->mURL = $mc['iw_url'];
			$iw->mLocal = $mc['iw_local'];
			$iw->mTrans = $mc['iw_trans'];
			$iw->mAPI = isset( $mc['iw_api'] ) ? $mc['iw_api'] : '';
			$iw->mWikiID = isset( $mc['iw_wikiid'] ) ? $mc['iw_wikiid'] : '';
			
			return $iw;
		}
		return false;
	}

	/**
	 * Get the URL for a particular title (or with $1 if no title given)
	 * 
	 * @param $title String: what text to put for the article name
	 * @return String: the URL
	 */
	public function getURL( $title = null ) {
		$url = $this->mURL;
		if( $title != null ) {
			$url = str_replace( "$1", $title, $url );
		}
		return $url;
	}

	/**
	 * Get the API URL for this wiki
	 * 
	 * @return String: the URL
	 */
	public function getAPI( ) {
		return $this->mAPI;
	}

	/**
	 * Get the DB name for this wiki
	 * 
	 * @return String: the DB name
	 */
	public function getWikiID( ) {
		return $this->mWikiID;
	}

	/**
	 * Is this a local link from a sister project, or is
	 * it something outside, like Google
	 *
	 * @return Boolean
	 */
	public function isLocal() {
		return $this->mLocal;
	}

	/**
	 * Can pages from this wiki be transcluded?
	 * Still requires $wgEnableScaryTransclusion
	 *
	 * @return Boolean
	 */
	public function isTranscludable() {
		return $this->mTrans;
	}

	/**
	 * Get the name for the interwiki site
	 *
	 * @return String
	 */
	public function getName() {
		$key = 'interwiki-name-' . $this->mPrefix;
		$msg = wfMsgForContent( $key );
		return wfEmptyMsg( $key, $msg ) ? '' : $msg;
	}

	/**
	 * Get a description for this interwiki
	 *
	 * @return String
	 */
	public function getDescription() {
		$key = 'interwiki-desc-' . $this->mPrefix;
		$msg = wfMsgForContent( $key );
		return wfEmptyMsg( $key, $msg ) ? '' : $msg;
	}
	
	

	/**
	 * Transclude an interwiki link.
	 * TODO: separate in interwikiTranscludeFromDB & interwikiTranscludeFromAPI according to the iw type 
	 */
	public static function interwikiTransclude( $title ) {
		
		global $wgEnableScaryTranscluding;

		if ( !$wgEnableScaryTranscluding ) {
			return wfMsg('scarytranscludedisabled');
		}
		
		// If we have a wikiID, we will use it to get an access to the remote database
		// if not, we will use the API URL to retrieve the data through a HTTP Get
		
		$wikiID = $title->getTransWikiID( );
		$transAPI = $title->getTransAPI( );
		
		if ( $wikiID !== '') {
		
			$finalText = self::fetchTemplateFromDB( $wikiID, $title->getNamespace(), $title->getDBkey());
			$subTemplates = self::fetchSubTemplatesListFromDB( $wikiID, $title->getNamespace(), $title->getDBkey());
			
			foreach ($subTemplates as $template) {
				$listSubTemplates.=$template['namespace'].':'.$template['title']."\n";
				$list2.="<h2>".$template['title']."</h2>\n<pre>".self::fetchTemplateFromDB( $wikiID, $template['namespace'], $template['title'])."</pre>";
			}

		} else if( $transAPI !== '' ) {
	
			$url1 = $transAPI."?action=query&prop=revisions&titles=$fullTitle&rvprop=content&format=json";
	
			if ( strlen( $url1 ) > 255 ) {
				return wfMsg( 'Interwiki-transclusion-url-too-long' );
			}
			
			$text = self::fetchTemplateHTTPMaybeFromCache( $url1 );
			
			$fullTitle = $title->getNsText().':'.$title->getText();
	
			$url2 = $transAPI."?action=parse&text={{".$fullTitle."}}&prop=templates&format=json";
			
			$get = Http::get( $url2 );
			$myArray = FormatJson::decode($get, true);
			
			if ( ! empty( $myArray['parse'] )) {
				$templates = $myArray['parse']['templates'];
			}
			
			
			// TODO: The templates are retrieved one by one.
			// We should split the templates in two groups: up-to-date and out-of-date
			// Only the second group would be retrieved through the API or DB request
			for ($i = 0 ; $i < count( $templates ) ; $i++) {
				$newTitle = $templates[$i]['*'];
				
				$url = $transAPI."?action=query&prop=revisions&titles=$newTitle&rvprop=content&format=json";
				
				$listSubTemplates.= $newTitle."\n";
				$list2.="<h2>".$newTitle."</h2>\n<pre>".self::fetchTemplateHTTPMaybeFromCache( $url )."</pre>";
	
			}
			
			$finalText = "$url1\n$url2\n$text";
			
		} else {
			return wfMsg( 'Interwiki-transclusion-failed' );
		}

		return "<h2>$fullTitle</h2><pre>$finalText</pre> List of templates: <pre>".$listSubTemplates.'</pre>' . $list2;
	}
	
	public static function fetchTemplateFromDB ( $wikiID, $namespace, $DBkey ) {
		
		try {
			$dbr = wfGetDb( DB_SLAVE, array(), $wikiID );
		} catch (Exception $e) {
			return wfMsg( 'Failed-to-connect-the-distant-DB' );
		}
		
		$fields = array('old_text', 'page_id');
		$res = $dbr->select(
			array( 'page', 'revision', 'text' ),
			$fields,
			array( 'rev_id=page_latest',
			       'page_namespace' => $namespace,
			       'page_title'     => $DBkey,
			       'page_id=rev_page',
			       'rev_text_id=old_id'),
			null,
			array( 'LIMIT' => 1 )
			);

		$obj = $dbr->resultObject( $res );

		if ( $obj ) {		
			$row = $obj->fetchObject();
			$obj->free();
			
			if( $row ) {
				$res = new Revision( $row );
				$articleID = $res->mTextRow->page_id;
				$text = $articleID."\n".$res->mTextRow->old_text;
			}
		}
				
		return $text;
	}
	
	public static function fetchSubTemplatesListFromDB ( $wikiID, $namespace, $DBkey ) {
		
		try {
			$dbr = wfGetDb( DB_SLAVE, array(), $wikiID );
		} catch (Exception $e) {
			return wfMsg( 'Failed-to-connect-the-distant-DB' );
		}
		
		$fields = array('tl_namespace', 'tl_title');
		$res = $dbr->select(
			array( 'page', 'templatelinks' ),
			$fields,
			array( 'page_namespace' => $namespace,
			       'page_title'     => $DBkey,
			       'tl_from=page_id' )
			);
		
		$obj = $dbr->resultObject( $res );

		if ( $obj ) {
			
			$listTemplates = array();
			
			while ($row = $obj->fetchObject() ) {
				$listTemplates[] = array( 'namespace' => $row->tl_namespace, 'title' => $row->tl_title );
			}
			$obj->free();
		}
		
		return $listTemplates;
	}
	
	public static function fetchTemplateHTTPMaybeFromCache( $url ) {
		global $wgTranscludeCacheExpiry;
		$dbr = wfGetDB( DB_SLAVE );
		$tsCond = $dbr->timestamp( time() - $wgTranscludeCacheExpiry );
		$obj = $dbr->selectRow( 'transcache', array('tc_time', 'tc_contents' ),
				array( 'tc_url' => $url, "tc_time >= " . $dbr->addQuotes( $tsCond ) ) );

		if ( $obj ) {
			return $obj->tc_contents;
		}
	
		$get = Http::get( $url );
		
		$content = FormatJson::decode( $get, true );
			
		if ( ! empty($content['query']['pages']) ) {
			
			$page = array_pop( $content['query']['pages'] );
			$text = $page['revisions'][0]['*'];
			
		} else	{
			
			return wfMsg( 'scarytranscludefailed', $url );
			
		}
	
		$dbw = wfGetDB( DB_MASTER );
		$dbw->replace( 'transcache', array('tc_url'), array(
			'tc_url' => $url,
			'tc_time' => $dbw->timestamp( time() ),
			'tc_contents' => $text)
		);
				
		return $text;
	}	

	
	
	
}

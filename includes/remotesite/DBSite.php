<?php

/**
 * Remote site which we access via the DB classes
 * @ingroup RemoteSite
 */
class DBSite extends RemoteSite {

	protected $dbType, $dbServer, $dbUser, $dbPassword, $dbName, $tablePrefix, $dbConn, $dbInfo;

	/**
	 * Fetch a DBSite object for a DB string
	 * @param $url A scriptpath url, with $1 instead of a script name (e.g. http://en.wikipedia.org/w/$1)
	 * @return APISite An instance of APISite, or null if we cannot find the scriptpath for it
	 */
	public static function getForDB( $dbstring, $shortname = null ){
		
		$site = new DBSite();
		$site->mShortname = $shortname;
		$site->mScriptURL = null;
		$site->dbInfo = $dbstring;
		
		list( $site->dbType, $site->dbServer, $site->dbUser, $site->dbPassword, $site->dbName, $site->tablePrefix ) = explode( "|", $dbstring );
		
		return $site;
	}

	function getDB() {
		if ( !isset( $this->dbConn ) ) {
			$class = 'Database' . ucfirst( $this->dbType );
			$this->dbConn = new $class( $this->dbServer, $this->dbUser,
				$this->dbPassword, $this->dbName, false, 0,
				$this->tablePrefix );
		}
		return $this->dbConn;
	}

	protected function fetchSiteinfoInternal(){
		//$data = $this->doAPIQuery( 'query', array( 'meta' => 'siteinfo', 'maxage' = 86400, 'smaxage' = 86400 ), 'GET' );
		
		
		return null;//$data['query']['general'];
	}

	protected function checkPageExistanceInternal( $title ){
		$db = $this->getDB();
		$title = Title::newFromText( $title );
		$result = $db->selectField( 'page', 'page_id', array( 'page_namespace' => $title->getNamespace(), 'page_title' => $title->getDbKey() ), __METHOD__ );
		return $result;
	}

}
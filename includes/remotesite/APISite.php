<?php

/**
 * Remote site which we access via the API
 * @ingroup RemoteSite
 */
class APISite extends RemoteSite {

	var $mApiURL = '';

	/**
	 * Fetch an APISite object for a given URL
	 * @param $url A scriptpath url, with $1 instead of a script name (e.g. http://en.wikipedia.org/w/$1)
	 * @return APISite An instance of APISite, or null if we cannot find the scriptpath for it
	 */
	public static function get( $iwObj ){
		$apiurl = $iwObj->getScriptURL( 'api.php' );
		
		$site = new APISite();
		$site->mInterwikiObject = $iwObj;
		$site->mApiURL = $apiurl;
		
		return $site;
	}

	protected function fetchSiteinfoInternal(){
		$data = $this->doAPIQuery( 'query', array( 'meta' => 'siteinfo', 'maxage' => 86400, 'smaxage' => 86400 ), 'GET' );
		
		return $data['query']['general'];
	}
	
	/**
	 * Perform an API query against the target wiki. Only use when there is not a wrapper available
	 * @param $url A scriptpath url, with $1 instead of a script name (e.g. http://en.wikipedia.org/w/$1)
	 * @return RemoteSite An instance of RemoteSite, or null if we cannot find the scriptpath for it
	 * @protected
	 */
	protected function doAPIQuery( $action, $params, $method = 'GET' ){
		$query = wfArrayToCGI( $params );
		$format = 'php';
		if( function_exists( 'json_decode' ) ){
			$format = 'json';
		}
		$url = $this->mApiURL . "?action=" . $action . "&format=" . $format;
		$opts = array();
		if( $method = 'GET' ){
			$url .= "&" . $query;
		}else{
			$opts['CURLOPT_POSTFIELDS'] = $query;
		}
		
		$response = Http::request( $method, $url, 5, $opts, ( $method == 'POST' ) );
		if( !$response ){
			return false;
		}
		if( $format == 'json' ){
			return json_decode( $response, 1 );
		}else if( $format == 'php' ){
			return unserialize( $response );
		}
	}

	protected function checkPageExistanceInternal( $title ){
		$data = $this->doAPIQuery( 'query', array( 'prop' => 'info', 'titles' => $title ), 'GET' );
		$data = $data['query']['pages'];
		$data = array_pop($data);
		if( isset( $data['missing'] ) ){
			return false;
		}else{
			return true;
		}
	}

}
<?php

/**
 * Class for accessing the MogileFS file system
 * Allows creation of classes, retrieval and storage of files, querying
 * existence of a file, etc.
 */


class MogileFS {
	var $socket;
	var $error;
	
	/**
	 * Constructor
	 * 
	 * TODO
	 */
	function MogileFS( $domain,
			   $hosts = null,
			   $root = '' )
	{
		global $wgMogileTrackers;

		if ($hosts == null) {
			if ($wgMogileTrackers!=null) {
				$hosts=$wgMogileTrackers;
			} else {
				die("wgMogileTrackers empty, please define hosts");
			}
		}

		$this->domain = $domain;
		$this->hosts  = $hosts;
		$this->root   = $root;
		$this->error  = '';
	}

	/**
	 * Factory method
	 * Creates a new MogileFS object and tries to connect to a
	 * mogilefsd.
	 *
	 * Returns false if it can't connect to any mogilefsd.
	 *
	 * TODO
	 */
	function NewMogileFS( $domain,
			      $hosts = null,
			      $root = '' )
	{
		$mfs = new MogileFS( $domain, $hosts, $root );
		return ( $mfs->connect() ? $mfs : false );
	}

	/**
	 * Connect to a mogilefsd
	 * Scans through the list of daemons and tries to connect one.
	 */
	function connect()
	{
		foreach ( $this->hosts as $host ) {
			list($ip,$port)=split(':',$host,2);
			if ($port==null)
				$port=7001;
			$this->socket = fsockopen( $ip, $port );
			if ( $this->socket ) {
				break;
			}
		}

		return $this->socket;
	}

	/**
	 * Send a request to mogilefsd and parse the result.
	 * @private
	 */
	function doRequest( $cmd )
	{
		if ( ! $this->socket ) {
			$this->connect();
		}

		fwrite( $this->socket, $cmd . "\n" );

		$line = fgets( $this->socket );
		#print $line;
		$words = explode( ' ', $line );
		if ( $words[0] == 'OK' ) {
			parse_str( trim( $words[1] ), $result );
		} else {
			$result = false;
			$this->error = join(" ",$words);
		}

		return $result;
	}

	/**
	 * Return a list of domains
	 */
	function getDomains()
	{
		$res = $this->doRequest( 'GET_DOMAINS' );
		if ( ! $res ) {
			return false;
		}
		$domains = array();
		for ( $i=1; $i <= $res['domains']; $i++ ) {
			$dom = 'domain'.$i;
			$classes = array();
			for ( $j=1; $j<=$res[$dom.'classes']; $j++ ) {
				$classes[$res[$dom.'class'.$j.'name']] = $res[$dom.'class'.$j.'mindevcount'];
			}
			$domains[] = array( 'name' => $res[$dom],
					'classes' => $classes );
		}
		return $domains;
	}

	/**
	 * Get an array of paths
	 */
	function getPaths( $key )
	{
		$res = $this->doRequest( "GET_PATHS domain={$this->domain}&key={$key}" );
		unset( $res['paths'] );
		return $res;
	}

	/**
	 * Get a file from the file service and return it as a string
	 * TODO
	 */
	function getFileData( $key )
	{
		$paths = $this->getPaths( $key );
		if ($paths == false)
			return false;
		foreach ( $paths as $path ) {
			$fh = fopen( $path, 'r' );
			$contents = '';

			if ( $fh ) {
				while (!feof($fh)) {
					$contents .= fread($fh, 8192);
				}
				fclose( $fh );
				return $contents;
			}
		}
		return false;
	}

	/**
	 * Get a file from the file service and send it directly to stdout
	 * uses fpassthru()
	 * TODO
	 */
	function getFileDataAndSend( $key )
	{
		$paths = $this->getPaths( $key );
		if (!$paths) 
			return false;
		foreach ( $paths as $path ) {
			$fh = fopen( $path, 'r' );

			if ( $fh ) {
				$success = fpassthru( $fh );
			}
			fclose( $fh );
			return $success;
		}
		return false;
	}

	/**
	 * Save a file to the MogileFS
	 * TODO
	 */
	function saveFile( $key, $class, $filename )
	{
		$res = $this->doRequest( "CREATE_OPEN domain={$this->domain}&key={$key}&class={$class}" );

		if ( ! $res )
			return false;

		if ( preg_match( '/^http:\/\/([a-z0-9.-]*):([0-9]*)\/(.*)$/', $res['path'], $matches ) ) {
			$host = $matches[1];
			$port = $matches[2];
			$path = $matches[3];

			// $fout = fopen( $res['path'], 'w' );
			$fin = fopen( $filename, 'r' );
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_PUT,1);
			curl_setopt($ch,CURLOPT_URL, $res['path']);
			curl_setopt($ch,CURLOPT_VERBOSE, 0);
			curl_setopt($ch,CURLOPT_INFILE, $fin);
			curl_setopt($ch,CURLOPT_INFILESIZE, filesize($filename));
			curl_setopt($ch,CURLOPT_TIMEOUT, 4);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if(!curl_exec($ch)) {
				$this->error=curl_error($ch);
				curl_close($ch);
				return false;
			}
			curl_close($ch);

			$res = $this->doRequest( "CREATE_CLOSE domain={$this->domain}&key={$key}&class={$class}&devid={$res['devid']}&fid={$res['fid']}&path={$res['path']}" );
			return true;
		}
	}
}

####
####
#### Testing rules
####
####
if( !defined( 'MEDIAWIKI' ) ) {
	$wgMogileTrackers=array('10.0.0.1:7001','10.0.0.2:7003');
	$mfs = MogileFS::NewMogileFS('test');
	$mfs->saveFile('testkey','normal','testfile');
	$mfs->getFileDataAndSend( 'testkey' );
}

?>

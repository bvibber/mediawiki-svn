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
			   $hosts = array( array( 'host'=>'localhost', 'port'=>'6001' ) ),
			   $root = '' )
	{
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
			      $hosts = array( array( 'host'=>'localhost', 'port'=>'6001' ) ),
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
			$this->socket = fsockopen( $host['host'], $host['port'] );
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

		$words = explode( ' ', $line );
		if ( $words[0] == 'OK' ) {
			parse_str( trim( $words[1] ), $result );
		} else {
			$result = false;
			$this->error = $words;
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

			$fout = 
		$fout = fopen( $res['path'], 'w' );
		$fin = fopen( $filename, 'r' );

		if ( ! $fout || ! $fin ) {
			return false;
		}

		while ( !feof( $fin ) ) {
			fwrite( $fout, fread( $fin, filesize( $filename ) ) );
		}
		fclose( $fout );
		fclose( $fin );

		$res = $this->doRequest( "CREATE_CLOSE domain={$this->domain}&key={$key}&class={$class}&devid={$res['devid']}&fid={$res['fid']}&path={$res['path']}" );

		return true;
	}


		


}

####
####
####         T E S T 
####
####
$mfs = MogileFS::NewMogileFS( 'en' );
$mfs->getFileDataAndSend( '100x100_frankfurt.jpg' );
$mfs->saveFile( 'jeluf', 'thumbnail', '/tmp/jeluf' );

?>

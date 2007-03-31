<?php

/**
 * A 404 handler to render thumbnails via a cluster of scaler servers
 */

require( dirname( __FILE__ ) . '/WebStoreCommon.php' );
$IP = dirname( realpath( __FILE__ ) ) . '/../..';
chdir( $IP );
require( './includes/WebStart.php' );

class WebStore404Handler extends WebStoreCommon {
	function execute() {
		global $wgUploadBaseUrl, $wgUploadPath, $wgScriptPath, $wgServer;

		// Determine URI
		if ( $_SERVER['REQUEST_URI'][0] == '/' ) {
			$url = ( !empty( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' ) . 
				$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} else {
			$url = $_SERVER['REQUEST_URI'];
		}

		if ( $wgUploadBaseUrl ) {
			$thumbBase = $wgUploadBaseUrl . $wgUploadPath . '/thumb';
		} else {
			$thumbBase = $wgServer . $wgUploadPath . '/thumb';
		}
		if ( substr( $url, 0, strlen( $thumbBase ) ) != $thumbBase ) {
			// Not a thumbnail URL
			header( 'X-Debug: not thumb' );
			$this->real404();
			return true;
		}

		$rel = substr( $url, strlen( $thumbBase ) + 1 ); // plus one for slash
		// Check for path traversal
		if ( !$this->validateFilename( $rel ) ) {
			header( 'X-Debug: invalid path traversal' );
			$this->real404();
			return false;
		}

		if ( !preg_match( '!^(\w)/(\w\w)/([^/]*)/(page(\d*)-)?(\d*)px-([^/]*)$!', $rel, $parts ) ) {
			header( 'X-Debug: regex mismatch' );
			$this->real404();
			return false;
		}

		list( $all, $hash1, $hash2, $filename, $pagefull, $pagenum, $size, $fn2 ) = $parts;
		if( $filename != $fn2 && "$filename.png" != $fn2 ) {
			header( 'X-Debug: filename/fn2 mismatch' );
			$this->real404();
			return false;
		}

		// Open the destination temporary file
		$dstPath = "{$this->publicDir}/thumb/$rel";
		$tmpPath = "$dstPath.temp.MW_WebStore";
		$tmpFile = @fopen( $tmpPath, 'a+' );
		if ( !$tmpFile ) {
			$this->error( 500, 'webstore_temp_open' );
			return false;
		}

		// Get an exclusive lock
		if ( !flock( $tmpFile, LOCK_EX | LOCK_NB ) ) {
			wfDebug( "Waiting for shared lock..." );
			if ( !flock( $tmpFile, LOCK_SH ) ) {
				wfDebug( "failed\n" );
				$this->error( 500, 'webstore_temp_lock' );
				return false;
			}
			wfDebug( "OK\n" );
			// Close it and see if it appears at $dstPath
			fclose( $tmpFile );
			if ( $this->windows ) {
				// Rename happens after unlock on windows, so we have to wait for it
				usleep( 200000 );
			}
			if ( file_exists( $dstPath ) ) {
				// Stream it out
				$magic = MimeMagic::singleton();
				$type = $magic->guessMimeType( $dstPath );
				$dstFile = fopen( $dstPath, 'r' );
				if ( !$dstFile ) {
					$this->error( 500, 'webstore_dest_open' );
					return false;
				}

				$this->streamFile( $dstFile, $type );
				fclose( $dstFile );
				return true;
			} else {
				// Something went wrong, only the forwarding process knows what
				$this->real404();
				return true;
			}
		}

		// Send an image scaling request to a host in the scaling cluster

		$error = false;
		$errno = false;
		do {
			$this->sourceFile = @fopen( "{$this->publicDir}/$hash1/$hash2/$filename", 'r' );
			if ( !$this->sourceFile ) {
				header( 'X-Debug: no source' );
				$this->real404();
				break;
			}

			$this->boundary = dechex( mt_rand() ) . dechex( mt_rand() ) . dechex( mt_rand() );
			
			// Form parameters
			$this->formDataPrefix .= "--{$this->boundary}\r\n" .
				// Width
				"Content-Disposition: form-data; name=\"width\"\r\n\r\n" .
				urlencode( $size ) .
				"\r\n--{$this->boundary}\r\n" .
				// Height
				"Content-Disposition: form-data; name=\"page\"\r\n\r\n" .
				urlencode( $pagenum ). 
				"\r\n--{$this->boundary}\r\n" .
				// Data file
				"Content-Disposition: form-data; name=\"data\"; filename=\"" . urlencode( $filename ) . "\"\r\n" .
				"Content-Transfer-Encoding: binary\r\n\r\n";


			$scalerUrl = "$wgServer$wgScriptPath/extensions/WebStore/inplace-scaler.php";

			// Pick a server
			$servers = $this->scalerServers;
			shuffle( $servers );
			foreach( $servers as $server ) {
				if ( strpos( $server, ':' ) === false ) {
					$server .= ':80';
				}
				// Retry connection once, standard application-level hack for overlong linux SYN timeout values
				for ( $i = 0; $i < 2; $i++ ) {
					// Truncate the file after possible previous failed attempts
					ftruncate( $tmpFile, 0 );
					fseek( $this->sourceFile, 0 );
					$this->prefixSent = false;
					$this->suffixSent = false;

					// Send the request to the scaler
					$curl = curl_init( $scalerUrl );
					curl_setopt( $curl, CURLOPT_POST, true );
					curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 
						"Content-Type: multipart/form-data; boundary={$this->boundary}",
						'Transfer-Encoding: chunked',
						'TE: chunked',
					));
					curl_setopt( $curl, CURLOPT_PROXY, $server );
					curl_setopt( $curl, CURLOPT_FILE, $tmpFile );
					curl_setopt( $curl, CURLOPT_READFUNCTION, array( $this, 'curlRead' ) );
					curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, $this->httpConnectTimeout );
					curl_setopt( $curl, CURLOPT_TIMEOUT, $this->httpOverallTimeout );

					wfDebug( "Sending curl request: $scalerUrl on $server\n" );
					if ( !curl_exec( $curl ) ) {
						$errno = curl_errno( $curl );
						$error = curl_error( $curl );
						if ( $errno == CURLE_COULDNT_CONNECT ) {
							// Try again
							curl_close( $curl );
							continue;
						}
					} else {
						$errno = false;
						$responseCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
						$contentType = curl_getinfo( $curl, CURLINFO_CONTENT_TYPE );
					}
					curl_close( $curl );
					break;
				}
				// Try next server unless that one was successful
				if ( !$errno ) {
					break;
				}
			}

			if ( $errno ) {
				break;
			}

			if ( $responseCode != 200 ) {
				# Pass through image scaler errors (but don't keep the file)
				$info = self::$httpErrors[$responseCode];
				header( "HTTP/1.1 $responseCode $info" );
				$this->streamFile( $tmpFile );
				$this->closeAndDelete( $tmpFile, $tmpPath );
				$tmpFile = false;
				break;
			}

			// Request completed successfully.
			// Move the file to its destination
			if ( $this->windows ) {
				fclose( $tmpFile );
				// Wait for other processes to close the file if rename fails
				for ( $i = 0; $i < 10; $i++ ) {
					if ( !rename( $tmpPath, $dstPath ) ) {
						usleep( 50000 );
					} else {
						break;
					}
				}
				$tmpFile = fopen( $dstPath, 'r' );
				if ( !$tmpFile ) {
					$this->error( 500, 'webstore_dest_open' );
				}
			} else {
				rename( $tmpPath, $dstPath );
				// Unlock so that other processes can start streaming the file out
				flock( $tmpFile, LOCK_UN );
			}

			// Stream it ourselves
			$this->streamFile( $tmpFile, $contentType );
		} while (false);

		if ( $this->sourceFile ) {
			fclose( $this->sourceFile );
		}
		if ( $tmpFile ) {
			fclose( $tmpFile );
		}

		if ( $errno ) {
			$this->error( 500, 'webstore_curl', $error );
			return false;
		}

		return true;
	}

	function streamFile( $file, $contentType = false ) {
		if ( $contentType ) {
			header( "Content-Type: $contentType" );
		}
		fseek( $file, 0 );
		fpassthru( $file );
		return true;
	}

	function real404() {
		if ( $this->fallback404 ) {
			require( $this->fallback404 );
		} else {
			$this->error( 404, 'webstore_404' );
		}
	}

	function curlRead( $curl, $inFile, $maxLength ) {
		if ( !$this->prefixSent ) {
			$data = $this->formDataPrefix;
			$this->prefixSent = true;
		} elseif ( feof( $this->sourceFile ) ) {
			if ( !$this->suffixSent ) {
				$data = "\r\n--{$this->boundary}--\r\n";
				$this->suffixSent = true;
			} else {
				$data = '';
			}
		} else {
			$data = fread( $this->sourceFile, $maxLength - 30 );
		}
		return $data;
	}
}

$h = new WebStore404Handler;
$h->execute();

?>

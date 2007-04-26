<?php

// Requires PEAR XML_RPC module

require_once( 'PEAR.php' );
require_once( 'XML/RPC.php' );

$mwSearchUpdateHost = 'localhost';
$mwSearchUpdatePort = 8321;   # HTTP default port is 8321
$mwSearchUpdateDebug = false;

// the interface
$mwSearchUpdater = new HTTPMWSearchUpdater; 

/** Delegate class to either HttpMWSearchUpdater or XMLRPCMWSearchUpdater */
class MWSearchUpdater {
	/**
	 * Queue a request to update a page in the search index.
	 *
	 * @param string $dbname
	 * @param Title $title
	 * @param string $text
	 * @return bool
	 * @static
	 */
	function updatePage( $dbname, $title, $text, $isredirect=0) {
		global $mwSearchUpdater;
		$mwSearchUpdater->updatePage( $dbname, $title, $text, $isredirect);
	}

	/**
	 * Queue a request to delete a page from the search index.
	 *
	 * @param string $dbname
	 * @param Title $title
	 * @return bool
	 * @static
	 */
	function deletePage( $dbname, $title ) {
		global $mwSearchUpdater;
		$mwSearchUpdater->deletePage( $dbname, $title );
	}

	/**
	 * Get a brief bit of status info on the update daemon.
	 * @return string
	 * @static
	 */
	function getStatus() {
		global $mwSearchUpdater;
		return $mwSearchUpdater->getStatus();
	}
	
	/**
	 * Request that the daemon start applying updates if it's stopped.
	 * @return bool
	 * @static
	 */
	function start() {
		global $mwSearchUpdater;
		$mwSearchUpdater->start();
	}
	
	/**
	 * Request that the daemon stop applying updates and close open indexes.
	 * @return bool
	 * @static
	 */
	function stop() {
		global $mwSearchUpdater;
		$mwSearchUpdater->stop();
	}
	
	/**
	 * Request that the daemon stop applying updates and close open indexes.
	 * @return bool
	 * @static
	 */
	function quit() {
		global $mwSearchUpdater;
		$mwSearchUpdater->quit();
	}

	/**
	 * Request that the daemon flush and reopen all indexes, without changing
	 * the global is-running state.
	 * @return bool
	 * @static
	 */
	function flushAll() {
		global $mwSearchUpdater;
		$mwSearchUpdater->flushAll();
	}
	
	/**
	 * Request that the daemon flush and reopen all indexes, without changing
	 * the global is-running state, and that indexes should be optimized when
	 * closed.
	 * @return bool
	 * @static
	 */
	function optimize() {
		global $mwSearchUpdater;
		$mwSearchUpdater->optimize();
	}
	
	/**
	 * Request that the daemon flush and reopen a given index, without changing
	 * the global is-running state.
	 * @return bool
	 * @static
	 */
	function flush( $dbname ) {
		global $mwSearchUpdater;
		$mwSearchUpdater->flush($dbname);
	}

	/**
	 * Request that the daemon to make snapshot of all indexes
	 * the global is-running state.
	 * @return bool
	 * @static
	 */
	function snapshot() {
		global $mwSearchUpdater;
		$mwSearchUpdater->snapshot();
	}
	
}

class HttpMWSearchUpdater{

	/**
	 * Call remote method via the special http server
	 * URI: /method?param1=value1&param2=value2 
	 *      (all values urlencoded);
	 */
	function invokeRemote( $uri, $content = null){
		global $mwSearchUpdateHost, $mwSearchUpdatePort, $mwSearchUpdateDebug;
		//global $socket;
		$host = $mwSearchUpdateHost;
		$port = $mwSearchUpdatePort;

		if($content === null){
			$req =
				"POST $uri HTTP/1.0\r\n".
				"\r\n"; 			
		} else{
			$contentLength = strlen($content);
			$req =
				"POST $uri HTTP/1.0\r\n".
				"Content-Type: application/octet-stream\r\n".
				"Content-Length: $contentLength\r\n\r\n".
				"$content"; 
		}
		// open socket
		$socket = fsockopen($host, $port, $errno, $errstr, 10);
		if(!$socket){
			$debug = "MWSearchUpdater.php: Error opening socket\n";
			wfDebug($debug);
			if( $mwSearchUpdateDebug )
				print($debug);
			return null;
		}

		// send request
		fwrite($socket, $req);

		// read server reply
		$headers = "";
		while ($str = trim(fgets($socket, 4096)))
			$headers .= "$str\n";

		$body = "";
		while (!feof($socket))
		$body .= fgets($socket, 4096); 

		// no keep-alive, just close the connection
		fclose($socket);

		// process headers, just read the http code 
		$headerLines = explode("\n",$headers);
		$code = $headerLines[0];
		$bits = explode(' ',$code);

		// report if there was an error
		if($bits[1]!="200"){
			$debug = "MWSearchUpdater.php: Error invoking remote procedure with uri $uri, got: ".$bits[1].' '.$bits[2];
		
			wfDebug( $debug );
			if( $mwSearchUpdateDebug ) {
				echo $debug;
			}
		}
		// get reply if any
		$ret = $body;

		return $ret;
	}

	function updatePage( $dbname, $title, $text, $isredirect=0 ) {
		$ns = $title->getNamespace();
		$titleText = urlencode($title->getText());
		if($text == null) $text = "";
		return $this->invokeRemote("/updatePage?db=$dbname&namespace=$ns&title=$titleText&isredirect=$isredirect",$text);
	}

	function addNGram( $dbname, $title, $text ) {
		$ns = $title->getNamespace();
		$titleText = urlencode($title->getText());
		if($text == null) $text = "";
		return $this->invokeRemote("/addNgram?db=$dbname&namespace=$ns&title=$titleText",$text);

	}

	function flushNGram( $dbname) {
		return $this->invokeRemote("/flushNgram?db=$dbname");;
	}


	function deletePage( $dbname, $title ) {
		$ns = $title->getNamespace();
		$titleText = urlencode($title->getText());
		return $this->invokeRemote("/deletePage?db=$dbname&namespace=$ns&title=$titleText");;
	}


	function getStatus() {
		return $this->invokeRemote("/getStatus");
	}
	
	function start() {
		return $this->invokeRemote("/start");
	}
	
	function stop() {
		return $this->invokeRemote("/stop");
	}
	
	function quit() {
		return $this->invokeRemote("/quit");
	}

	function flushAll() {
		return $this->invokeRemote("/flushAll");
	}

	function snapshot() {
		return $this->invokeRemote("/makeSnapshots");
	}

	
	function optimize() {
		return $this->invokeRemote("/optimize");
	}
	
	function flush( $dbname ) {
		return $this->invokeRemote("/flush?db=$dbname");
	}
}



class XMLRPCMWSearchUpdater {
	/**
	 * Queue a request to update a page in the search index.
	 *
	 * @param string $dbname
	 * @param Title $title
	 * @param string $text
	 * @return bool
	 * @static
	 */
	function updatePage( $dbname, $title, $text, $isRedirect ) {
		return XMLRPCMWSearchUpdater::sendRPC( 'searchupdater.updatePage',
			array( $dbname, $title, $text, $isRedirect) );
	}


	/**
	 * Queue a request to update a page in the search index,
	 * including metadata fields.
	 *
	 * @param string $dbname
	 * @param Title $title
	 * @param string $text
	 * @param array $metadata
	 * @return bool
	 * @static
	 */
	function updatePageData( $dbname, $title, $text, $metadata ) {
		$translated = array();
		foreach( $metadata as $pair ) {
			list( $key, $value ) = explode( '=', $pair, 2 );
			$translated[] = array( 'Key' => $key, 'Value' => $value );
		}
		return XMLRPCMWSearchUpdater::sendRPC( 'searchupdater.updatePageData',
			array( $dbname, $title, $text, $translated ) );
	}

	/**
	 * Queue a request to delete a page from the search index.
	 *
	 * @param string $dbname
	 * @param Title $title
	 * @return bool
	 * @static
	 */
	function deletePage( $dbname, $title ) {
		return XMLRPCMWSearchUpdater::sendRPC( 'searchupdater.deletePage',
			array( $dbname, $title ) );
	}

	/**
	 * Get a brief bit of status info on the update daemon.
	 * @return string
	 * @static
	 */
	function getStatus() {
		return XMLRPCMWSearchUpdater::sendRPC( 'searchupdater.getStatus' );
	}
	
	/**
	 * Request that the daemon start applying updates if it's stopped.
	 * @return bool
	 * @static
	 */
	function start() {
		return XMLRPCMWSearchUpdater::sendRPC( 'searchupdater.start' );
	}
	
	/**
	 * Request that the daemon stop applying updates and close open indexes.
	 * @return bool
	 * @static
	 */
	function stop() {
		return XMLRPCMWSearchUpdater::sendRPC( 'searchupdater.stop' );
	}
	
	/**
	 * Request that the daemon stop applying updates and close open indexes.
	 * @return bool
	 * @static
	 */
	function quit() {
		return XMLRPCMWSearchUpdater::sendRPC( 'searchupdater.quit' );
	}

	/**
	 * Request that the daemon flush and reopen all indexes, without changing
	 * the global is-running state.
	 * @return bool
	 * @static
	 */
	function flushAll() {
		return XMLRPCMWSearchUpdater::sendRPC( 'searchupdater.flushAll' );
	}
	
	/**
	 * Request that the daemon flush and reopen all indexes, without changing
	 * the global is-running state, and that indexes should be optimized when
	 * closed.
	 * @return bool
	 * @static
	 */
	function optimize() {
		return XMLRPCMWSearchUpdater::sendRPC( 'searchupdater.optimize' );
	}
	
	/**
	 * Request that the daemon flush and reopen a given index, without changing
	 * the global is-running state.
	 * @return bool
	 * @static
	 */
	function flush( $dbname ) {
		return XMLRPCMWSearchUpdater::sendRPC( 'searchupdater.flush',
			array( $dbname ) );
	}
	
	/**
	 * @access private
	 * @static
	 */
	function outParam( $param ) {
		if( is_object( $param ) && is_a( $param, 'Title' ) ) {
			return new XML_RPC_Value(
				array(
					'namespace' => new XML_RPC_Value( $param->getNamespace(), 'int' ),
					'title'      => new XML_RPC_Value( $param->getText(), 'string' ) ),
				'struct' );
		} elseif( is_string( $param ) ) {
			return new XML_RPC_Value( $param, 'string' );
		} elseif( is_array( $param ) ) {
			$type = 'array';
			if( count( $param ) ) {
				$keys = array_keys( $param );
				if( $keys[0] !== 0 ) {
					$type = 'struct';
				}
			}
			$translated = array_map( array( 'XMLRPCMWSearchUpdater', 'outParam' ), $param );
			return new XML_RPC_Value( $translated, $type );
		} else {
			return new WikiError( 'XMLRPCMWSearchUpdater::sendRPC given bogus parameter' );
		}
	}
	
	/**
	 * @access private
	 * @static
	 */
	function sendRPC( $method, $params=array() ) {
		global $mwSearchUpdateHost, $mwSearchUpdatePort, $mwSearchUpdateDebug;
		$client = new XML_RPC_Client( '/SearchUpdater', $mwSearchUpdateHost, $mwSearchUpdatePort );
		if( $mwSearchUpdateDebug ) {
			$client->debug = true;
		}
		
		$rpcParams = array_map( array( 'XMLRPCMWSearchUpdater', 'outParam' ), $params );
		
		$message = new XML_RPC_Message( $method, $rpcParams );
		wfSuppressWarnings();
		$start = wfTime();
		$result = $client->send( $message );
		$delta = wfTime() - $start;
		wfRestoreWarnings();
		
		$debug = sprintf( "XMLRPCMWSearchUpdater::sendRPC for %s took %0.2fms\n",
			$method, $delta * 1000.0 );
		wfDebug( $debug );
		if( $mwSearchUpdateDebug ) {
			echo $debug;
		}
		
		if( !is_object( $result ) ) {
			return new WikiError( "Unknown XML-RPC error" );
		} elseif( $result->faultCode() ) {
			return new WikiError( $result->faultCode() . ': ' . $result->faultString() );
		} else {
			$value = $result->value();
			return $value->getval();
		}
	}
}


?>

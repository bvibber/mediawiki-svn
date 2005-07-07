<?php

// Requires PEAR XML_RPC module

require_once( 'PEAR.php' );
require_once( 'XML/RPC.php' );

$mwSearchUpdateHost = 'localhost';
$mwSearchUpdatePort = 8124;

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
	function updatePage( $dbname, $title, $text ) {
		return MWSearchUpdater::sendRPC( 'searchupdater.updatePage',
			array( $dbname, $title, $text ) );
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
		return MWSearchUpdater::sendRPC( 'searchupdater.deletePage',
			array( $dbname, $title ) );
	}

	/**
	 * Get a brief bit of status info on the update daemon.
	 * @return string
	 * @static
	 */
	function getStatus() {
		return MWSearchUpdater::sendRPC( 'searchupdater.getStatus' );
	}
	
	/**
	 * Request that the daemon start applying updates if it's stopped.
	 * @return bool
	 * @static
	 */
	function start() {
		return MWSearchUpdater::sendRPC( 'searchupdater.start' );
	}
	
	/**
	 * Request that the daemon stop applying updates and close open indexes.
	 * @return bool
	 * @static
	 */
	function stop() {
		return MWSearchUpdater::sendRPC( 'searchupdater.stop' );
	}
	
	/**
	 * Request that the daemon stop applying updates and close open indexes.
	 * @return bool
	 * @static
	 */
	function quit() {
		return MWSearchUpdater::sendRPC( 'searchupdater.quit' );
	}
	
	/**
	 * @access private
	 * @static
	 */
	function sendRPC( $method, $params=array() ) {
		global $mwSearchUpdateHost, $mwSearchUpdatePort;
		$client = new XML_RPC_Client( '/SearchUpdater', $mwSearchUpdateHost, $mwSearchUpdatePort );
		
		$rpcParams = array();
		foreach( $params as $param ) {
			if( is_object( $param ) && is_a( $param, 'Title' ) ) {
				$rpcParams[] = new XML_RPC_Value(
					array(
						'Namespace' => new XML_RPC_Value( $param->getNamespace(), 'int' ),
						'Text'      => new XML_RPC_Value( $param->getText(), 'string' ) ),
					'struct' );
			} elseif( is_string( $param ) ) {
				$rpcParams[] = new XML_RPC_Value( $param, 'string' );
			} else {
				return new WikiError( 'MWSearchUpdater::sendRPC given bogus parameter' );
			}
		}
		
		$message = new XML_RPC_Message( $method, $rpcParams );
		wfSuppressWarnings();
		$result = $client->send( $message );
		wfRestoreWarnings();
		
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

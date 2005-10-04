<?php

/**
 * OAI-PMH update harvester extension for MediaWiki 1.4+
 *
 * Copyright (C) 2005 Brion Vibber <brion@pobox.com>
 * http://www.mediawiki.org/
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 *
 * This initial implementation is somewhat special-purpose;
 * it doesn't support foreign repositories using Dublin Core
 * and only performs basic updates and deletions. It's suitable
 * for updating rows in a cur table from a master wiki and
 * logging changes.
 *
 * PHP's domxml extension is required.
 *
 * @todo Charset conversion for Latin-1 wikis
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die();
}

# Need shared code...
require_once( 'OAIRepo.php' );
require_once( 'maintenance/refreshLinks.inc' );

global $oaiSourceRepository;
global $oaiAgentExtra;

/**
 * Set to the repository URL,
 */
$oaiSourceRepository = null;
$oaiUserAgent = 'MediaWiki/OAI 0.1';

class OAIError {
	function OAIError( $message ) {
		$this->_message = $message;
		$this->_stacktrace = debug_backtrace();
	}
	
	function toString() {
		return $this->_message . "\n";
	}
	
	/**
	 * @static
	 */
	function isError( $object ) {
		return is_a( $object, 'OAIError' );
	}
}

class OAIHarvester {
	function OAIHarvester( $baseURL ) {
		$this->_baseURL = $baseURL;
	}
	
	
	function listUpdates( $from, $callback ) {
		$token = false;
		do {
			if( $token ) {
				echo "-> resuming at $token\n";
				$params = array(
					'verb'           => 'ListRecords',
					'metadataPrefix' => 'mediawiki',
					'resumptionToken' => $token );
			} else {
				$params = array(
					'verb'           => 'ListRecords',
					'metadataPrefix' => 'mediawiki',
					'from'           => oaiDatestamp( $from ) );
			}
			$xml = $this->callRepo( $params );
			
			if( OAIError::isError( $xml ) )
				return $xml;
	
			if( !( $doc = domxml_open_mem( $xml ) ) )
				return new OAIError( "Invalid XML returned from OAI repository." );
	
			#echo $doc->dump_mem();
			
			$xp = $doc->xpath_new_context();
			xpath_register_ns( $xp, 'oai', 'http://www.openarchives.org/OAI/2.0/' );
			
			if( $errors = $this->checkResponseErrors( $xp ) )
				return $errors;
			
			$resultSet = $xp->xpath_eval( '/oai:OAI-PMH/oai:ListRecords/oai:record' );
			foreach( $resultSet->nodeset as $node ) {
				$record = OAIUpdateRecord::newFromNode( $node );
				if( OAIError::isError( $record ) )
					return $record;
				call_user_func( $callback, $record );
				unset( $record );
			}
			
			$tokenSet = $xp->xpath_eval( '/oai:OAI-PMH/oai:ListRecords/oai:resumptionToken' );
			$token = ( $tokenSet && isset( $tokenSet->nodeset[0] ) )
				? $tokenSet->nodeset[0]->get_content()
				: false;

			$doc->free();
			unset( $tokenSet );
			unset( $resultSet );
			unset( $xp );
			unset( $doc );
			unset( $xml );
		} while( $token );
		return true;
	}
	
	/**
	 * Check for OAI errors, and return a formatted exception or null.
	 *
	 * @param XPathObject $xp
	 * @return OAIError or null if no errors
	 */
	function checkResponseErrors( $xp ) {
		$errors = $xp->xpath_eval( '/oai:OAI-PMH/oai:error' );
		if( !$errors )
			return new OAIError( "Doesn't seem to be an OAI document...?" );
		
		if( !count( $errors->nodeset ) )
			return null;
		
		return new OAIError(
			implode( "\n",
				array_map(
					array( &$this, 'oaiErrorMessage' ),
					$errors->nodeset ) ) );
	}
	
	/**
	 * Format a single OAI error response as a text message
	 * @param  DomNode $node
	 * @return string
	 */
	function oaiErrorMessage( $node ) {
		$code = $node->get_attribute( 'code' );
		$text = $node->get_content();
		return "$code: $text";
	}
	
	function throwOAIErrors( $errors ) {
		$message = array();
		foreach( $errors as $node ) {
			$message[] = $node->x;
		}
	}
	
	/**
	 * Traverse a MediaWiki-format record set, sending an associative array
	 * of data to a callback function.
	 * @param DomNode $node     the <records> node
	 * @param mixed   $callback
	 */
	function traverseRecords( &$recordSet, $callback ) {
		for( $node = $recordSet->first_child; $node = $node->next_sibling(); $node ) {
			$data = $this->extractMediaWiki( $node );
			call_user_func( $callback, $data );
		}
	}
	
	function extractMediaWiki( $node ) {
		return array( 'everything' => 'testing' );
	}
	
	/**
	 * Contact the HTTP repository with a given set of parameters,
	 * and return the raw XML response data.
	 *
	 * @param  array  $params Associative array of parameters
	 * @return string         Raw XML response, or an OAIError
	 */
	function callRepo( $params ) {
		$resultCode = null;
		$url = $this->requestURL( $params );
		wfDebug( "OAIHarvest::callRepo() - calling to $url\n" );
		$xml = $this->fetchURL( $url, $resultCode );
		if( $resultCode == 200 ) {
			return $xml;
		} else {
			return new OAIError( "Repository returned HTTP result code $resultCode" );
		}
	}
	
	function requestURL( $params ) {
		return $this->_baseURL . '?' . wfArrayToCGI( $params );
	}
	
	function userAgent() {
		global $oaiAgentExtra;
		$agent = 'MediaWiki OAI Harvester 0.1 (http://www.mediawiki.org/)';
		if( $oaiAgentExtra ) {
			$agent .= ' ' . $oaiAgentExtra;
		}
		return $agent;
	}
	
	
	function fetchURL( $url, &$resultCode ) {
		if( !ini_get( 'allow_url_fopen' ) ) {
			return new OAIError( "Can't open URLs; must turn on allow_url_fopen" );
		}
		
		$uagent = ini_set( 'user_agent', $this->userAgent() );
		echo "Fetching: $url\n";
		$result = file_get_contents( $url );
		ini_set( 'user_agent', $uagent );
		
		# FIXME
		if( $result === false ) {
			$resultCode = 500;
		} else {
			$resultCode = 200;
		}
		return $result;
	}
	
	function fetchURLviaCURL( $url, &$resultCode ) {
		$fetch = curl_init( $url );
		if( defined( 'OAIDEBUG' ) ) {
			curl_setopt( $fetch, CURLOPT_VERBOSE, 1 );
		}
		# CURLOPT_TIMEOUT
		# CURLOPT_REFERER?
		curl_setopt( $fetch, CURLOPT_USERAGENT, $this->userAgent() );
		
		ob_start();
		$ok = curl_exec( $fetch );
		$result = ob_get_contents();
		ob_end_clean();
		
		$info = curl_getinfo( $fetch );
		if( !$ok ) {
			echo "Something went awry...\n";
			var_dump( $info );
			die();
		}
		curl_close( $fetch );
		
		$resultCode = $info['http_code']; # ????
		return $result;
	}
}



class OAIUpdateRecord {
	var $_page = array();
	
	function OAIUpdateRecord( $pageData ) {
		$this->_page = $pageData;
	}
	
	function getArticleId() {
		return IntVal( $this->_page['id'] );
	}
	
	function isDeleted() {
		return isset( $this->_page['deleted'] );
	}
	
	function getTitle() {
		return Title::newFromText( $this->_page['title'] );
	}
	
	function getTimestamp( $time ) {
		if( preg_match( '/^(\d\d\d\d)-(\d\d)-(\d\d)T(\d\d):(\d\d):(\d\d)Z$/', $time, $matches ) ) {
			return wfTimestamp( TS_MW,
				$matches[1] . $matches[2] . $matches[3] .
				$matches[4] . $matches[5] . $matches[6] );
		} else {
			return 0;
		}
	}
	
	function dump() {
		if( $this->isDeleted() ) {
			printf( "%14s %10d\n", '(deleted page)', $this->getArticleId() );
		} else {
			$title = $this->getTitle();
			if( $title ) {
				printf( "%s %10d [[%s]]\n",
					$this->getTimestamp( $this->_page['revisions'][0]['timestamp'] ),
					$this->getArticleId(),
					$title->getPrefixedText() );
			} else {
				printf( "*** INVALID TITLE on %d: \"%s\"\n",
					$this->getArticleId(),
					$this->_page['title'] );
			}
		}
	}
	
	function apply() {
		if( $this->isDeleted() ) {
			return $this->doDelete();
		}
		
		$title = $this->getTitle();
		if( is_null( $title ) ) {
			return new OAIError( sprintf(
				"Bad title for update to page #%d; cannot apply update: \"%s\"",
				$this->getArticleId(),
				$this->_page['title'] ) );
		}
		
		$id = 0;
		foreach( $this->_page['revisions'] as $revision ) {
			$id = $this->applyRevision( $revision );
		}
		
		fixLinksFromArticle( $id );
		
		if( isset( $this->_page['uploads'] ) ) {
			foreach( $this->_page['uploads'] as $upload ) {
				if( OAIError::isError( $err = $this->applyUpload( $upload ) ) )
					return $err;
			}
		}
		
		return true;
	}
	
	function applyRevision( $revision ) {
		$fname = 'OAIUpdateRecord::applyRevision';
		
		$title = $this->getTitle();
		$id = $this->getArticleId();
		$timestamp = $this->getTimestamp( $revision['timestamp'] );
		
		$dbw =& wfGetDB( DB_WRITE );
		$dbw->begin();
		
		/* Check for name conflicts */
		$existing = $dbw->selectField( 'cur', 'cur_id',
			array(
				'cur_namespace' => $title->getNamespace(),
				'cur_title'     => $title->getDbkey()
			), $fname );
		if( $existing && $existing != $id ) {
			echo "Hiding existing page [[" . $title->getPrefixedText() . "]] at id $existing\n";
			$dbw->update( 'cur',
				array( 'cur_title' => ' hidden@' . $existing ),
				array( 'cur_id' => $existing ),
				$fname );
		}
	
		/* And do it! */
		$present = $dbw->selectField( 'cur', 'cur_id', array( 'cur_id' => $id ), $fname );
		$redir = preg_match( '/^#redirect \[\[/i', $revision['text'] );
		$data = array(
			'cur_id' => $id,
			'cur_namespace'     => $title->getNamespace(),
			'cur_title'         => $title->getDbkey(),
			'cur_text'          => $revision['text'],
			'cur_comment'       => StrVal( @$revision['comment'] ),
			'cur_user'          => IntVal( @$revision['contributor']['id'] ),
			'cur_timestamp'     => $timestamp,
			'cur_minor_edit'    => isset( $revision['minor'] ) ? 1 : 0,
			'cur_counter'       => 0,
			'cur_restrictions'  => StrVal( @$revision['restrictions'] ),
			'cur_user_text'     => isset( $revision['contributor']['username'] )
								   ? $revision['contributor']['username']
								   : $revision['contributor']['ip'],
			'cur_is_redirect'   => $redir,
			'cur_is_new'        => $present ? 0 : 1,
			'cur_random'        => wfRandom(),
			'cur_touched'       => $dbw->timestamp(),
			'inverse_timestamp' => wfInvertTimestamp( $timestamp ) );
		if( $present ) {
			echo "UPDATING\n";
			$result = $dbw->update(
				'cur',
				$data,
				array( 'cur_id' => $id ),
				$fname );
		} else {
			# Insert new article
			echo "INSERTING\n";
			$result = $dbw->insert(
				'cur',
				$data,
				$fname );
		}
		$dbw->commit();
		
		return $id;
	}
	
	function applyUpload( $upload ) {
		$fname = 'WikiOAIUpdate::applyUpload';
		
		# FIXME: validate these files...
		if( strpos( $upload['filename'], '/' ) !== false
			|| strpos( $upload['filename'], '\\' ) !== false
			|| $upload['filename'] == ''
			|| $upload['filename'] !== trim( $upload['filename'] ) ) {
			return new OAIError( 'Invalid filename "' . $upload['filename'] . '"' );
		}
		
		$dbw =& wfGetDB( DB_MASTER );
		$data = array(
			'img_name'        => $upload['filename'],
			'img_size'        => IntVal( $upload['size'] ),
			'img_description' => $upload['comment'],
			'img_user'        => IntVal( @$upload['contributor']['id'] ),
			'img_user_text'   => isset( $revision['contributor']['username'] )
								   ? strval( $revision['contributor']['username'] )
								   : strval( $revision['contributor']['ip'] ),
			'img_timestamp'   => $dbw->timestamp( $this->getTimestamp( $upload['timestamp'] ) ) );
		
		$dbw->begin();
		echo "REPLACING image row\n";
		$dbw->replace( 'image', array( 'img_name' ), $data, $fname );
		$dbw->commit();
		
		return $this->downloadUpload( $upload );
	}
	
	function downloadUpload( $upload ) {
		global $wgDisableUploads;
		if( $wgDisableUploads ) {
			echo "Uploads disabled locally: NOT fetching URL '" .
				$upload['src'] . "'.\n";
			return true;
		}
		
		# We assume the filename has already been validated by code above us.
		$filename = wfImageDir( $upload['filename'] ) . '/' . $upload['filename'];
		
		$timestamp = wfTimestamp( TS_UNIX, $this->getTimestamp( $upload['timestamp'] ) );
		if( file_exists( $filename )
			&& filemtime( $filename ) == $timestamp
			&& filesize( $filename ) == $upload['size'] ) {
			echo "Local file $filename matches; skipping download.\n";
			return true;
		}
		
		if( !preg_match( '!^http://!', $upload['src'] ) )
			return new OAIError( 'Invalid image source URL "' . $upload['src'] . "'." );
		
		$input = fopen( $upload['src'], 'rb' );
		if( !$input ) {
			unlink( $filename );
			return new OAIError( 'Could not fetch image source URL "' . $upload['src'] . "'." );
		}
		
		if( file_exists( $filename ) ) {
			unlink( $filename );
		}
		if( !( $output = fopen( $filename, 'xb' ) ) ) {
			return new OAIError( 'Could not create local image file "' . $filename . '" for writing.' );
		}

		echo "Fetching " . $upload['src'] . " to $filename: ";
		while( !feof( $input ) ) {
			$buffer = fread( $input, 65536 );
			fwrite( $output, $buffer );
			echo ".";
		}
		fclose( $input );
		fclose( $output );
		
		touch( $filename, $timestamp );
		echo " done.\n";
		
		return true;
	}
	
	function doDelete() {
		$fname = 'OAIUpdateRecord::doDelete';
		$id = $this->getArticleId();
		
		echo "DELETING\n";
		
		/*
		$dbw =& wfGetDB( DB_WRITE );
		$dbw->begin();
		$dbw->delete( 'cur', array( 'cur_id' => $id ), $fname );
		$dbw->commit();
		*/
		$dbw =& wfGetDB( DB_WRITE );
		$dbw->begin();
		$title = Title::newFromId( $id );
		if( is_null( $title ) ) {
			$dbw->commit();
			return new OAIError( "Failed to delete article id $id" );
		} else {
			$article = new Article( $title );
			$article->doDeleteArticle( '(deleted via OAI updater)' );
			
			global $wgDeferredUpdateList, $wgPostCommitUpdateList;
			while( $up = array_shift( $wgDeferredUpdateList ) ) {
				$up->doUpdate();
			}
			$dbw->commit();
			while( $up = array_shift( $wgPostCommitUpdateList ) ) {
				$up->doUpdate();
			}
		}
		return true;
	}
	
	/**
	 * @param DomNode $node
	 */
	function newFromNode( $node ) {
		$pageData = OAIUpdateRecord::readRecord( $node );
		if( OAIError::isError( $pageData ) )
			return $pageData;
		
		$record = new OAIUpdateRecord( $pageData );
		return $record;
	}
	
	function readRecord( $node ) {
		/*
		<record>
		  <header>
		  <metadata>
		    <mediawiki>
		      <page>
		      <title>
		      <id>
		      <restrictions>
		      <revision>
		        <timestamp>
		        <contributor>
		          <ip>
		          <id>
		          <username>
		        <comment>
		        <text>
		        <minor>
		*/
		if( OAIError::isError( $header = oaiNextChild( $node, 'header' ) ) )
			return $header;
		
		if( $header->get_attribute( 'status' ) == 'deleted' ) {
			$pagedata = OAIUpdateRecord::grabDeletedPage( $header );
			return $pagedata;
		}
		
		if( OAIError::isError( $metadata = oaiNextSibling( $header, 'metadata' ) ) )
			return $metadata;
		
		if( OAIError::isError( $mediawiki = oaiNextChild( $metadata, 'mediawiki' ) ) )
			return $mediawiki;
		
		if( OAIError::isError( $page = oaiNextChild( $mediawiki, 'page' ) ) )
			return $page;
		
		if( OAIError::isError( $pagedata = OAIUpdateRecord::grabPage( $page ) ) )
			return $pagedata;
		
		return $pagedata;
	}
	
	function grabDeletedPage( $header ) {
		/*
			<header status="deleted">
				<identifier>oai:en.wikipedia.org:enwiki:1581436</identifier>
				<datestamp>2005-03-08T07:07:36Z</datestamp>
			</header>
		*/
		if( OAIError::isError( $identifier = oaiNextChild( $header, 'identifier' ) ) )
			return $identifier;
		
		$ident = $identifier->get_content();
		$bits = explode( ':', $ident );
		$id = IntVal( $bits[count( $bits ) - 1] );
		if( $id <= 0 )
			return new OAIError( "Couldn't understand deleted page identifier '$ident'" );
		
		return array(
			'id' => $id,
			'deleted' => true );
	}
	
	function grabPage( $page ) {
		$data = array();
		for( $node = oaiNextChild( $page );
			 !OAIError::isError( $node );
			 $node = oaiNextSibling( $node ) ) {
			switch( $element = $node->node_name() ) {
			case 'title':
			case 'id':
			case 'restrictions':
				$data[$element] = OAIUpdateRecord::decode( $node->get_content() );
				break;
			case 'revision':
				if( OAIError::isError( $revision = OAIUpdateRecord::grabRevision( $node ) ) )
					return $revision;
				$data['revisions'][] = $revision;
				break;
			case 'upload':
				if( OAIError::isError( $upload = OAIUpdateRecord::grabUpload( $node ) ) )
					return $upload;
				$data['uploads'][] = $upload;
				break;
			default:
				return new OAIError( "Unexpected page element <$element>" );
			}
		}
		return $data;
	}
	
	function grabRevision( $revision ) {
		$data = array();
		for( $node = oaiNextChild( $revision );
			 !OAIError::isError( $node );
			 $node = oaiNextSibling( $node ) ) {
			switch( $element = $node->node_name() ) {
			case 'id':
			case 'timestamp':
			case 'comment':
			case 'minor':
			case 'text':
				$data[$element] = OAIUpdateRecord::decode( $node->get_content() );
				break;
			case 'contributor':
				if( OAIError::isError( $contrib = OAIUpdateRecord::grabContributor( $node ) ) )
					return $contrib;				
				$data[$element] = $contrib;
				break;
			default:
				return new OAIError( "Unexpected revision element <$element>" );
			}
		}
		return $data;
	}
	
	function grabUpload( $upload ) {
		$data = array();
		for( $node = oaiNextChild( $upload );
			 !OAIError::isError( $node );
			 $node = oaiNextSibling( $node ) ) {
			switch( $element = $node->node_name() ) {
			case 'timestamp':
			case 'comment':
			case 'filename':
			case 'src':
			case 'size':
				$data[$element] = OAIUpdateRecord::decode( $node->get_content() );
				break;
			case 'contributor':
				if( OAIError::isError( $contrib = OAIUpdateRecord::grabContributor( $node ) ) )
					return $contrib;				
				$data[$element] = $contrib;
				break;
			default:
				return new OAIError( "Unexpected upload element <$element>" );
			}
		}
		return $data;
	}
	
	function grabContributor( $node ) {
		$data = array();
		for( $node = oaiNextChild( $node );
			 !OAIError::isError( $node );
			 $node = oaiNextSibling( $node ) ) {
			switch( $element = $node->node_name() ) {
			case 'id':
			case 'ip':
			case 'username':
				$data[$element] = OAIUpdateRecord::decode( $node->get_content() );
				break;
			default:
				return new OAIError( "Unexpected contributor element <$element>" );
			}
		}
		return $data;
	}
	
	function decode( $string ) {
		global $wgUseLatin1;
		if( $wgUseLatin1 ) {
			return utf8_decode( $string );
		} else {
			return $string;
		}
	}
}

/**
 * Returns the first element node of a given tag name within the set of
 * the start node and its subsequent siblings.
 * If no such tag is found, an error object is returned.
 * @param DomNode $startNode
 * @param string  $element Optionally ignore other node types.
 */
function oaiNextElement( $startNode, $element = null ) {
	for( $node = $startNode;
		 $node;
		 $node = $node->next_sibling() )
		if( $node->node_type() == XML_ELEMENT_NODE
		 && ( is_null( $element ) || $node->node_name() == $element ) )
			return $node;
	
	return new OAIError(
		is_null( $element )
			? "No more elements"
			: "Couldn't locate <$element>" );
}

function oaiNextChild( $parentNode, $element = null ) {
	if( !is_object( $parentNode ) ) {
		wfDebugDieBacktrace( 'oaiNextChild given bogus node' );
	}
	return oaiNextElement( $parentNode->first_child(), $element );
}

function oaiNextSibling( $oneesan, $element = null ) {
	if( !is_object( $oneesan ) ) {
		wfDebugDieBacktrace( 'oaiNextSibling given bogus node' );
	}
	return oaiNextElement( $oneesan->next_sibling(), $element );
}

?>

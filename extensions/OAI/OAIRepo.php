<?php

/**
 * OAI-PMH repository extension for MediaWiki 1.4+
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
 * @todo Charset conversion for Latin-1 wikis
 * @todo Add hooks to update the updates table!
 * @todo Make sure identifiers are correct format
 * @todo Configurable bits n pieces
 * @todo Test for conformance & error conditions
 * @todo Finish the harvester side ;)
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die();
}

$wgExtensionFunctions[] = 'oaiSetupRepo';

function oaiSetupRepo() {
	global $IP;
	require_once( "$IP/includes/SpecialPage.php" );

class OAIRepository extends UnlistedSpecialPage {
	function OAIRepository() {
		UnlistedSpecialPage::UnlistedSpecialPage( 'OAIRepository' );
	}
	
	function setHeaders() {
		// NOP
	}
	
	function execute( $par ) {
		global $wgRequest, $wgOut;
		$wgOut->disable();
		
		# FIXME: Replace the DB error handler
		header( 'Content-type: text/xml' );
		
		require_once( 'includes/SpecialExport.php' );
		$repo = new OAIRepo( $wgRequest );
		$repo->respond();
	}
}

/* repo notes:
	302 -- failover server?
	503 - service unavailable, include a Retry-After!
*/

/**
 * @return string
 */
function oaiDatestamp( $timestamp, $granularity = 'YYYY-MM-DDThh:mm:ssZ' ) {
	$formats = array(
		'YYYY-MM-DD'           => '$1-$2-$3',
		'YYYY-MM-DDThh:mm:ssZ' => '$1-$2-$3T$4:$5:$6Z' );
	if( !isset( $formats[$granularity] ) ) {
		wfDebugDieBacktrace( 'oaiFormatDate given illegal output format' );
	}
	return preg_replace(
		'/^(....)(..)(..)(..)(..)(..)$/',
		$formats[$granularity],
		wfTimestamp( TS_MW, $timestamp ) );
}

/**
 * @param string $element
 * @param array $attribs Name=>value pairs. Values will be escaped.
 * @param bool $contents NULL to make an open tag only
 * @return string
 */
function oaiTag( $element, $attribs, $contents = NULL) {
	$out = '<' . $element;
	foreach( $attribs as $name => $val ) {
		$out .= ' ' . $name . '="' . xmlsafe( $val ) . '"';
	}
	if( is_null( $contents ) ) {
		$out .= '>';
	} else {
		if( $contents == '' ) {
			$out .= '/>';
		} else {
			$out .= '>';
			$out .= xmlsafe( $contents );
			$out .= "</$element>";
		}
	}
	return $out;
}

class OAIRepo {
	function OAIRepo( &$request ) {
		$this->_db =& wfGetDB( DB_SLAVE );
		$this->_errors = array();
		$this->_request = $this->initRequest( $request );
	}
	
	function addError( $code, $message ) {
		$this->_errors[] = array( $code, $message );
	}
	
	function errorCondition() {
		return !empty( $this->_errors );
	}
	
	function initRequest( &$request ) {
		/* Legal verbs and their parameters */
		$verbs = array(
			'GetRecord' => array(
				'required'  => array( 'identifier', 'metadataPrefix' ) ),
			'Identify' => array(),
			'ListIdentifiers' => array(
				'exclusive' =>        'resumptionToken',
				'required'  => array( 'metadataPrefix' ),
				'optional'  => array( 'from', 'until', 'set' ) ),
			'ListMetadataFormats' => array(
				'optional'  => array( 'identifier' ) ),
			'ListRecords' => array(
				'exclusive' =>        'resumptionToken',
				'required'  => array( 'metadataPrefix' ),
				'optional'  => array( 'from', 'until', 'set' ) ),
			'ListSets' => array(
				'exclusive' => 'resumptionToken' ) );
			
		$req = array();
		$verb = $request->getVal( 'verb' );
		if( isset( $verbs[$verb] ) ) {
			$req['verb'] = $verb;
			$params = $verbs[$verb];
			
			/* If an exclusive parameter is set, it's the only one we'll see */
			if( isset( $params['exclusive'] ) ) {
				$exclusive = $request->getVal( $params['exclusive'] );
				if( !is_null( $exclusive ) ) {
					# FIXME: complain if other values found
					$req[$params['exclusive']] = $exclusive;
					return $req;
				}
			}
			
			/* Required parameters must all be present if no exclusive was found */
			if( isset( $params['required'] ) ) {
				foreach( $params['required'] as $name ) {
					$val = $request->getVal( $name );
					if( is_null( $val ) ) {
						$this->addError( 'badArgument', "Missing required argument '" . $name . "'." );
					} else {
						$req[$name] = $val;
					}
				}
			}
			
			/* Optionals are, well, optional. */
			if( isset( $params['optional'] ) ) {
				foreach( $params['optional'] as $name ) {
					$val = $request->getVal( $name );
					if( !is_null( $val ) ) {
						$req[$name] = $val;
					}
				}
			}
		} else {
			$this->addError( 'badVerb', 'Unrecognized or no verb provided.' );
		}
		return $req;
	}
	
	function validateMetadata( $var ) {
		if( isset( $this->_request[$var] ) ) {
			$prefix = $this->_request[$var];
			$formats = $this->metadataFormats();
			if( isset( $formats[$prefix] ) ) {
				return $this->_request[$var];
			} else {
				$this->addError( 'cannotDisseminateFormat', 'Requested unsupported metadata format.' );
				return null;
			}
		} else {
			return null;
		}
	}
	
	function validateDatestamp( $var ) {
		if( isset( $this->_request[$var] ) ) {
			$time = $this->_request[$var];
			if( preg_match( '/^(\d\d\d\d)-(\d\d)-(\d\d)$/', $time, $matches ) ) {
				return wfTimestamp( TS_UNIX,
					$matches[1] . $matches[2] . $matches[3] . '000000' );
			} elseif( preg_match( '/^(\d\d\d\d)-(\d\d)-(\d\d)T(\d\d):(\d\d):(\d\d)Z$/', $time, $matches ) ) {
				return wfTimestamp( TS_UNIX,
					$matches[1] . $matches[2] . $matches[3] .
					$matches[4] . $matches[5] . $matches[6] );
			} else {
				$this->addError( 'badArgument', "Illegal timestamp format in '$var'" );
				return null;
			}
		} else {
			return null;
		}
	}
	
	
	
	function respond() {
		header( 'Content-type: text/xml' );
		echo '<' . '?xml version="1.0" encoding="UTF-8" ?' . ">\n";
		echo oaiTag( 'OAI-PMH', array(
			'xmlns'              => 'http://www.openarchives.org/OAI/2.0/',
			'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
			'xsi:schemaLocation' => 'http://www.openarchives.org/OAI/2.0/ ' .
				                    'http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd' ) )
			. "\n";
		echo $this->responseDate();
		echo $this->regurgitateRequest();
		if( !$this->errorCondition() ) {
			$this->doResponse( $this->_request['verb'] );
		}
		$this->showErrors();
		echo "</OAI-PMH>\n";
	}
	
	function responseDate() {
		$date = oaiDatestamp( time(), $this->timeGranularity() );
		return "<responseDate>$date</responseDate>\n";
	}
	
	function regurgitateRequest() {
    	return oaiTag( 'request', $this->_request, '' ) . "\n";
	}
	
	function showErrors() {
		foreach( $this->_errors as $err ) {
			echo $this->errorMessage( $err[0], $err[1] );
			echo "\n";
		}
	}
	
	function errorMessage( $code, $message ) {
		return oaiTag( 'error',
			array( 'code' => $code ),
			$message );
	}
	
	function doResponse( $verb ) {
		switch( $verb ) {
		case 'Identify':
			$this->Identify();
			break;
		case 'ListIdentifiers':
		case 'ListRecords':
			$this->listRecords( $verb );
			break;
		case 'ListSets':
			$this->addError( 'noSetHierarchy', "This repository doesn't support sets." );
			break;
		case 'ListMetadataFormats':
			$this->listMetadataFormats();
			break;
		case 'GetRecord':
			$this->GetRecord();
			break;
		default:
			# This shouldn't happen
			wfDebugDieBacktrace( 'Verb not implemented' );
		}
	}
	
	function Identify() {
		echo "<Identify>\n";
		foreach( $this->identifyInfo() as $field => $val ) {
			echo oaiTag( $field, array(), $val ) . "\n";
		}
		echo "</Identify>\n";
	}
	
	function listMetadataFormats() {
		if( isset( $this->_request['identifier'] ) ) {
			# We have the same formats for all records...
			# If given an identifier, just check it for existence.
			$row = $this->getRecordItem( $this->_request['identifier'] );
			if( $this->errorCondition() ) {
				return;
			}
		}
		
		$formats = $this->metadataFormats();
		echo "<ListMetadataFormats>\n";
		foreach( $formats as $prefix => $format ) {
			echo "<metadataFormat>\n";
			echo oaiTag( 'metadataPrefix', array(), $prefix ) . "\n";
			echo oaiTag( 'schema', array(), $format['schema'] ) . "\n";
			echo oaiTag( 'metadataNamespace', array(), $format['namespace'] ) . "\n";
			echo "</metadataFormat>\n";
		}
		echo "</ListMetadataFormats>\n";
	}
	
	function validateToken( $var ) {
		if( !isset( $this->_request[$var] ) ) {
			return null;
		}
		if( preg_match( '/^([a-z_]+):(\d+):(\d{14})$/', $this->_request[$var], $matches ) ) {
			$token['metadataPrefix'] = $matches[1];
			$token['resume']         = IntVal( $matches[2] );
			$token['until']          = wfTimestamp( TS_MW, $matches[3] );
			$formats = $this->metadataFormats();
			if( isset( $formats[$token['metadataPrefix']] ) ) {
				return $token;
			}
		}
		$this->addError( 'badResumptionToken', 'Invalid resumption token.' );
	}
	
	function listRecords( $verb ) {
		$withData = ($verb == 'ListRecords');
		
		$token = $this->validateToken( 'resumptionToken' );
		if( $this->errorCondition() ) {
			return;
		}
		if( $token ) {
			$metadataPrefix = $token['metadataPrefix'];
			$resume         = $token['resume'];
			$from           = null;
			$until          = $token['until'];
		} else {
			$metadataPrefix = $this->validateMetadata( 'metadataPrefix' );
			$resume         = null;
			$from           = $this->validateDatestamp( 'from' );
			$until          = $this->validateDatestamp( 'until' );
			if( isset( $this->_request['set'] ) ) {
				$this->addError( 'noSetHierarchy', 'This repository does not support sets.' );
			}
			if( $this->errorCondition() ) {
				return;
			}
		}
		
		# Fetch one extra row to check if we need a resumptionToken
		# If no until limit is set, this will get the current time.
		$limit = wfTimestamp( TS_MW, $until );
		$resultSet = $this->fetchRows( $from, $until, $this->chunkSize() + 1, $resume );
		$count = min( $resultSet->numRows(), $this->chunkSize() );
		if( $count ) {
			echo "<$verb>\n";
			$this->_lastSequence = null;
			for( $i = 0; $i < $count; $i++ ) {
				$row = $resultSet->fetchObject();
				$item = new WikiOAIRecord( $row );
				if( $withData ) {
					echo $item->renderRecord( $metadataPrefix, $this->timeGranularity() );
				} else {
					echo $item->renderHeader( $this->timeGranularity() );
				}
				$this->_lastSequence = $row->up_sequence;
			}
			if( $row = $resultSet->fetchObject() ) {
				$token = "$metadataPrefix:$row->up_sequence:$limit";
				echo oaiTag( 'resumptionToken', array(), $token ) . "\n";
			}
			echo "</$verb>\n";
		} else {
			$this->addError( 'noRecordsMatch', 'No records available match the request.' );
		}
		$resultSet->free();
	}
	
	function getRecord() {
		$metadataPrefix =  $this->validateMetadata( 'metadataPrefix' );
		if( !$this->errorCondition() ) {
			$row = $this->getRecordItem( $this->_request['identifier'] );
			if( !$this->errorCondition() ) {
				$item = new WikiOAIRecord( $row );
				echo "<GetRecord>\n";
				echo $item->renderRecord( $metadataPrefix, $this->timeGranularity() );
				echo "</GetRecord>\n";
			}
		}
	}
	
	function getRecordItem( $identifier ) {
		$pageid = $this->stripIdentifier( $identifier );
		if( $pageid ) {
			$resultSet = $this->fetchRecord( $pageid );
			$row = $resultSet->fetchObject();
			$resultSet->free();
			if( $row ) {
				return $row;
			}
		}
		$this->addError( 'idDoesNotExist', 'Requested identifier is invalid or does not exist.' );
		return null;
	}
	
	function stripIdentifier( $identifier ) {
		global $wgServerName, $wgDBname;
		$prefix = "oai:$wgServerName:$wgDBname:";
		if( substr( $identifier, 0, strlen( $prefix ) ) == $prefix ) {
			$pageid = substr( $identifier, strlen( $prefix ) );
			if( preg_match( '/^\d+$/', $pageid ) ) {
				return IntVal( $pageid );
			}
		}
		return false;
	}
	
	function timeGranularity() {
		return 'YYYY-MM-DDThh:mm:ssZ';
	}
	
	function chunkSize() {
		return 50;
	}
	
	function baseUrl() {
		$title =& Title::makeTitle( NS_SPECIAL, 'OAIRepository' );
		return $title->getFullUrl();
	}
	
	function earliestDatestamp() {
		$updates = $this->_db->tableName( 'updates' );
		$result = $this->_db->query( "SELECT MIN(up_timestamp) AS min FROM $updates" );
		$row = $this->_db->fetchObject( $result );
		if( $row ) {
			$this->_db->freeResult( $result );
			return $row->min;
		} else {
			wfDebugDieBacktrace( 'Bogus result.' );
		}
	}
	
	function fetchRecord( $pageid ) {
		$updates = $this->_db->tableName( 'updates' );
		$cur = $this->_db->tableName( 'cur' );
		
		$sql = "SELECT up_page,up_timestamp,up_action,up_sequence,
		cur_namespace    AS namespace,
		cur_title        AS title,
		cur_text         AS text,
		cur_comment      AS comment,
		cur_user         AS user,
		cur_user_text    AS user_text,
		cur_timestamp    AS timestamp,
		cur_restrictions AS restrictions,
		cur_minor_edit   AS minor_edit
		FROM $updates LEFT JOIN $cur ON cur_id=up_page
		WHERE up_page=" . IntVal( $pageid ) .
		' LIMIT 1';
		
		return $this->_db->resultObject( $this->_db->query( $sql ) );
	}
	
	function fetchRows( $from, $until, $chunk, $token = null ) {
		$updates = $this->_db->tableName( 'updates' );
		$cur = $this->_db->tableName( 'cur' );
		$chunk = IntVal( $chunk );
		
		$sql = "SELECT up_page,up_timestamp,up_action,up_sequence,
		cur_namespace    AS namespace,
		cur_title        AS title,
		cur_text         AS text,
		cur_comment      AS comment,
		cur_user         AS user,
		cur_user_text    AS user_text,
		cur_timestamp    AS timestamp,
		cur_restrictions AS restrictions,
		cur_minor_edit   AS minor_edit
		FROM $updates LEFT JOIN $cur ON cur_id=up_page ";
		$where = array();
		if( $token ) {
			$where[] = 'up_sequence >= ' . IntVal( $token );
			$order = 'up_sequence';
		} else {
			$order = 'up_timestamp';
		}
		if( $from ) {
			$where[] = 'up_timestamp >= ' . $this->_db->timestamp( $from );
		}
		if( $until ) {
			$where[] = 'up_timestamp <= ' . $this->_db->timestamp( $until );
		}
		if( !empty( $where ) ) {
			$sql .= ' WHERE ' . implode( ' AND ', $where );
		}
		$sql .= " ORDER BY $order LIMIT $chunk";
		
		return $this->_db->resultObject( $this->_db->query( $sql ) );
	}
	
	function identifyInfo() {
		return array(
			'repositoryName' => 'Wikipedia or something',
			'baseURL' => $this->baseUrl(),
			'protocolVersion' => '2.0',
			'adminEmail' => 'brion@pobox.com',
			'earliestDatestamp' => oaiDatestamp(
				$this->earliestDatestamp(), $this->timeGranularity() ),
			'deletedRecord' => 'persistent',
			'granularity' => $this->timeGranularity(),
			
			# Optional
			'compression' => 'gzip',
			#'description'
			);
	}

	function metadataFormats() {
		return array(
			'oai_dc' => array(
				'namespace' => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
				'schema'    => 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd' ),
			'mediawiki' => array(
				'namespace'	=> 'http://www.mediawiki.org/xml/export-0.1/',
				'schema'    => 'http://www.mediawiki.org/xml/export-0.1.xsd' ) );
	}
	
}

class OAIRecord {
	function renderRecord( $format, $datestyle ) {
		$header = $this->renderHeader( $datestyle );
		$metadata = $this->isDeleted()
			? ''
			: $this->renderMetadata( $format );
		$about = $this->isDeleted()
			? ''
			: $this->renderAbout();
		return "<record>\n$header$metadata$about</record>\n";
	}
	
	function renderHeader( $datestyle ) {
		$tag = $this->isDeleted()
			? 'header status="deleted"'
			: 'header';
		$ident = xmlsafe( $this->getIdentifier() );
		$date = oaiDatestamp( $this->getDatestamp(), $datestyle );
		return "<$tag>\n" .
		       "  <identifier>$ident</identifier>\n" .
		       "  <datestamp>$date</datestamp>\n" .
		       "</header>\n";
	}
	
	function renderMetadata( $format ) {
		wfDebugDieBacktrace( 'Abstract' );
	}
	
	function renderAbout() {
		# Not supported yet
		return '';
	}
	
	/**
	 * Return the date and time when this record was last modified,
	 * created or deleted. This is needed for the header output.
	 * Override this...
	 *
	 * @return int UNIX timestamp (or other wfTimestamp()-compatible)
	 * @abstract
	 */
	function getDatestamp() {
		wfDebugDieBacktrace( 'Abstract OAIRecord::getDatestamp() called.' );
	}
	
	/**
	 * Return the record's unique OAI identifier.
	 * This is needed for the header output.
	 * Override this...
	 *
	 * @return string
	 * @abstract
	 */
	function getIdentifier() {
		wfDebugDieBacktrace( 'Abstract OAIRecord::getIdentifier() called.' );
	}
	
	/**
	 * True if this is a deleted record, false otherwise.
	 * Override if your repository supports marking deleted records.
	 *
	 * @return bool
	 */
	function isDeleted() {
		return false;
	}
}

class WikiOAIRecord extends OAIRecord {
	/**
	 * @param object $row database row
	 */
	function WikiOAIRecord( $row ) {
		$this->_id        = $row->up_page;
		$this->_timestamp = $row->up_timestamp;
		$this->_deleted   = ($row->up_action == 'delete');
		$this->_row       = $row;
	}
	
	function isDeleted() {
		return $this->_deleted;
	}
	
	function getIdentifier() {
		global $wgDBname, $wgServerName;
		return "oai:$wgServerName:$wgDBname:{$this->_id}";
	}
	
	function getDatestamp() {
		return $this->_timestamp;
	}
	
	function renderMetadata( $format ) {
		switch( $format ) {
		case 'oai_dc':
			$data = $this->renderDublinCore();
			break;
		case 'mediawiki':
			$data = $this->renderMediaWiki();
			break;
		default:
			wfDebugDieBacktrace( 'Unsupported metadata format.' );
		}
		return "<metadata>\n$data</metadata>\n";
	}
	
	function renderDublinCore() {
		$title = Title::makeTitle( $this->_row->namespace, $this->_row->title );
		global $wgMimeType, $wgContLanguageCode;
		
		$out = oaiTag( 'oai_dc:dc', array(
			'xmlns:oai_dc'       => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
			'xmlns:dc'           => 'http://purl.org/dc/elements/1.1/',
			'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
			'xsi:schemaLocation' => 'http://www.openarchives.org/OAI/2.0/oai_dc/ ' .
			                        'http://www.openarchives.org/OAI/2.0/oai_dc.xsd' ) ) . "\n" .
			oaiTag( 'dc:title',       array(), $title->getPrefixedText() ) . "\n" .
			oaiTag( 'dc:language',    array(), $wgContLanguageCode ) . "\n" .
			oaiTag( 'dc:type',        array(), 'Text' ) . "\n" .
			oaiTag( 'dc:format',      array(), $wgMimeType ) . "\n" .
			oaiTag( 'dc:identifier',  array(), $title->getFullUrl() ) . "\n" .
			oaiTag( 'dc:contributor', array(), $this->_row->user_text ) . "\n" .
			oaiTag( 'dc:date',        array(), oaiDatestamp( $this->getDatestamp() ) ) . "\n" .
			oaiTag( 'dc:description', array(), $this->_row->text ) . "\n" .
			"</oai_dc:dc>\n";
		return $out;
	}
	
	function renderMediaWiki() {
		global $wgContLanguageCode;
		$title = Title::makeTitle( $this->_row->namespace, $this->_row->title );
		$out = oaiTag( 'mediawiki', array(
			'xmlns'              => 'http://www.mediawiki.org/xml/export-0.1/',
			'xmlns:xsi'          => 'http://www.w3.org/2001/XMLSchema-instance',
			'xsi:schemaLocation' => 'http://www.mediawiki.org/xml/export-0.1/ ' .
			                        'http://www.mediawiki.org/xml/export-0.1.xsd',
			'version'            => '0.1',
			'xml:lang'           => $wgContLanguageCode ) ) . "\n";
		$out .= "<page>\n";
		$out .= oaiTag( 'title', array(), $title->getPrefixedText() ) . "\n";
		$out .= oaiTag( 'id', array(), $this->_id ) . "\n";
		if( $this->_row->restrictions ) {
			$out .= oaiTag( 'restrictions', array(), $this->_row->restrictions ) . "\n";
		}
		$out .= revision2xml( $this->_row, true, true );
		$out .= "</page>\n";
		$out .= "</mediawiki>\n";
		return $out;
	}
	
}

	SpecialPage::addPage( new OAIRepository );
	global $wgMessageCache;
	$wgMessageCache->addMessage( "oairepository", "OAI Repository" );
}

?>
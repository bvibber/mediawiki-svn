<?php
/**
 * Bot Query extension for MediaWiki 1.7+
 *
 * Copyright (C) 2006 Yuri Astrakhan <FirstnameLastname@gmail.com>
 * Uses bits from the original query.php code written by Tim Starling.
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
 */


define( 'MEDIAWIKI', true );
unset( $IP );
if ( isset( $_REQUEST['GLOBALS'] ) ) {
	echo '<a href="http://www.hardened-php.net/index.76.html">$GLOBALS overwrite vulnerability</a>';
	die( -1 );
}

define( 'MW_NO_OUTPUT_BUFFER', true );
$wgNoOutputBuffer = true;

$IP = dirname( realpath( __FILE__ ) ) . '/../..';
chdir( $IP );
require_once( "$IP/includes/Defines.php" );
require_once( "$IP/LocalSettings.php" );
require_once( "$IP/includes/Setup.php" );

$db =& wfGetDB( DB_SLAVE );
$bqp = new BotQueryProcessor( $db );
$bqp->execute();
$bqp->output();



class BotQueryProcessor {
	var $classname = 'BotQueryProcessor';

	/**
	* Output generators - each format name points to an array of the following parameters:
	*     0) mime type 
	*     1) Function to call
	*     2) Format description
	*/
	var $outputGenerators = array(
		'xml' => array( 'text/xml', 'printXML', 'XML format with optional indentation (see Notes)' ),
		'txt' => array( 'application/x-wiki-botquery-print_r', 'printHumanReadable', 'Pretty-printed human readable format' ),
		'json' => array( 'application/json', 'printJSON', 'JSON format' ),
		'php' => array( 'application/vnd.php.serialized', 'printPHP', 'PHP serialized format' ),
		'dbg' => array( 'application/x-wiki-botquery-var_export', 'printParsableCode', 'PHP source code format' ),
//		'tsv' => array( 'text/tab-separated-values', 'print', '' ),
	);

	/**
	* Properties generators - each property points to an array of the following parameters:
	*     0) true/false - does this property work on individual pages?  (false for site's metadata)
	*     1) Function to call
	*     2) property description
	*/
	var $propGenerators = array(

		// Site-wide Generators
		'siteinfo'       => array( false, "genMetaSiteInfo", "basic site information" ),
		'sitenamespaces' => array( false, "genMetaNamespaceInfo", "list of localized namespaces" ),
		'userinfo'       => array( false, "genMetaUserInfo", "user information" ),
		'dblredirects'   => array( false, "genMetaDoubleRedirects", "list of double-redirect pages" ),

		// Page-specific Generators
		'langlinks'      => array( true, "genPageLangLinks", "interlanguage links" ),
		'templates'      => array( true, "genPageTemplates", "template names" ),
		'links'          => array( true, "genPageLinks", "regular links to other pages" ),
		'backlinks'      => array( true, "genPageBackLinks", "returns pages that link here" ),
		'revisions'      => array( true, "genPageHistory", "revision history (see Notes)" ),
	);

	function BotQueryProcessor( $db ) {
		global $wgRequest;

		$this->db = $db;
		$this->format = $this->parseFormat( $wgRequest->getVal('format') );
		$this->properties = $this->parseProperties( $wgRequest->getVal('properties'));
		$this->data = array();
		
		// Neither one of these variables is referenced directly!
		// Meta generators may append titles or pageids to these varibales.
		// Do not modify this values directly - use the AddRaw() method
		$this->titles = null;
		$this->pageids = null;
	}

	function execute() {
	
		// Enforce result consistency
		$this->db->begin( $this->classname );
	
		// Process metadata generators
		$this->callGenerators( false );

		// Query page table and initialize page ids.
		if( $this->genPageInfo() ) {
			// Process page-related generators
			$this->callGenerators( true );
		}
		
		// Complete transaction
		$this->db->commit( $this->classname );
		
		// Report empty query
		if( !$this->data ) {
			$this->dieUsage( 'Nothing to do' );
		}
	}

	function callGenerators( $callPageGenerators ) {
		foreach( $this->propGenerators as $property => $generator ) {
			if( $generator[0] === $callPageGenerators && in_array( $property, $this->properties )) {
				$this->{$generator[1]}();
			}
		}
	}

	function output() {
		list( $mime, $printer ) = $this->outputGenerators[$this->format];		
		header( "Content-Type: $mime; charset=utf-8;" );
		$printer( $this->data );
	}


	//
	// ************************************* INPUT PARSERS *************************************
	//
	function parseFormat( $format ) {
		if( array_key_exists($format, $this->outputGenerators) ) {
			return $format;
		} else {
			$this->dieUsage( "Unrecognised format '$format'" );
		}
	}

	function parseProperties( $properties ) {
		global $wgUser;

		if ( $properties == '' ) {
			$this->dieUsage( 'No properties given' );
		}

		$propList = explode( '|', $properties );
		$unknownProperties = array_diff( $propList, array_keys( $this->propGenerators ));
		if( $unknownProperties ) {
			$this->dieUsage( "Unrecognised propert" . (count($unknownProperties)>1?"ies ":"y ") . implode(', ', $unknownProperties) );
		}

		return $propList;
	}


	//
	// ************************************* GENERATORS *************************************
	//
	function genPageInfo() {
		global $wgUser, $wgRequest;
		
		$where = array();
		$requestsize = 0;
		
		//
		// List of titles
		//
		$titles = $this->addRaw( 'titles', $wgRequest->getVal('titles') );
		if( $titles !== null ) {
			$titles = explode( '|', $titles );
			$linkBatch = new LinkBatch;
			foreach ( $titles as $titleString ) {
				$titleObj = Title::newFromText( $titleString );
				if ( !$titleObj ) {
					$this->dieUsage("bad title $titleString" );
				}
				if ( !$titleObj->userCanRead() ) {
					$this->dieUsage("No read permission for $titleString" );
				}
				$linkBatch->addObj( $titleObj );
			}
			if ( $linkBatch->isEmpty() ) {
				$this->dieUsage("no titles could be found" );
			}
			// Create a list of pages to query
			$where[] = $linkBatch->constructSet( 'page', $this->db );
			$requestsize += $linkBatch->getSize();
			
			// we don't need the batch any more, data can be destroyed
			$nonexistentPages = &$linkBatch->data;
		} else {
			$nonexistentPages = array();	// empty data to keep unset() happy
		}
		
		//
		// List of Page IDs
		//
		$pageids = $this->addRaw( 'pageids', $wgRequest->getVal('pageids') );
		if ( $pageids !== null ) {
			$pageids = explode( '|', $pageids );
			$pageids = array_map( 'intval', $pageids );
			$pageids = array_unique($pageids);
			sort( $pageids, SORT_NUMERIC );
			if( $pageids[0] <= 0 ) {
				$this->dieUsage("pageids contains a bad id" );
			}
			$where['page_id'] = $pageids;
			$requestsize += count($pageids);
		}

		// Do we have anything to do?
		if( $requestsize == 0 ) {
			return false;	// Nothing to do for any of the page generators
		}
		
		//
		// User restrictions
		//
		if( $wgUser->isBot() ) {
			if ( $requestsize > 1000 ) {
				$this->dieUsage( 'Bots may not request over 1000 pages' );
			}
		} else {
			if( $requestsize > 20 ) {
				$this->dieUsage( 'Users may not request over 20 pages' );
			}
		}
		
		//
		// Make sure that this->data['pages'] is empty
		//
		if( array_key_exists('pages', $this->data) ) {
			die( "internal error - 'pages' should not yet exist" );
		}
				
		//
		// Query page information with the given lists of titles & pageIDs
		//
		//$this->existingLinkBatch = new LinkBatch;
		//$this->nonRedirLinkBatch = new LinkBatch;
		$redirects = array();
		$res = $this->db->select( 'page',
			array( 'page_id', 'page_namespace', 'page_title', 'page_is_redirect', 'page_latest' ),
			$this->db->makeList( $where, LIST_OR ),
			$this->classname . '::genPageInfo' );
		while( $row = $this->db->fetchObject( $res ) ) {
			$title = Title::makeTitle( $row->page_namespace, $row->page_title );
			if ( !$title->userCanRead() ) {
				$this->db->freeResult( $res );
				$this->dieUsage("No read permission for $titleString" );
			}
			$data = &$this->data['pages'][$row->page_id];
			$data['_obj']  = $title;
			$data['ns']    = $title->getNamespace();
			$data['title'] = $title->getPrefixedText();
			$data['id']    = $row->page_id;
			$data['revid'] = $row->page_latest;
			//$this->existingLinkBatch->add( $title->getNamespace(), $title->getDBkey() );
			if ( $row->page_is_redirect ) {
				$data['redirect'] = '';
				$redirects[] = $row->page_id;
			} else {
				//$this->nonRedirLinkBatch->add( $title->getNamespace(), $title->getDBkey() );
			}

			// Strike out link
			unset( $nonexistentPages[$row->page_namespace][$row->page_title] );
		}
		$this->db->freeResult( $res );

		//
		// At this point we assume that this->data['pages'] contains ONLY valid existing entries.
		// Create lists that can later be used to filter other tables by page Id or other useful query strings
		//
		$this->existingPageIds = array_keys( $this->data['pages'] );
		$this->nonRedirPageIds = array_diff_key($this->existingPageIds, $redirects);
		
		//
		// Create records for non-existent page IDs
		//
		if( $pageids !== null ) {
			foreach( array_diff_key($pageids, $this->existingPageIds) as $pageid ) {
				$data = &$this->data['pages'][$pageid];
				$data['id'] = 0;
				$data['bad_id'] = $pageid;
			}
		}
		
		$this->data['pages']['_element'] = 'page';

		//
		// Add entries for non-existent page titles
		//
//		$this->allLinkBatch = $this->existingLinkBatch;	// make a copy that will include nonexisting items as well
		$i = -1;
		foreach( $nonexistentPages as $namespace => $stuff ) {
			foreach( $stuff as $dbk => $arbitrary ) {
				$title = Title::makeTitle( $namespace, $dbk );
				// Must do this check even for non-existent pages, as some generators can give related information
				if ( !$title->userCanRead() ) {
					$this->dieUsage("No read permission for $titleString" );
				}
				$data = &$this->data['pages'][$i--];
				$data['_obj']    = $title;
				$data['title']   = $title->getPrefixedText();
				$data['ns']      = $title->getNamespace();
				$data['id']      = 0;
				//$this->allLinkBatch->add( $title->getNamespace(), $title->getDBkey() );
			}
		}
		
		//
		// Process redirects
		//
		if( $redirects ) {
			// If the user requested links, redirect links will be populated.
			// Otherwise, we have to do it manually here by calling links generator with a custom list of IDs
			if( !in_array( 'links', $this->properties )) {
				$this->{$this->propGenerators['links'][1]}( $redirects );
			}
		}

		return true; // success
	}

	function genMetaSiteInfo() {
		global $wgSitename, $wgVersion, $wgCapitalLinks;
		$meta = array();
		$mainPage = Title::newFromText( wfMsgForContent( 'mainpage' ) );

		$meta['mainpage'] = $mainPage->getText();
		$meta['base']     = $mainPage->getFullUrl();
		$meta['sitename'] = $wgSitename;
		$meta['generator']= "MediaWiki $wgVersion";
		$meta['case']     = $wgCapitalLinks ? 'first-letter' : 'case-sensitive'; // "case-insensitive" option is reserved for future

		$this->data['meta']['site'] = $meta;
	}

	function genMetaNamespaceInfo() {
		global $wgContLang;
		$meta = array();
		$meta['_element'] = 'ns';
		foreach( $wgContLang->getFormattedNamespaces() as $ns => $title ) {
			$meta[$ns] = array( "id"=>$ns, "*" => $title );
		}
		$this->data['meta']['namespaces'] = $meta;
	}

	function genMetaUserInfo() {
		global $wgUser;
		
		$meta = array();
		$meta['name'] = $wgUser->getName();
		if( $wgUser->isAnon() ) $meta['anonymous'] = '';
		if( $wgUser->isBot() ) $meta['bot'] = '';
		if( $wgUser->isBlocked() ) $meta[' blocked'] = '';
		$meta['groups'] = $wgUser->getGroups();
		$meta['groups']['_element'] = 'g';
		$meta['rights'] = $wgUser->getRights();
		$meta['rights']['_element'] = 'r';
		$this->data['meta']['user'] = $meta;
	}

	function genMetaDoubleRedirects() {
		global $wgRequest, $wgUser;

		$this->dieUsage( "DoubleRedirect generator is disabled until caching is implemented" );
		
		if( !$wgUser->isBot() ) {
			$this->dieUsage( "Only bots are allowed to query for double-redirects" );
		}

		extract( $this->db->tableNames( 'page', 'pagelinks' ) );
		
			$offset = $wgRequest->getInt( 'droffset', 0 );
		$limit = $wgRequest->getInt( 'drlimit', 50 );
		$sql = "SELECT " .
			 " pa.page_id id_a," .
			 " pb.page_id id_b," .
			 " pc.page_id id_c" .
			" FROM $pagelinks AS la, $pagelinks AS lb, $page AS pa, $page AS pb, $page AS pc" .
			" WHERE pa.page_is_redirect=1 AND pb.page_is_redirect=1" .
			 " AND la.pl_from=pa.page_id" .
			 " AND la.pl_namespace=pb.page_namespace" .
			 " AND la.pl_title=pb.page_title" .
			 " AND lb.pl_from=pb.page_id" .
			 " AND lb.pl_namespace=pc.page_namespace" .
			 " AND lb.pl_title=pc.page_title" .
			 " LIMIT $limit";
		if( $offset > 0 ) {
			$sql .= " OFFSET $offset";
		}

		// Add found page ids to the list of requested ids - they will be auto-populated later
		$res = $this->db->query( $sql, $this->classname . '::genMetaDoubleRedirects' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addRaw( 'pageids', $row->id_a .'|'. $row->id_b .'|'. $row->id_c );
			$this->data['pages'][$row->id_a]['dblredirect'] = $row->id_c;
		}
		$this->db->freeResult( $res );
	}

	function genPageLangLinks() {
		if( !$this->nonRedirPageIds ) {
			return;
		}		
		$res = $this->db->select(
			array( 'langlinks' ),
			array( 'll_from', 'll_lang', 'll_title' ),
			array( 'll_from' => $this->nonRedirPageIds ),
			$this->classname . '::genPageLangLinks' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->ll_from, 'langlinks', 'll', array('lang' => $row->ll_lang, '*' => $row->ll_title));
		}
		$this->db->freeResult( $res );
	}

	function genPageTemplates() {
		if( !$this->nonRedirPageIds ) {
			return;
		}
		$res = $this->db->select(
			'templatelinks',
			array( 'tl_from', 'tl_namespace', 'tl_title' ),
			array( 'tl_from' => $this->nonRedirPageIds ),
			$this->classname . '::genPageTemplates' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->tl_from, 'templates', 'tl', $this->getLinkInfo( $row->tl_namespace, $row->tl_title ));
		}
		$this->db->freeResult( $res );
	}

	/**
	* Generates list of links for all pages. Optionally it can be called to populate only a subset of pages by given ids.
	*/
	function genPageLinks( $pageIdsList = null ) {
		if( $pageIdsList === null ) {
			$pageIdsList = $this->nonRedirPageIds;
		}
		if( !$pageIdsList ) {
			return;
		}
		$res = $this->db->select(
			'pagelinks',
			array( 'pl_from', 'pl_namespace', 'pl_title' ),
			array( 'pl_from' => $pageIdsList ),
			$this->classname . '::genPageLinks' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->pl_from, 'links', 'l', $this->getLinkInfo( $row->pl_namespace, $row->pl_title ));
		}
		$this->db->freeResult( $res );
	}

	function genPageBackLinks() {
		global $wgRequest;

		$offset = $wgRequest->getInt( 'bloffset', 0 );
		$limit = $wgRequest->getInt( 'bllimit', 50 );
		$blfilter = $wgRequest->getVal( 'blfilter', 'existing' );

		$nonredir = $existing = $all = false;
		switch( $blfilter ) {
			case 'all' :
				$all = true;
				// fallthrough
			case 'existing' :
				$existing = true;
				// fallthrough
			case 'nonredirects' :
				$nonredir = true;
				break;
			default:
				$this->dieUsage( "Backlink filter '$blfilter' is not one of allowed: 'all', 'existing' [default], and 'nonredirects'" );
		}

		$linkBatch = new LinkBatch;
		foreach( $this->data['pages'] as $key => $page ) {
			if(( $key < 0 && $all && array_key_exists('_obj', $page) ) ||
			   ( $key > 0 && ($existing || ($nonredir && !array_key_exists('redirect', $page))) )) {

				$linkBatch->addObj( $page['_obj'] );
			}
		}
		
		if( $linkBatch->isEmpty() ) {
			return; // Nothing to do
		}

		$allowedBLTypes = array( 'all', 'templates', 'links' );
		$type = $wgRequest->getVal( 'bltype', 'all' );
		if( array_key_exists( $type, $allowedBLTypes )) {
			$this->dieUsage( "Backlink type '$type' is not one of allowed: " . implode(', ', $allowedBLTypes) );
		}
		if( $type === 'all' || $type === 'template' ) {
			$this->genPageBackLinksHelper( 'template', 'tl', $offset, $limit, $linkBatch );
		}
		if( $type === 'all' || $type === 'links' ) {
			$this->genPageBackLinksHelper( 'page', 'pl', $offset, $limit, $linkBatch );
		}
	}
	/**
	* Generate backlinks for either links, templates, or both
	*/
	function genPageBackLinksHelper( $type, $code, $offset, $limit, &$linkBatch ) {
		
		$page = $this->db->tableName( 'page' );
		$linktbl = $this->db->tableName( $type . 'links' );

		$sql = "SELECT"
			." pfrom.page_id from_id, pfrom.page_namespace from_namespace, pfrom.page_title from_title,"
			." pto.page_id to_id, {$code}_namespace to_namespace, {$code}_title to_title"
		." FROM"
			." ("
				  ." $linktbl INNER JOIN $page pfrom ON {$code}_from = pfrom.page_id"
			." )"
			." LEFT JOIN $page pto ON {$code}_namespace = pto.page_namespace AND {$code}_title = pto.page_title"
		." WHERE"
			." " . $linkBatch->constructSet( $code, $this->db )
		." ORDER BY"
			." {$code}_namespace, {$code}_title"
		." LIMIT $limit"
		. ( $offset > 0 ? " OFFSET $offset" : "" );

		$res = $this->db->query( $sql, $this->classname . "::genPageBackLinks_{$code}" );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$pageId = $row->to_id;
			if( $pageId === null ) {
				$pageId = $this->lookupInvalidPageId( $row->to_namespace, $row->to_title );
			}
			$values = $this->getLinkInfo( $row->from_namespace, $row->from_title, $row->from_id );
			$values['type'] = $type;
			$this->addPageSubElement( $pageId, 'backlinks', 'bl', $values );
		}
		$this->db->freeResult( $res );
	}
	
	function genPageHistory() {
		global $wgRequest;

		if( !$this->existingPageIds ) {
			return;
		}

		$includeComments = $wgRequest->getCheck('rvcomments');

		// select *:  rev_page, rev_text_id, rev_comment, rev_user, rev_user_text, rev_timestamp, rev_minor_edit, rev_deleted
		$fields = array('rev_id','rev_timestamp','rev_user','rev_user_text','rev_minor_edit');
		if( $includeComments ) {
			$fields[] = 'rev_comment';
		}

		$conds = array(
			'rev_deleted' => 0,
		);

		$start = $wgRequest->getVal( 'rvstart' );
		if ( $start != '' ) {
			$conds[] = 'rev_timestamp >= ' . $this->prepareTimestamp($start);
		}

		$end = $wgRequest->getVal( 'rvend' );
		if ( $end != '' ) {
			$conds[] = 'rev_timestamp <= ' . $this->prepareTimestamp($end);
		}

		$limit = $wgRequest->getInt( 'rvlimit', 50 );
		$options = array(
			'LIMIT' => $limit,
			'ORDER BY' => 'rev_timestamp DESC'
		);

		if( $limit * count($this->existingPageIds) > 20000 ) {
			$this->dieUsage( "rvlimit multiplied by number of requested titles must be less than 20000" );
		}

		foreach( $this->existingPageIds as $pageId ) {
			$conds['rev_page'] = $pageId;
			$res = $this->db->select( 'revision', $fields, $conds, $this->classname . '::genPageHistory', $options );
			while ( $row = $this->db->fetchObject( $res ) ) {
				$vals = array(
					'revid' => $row->rev_id,
					'timestamp' => wfTimestamp( TS_ISO_8601, $row->rev_timestamp ),
					'user' => $row->rev_user_text,
					);
				if( !$row->rev_user ) {
					$vals['anon'] = '';
				}
				if( $row->rev_minor_edit ) {
					$vals['minor'] = '';
				}
				$vals['*'] = $includeComments ? $row->rev_comment : '';
				$this->addPageSubElement( $pageId, 'revisions', 'rv', $vals);
			}
			$this->db->freeResult( $res );
		}
	}

	//
	// ************************************* UTILITIES *************************************
	//
	
	
	/**
	* Lookup of the page id by ns:title in the data array. Very slow - lookup by id if possible.
	* This method will die if no such title is found
	*/	
	function lookupInvalidPageId( $ns, &$dbkey ) {
		// TODO: optimize.
		$ns = intval($ns);
		foreach( $this->data['pages'] as $id => $page ) {
			if( $id < 0 && array_key_exists( '_obj', $page )) {
				$title = &$page['_obj'];
				if( $title->getNamespace() === $ns && $title->getDBkey() === $dbkey ) {
					return $id;
				}
			}
		}
		die( "internal error - '$ns:$dbkey' not found" );
	}
	
	/**
	* Use this method to add 'titles' or 'pageids' during meta generation in addition to any supplied by the user.
	*/
	function addRaw( $type, $elements ) {
		$val = & $this->{$type};
		if( $elements !== null && $elements !== '' ) {
			if( is_array( $elements )) {
				$elements = implode( '|', $elements );
			}
			if( $val !== null ) {
				$val .= '|';
			}
			$val .= $elements;
		}
		return $val;
	}
	
	function getTitleInfo( $title, $id = 0 ) {
		$data = array();
		if( $title->getNamespace() != NS_MAIN ) {
			$data['ns'] = $title->getNamespace();
		}
		if( $title->isExternal() ) {
			$data['iw'] = $title->getInterwiki();
		}
		if( $id !== 0 ) {
			$data['id'] = $id;
		}
		$data['*'] = $title->getPrefixedText();

		return $data;
	}

	function getLinkInfo( $ns, $title, $id = 0 ) {
		return $this->getTitleInfo( Title::makeTitle( $ns, $title ));
	}

	function addPageSubElement( $pageId, $mainElem, $itemElem, $params ) {
		$data = & $this->data['pages'][$pageId][$mainElem];
		$data['_element'] = $itemElem;
		$data[] = $params;
	}

	function prepareTimestamp( $value ) {
		if ( preg_match( '/^[0-9]{14}$/', $value ) ) {
			return $this->db->addQuotes( $value );
		} else {
			$this->dieUsage( 'Incorrect timestamp format' );
		}
	}
	
	/**
	* Recursivelly removes any elements from the array that begin with an '_'.
	* The content element '*' is the only special element that is left.
	* Use this method when the entire data object gets sent to the user.
	*/
	function sanitizeOutputData( &$data ) {
		foreach( $data as $key => $value ) {
			if( $key[0] === '_' ) {
				unset( $data[$key] );
			} elseif ( is_array( $value )) {
				sanitizeOutputData( $value );
			}
		}
	}
	
	function dieUsage( $message ) {
		global $wgUser;

		$formats = "";
		foreach( $this->outputGenerators as $format => $generator ) {
			$formats .= sprintf( "  %-20s - %s\n", $format, $generator[2]);
		}

		$props = "";
		foreach( $this->propGenerators as $property => $generator ) {
			$props .= sprintf( "  %-20s - %s\n", $generator[0] ? $property : $property." (*)", $generator[2]);
		}
		$props .= "  (*) These properties return information about the whole site\n";

		// This will prevent any code injection attacks
		$message = htmlspecialchars($message);

		header( "Content-Type: text/plain; charset=utf-8;" );

		echo "\n   ------ Error: $message ------\n\n"
			."Usage:\n"
			."  query.php ? format=a & properties=b|c|d & titles=e|f|g & ...\n"
			."\n"
			."Examples:\n"
			."    query.php?format=xml&properties=links|templates&titles=User:Yurik\n"
			."  This query will return a list of all links and templates used on the User:Yurik\n"
			."\n"
			."    query.php?format=xml&properties=revisions&titles=Main_Page&rvlimit=100&rvstart=20060401000000&rvcomments\n"
			."  Get a list of 100 last revisions of the main page with comments, but only if it happened after midnight April 1st 2006\n"
			."\n"
			."Supported Formats:\n"
			.$formats
			."\n"
			."Supported Properties:\n"
			.$props
			."\n"
			."Notes:\n"
			."  - format and either properties and/or titles must be specified\n"
			."  - xml will be pretty-printed if an optional 'xmlindent' parameter is given\n"
			."  - revisions property supports optional parameters:\n"
			."      rvstart, rvend   - limits revisions by start and/or end time. The value must be 14 characters.\n"
			."                         example: '20060409000000' (year month date hour minute second)\n"
			."      rvlimit          - the number of revisions per title to return. Default is 50.\n"
			."      rvcomments       - if present, includes the revision comment in the output\n"
			."\n"
			."Credits:\n"
			."  This extension came as the result of IRC discussion between Yuri Astrakhan (en:Yurik), Tim Starling (en:Tim Starling), and Daniel Kinzler(de:Duesentrieb)\n"
			."  The extension was first implemented by Tim to provide interlanguage links and history.\n"
			."  It was later completelly rewritten by Yuri to allow for modular properties, meta information, and various formatting options.\n"
			."\n"
			."  The code is maintained by Yurik. You can leave your comments at http://en.wikipedia.org/wiki/User_talk:Yurik\n"
			."\n"
			."User Status:\n"
			."  You are " . ($wgUser->isAnon() ? 'an anonymous' : 'a logged-in') . ' ' . ($wgUser->isBot() ? 'bot' : 'user') . ' ' . $wgUser->getName() . "\n"
			."\n"
			."Version:\n"
			.'  $Id$';

		die(1);
	}
}

//
// ************************************* Print Methods *************************************
//
function printXML( &$data ) {
	global $wgRequest;
	echo '<?xml version="1.0" encoding="utf-8"?>';
	recXmlPrint( "yurik", $data, $wgRequest->getCheck('xmlindent') ? -2 : null );
}
function printHumanReadable( &$data ) {
	sanitizeOutputData($data);
	print_r($data);
}
function printParsableCode( &$data ) {
	sanitizeOutputData($data);
	var_export($data);
}
function printPHP( &$data ) {
	sanitizeOutputData($data);
	echo serialize($data);
}
function printJSON( &$data ) {
	sanitizeOutputData($data);
	if ( !function_exists( 'json_encode' ) ) {
		require_once 'json.php';
		$json = new Services_JSON();
		echo $json->encode( $data );
	} else {
		echo json_encode( $data );
	}
}

/**
* This method takes an array and converts it into an xml.
* There are several noteworthy cases:
*
*  If array contains a key "_element", then the code assumes that ALL other keys are not important and replaces them with the value['_element'].
*	Example:	name="root",  value = array( "_element"=>"page", "x", "y", "z") creates <root>  <page>x</page>  <page>y</page>  <page>z</page> </root>
*
*  If any of the array's element key is "*", then the code treats all other key->value pairs as attributes, and the value['*'] as the element's content.
*	Example:	name="root",  value = array( "*"=>"text", "lang"=>"en", "id"=>10)   creates  <root lang="en" id="10">text</root>
*
* If neither key is found, all keys become element names, and values become element content.
* The method is recursive, so the same rules apply to any sub-arrays.
*/
function recXmlPrint( $elemName, &$elemValue, $indent = -2) {
	$indstr = "";
	if( !is_null($indent) ) {
		$indent += 2;
		$indstr = "\n" . str_repeat(" ", $indent);
	}

	switch( gettype($elemValue) ) {
		case 'array':
			if( array_key_exists('*', $elemValue) ) {
				$subElemContent = $elemValue['*'];
				unset( $elemValue['*'] );
				if( gettype( $subElemContent ) === 'array' ) {
					echo $indstr . wfElement( $elemName, $elemValue, null );
					recXmlPrint( $elemName, $subElemValue, $indent );
					echo $indstr . "</$elemName>";
				} else {
					echo $indstr . wfElement( $elemName, $elemValue, $subElemContent );
				}
			} else {
				echo $indstr . wfElement( $elemName, null, null );
				if( array_key_exists('_element', $elemValue) ) {
					$subElemName = $elemValue['_element'];
					foreach( $elemValue as $subElemId => $subElemValue ) {
						if( $subElemId !== '_element' ) {
							recXmlPrint( $subElemName, $subElemValue, $indent );
						}
					}
				} else {
					foreach( $elemValue as $subElemName => $subElemValue ) {
						recXmlPrint( $subElemName, $subElemValue, $indent );
					}
				}
				echo $indstr . "</$elemName>";
			}
			break;
		case 'object':
			// ignore
			break;
		default:
			echo $indstr . wfElement( $elemName, null, $elemValue );
			break;
	}
}

?>

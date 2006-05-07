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

define( 'GEN_FUNCTION', 0 );
define( 'GEN_MIME',     1 );
define( 'GEN_ISMETA',   1 );
define( 'GEN_PARAMS',   2 );
define( 'GEN_DEFAULTS', 3 );
define( 'GEN_DESC',     4 );

$db =& wfGetDB( DB_SLAVE );
$bqp = new BotQueryProcessor( $db );
$bqp->execute();
$bqp->output();
	


class BotQueryProcessor {
	var $classname = 'BotQueryProcessor';

	/**
	* Output generators - each format name points to an array of the following parameters:
	*     0) Function to call
	*     1) mime type 
	*     2) array of accepted parameters
	*     3) array of default parameter values
	*     4) Format description
	*/
	var $outputGenerators = array(
		'xml' => array( 'printXML', 'text/xml',
			array('xmlindent', 'nousage'), 
			array(null, null), 
			array(
			"XML format",
			"Optional indentation can be enabled by supplying 'xmlindent' parameter.",
			"Errors will return this usage screen, unless 'nousage' parameter is given.",
			"Internet Explorer is known to have many issues with text/xml output.",
			"Please use other browsers or switch to html format while debuging.",
			"Example: query.php?what=info&format=xml",
			)),
		'html'=> array( 'printHTML', 'text/html',
			array('nousage'),
			array(null),
			array(
			"HTML format",
			"The data is presented as an indented syntax-highlighted XML format.",
			"Errors will return this usage screen, unless 'nousage' parameter is given.",
			"Example: query.php?what=info&format=html",
			)),
		'txt' => array( 'printHumanReadable', 'application/x-wiki-botquery-print_r', null, null, array(
			"Human-readable format using print_r() (http://www.php.net/print_r)",
			"Example: query.php?what=info&format=txt",
			)),
		'json'=> array( 'printJSON', 'application/json', null, null, array(
			"JSON format (http://en.wikipedia.org/wiki/JSON)",
			"Example: query.php?what=info&format=json",
			)),
		'php' => array( 'printPHP', 'application/vnd.php.serialized', null, null, array(
			"PHP serialized format using serialize() (http://www.php.net/serialize)",
			"Example: query.php?what=info&format=php",
			)),
		'dbg' => array( 'printParsableCode', 'application/x-wiki-botquery-var_export', null, null, array(
			"PHP source code format using var_export() (http://www.php.net/var_export)",
			"Example: query.php?what=info&format=dbg",
			)),
	);

	/**
	* Properties generators - each property points to an array of the following parameters:
	*     0) Function to call
	*     1) true/false - does this property work on individual pages?  (false for site's metadata)
	*     2) array of accepted parameters
	*     3) array of default parameter values
	*     4) Format description
	*/
	var $propGenerators = array(

		// Site-wide Generators
		'info'           => array( "genMetaSiteInfo", true, null, null, array(
			"General site information",
			"Example: query.php?what=info",
			)),
		'namespaces'     => array( "genMetaNamespaceInfo", true, null, null, array(
			"List of localized namespace names",
			"Example: query.php?what=namespaces",
			)),
		'userinfo'       => array( "genMetaUserInfo", true, null, null, array(
			"Information about current user",
			"Example: query.php?what=userinfo",
			)),
		'recentchanges'  => array( "genMetaRecentChanges", true,
			array( 'rcfrom', 'rclimit', 'rchide' ),
			array( null, 50, array(null, 'minor', 'bots', 'anons', 'liu') ),
			array(
			"Adds recently changed articles to the output list.",
			"Parameters supported:",
			"rcfrom     - Timestamp of the first entry to start from. The list order reverses.",
			"rclimit    - how many total links to return.",
			"             Smaller size is possible if pages changes multiple times.",
			"rchide     - Which entries to ignore 'minor', 'bots', 'anons', 'liu' (loged-in users).",
			"             Cannot specify both anons and liu.",
			"Example: query.php?what=recentchanges&rchide=liu|bots",
			)),
		'allpages'       => array( "genMetaAllPages", true,
			array( 'aplimit', 'apfrom', 'apnamespace' ),
			array( 50, '!', 0 ),
			array(
			"Enumerates all available pages to the output list.",
			"Parameters supported:",
			"aplimit      - how many total pages to return",
			"apfrom       - the page title to start enumerating from. Default is '!'",
			"apnamespaces - limits which namespace to enumerate. Default 0 (Main)",
			"Example: query.php?what=allpages&aplimit=50",
			)),
		'users'          => array( "genUserPages", true,
			array( 'usfrom', 'uslimit' ),
			array( null, 50 ),
			array(
			"Adds user pages to the output list.",
			"Parameters supported:",
			"usfrom     - Start user listing from...",
			"uslimit    - how many total links to return.",
			"Example: query.php?what=users&usfrom=Y",
			)),
		'dblredirects'   => array( "genMetaDoubleRedirects", true,
			array( 'dfoffset', 'drlimit' ),
			array( 0, 50 ),
			array(
			"List of double-redirect pages",
			"THIS QUERY IS CURRENTLY DISABLED DUE TO PERFORMANCE REASONS",
			"Example: query.php?what=dblredirects",
			)),

		// Page-specific Generators
		'links'          => array( "genPageLinks", false, null, null, array(
			"List of regular page links",
			"Example: query.php?what=links&titles=MediaWiki|Wikipedia",
			)),
		'langlinks'      => array( "genPageLangLinks", false, null, null, array(
			"Inter-language links",
			"Example: query.php?what=langlinks&titles=MediaWiki|Wikipedia",
			)),
		'templates'      => array( "genPageTemplates", false, null, null, array(
			"List of used templates",
			"Example: query.php?what=templates&titles=Main%20Page",
			)),
		'backlinks'      => array( "genPageBackLinksHelper", false,
			array( 'blfilter', 'bllimit', 'blcontfrom' ),
			array( array('existing', 'nonredirects', 'all'), 50, null ),
			array(
			"What pages link to this page(s)",
			"Parameters supported:",
			"blfilter   - Of all given pages, which should be queried:",
			"  'nonredirects', 'existing' (blue links, default), or 'all' (red links)",
			"bllimit    - how many total links to return",
			"blcontfrom - from which point to continue. Use the 'next' value from previous queries.",
			"Example: query.php?what=backlinks&titles=Main%20Page&bllimit=10",
			)),
		'embeddedin'     => array( "genPageBackLinksHelper", false, 
			array( 'eifilter', 'eilimit', 'eicontfrom' ), 
			array( array('existing', 'nonredirects', 'all'), 50, null ),
			array(
			"What pages include this page(s) as template(s)",
			"Parameters supported:",
			"eifilter   - Of all given pages, which should be queried:",
			"  'nonredirects', 'existing' (blue links, default), or 'all' (red links)",
			"eilimit    - how many total links to return",
			"eicontfrom - from which point to continue. Use the 'next' value from previous queries.",
			"Example: query.php?what=embeddedin&titles=Template:Stub&eilimit=10",
			)),
		'imagelinks'     => array( "genPageBackLinksHelper", false, 
			array( 'ilfilter', 'illimit', 'ilcontfrom' ),
			array( array('existing', 'nonredirects', 'all'), 50, null ),
			array(
			"What pages use this image(s)",
			"ilfilter   - Of all given images, which should be queried:",
			"  'nonredirects', 'existing' (default), or 'all' (including non-existant)",
			"illimit    - how many total links to return",
			"ilcontfrom - from which point to continue. Use the 'next' value from previous queries.",
			"Example: query.php?what=imagelinks&titles=image:test.jpg&illimit=10",
			)),
		'revisions'      => array( "genPageHistory", false,
			array( 'rvcomments', 'rvlimit', 'rvoffset', 'rvstart', 'rvend' ),
			array( null, 50, 0, null, null ),
			array(
			"Revision history - Lists edits performed to the given pages",
			"Parameters supported:",
			"rvcomments - if specified, the result will include summary strings",
			"rvlimit    - how many links to return *for each title*",
			"rvoffset   - when too many results are found, use this to page",
			"rvstart    - timestamp of the earliest entry",
			"rvend      - timestamp of the latest entry",
			"Example: query.php?what=revisions&titles=Main%20Page&rvlimit=10&rvcomments",
			)),
	);

	/**
	* Object Constructor, uses a database connection as a parameter
	*/
	function BotQueryProcessor( $db ) {
		global $wgRequest;

		$this->totalStartTime = wfTime();

		$this->data = array();
		$this->pageIdByText = array();	// reverse page ID lookup
		$this->requestsize = 0;
		$this->db = $db;

		$this->enableProfiling = !$wgRequest->getCheck('noprofile');
		
		$this->format = 'html'; // set it here because if parseFormat fails, the usage output rilies on this variable
		$this->format = $this->parseFormat( $wgRequest->getVal('format', 'html') );

		$allProperties = array_merge(array(null), array_keys( $this->propGenerators ));
		$this->properties = $this->parseMultiValue( 'what', $allProperties );

		// Neither one of these variables is referenced directly!
		// Meta generators may append titles or pageids to these varibales.
		// Do not modify this values directly - use the AddRaw() method
		$this->titles = null;
		$this->pageids = null;
		$this->normalizedTitles = array();
	}

	function execute() {
	
		// Process metadata generators
		$this->callGenerators( true );

		// Query page table and initialize page ids.
		if( $this->genPageInfo() ) {
			// Process page-related generators
			$this->callGenerators( false );
		}
		
		// Report empty query
		if( !$this->data ) {
			$this->dieUsage( 'Nothing to do', 'emptyresult' );
		}
	}

	function callGenerators( $callMetaGenerators ) {
		foreach( $this->propGenerators as $property => &$generator ) {
			if( $generator[GEN_ISMETA] === $callMetaGenerators && in_array( $property, $this->properties )) {
				$this->{$generator[GEN_FUNCTION]}($property, $generator);
			}
		}
	}

	function output($isError = false) {
		global $wgRequest, $wgUser;

		// hack: pretend that profiling was started at the begining of the class execution.
		$this->startTime = $this->totalStartTime;
		$this->endProfiling( 'total' );
		
		$printer = $this->outputGenerators[$this->format][GEN_FUNCTION];
		$mime = $this->outputGenerators[$this->format][GEN_MIME];
		header( "Content-Type: $mime; charset=utf-8;" );
		if( !$isError ) {
			$printer( $this->data );
		} else {
			$printer( $this->data['query'] );
		}
		
		//
		// Log request - userid (non-identifiable), status, what is asked, request size, additional parameters
		//
		$userIdentity = md5( $wgUser->getName() ) . "-" . ($wgUser->isAnon() ? "anon" : ($wgUser->isBot() ? "bot" : "usr"));
		$what = $wgRequest->getVal('what');
		$format = $wgRequest->getVal('format');
		$params = mergeParameters( $this->propGenerators );
		$params = array_merge( $params, mergeParameters( $this->outputGenerators ));
		$params = array_unique($params);
		$paramVals = array();
		foreach( $params as $param ) {
			$val = $wgRequest->getVal($param);
			if( $val !== null ) {
				$paramVals[] = "$param=$val";
			}
		}
		$paramStr = implode( '&', $paramVals );
		$perfVals = array();
		if( array_key_exists('query', $this->data) ) {
			foreach( $this->data['query'] as $module => $values ) {
				if( is_array( $values ) && array_key_exists('time', $values) ) {
					$perfVals[] = "$module={$values['time']}";
				}
			}
		}
		$perfStr = implode( '&', $perfVals );
		$msg = "$userIdentity\t$format\t$what\t{$this->requestsize}\t$paramStr\t$perfStr";
		wfDebugLog( 'query', $msg );
	}


	//
	// ************************************* INPUT PARSERS *************************************
	//
	function parseFormat( $format ) {
		if( array_key_exists($format, $this->outputGenerators) ) {
			return $format;
		} else {
			$this->dieUsage( "Unrecognised format '$format'", 'badformat' );
		}
	}
	
	function parseMultiValue( $valueName, $allowedValues ) {
		global $wgRequest;

		$values = $wgRequest->getVal($valueName, $allowedValues[0]);		
		$valuesList = explode( '|', $values );
		$unknownValues = array_diff( $valuesList, $allowedValues);
		if( $unknownValues ) {
			$this->dieUsage("Unrecognised value" . (count($unknownValues)>1?"s '":" '") . implode("', '", $unknownValues) . "' for parameter '$valueName'",
							"unknown_$valueName" );
		}

		return $valuesList;
	}

	//
	// ************************************* GENERATORS *************************************
	//
	function genPageInfo() {
		global $wgUser, $wgRequest;
		
		$where = array();
		
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
					$this->dieUsage( "bad title $titleString", 'pi_invalidtitle' );
				}
				if ( !$titleObj->userCanRead() ) {
					$this->dieUsage( "No read permission for $titleString", 'pi_titleaccessdenied' );
				}
				$linkBatch->addObj( $titleObj );
				
				// Make sure we remember the original title that was given to us
				// This way the caller can correlate new titles with the originally requested if they change namespaces, etc
				if( $titleString !== $titleObj->getPrefixedText() ) {
					$this->normalizedTitles[$titleString] = &$titleObj;
				}
				
			}
			if ( $linkBatch->isEmpty() ) {
				$this->dieUsage( "no titles could be found", 'pi_novalidtitles' );
			}
			// Create a list of pages to query
			$where[] = $linkBatch->constructSet( 'page', $this->db );
			$this->requestsize += $linkBatch->getSize();
			
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
				$this->dieUsage( "pageids contains a bad id", 'pi_badpageid' );
			}
			$where['page_id'] = $pageids;
			$this->requestsize += count($pageids);
		}

		// Do we have anything to do?
		if( $this->requestsize == 0 ) {
			return false;	// Nothing to do for any of the page generators
		}
		
		//
		// User restrictions
		//
		$this->validateLimit( 'pi_botquerytoobig', $this->requestsize, 50, 1000 );
		
		//
		// Make sure that this->data['pages'] is empty
		//
		if( array_key_exists('pages', $this->data) ) {
			die( "internal error - 'pages' should not yet exist" );
		}
		$this->data['pages'] = array();

		//
		// Query page information with the given lists of titles & pageIDs
		//
		$redirects = array();
		$this->startProfiling();
		$res = $this->db->select( 'page',
			array( 'page_id', 'page_namespace', 'page_title', 'page_is_redirect', 'page_touched', 'page_latest' ),
			$this->db->makeList( $where, LIST_OR ),
			$this->classname . '::genPageInfo' );
		$this->endProfiling('pageInfo');
		while( $row = $this->db->fetchObject( $res ) ) {
			$title = Title::makeTitle( $row->page_namespace, $row->page_title );
			if ( !$title->userCanRead() ) {
				$this->db->freeResult( $res );
				$this->dieUsage( "No read permission for $titleString", 'pi_pageidaccessdenied' );
			}
			$data = &$this->data['pages'][$row->page_id];
			$this->pageIdByText[$title->getPrefixedText()] = $row->page_id;
			$data['_obj']    = $title;
			$data['ns']      = $title->getNamespace();
			$data['title']   = $title->getPrefixedText();
			$data['id']      = $row->page_id;
			$data['touched'] = $row->page_touched;
			$data['revid']   = $row->page_latest;
			if ( $row->page_is_redirect ) {
				$data['redirect'] = '';
				$redirects[] = $row->page_id;
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
		$i = -1;
		foreach( $nonexistentPages as $namespace => &$stuff ) {
			foreach( $stuff as $dbk => &$arbitrary ) {
				$title = Title::makeTitle( $namespace, $dbk );
				// Must do this check even for non-existent pages, as some generators can give related information
				if ( !$title->userCanRead() ) {
					$this->dieUsage( "No read permission for $titleString", 'pi_nopageaccessdenied' );
				}
				$data = &$this->data['pages'][$i];
				$this->pageIdByText[$title->getPrefixedText()] = $i;
				$data['_obj']    = $title;
				$data['title']   = $title->getPrefixedText();
				$data['ns']      = $title->getNamespace();
				$data['id']      = 0;
				$i--;
			}
		}
		
		//
		// Process redirects
		//
		if( $redirects ) {
			// If the user requested links, redirect links will be populated.
			// Otherwise, we have to do it manually here by calling links generator with a custom list of IDs
			$prop = 'links';
			if( !in_array( $prop, $this->properties )) {
				$generator = $this->propGenerators[$prop];
				$this->{$generator[GEN_FUNCTION]}( $prop, $generator, $redirects );
			}
		}

		//
		// When normalized title differs from what was given, append the given title(s)
		//
		foreach( $this->normalizedTitles as $givenTitle => &$title ) {
			$pageId = $this->pageIdByText[$title->getPrefixedText()];
			$data = &$this->data['pages'][$pageId]['rawTitles'];
			$data['_element'] = 'title';
			$data[] = $givenTitle;
		}
		
		return true; // success
	}

	function genMetaSiteInfo(&$prop, &$genInfo) {
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

	function genMetaNamespaceInfo(&$prop, &$genInfo) {
		global $wgContLang;
		$meta = array();
		$meta['_element'] = 'ns';
		foreach( $wgContLang->getFormattedNamespaces() as $ns => $title ) {
			$meta[$ns] = array( "id"=>$ns, "*" => $title );
		}
		$this->data['meta']['namespaces'] = $meta;
	}

	function genMetaUserInfo(&$prop, &$genInfo) {
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

	function genMetaRecentChanges(&$prop, &$genInfo) {
		
		extract( $this->getParams( $prop, $genInfo ));		
		# It makes no sense to hide both anons and logged-in users
		if( in_array('anons', $rchide) && in_array('liu', $rchide) ) {
			$this->dieUsage( "Both 'anons' and 'liu' cannot be given for 'rchide' parameter", 'rc_badrchide' );
		}
		$this->validateLimit( 'rc_badrclimit', $rclimit, 100, 5000 );

		$conds = array();		
		if ( $rcfrom != '' ) {
			$conds[] = 'rc_timestamp >= ' . $this->prepareTimestamp($rcfrom);
		}

		foreach( $rchide as &$elem ) {
			switch( $elem ) {
				case '': // nothing
					break;
				case 'minor':
					$conds[] = 'rc_minor = 0';
					break;
				case 'bots':
					$conds[] = 'rc_bot = 0';
					break;
				case 'anons':
					$conds[] = 'rc_user != 0';
					break;
				case 'liu':
					$conds[] = 'rc_user = 0';
					break;
				default:
					die( "Internal error - Unknown hide param '$elem'" );
			}
		}	

		$options = array( 'USE INDEX' => 'rc_timestamp', 'LIMIT' => $rclimit );
		$options['ORDER BY'] = 'rc_timestamp' . ( $rcfrom != '' ? '' : ' DESC' );

		$this->startProfiling();
		$res = $this->db->select(
			'recentchanges',
			'rc_cur_id',
			$conds,
			$this->classname . '::genMetaRecentChanges',
			$options
			);
		$this->endProfiling($prop);
		while ( $row = $this->db->fetchObject( $res ) ) {
			if( $row->rc_cur_id != 0 ) {
				$this->addRaw( 'pageids', $row->rc_cur_id );
			}
		}
		$this->db->freeResult( $res );
	}
	
	function genUserPages(&$prop, &$genInfo) {
		global $wgContLang;
		
		extract( $this->getParams( $prop, $genInfo ));

		$this->startProfiling();
		$res = $this->db->select(
			'user',
			'user_name',
			"user_name >= " . $this->db->addQuotes($usfrom),
			$this->classname . '::genUserPages',
			array( 'ORDER BY' => 'user_name', 'LIMIT' => $uslimit )
			);
		$this->endProfiling($prop);
		
		$userNS = $wgContLang->getNsText(NS_USER);
		if( !$userNS ) $userNS = 'User';
		$userNS .= ':';
		
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addRaw( 'titles', $userNS . $row->user_name );
		}
		$this->db->freeResult( $res );
	}

	//
	// TODO: This is very inefficient - we can get the actual page information, instead we make two identical query.
	//
	function genMetaAllPages(&$prop, &$genInfo) {
		global $wgContLang;
		extract( $this->getParams( $prop, $genInfo ));

		$ns = $wgContLang->getNsText($apnamespace);
		if( $ns === false ) {
			$this->dieUsage( "Unknown namespace $ns", 'ap_badnamespace' );
		}
		$ns .= ':';

		$this->startProfiling();
		$res = $this->db->select(
			array( 'page' ),
			array( 'page_title' ),
			array( 'page_namespace=' . $this->db->addQuotes($apnamespace) . ' AND page_title>=' . $this->db->addQuotes($apfrom) ),
			$this->classname . '::genMetaAllPages',
			array( 'FORCE INDEX' => 'name_title', 'LIMIT' => $aplimit+1, 'ORDER BY' => 'page_title' ));
		$this->endProfiling($prop);

		// Add found page ids to the list of requested titles - they will be auto-populated later
		$count = 0;
		while ( $row = $this->db->fetchObject( $res ) ) {
			if( ++$count >= $aplimit ) {
				// We've reached the one extra which shows that there are
				// additional pages to be had. Stop here...
				break;
			}
			$this->addRaw( 'titles', $ns . $row->page_title );
		}
		if( $count < $aplimit || !$row ) {
			$this->addStatusMessage( $prop, array('next' => 0) );
		} else {
			$this->addStatusMessage( $prop, array('next' => $row->page_title) );
		}
		$this->db->freeResult( $res );
	}

	function genMetaDoubleRedirects(&$prop, &$genInfo) {
		global $wgUser;

		$this->dieUsage( "DoubleRedirect generator is disabled until caching is implemented", 'dr_disabled' );
		
		if( !$wgUser->isBot() ) {
			$this->dieUsage( "Only bots are allowed to query for double-redirects", 'dr_notbot' );
		}

		extract( $this->getParams( $prop, $genInfo ));
		extract( $this->db->tableNames( 'page', 'pagelinks' ) );
		
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
			" AND lb.pl_title=pc.page_title";
			
		$sql = $this->db->limitResult( $sql, $drlimit, $droffset );

		// Add found page ids to the list of requested ids - they will be auto-populated later
		$this->startProfiling();
		$res = $this->db->query( $sql, $this->classname . '::genMetaDoubleRedirects' );
		$this->endProfiling($prop);
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addRaw( 'pageids', $row->id_a .'|'. $row->id_b .'|'. $row->id_c );
			$this->data['pages'][$row->id_a]['dblredirect'] = $row->id_c;
		}
		$this->db->freeResult( $res );
	}

	function genPageLangLinks(&$prop, &$genInfo) {
		if( !$this->nonRedirPageIds ) {
			return;
		}		
		$this->startProfiling();
		$res = $this->db->select(
			array( 'langlinks' ),
			array( 'll_from', 'll_lang', 'll_title' ),
			array( 'll_from' => $this->nonRedirPageIds ),
			$this->classname . '::genPageLangLinks' );
		$this->endProfiling($prop);
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->ll_from, 'langlinks', 'll', array('lang' => $row->ll_lang, '*' => $row->ll_title));
		}
		$this->db->freeResult( $res );
	}

	function genPageTemplates(&$prop, &$genInfo) {
		if( !$this->nonRedirPageIds ) {
			return;
		}
		$this->startProfiling();
		$res = $this->db->select(
			'templatelinks',
			array( 'tl_from', 'tl_namespace', 'tl_title' ),
			array( 'tl_from' => $this->nonRedirPageIds ),
			$this->classname . '::genPageTemplates' );
		$this->endProfiling($prop);
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->tl_from, 'templates', 'tl', $this->getLinkInfo( $row->tl_namespace, $row->tl_title ));
		}
		$this->db->freeResult( $res );
	}

	/**
	* Generates list of links for all pages. Optionally it can be called to populate only a subset of pages by given ids.
	*/
	function genPageLinks(&$prop, &$genInfo, $pageIdsList = null ) {
		if( $pageIdsList === null ) {
			$pageIdsList = $this->nonRedirPageIds;
		}
		if( !$pageIdsList ) {
			return;
		}
		$this->startProfiling();
		$res = $this->db->select(
			'pagelinks',
			array( 'pl_from', 'pl_namespace', 'pl_title' ),
			array( 'pl_from' => $pageIdsList ),
			$this->classname . '::genPageLinks' );
		$this->endProfiling($prop);
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->pl_from, 'links', 'l', $this->getLinkInfo( $row->pl_namespace, $row->pl_title ));
		}
		$this->db->freeResult( $res );
	}	
	
	/**
	* Generate backlinks for either links, templates, or both
	* $type - either 'template' or 'page'
	*/
	function genPageBackLinksHelper(&$prop, &$genInfo) {
		//
		// Determine what is being asked
		//		
		$isImage = false;
		switch( $prop ) {
			case 'embeddedin' :
				$prefix = 'tl';	// database column name prefix
				$code = 'ei';			// 
				$linktbl = 'templatelinks';
				break;
			case 'backlinks' :
				$prefix = 'pl';
				$code = 'bl';
				$linktbl = 'pagelinks';
				break;
			case 'imagelinks' :
				$prefix = 'il';
				$code = 'il';
				$linktbl = 'imagelinks';
				$isImage = true;
				break;
			default :
				die("unknown type");
		}

		//
		// Parse and validate parameters
		//
		$parameters = $this->getParams( $prop, $genInfo );		
		$contFrom = $parameters["{$code}contfrom"];
		$limit  = intval($parameters["{$code}limit"]) + 1;
		$filter = $parameters["{$code}filter"];
		if( count($filter) != 1 ) {
			$this->dieUsage( "{$code}filter must either be 'all', 'existing', or 'nonredirects'", "{$code}_badmultifilter" );
		} else {
			$filter = $filter[0];
		}

		//
		// Prase contFrom - will be in the format    ns|db_key|page_id - determine how to continue
		//
		if( $contFrom ) {
			$contFromList = explode( '|', $contFrom );
			$contFromValid = count($contFromList) === 3;
			if( $contFromValid ) {
				$fromNs = intval($contFromList[0]);
				$fromTitle = $contFromList[1];
				$contFromValid = (($fromNs !== 0 || $contFromList[0] === '0') && count($fromTitle) > 0);
			}
			if( $contFromValid ) {
				$fromPageId = intval($contFromList[2]);
				$contFromValid = ($fromPageId > 0);
			}
			if( !$contFromValid ) {
				$this->dieUsage( "{$code}contfrom is invalid. You should pass the original value retured by the previous query", "{$code}_badcontfrom" );
			}
		}
		//
		// Parse page type filtering
		//
		$nonredir = $existing = $all = false;
		switch( $filter ) {
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
				$this->dieUsage( "{$code}filter '$filter' is not one of the allowed: 'all', 'existing' [default], and 'nonredirects'", "{$code}_badfilter" );
		}

		//
		// Make a list of pages to query
		//
		$linkBatch = new LinkBatch;
		foreach( $this->data['pages'] as $key => &$page ) {
			if( (
				( $key < 0 && $all && array_key_exists('_obj', $page) ) ||
				( $key > 0 && ($existing || ($nonredir && !array_key_exists('redirect', $page))) )
				)
			&&
				( !$isImage || $page['ns'] == NS_IMAGE )	// when doing image links search, only allow NS_IMAGE
			) {
				$title = $page['_obj'];
				// remove any items already processed by previous queries
				if( $contFrom ) {
					if( $title->getNamespace() < $fromNs ||
						($title->getNamespace() === $fromNs && $title->getDBkey() < $fromTitle)) {
						continue;
					}
				}
				$linkBatch->addObj( $title );
			}
		}
		
		if( $linkBatch->isEmpty() ) {
			$this->addStatusMessage( $prop, array('error'=>'emptyrequest') );
			return; // Nothing to do
		}
		
		//
		// Create query parameters
		//
		$columns = array( "{$prefix}_from from_id", 'page_namespace from_namespace', 'page_title from_title' );
		$where = array( "{$prefix}_from = page_id" );
		if( $isImage ) {
			$columns[] = "{$prefix}_to to_title";
			$where["{$prefix}_to"] = array_keys($linkBatch->data[NS_IMAGE]);
			$orderBy   = "{$prefix}_to, {$prefix}_from";
			if( $contFrom ) {
				$where[] = "(({$prefix}_to > " . $this->db->addQuotes( $fromTitle ) ." ) OR "
						  ."({$prefix}_to = " . $this->db->addQuotes( $fromTitle ) ." AND {$prefix}_from >= $fromPageId))"; 
			}
		} else {
			$columns[] = "{$prefix}_namespace to_namespace";
			$columns[] = "{$prefix}_title to_title";
			$where[]   = $linkBatch->constructSet( $prefix, $this->db );
			$orderBy   = "{$prefix}_namespace, {$prefix}_title, {$prefix}_from";
			if( $contFrom ) {
				$where[] = 	 "({$prefix}_namespace > " . $fromNs ." OR "
							."({$prefix}_namespace = " . $fromNs ." AND "
								."({$prefix}_title > " . $this->db->addQuotes( $fromTitle ) ." OR "
								."({$prefix}_title = " . $this->db->addQuotes( $fromTitle ) ." AND "
									."{$prefix}_from >= $fromPageId))))"; 
			}
		}
		$options = array( 'ORDER BY' => $orderBy, 'LIMIT' => $limit );
		
		//
		// Execute
		//
		$this->startProfiling();
		$res = $this->db->select(
			array( $linktbl, 'page' ),
			$columns,
			$where,
			$this->classname . "::genPageBackLinks_{$code}",
			$options );
		$this->endProfiling($prop);

		$count = 0;
		while ( $row = $this->db->fetchObject( $res ) ) {
			if( ++$count >= $limit ) {
				// We've reached the one extra which shows that there are
				// additional pages to be had. Stop here...
				break;
			}
			$pageId = $this->lookupPageIdByTitle( ($isImage ? NS_IMAGE : $row->to_namespace), $row->to_title );
			$values = $this->getLinkInfo( $row->from_namespace, $row->from_title, $row->from_id );
			$this->addPageSubElement( $pageId, $prop, $code, $values );
		}
		if( $count < $limit || !$row ) {
			$this->addStatusMessage( $prop, array('next' => 0) );
		} else {
			$this->addStatusMessage( $prop, 
				array('next' => ($isImage ? NS_IMAGE : $row->to_namespace) ."|{$row->to_title}|{$row->from_id}") );
		}
		$this->db->freeResult( $res );
	}
	
	function genPageHistory(&$prop, &$genInfo) {
		if( !$this->existingPageIds ) {
			return;
		}
		extract( $this->getParams( $prop, $genInfo ));

		$fields = array('rev_id', 'rev_timestamp', 'rev_user', 'rev_user_text', 'rev_minor_edit');
		if( isset($rvcomments) ) {
			$fields[] = 'rev_comment';
		}
		$conds = array( 'rev_deleted' => 0 );
		if ( isset($rvstart) ) {
			$conds[] = 'rev_timestamp >= ' . $this->prepareTimestamp($rvstart);
		}
		if ( isset($rvend) ) {
			$conds[] = 'rev_timestamp <= ' . $this->prepareTimestamp($rvend);
		}
		$options = array(
			'LIMIT' => $rvlimit,
			'ORDER BY' => 'rev_timestamp DESC'
		);
		if( $rvoffset !== 0 ) {
			$options['OFFSET'] = $rvoffset;
		}
		if( $rvlimit * count($this->existingPageIds) > 20000 ) {
			$this->dieUsage( "rvlimit multiplied by number of requested titles must be less than 20000", 'rv_querytoobig' );
		}

		$this->startProfiling();
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
				$vals['*'] = $rvcomments ? $row->rev_comment : '';
				$this->addPageSubElement( $pageId, 'revisions', 'rv', $vals);
			}
			$this->db->freeResult( $res );
		}
		$this->endProfiling($prop);
	}

	//
	// ************************************* UTILITIES *************************************
	//
	
	/**
	* From two parameter arrays, makes an array of the values provided by the user.
	*/
	function getParams( &$property, &$generator ) {
		global $wgRequest;
		
		$paramNames = &$generator[GEN_PARAMS];
		$paramDefaults = &$generator[GEN_DEFAULTS];
		if( count($paramNames) !== count($paramDefaults) ) {
			die("Internal error: '$property' param count mismatch");
		}
		$results = array();
		for( $i = 0; $i < count($paramNames); $i++ ) {
			$param = &$paramNames[$i];
			$dflt = &$paramDefaults[$i];
			switch( gettype($dflt) ) {
				case 'NULL':
				case 'string':
					$result = $wgRequest->getVal( $param, $dflt );
					break;
				case 'integer':
					$result = $wgRequest->getInt( $param, $dflt );
					break;
				case 'array':
					$result = $this->parseMultiValue( $param, $dflt );
					break;
				default:
					die('Internal error: unprocessed type ' . gettype($dflt));
			}
			$results[$param] = $result;
		}
		return $results;
	}
	
	/**
	* Lookup of the page id by ns:title in the data array, and will die if no such title is found
	*/
	function lookupPageIdByTitle( $ns, &$dbkey ) {
		$prefixedText = Title::makeTitle( $ns, $dbkey )->getPrefixedText();
		if( array_key_exists( $prefixedText, $this->pageIdByText )) {
			return $this->pageIdByText[$prefixedText];
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
			$this->dieUsage( 'Incorrect timestamp format', 'badtimestamp' );
		}
	}
	
	/**
	* NOTE: This function must not be called after calling header()
	* Creates a human-readable usage information message
	*/
	function dieUsage( $message, $errorcode ) {
		global $wgUser, $wgRequest;

		$this->addStatusMessage( 'error', $errorcode );
		if( !$wgRequest->getCheck('nousage') && 
				($this->format === 'xml' || $this->format === 'html' )) {
				
			$indentSize = 12;
			$indstr = str_repeat(" ", $indentSize+7);
			
			$formats = "";
			foreach( $this->outputGenerators as $format => &$generator ) {
				$formats .= sprintf( "  %-{$indentSize}s - %s\n", 
					$format,
					mergeDescriptionStrings($generator[GEN_DESC], $indstr));
			}

			$props = "\n  *These properties apply to the entire site*\n";
			foreach( $this->propGenerators as $property => &$generator ) {
				if( $generator[GEN_ISMETA] ) {
					$props .= sprintf( "  %-{$indentSize}s - %s\n", $property, 
								mergeDescriptionStrings($generator[GEN_DESC], $indstr));
				}
			}
			$props .= "\n  *These properties apply to the specified pages*\n";
			foreach( $this->propGenerators as $property => &$generator ) {
				if( !$generator[GEN_ISMETA] ) {
					$props .= sprintf( "  %-{$indentSize}s - %s\n", $property, 
								mergeDescriptionStrings($generator[GEN_DESC], $indstr));
				}
			}

			// No need to html-escape $message - it gets done as part of the xml/html generation
			$msg = array(
				"",
				"",
				"*------ Error: $message ($errorcode) ------*",
				"",
				"Summary:",
				"  This API provides a way for your applications to query data directly from the MediaWiki servers.",
				"  One or more pieces of information about the site and/or a given list of pages can be retrieved.",
				"  Information may be returned in either a machine (xml, json, php) or a human readable (html, dbg) format.",
				"",
				"Usage:",
				"  query.php ? format=... & what=...|...|... & titles=...|...|... & ...",
				"",
				"Common parameters:",
				"    format     - How should the output be formatted. See formats section.",
				"    what       - What information the server should return. See properties section.",
				"    titles     - A list of titles, separated by the pipe '|' symbol.",
				"    pageids    - A list of page ids, separated by the pipe '|' symbol.",
				"    noprofile  - When present, each sql query execution time will be hidden. (Optional)",
				"",
				"Examples:",
				"    query.php?format=xml&what=links|templates&titles=User:Yurik",
				"  This query will return a list of all links and templates used on the User:Yurik",
				"",
				"    query.php?format=xml&what=revisions&titles=Main_Page&rvlimit=100&rvstart=20060401000000&rvcomments",
				"  Get a list of 100 last revisions of the main page with comments, but only if it happened after midnight April 1st 2006",
				"",
				"Supported Formats:",
				$formats,
				"",
				"Supported Properties:",
				$props,
				"",
				"Notes:",
				"  Some properties may add status information to the 'query' element.",
				"",
				"Credits:",
				"  This extension came as the result of IRC discussion between Yuri Astrakhan (en:Yurik), Tim Starling (en:Tim Starling), and Daniel Kinzler(de:Duesentrieb)",
				"  The extension was first implemented by Tim to provide interlanguage links and history.",
				"  It was later completelly rewritten by Yuri to allow for modular properties, meta information, and various formatting options.",
				"",
				"  The code is maintained by Yuri Astrakhan (FirstnameLastname@gmail.com)",
				"  You can also leave your comments and suggestions at http://en.wikipedia.org/wiki/User_talk:Yurik",
				"",
				"User Status:",
				"  You are " . ($wgUser->isAnon() ? "an anonymous" : "a logged-in") . " " . ($wgUser->isBot() ? "bot" : "user") . " " . $wgUser->getName(),
				"",
				"Version:",
				'  $Id$',
				"",
				);
		
			$this->addStatusMessage( 'usage', implode("\n", $msg), true );
		}
		$this->output(true);
		die(0);
	}
	
	function addStatusMessage( $module, $value, $preserveXmlSpacing = false ) {
		if( !array_key_exists( 'query', $this->data )) {
			$this->data['query'] = array();
		}
		if( !array_key_exists( $module, $this->data['query'] )) {
			$this->data['query'][$module] = array();
		}
		
		$element = &$this->data['query'][$module];
		if( is_array($value) ) {
			$element = array_merge( $element, $value );
			if( !array_key_exists( '*', $element )) {
				$element['*'] = '';
			}
		} else {
			if( array_key_exists( '*', $element )) {
				$element['*'] .= $value;
			} else {
				$element['*'] = $value;
			}
			if( $preserveXmlSpacing ) {
				$element['xml:space'] = 'preserve';
			}
		}
	}
	
	function startProfiling() {
		$this->startTime = wfTime();
	}
	function endProfiling( $module ) {
		$timeDelta = wfTime() - $this->startTime;
		unset($this->startTime);
		$this->addStatusMessage( $module, array( 'time' => sprintf( "%1.2fms", $timeDelta * 1000.0 ) ));
	}
	
	function validateLimit( $varname, &$value, $max, $botMax = false, $min = 1 ) {
		global $wgUser;
		if( !$botMax ) $botMax = $max;
		
		if ( $value < $min ) {
			$this->dieUsage( "Minimum cannot be less than $min", $varname );
		}
		if( $wgUser->isBot() ) {
			if ( $value > $botMax ) {
				$this->dieUsage( "Bots may not request over $botMax pages", $varname );
			}
		} else {
			if( $this->requestsize > $max ) {
				$this->dieUsage( "Users may not request over $max pages", $varname );
			}
		}
	}
}

//
// ************************************* Print Methods *************************************
//
function printHTML( &$data ) {
	global $wgRequest;
?>
<html>
<head>
	<title>MediaWiki Query Interface</title>
</head>
<body>
	<br/>
<?php
	if( !array_key_exists('usage', $data) ) {
?>
	<small>
	This page is being rendered in HTML format, which might not be suitable for your application.<br/>
	See <a href="query.php">query.php</a> for more information.<br/>
	</small>
<?php
	}
?>
<pre><?php
	recXmlPrint( 'htmlprinter', 'yurik', $data, -2 );
?></pre>
</body>
<?php
}
function htmlprinter( $text ) {
	// encode all tags as safe blue strings
	$text = ereg_replace( '\<([^>]+)\>', '<font color=blue>&lt;\1&gt;</font>', $text );
	// identify URLs
	$text = ereg_replace("[a-zA-Z]+://[^ ()<\n]+", '<a href="\\0">\\0</a>', $text);
	// identify requests to query.php
	$text = ereg_replace("query\\.php\\?[^ ()<\n]+", '<a href="\\0">\\0</a>', $text);
	// make strings inside * bold
	$text = ereg_replace("\\*[^<>\n]+\\*", '<b>\\0</b>', $text);
	echo $text;
}

function printXML( &$data ) {
	global $wgRequest;
	echo '<?xml version="1.0" encoding="utf-8"?>';
	recXmlPrint( 'echoprinter', 'yurik', $data, $wgRequest->getCheck('xmlindent') ? -2 : null );
}
function echoprinter( $text ) {
	echo $text;
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
* Recursivelly removes any elements from the array that begin with an '_'.
* The content element '*' is the only special element that is left.
* Use this method when the entire data object gets sent to the user.
*/
function sanitizeOutputData( &$data ) {
	foreach( $data as $key => &$value ) {
		if( $key[0] === '_' ) {
			unset( $data[$key] );
		} elseif ( is_array( $value )) {
			sanitizeOutputData( $value );
		}
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
function recXmlPrint( $printer, $elemName, &$elemValue, $indent = -2 ) {
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
					$printer( $indstr . wfElement( $elemName, $elemValue, null ));
					recXmlPrint( $printer, $elemName, $subElemValue, $indent );
					$printer( $indstr . "</$elemName>" );
				} else {
					$printer( $indstr . wfElement( $elemName, $elemValue, $subElemContent ));
				}
			} else {
				$printer( $indstr . wfElement( $elemName, null, null ));
				if( array_key_exists('_element', $elemValue) ) {
					$subElemName = $elemValue['_element'];
					foreach( $elemValue as $subElemId => &$subElemValue ) {
						if( $subElemId !== '_element' ) {
							recXmlPrint( $printer, $subElemName, $subElemValue, $indent );
						}
					}
				} else {
					foreach( $elemValue as $subElemName => &$subElemValue ) {
						recXmlPrint( $printer, $subElemName, $subElemValue, $indent );
					}
				}
				$printer( $indstr . "</$elemName>" );
			}
			break;
		case 'object':
			// ignore
			break;
		default:
			$printer( $indstr . wfElement( $elemName, null, $elemValue ));
			break;
	}
}

function mergeDescriptionStrings( &$value, $indstr ) {
	if( is_array($value) ) {
		$value = implode( "\n", $value );
	}
	return str_replace("\n", "\n$indstr", $value);
}

function mergeParameters( &$generators ) {
	$params = array();
	foreach( $generators as $property => &$generator ) {
		$value = &$generator[GEN_PARAMS];
		if( $value !== null ) {
			if( is_array($value) ) {
				$params = array_merge( $params, $value );
			} else {
				$params[] = $value;
			}
		}
	}
	return $params;
}

?>

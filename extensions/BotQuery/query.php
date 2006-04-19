<?php

define( 'MEDIAWIKI', true );
unset( $IP );
if ( isset( $_REQUEST['GLOBALS'] ) ) {
	echo '<a href="http://www.hardened-php.net/index.76.html">$GLOBALS overwrite vulnerability</a>';
	die( -1 );
}

define( 'MW_NO_OUTPUT_BUFFER', true );
$wgNoOutputBuffer = true;

require_once( '../../includes/Defines.php' );
require_once( '../../LocalSettings.php' );
require_once( "$IP/includes/Setup.php" );

$db =& wfGetDB( DB_SLAVE );
$bqp = new BotQueryProcessor( $db );
$bqp->execute();
$bqp->output();



class BotQueryProcessor {
	var $mimeTypes = array(
		'txt' => 'text/plain',
		'dbg' => 'text/plain',
		'tsv' => 'text/tab-separated-values',
		'json' => 'application/json',
		'xml' => 'text/xml',
		'php' => 'application/vnd.php.serialized',
	);

	function BotQueryProcessor( $db ) {
		global $wgRequest;

		$this->db = $db;
		$this->format = $this->parseFormat( $wgRequest->getText('format') );
		$this->linkBatch = $this->parseTitles( $wgRequest->getText('titles') );
		$this->properties = $this->parseProperties( $wgRequest->getText('properties'));

		$this->data = array();
	}

	function output() {
		$mime = $this->mimeTypes[$this->format];
		header( "Content-Type: $mime; charset=utf-8;" );

		switch ( $this->format ) {
			case 'txt':
				print_r($this->data);
				break;
			case 'dbg':
				var_export($this->data);
				break;
			case 'xml':
				echo '<?xml version="1.0" encoding="utf-8"?>';
				recXmlPrint( "yurik", $this->data );
				break;
			case 'php':
				echo serialize( $this->data );
				break;
			case 'json':
				if ( !function_exists( 'json_encode' ) ) {
					require_once 'json.php';
					$json = new Services_JSON();
					echo $json->encode( $this->data );
				} else {
					echo json_encode( $this->data );
				}
				break;
			default:
				die( 'Internal bug - unrecognised format' );
		}
	}

	function parseFormat( $format ) {
		switch ( $format ) {
			case 'php':
				return 'php';
			case 'json':
				return 'json';
			case 'xml':
				return 'xml';
			case 'tsv':
				die( 'TSV support is not yet implemented' );
			case '':
			case 'txt':
			case 'text':
				return 'txt';
			case 'dbg':
			case 'phpcode':
				return 'dbg';
			default:
				die( 'Unrecognised format' );
		}
	}

	function parseTitles( $titles ) {
		global $wgUser;

		if ( $titles === '' ) {
			return null;
		}

		$titles = explode( '|', $titles );
		if ( $wgUser->isBot() ) {
			if ( count( $titles ) > 1000 ) {
				die ( 'Error, too many titles specified' );
			}
		} else {
			if ( count( $titles ) > 20 ) {
				die( 'Error, too many titles specified' );
			}
		}
		$linkBatch = new LinkBatch;
		foreach ( $titles as $titleString ) {
			$titleObj = Title::newFromText( $titleString );
			if ( $titleObj ) {
				$linkBatch->addObj( $titleObj );
			} /* else ignore */
		}
		return $linkBatch;
	}

	function parseProperties( $properties ) {
		global $wgUser;

		if ( $properties == '' ) {
			die( 'Error, must specify one or more valid properties in the "properties" parameter' );
		}
		return explode( '|', $properties );
	}


	function execute() {
		$fname = 'BotQueryProcessor';

		$knownProperties = array( 'siteinfo', 'sitenamespaces', 'langlinks', 'templates', 'links', 'history', 'barehistory' );
		$unknownProperties = array_diff( $this->properties, $knownProperties);
		if( $unknownProperties ) {
			die( "Unrecognised property " . implode(',', $unknownProperties) .
				".\nCurrently supported properties: " . implode(',', $knownProperties));
		}

		// basic site information
		if ( $this->processProp('siteinfo') ) {
			$this->genMetaSiteInfo( $this->data['meta'] );
		}
		// the list of localized namespaces defined for this site
		if ( $this->processProp('sitenamespaces') ) {
			$this->genMetaNamespaceInfo( $this->data['meta'] );
		}

		if ( $this->linkBatch === null ) {
			return;   // No titles were given, skip any page generation
		}

		// Query page table
		// This call initializes *PageIds member variables
		// We stop if no pages need to be processed
		if( $this->genPageInfo( $this->data['pages'], $fname )) {
			return;
		}

		// gather interlanguage links
		if ( $this->processProp('langlinks') ) {
			$this->genPageLangLinks( $this->data['pages'], $fname );
		}

		// gather templates
		if ( $this->processProp('templates') ) {
			$this->genPageTemplates( $this->data['pages'], $fname );
		}

		// gather regular links to other pages
		if ( $this->processProp('links') ) {
			$this->genPageLinks( $this->data['pages'], $fname );
		}

		// gather revision history
		if ( $this->processProp('history') || $this->processProp('barehistory') ) {
			$this->genPageHistory( $this->data['pages'], $fname, $this->processProp('history') );
		}
	}

	function genMetaSiteInfo( &$data ) {
		global $wgSitename, $wgVersion, $wgCapitalLinks;
		$data['sitename']  = $wgSitename;
		$data['generator'] = "MediaWiki $wgVersion";
		$data['case']      = $wgCapitalLinks ? 'first-letter' : 'case-sensitive'; // "case-insensitive" option is reserved for future

		$mainPage = Title::newFromText( wfMsgForContent( 'mainpage' ) );
		$data['mainpage']  = $mainPage->getText();
		$data['base']      = $mainPage->getFullUrl();
	}

	function genMetaNamespaceInfo( &$data ) {
		global $wgContLang;
		$data['namespaces']['_element'] = 'ns';
		foreach( $wgContLang->getFormattedNamespaces() as $ns => $title ) {
			$data['namespaces'][$ns] = array( "id"=>$ns, "_content" => $title );
		}
	}

	function genPageInfo( &$data, $fname ) {
		// Create a list of pages to query
		$where = $this->linkBatch->constructSet( 'page', $this->db );
		if ( !$where ) {
			return True;   // Nothing to do
		}

		$res = $this->db->select( 'page',
			array( 'page_id', 'page_namespace', 'page_title', 'page_is_redirect' ),
			$where,
			$fname . '::genPageInfo' );

		$redirects = array();
		$nonexistentPages = $this->linkBatch->data;

		while ( $row = $this->db->fetchObject( $res ) ) {
			$title = Title::makeTitle( $row->page_namespace, $row->page_title );
			$data[$row->page_id] = array(
				'title' => $title->getPrefixedText(),
				'id' => $row->page_id,
			);

			// Strike out link
			unset( $nonexistentPages[$row->page_namespace][$row->page_title] );

			if ( $row->page_is_redirect ) {
				$redirects[] = $row->page_id;
			}
		}
		$this->db->freeResult( $res );

		// This list can later be used to filter other tables by page Id
		$this->allPageIds = array_keys( $data );
		$this->inAllPageIds = $this->db->makeList( $this->allPageIds );
		$this->inRealPageIds = $this->db->makeList( array_diff_key($this->allPageIds, $redirects) );

		// Must not alter $data[] until generating Page Ids
		$data['_element'] = 'page';

		// Add records for non-existent page titles
		$i = -1;
		foreach ( $nonexistentPages as $namespace => $stuff ) {
			foreach ( $stuff as $dbk => $arbitrary ) {
				$title = Title::makeTitle( $namespace, $dbk );
				$data[$i--] = array(
					'title' => $title->getPrefixedText(),
					'id' => 0
				);
			}
		}

		// Process redirects
		if( $redirects ) {
			$res = $this->db->select(
				'pagelinks',
				array( 'pl_from', 'pl_namespace', 'pl_title' ),
				'pl_from IN (' . implode( ',', $redirects ) . ')',
				$fname . '::genPageRedirects' );
			$redirectTargets = array();
			while ( $row = $this->db->fetchObject( $res ) ) {
				$data[$row->pl_from]['redirect'] = $this->getLinkInfo( $row->pl_namespace, $row->pl_title );
			}
			$this->db->freeResult( $res );
		}
	}

	function genPageLangLinks( &$data, $fname ) {
		$res = $this->db->select(
			array( 'langlinks' ),
			array( 'll_from', 'll_lang', 'll_title' ),
			"ll_from IN ({$this->inRealPageIds})",
			$fname . '::genPageLangLinks' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->ll_from, 'langlinks', 'll', array('lang' => $row->ll_lang, '_content' => $row->ll_title));
		}
		$this->db->freeResult( $res );
	}

	function genPageTemplates( &$data, $fname ) {
		$res = $this->db->select(
			'templatelinks',
			array( 'tl_from', 'tl_namespace', 'tl_title' ),
			"tl_from IN ({$this->inRealPageIds})",
			$fname . '::genPageTemplates' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->tl_from, 'templates', 'tl', $this->getLinkInfo( $row->tl_namespace, $row->tl_title ));
		}
		$this->db->freeResult( $res );
	}

	function genPageLinks( &$data, $fname ) {
		$res = $this->db->select(
			'pagelinks',
			array( 'pl_from', 'pl_namespace', 'pl_title' ),
			"pl_from IN ({$this->inRealPageIds})",
			$fname . '::genPageLinks' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->pl_from, 'links', 'l', $this->getLinkInfo( $row->pl_namespace, $row->pl_title ));
		}
		$this->db->freeResult( $res );
	}

	function genPageHistory( &$data, $fname, $includeComments ) {
		global $wgRequest;

		// select *:  rev_page, rev_text_id, rev_comment, rev_user, rev_user_text, rev_timestamp, rev_minor_edit, rev_deleted
		$fields = array('rev_timestamp','rev_user','rev_user_text','rev_minor_edit');
		if( $includeComments ) {
			$fields[] = 'rev_comment';
		}

		$conds = array(
			'rev_deleted' => 0,
		);

		$start = $wgRequest->getText( 'rvstart' );
		if ( $start != '' ) {
			$conds[] = 'rev_timestamp <= ' . $this->prepareTimestamp($start);
		}
		
		$end = $wgRequest->getText( 'rvend' );
		if ( $end != '' ) {
			$conds[] = 'rev_timestamp <= ' . $this->prepareTimestamp($end);
		}

		$limit = $wgRequest->getInt( 'rvlimit', 50 );
		$options = array(
			'LIMIT' => $limit,
			'ORDER BY' => 'rev_timestamp DESC'
		);

		if( $limit * count($this->allPageIds) > 20000 ) {
			die( "limit multiplied by number of requested titles must be <= 20000" );
		}

		foreach( $this->allPageIds as $pageId ) {
			$conds['rev_page'] = $pageId;
			$res = $this->db->select( 'revision', $fields, $conds, $fname . '::genPageHistory', $options );
			while ( $row = $this->db->fetchObject( $res ) ) {
				$vals = array(
					'timestamp' => wfTimestamp( TS_ISO_8601, $row->rev_timestamp ),
					'user' => $row->rev_user_text
					);
				if( !$row->rev_user ) {
					$vals['anon'] = '';
				}
				if( $row->rev_minor_edit ) {
					$vals['minor'] = '';
				}
				$vals['_content'] = $includeComments ? $row->rev_comment : '';
				$this->addPageSubElement( $pageId, 'revisions', 'rv', $vals);
			}
			$this->db->freeResult( $res );
		}
	}

	function getTitleInfo( $title ) {
		$data = array();
		if( $title->getNamespace() != NS_MAIN ) {
			$data['ns'] = $title->getNamespace();
		}
		if( $title->isExternal() ) {
			$data['iw'] = $title->getInterwiki();
		}
		$data['_content'] = $title->getPrefixedText();

		return $data;
	}

	function getLinkInfo( $ns, $title ) {
		return $this->getTitleInfo( Title::makeTitle( $ns, $title ));
	}

	function addPageSubElement( $pageId, $mainElem, $itemElem, $params ) {
		$data = & $this->data['pages'][$pageId][$mainElem];
		$data['_element'] = $itemElem;
		$data[] = $params;
	}

	function processProp( $property ) {
		return in_array( $property, $this->properties );
	}

	function prepareTimestamp( $value ) {
		if ( preg_match( '/^[0-9]{14}$/', $value ) ) {
			return $this->db->addQuotes( $value );
		} else {
			die( 'Error, rvstart and rvend parameters must be a 14-character numeric timestamp, e.g. 20060409000000' );
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
*  If any of the array's element key is "_content", then the code treats all other key->value pairs as attributes, and the value['_content'] as the element's content.
*	Example:	name="root",  value = array( "_content"=>"text", "lang"=>"en", "id"=>10)   creates  <root lang="en" id="10">text</root>
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
			if( array_key_exists('_content', $elemValue) ) {
				$subElemContent = $elemValue['_content'];
				unset( $elemValue['_content'] );
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
		default:
			echo $indstr . wfElement( $elemName, null, $elemValue );
			break;
	}
}

?>
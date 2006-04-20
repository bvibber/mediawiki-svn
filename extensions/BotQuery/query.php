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
		'xml' => array( 'text/xml', 'printXML', 'XML format' ),
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

		// Page-specific Generators
		'langlinks'      => array( true, "genPageLangLinks", "interlanguage links" ),
		'templates'      => array( true, "genPageTemplates", "template names" ),
		'links'          => array( true, "genPageLinks", "regular links to other pages" ),
		'revisions'      => array( true, "genPageHistory", "revision history (see Notes)" ),
	);

	function BotQueryProcessor( $db ) {
		global $wgRequest;

		$this->db = $db;
		$this->format = $this->parseFormat( $wgRequest->getVal('format') );
		$this->linkBatch = $this->parseTitles( $wgRequest->getVal('titles') );
		$this->properties = $this->parseProperties( $wgRequest->getVal('properties'));

		$this->data = array();
	}

	function execute() {
		// Process metadata generators
		$this->callGenerators( false );

		// Query page table and initialize page ids.
		if( !$this->genPageInfo() ) {
			if( $this->data ) {
				return;   // No titles were given, skip any page generation
			} else {
				$this->dieUsage( 'Nothing to do' );
			}
		}

		// Process page-related generators
		$this->callGenerators( true );
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

	function parseTitles( $titles ) {
		global $wgUser;

		if ( $titles === '' ) {
			return null;
		}

		$titles = explode( '|', $titles );
		if ( $wgUser->isBot() ) {
			if ( count( $titles ) > 1000 ) {
				$this->dieUsage( 'Error, too many titles specified' );
			}
		} else {
			if ( count( $titles ) > 20 ) {
				$this->dieUsage( 'Error, too many titles specified' );
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
		if ( !$this->linkBatch ) {
			return false;   // Nothing to do
		}
		// Create a list of pages to query
		$where = $this->linkBatch->constructSet( 'page', $this->db );
		if ( !$where ) {
			return false;   // Nothing to do
		}

		$redirects = array();
		$nonexistentPages = $this->linkBatch->data;
		$this->data['pages'] = array();

		$res = $this->db->select( 'page',
			array( 'page_id', 'page_namespace', 'page_title', 'page_is_redirect', 'page_latest' ),
			$where,
			$this->classname . '::genPageInfo' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$title = Title::makeTitle( $row->page_namespace, $row->page_title );
			$this->data['pages'][$row->page_id] = array(
				'title' => $title->getPrefixedText(),
				'id' => $row->page_id,
				'revid' => $row->page_latest,
			);

			// Strike out link
			unset( $nonexistentPages[$row->page_namespace][$row->page_title] );

			if ( $row->page_is_redirect ) {
				$redirects[] = $row->page_id;
			}
		}
		$this->db->freeResult( $res );

		// This list can later be used to filter other tables by page Id
		$this->allPageIds = array_keys( $this->data['pages'] );
		$this->realPageIds = array_diff_key($this->allPageIds, $redirects);
		
		// Must not alter $this->data['pages'][] until done generating Page Ids
		$this->data['pages']['_element'] = 'page';

		// Add records for non-existent page titles
		$i = -1;
		foreach ( $nonexistentPages as $namespace => $stuff ) {
			foreach ( $stuff as $dbk => $arbitrary ) {
				$title = Title::makeTitle( $namespace, $dbk );
				$this->data['pages'][$i--] = array(
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
				$this->classname . '::genPageRedirects' );
			$redirectTargets = array();
			while ( $row = $this->db->fetchObject( $res ) ) {
				$this->data['pages'][$row->pl_from]['redirect'] = $this->getLinkInfo( $row->pl_namespace, $row->pl_title );
			}
			$this->db->freeResult( $res );
		}
		
		return true; // success
	}

	function genMetaSiteInfo() {
		global $wgSitename, $wgVersion, $wgCapitalLinks;
		$this->data['meta']['sitename']  = $wgSitename;
		$this->data['meta']['generator'] = "MediaWiki $wgVersion";
		$this->data['meta']['case']	  = $wgCapitalLinks ? 'first-letter' : 'case-sensitive'; // "case-insensitive" option is reserved for future

		$mainPage = Title::newFromText( wfMsgForContent( 'mainpage' ) );
		$this->data['meta']['mainpage']  = $mainPage->getText();
		$this->data['meta']['base']	  = $mainPage->getFullUrl();
	}

	function genMetaNamespaceInfo() {
		global $wgContLang;
		$this->data['meta']['namespaces']['_element'] = 'ns';
		foreach( $wgContLang->getFormattedNamespaces() as $ns => $title ) {
			$this->data['meta']['namespaces'][$ns] = array( "id"=>$ns, "_content" => $title );
		}
	}

	function genPageLangLinks() {
		if( !$this->realPageIds ) {
			return;
		}		
		$res = $this->db->select(
			array( 'langlinks' ),
			array( 'll_from', 'll_lang', 'll_title' ),
			"ll_from IN (" . $this->db->makeList($this->realPageIds) . ")",
			$this->classname . '::genPageLangLinks' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->ll_from, 'langlinks', 'll', array('lang' => $row->ll_lang, '_content' => $row->ll_title));
		}
		$this->db->freeResult( $res );
	}

	function genPageTemplates() {
		if( !$this->realPageIds ) {
			return;
		}
		$res = $this->db->select(
			'templatelinks',
			array( 'tl_from', 'tl_namespace', 'tl_title' ),
			"tl_from IN (" . $this->db->makeList($this->realPageIds) . ")",
			$this->classname . '::genPageTemplates' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->tl_from, 'templates', 'tl', $this->getLinkInfo( $row->tl_namespace, $row->tl_title ));
		}
		$this->db->freeResult( $res );
	}

	function genPageLinks() {
		if( !$this->realPageIds ) {
			return;
		}
		$res = $this->db->select(
			'pagelinks',
			array( 'pl_from', 'pl_namespace', 'pl_title' ),
			"pl_from IN (" . $this->db->makeList($this->realPageIds) . ")",
			$this->classname . '::genPageLinks' );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$this->addPageSubElement( $row->pl_from, 'links', 'l', $this->getLinkInfo( $row->pl_namespace, $row->pl_title ));
		}
		$this->db->freeResult( $res );
	}

	function genPageHistory() {
		global $wgRequest;

		if( !$this->allPageIds ) {
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

		if( $limit * count($this->allPageIds) > 20000 ) {
			$this->dieUsage( "rvlimit multiplied by number of requested titles must be less than 20000" );
		}

		foreach( $this->allPageIds as $pageId ) {
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
				$vals['_content'] = $includeComments ? $row->rev_comment : '';
				$this->addPageSubElement( $pageId, 'revisions', 'rv', $vals);
			}
			$this->db->freeResult( $res );
		}
	}

	//
	// ************************************* UTILITIES *************************************
	//
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

	function prepareTimestamp( $value ) {
		if ( preg_match( '/^[0-9]{14}$/', $value ) ) {
			return $this->db->addQuotes( $value );
		} else {
			$this->dieUsage( 'Incorrect timestamp format' );
		}
	}

	function dieUsage( $message ) {
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
			."  query.php ? format=a & properties=b|c|d & titles=e|f|g & rvstart=timestamp & rvend=timestamp & rvlimit=num\n"
			."\n"
			."Example:\n"
			."    query.php?format=xml&properties=links|templates&titles=User:Yurik\n"
			."  This query will return a list of all links and templates used on the User:Yurik\n"
			."\n"
			."Supported Formats:\n"
			.$formats
			."\n"
			."Supported Properties:\n"
			.$props
			."\n"
			."Notes:\n"
			."  - spaces above are just for ease of reading\n"
			."  - properties and format are the only required parameters\n"
			."  - revisions property supports additional parameters:\n"
			."      rvstart, rvend   - limits revisions by start and/or end time. The timestamp format is 14 characters.\n"
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
			."Version:\n"
			.'  $Id$'
			;

		die(1);
	}
}

//
// ************************************* Print Methods *************************************
//
function printXML( &$data ) {
	echo '<?xml version="1.0" encoding="utf-8"?>';
	recXmlPrint( "yurik", $data );
}
function printHumanReadable( &$data ) {
	print_r($data);
}
function printParsableCode( &$data ) {
	var_export($data);
}
function printPHP( &$data ) {
	serialize( $data );
}
function printJSON( &$data ) {
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

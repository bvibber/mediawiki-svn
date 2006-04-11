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

switch ( $wgRequest->getText('what') ) {
	case 'yurik':
		list( $type, $text ) = $bqp->yurik();
		break;
	case 'history':
		list( $type, $text ) = $bqp->history();
		break;
	default:
		die( 'Invalid query string' );
}
header( "Content-Type: $type" );
echo $text;

class BotQueryProcessor {
	var $mimeTypes = array(
		'tsv' => 'text/tab-separated-values',
		'json' => 'application/json',
		'xml' => 'text/xml',
		'php' => 'application/vnd.php.serialized',
	);

	function BotQueryProcessor( $db ) {
		$this->db = $db;
	}

	function getFormat() {
		global $wgRequest;
		
		switch ( $wgRequest->getText('format') ) {
			case 'php':
			case '':
				return 'php';
			case 'json':
				if ( !function_exists( 'json_encode' ) ) {
					die( 'JSON format not supported' );
				}
				return 'json';
			case 'xml':
				return 'xml';
			case 'tsv':
				return 'tsv';
			default:
				die( 'Unrecognised format' );
		}
	}
	
	function getLinkBatch( $titles ) {
		global $wgUser;
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

	function cleanTsv( $text ) {
		return str_replace( "\t", '        ', $text );
	}

	function yurik() {
		global $wgRequest;
		$fname = 'BotQueryProcessor::yurik';

		$format = $this->getFormat();

		$titles = $wgRequest->getText('titles');
		$disambigString = $wgRequest->getText('disambigs');
		if ( $titles == '' ) {
			die( 'Error, must specify one or more valid titles in the "titles" parameter' );
		}
		$linkBatch = $this->getLinkBatch( $titles );
		if ( $disambigString ) {
			$disambigs = $this->getLinkBatch( $disambigString );
		} else {
			$disambigs = false;
		}

		$mainWhere = $linkBatch->constructSet( 'page', $this->db );

		if ( !$mainWhere ) {
			return;
		}

		$data = array();
		$redirects = array();
		$nonexistentPages = $linkBatch->data;

		// Query page table
		$res = $this->db->select( 'page', 
			array( 'page_id', 'page_namespace', 'page_title', 'page_is_redirect' ), 
			$mainWhere, $fname );
		while ( $row = $this->db->fetchObject( $res ) ) {
			$title = Title::makeTitle( $row->page_namespace, $row->page_title );
			$data[$row->page_id] = array( 
				'title' => $title->getPrefixedDBkey(),
				'id' => $row->page_id,
				'langlinks' => array()
			);
			
			// Strike out link
			unset( $nonexistentPages[$row->page_namespace][$row->page_title] );

			if ( $row->page_is_redirect ) {
				$redirects[] = $row->page_id;
			}
		}
		$this->db->freeResult( $res );

		// Determine "redirected to" field
		if ( $redirects ) {
			$res = $this->db->select( 'pagelinks', array( 'pl_from', 'pl_namespace', 'pl_title' ), 
				'pl_from IN (' . implode( ',', $redirects ) . ')', $fname );
			$redirectTargets = array();
			while ( $row = $this->db->fetchObject( $res ) ) {
				$title = Title::makeTitle( $row->pl_namespace, $row->pl_title );
				$data[$row->pl_from]['redirect'] = $title->getPrefixedDBkey();
			}
			$this->db->freeResult( $res );
		}

		// Determine disambiguation status
		if ( $disambigs ) {
			$where = array( 
				$disambigs->constructSet( 'tl', $this->db ), 
				'tl_from IN (' . $this->db->makeList( array_keys( $data ) ) . ')'
			);

			if ( $where ) {
				$res = $this->db->select( 'templatelinks', array( 'tl_from' ), $where, $fname );
				while ( $row = $this->db->fetchObject( $res ) ) {
					$data[$row->tl_from]['disambig'] = true;
				}
				$this->db->freeResult( $res );
			}
		}

		// Add records for non-existent page titles
		$i = -1;
		foreach ( $nonexistentPages as $namespace => $stuff ) {
			foreach ( $stuff as $dbk => $arbitrary ) {
				$title = Title::makeTitle( $namespace, $dbk );
				$data[$i--] = array( 
					'title' => $title->getPrefixedDBkey(),
					'id' => 0
				);
			}
		}

		// Fetch interlanguage links
		$res = $this->db->select( 
			array( 'page', 'langlinks' ), 
			array( 'page_id', 'page_namespace', 'page_title', 'll_lang', 'll_title' ),
			array( 'page_id=ll_from', $mainWhere ),
			$fname,
			array( 'ORDER BY' => 'page_namespace,page_title' )
		);

		$lastId = 0;
		while ( $row = $this->db->fetchObject( $res ) ) {
			$data[$row->page_id]['langlinks'][$row->ll_lang] = $row->ll_title;
		}
		$this->db->freeResult( $res );

		// Format result
		$result = array_values( $data );
		switch ( $wgRequest->getText('format') ) {
			case 'tsv':
				$s = "Title\tID\tRedirects to\tDisambig?\tLanguage links...\n";
				foreach ( $result as $record ) {
					$redirect = isset( $record['redirect'] ) ? $record['redirect'] : '';
					$disambig = isset( $record['disambig'] ) ? 'y' : '';
					$s .= "{$record['title']}\t{$record['id']}\t$redirect\t$disambig";
					foreach ( $record['langlinks'] as $lang => $title ) {
						$s .= "\t$lang:$title";
					}
					$s .= "\n";
				}
				return array( $this->mimeTypes['tsv'], $s );
			case 'json':
				return array( $this->mimeTypes['json'], json_encode( $result ) );
			case 'xml':
				$s = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n<yurik>\n";
				foreach ( $result as $record ) {
					$s .= "<page>\n";
					$s .= '<title>' . htmlspecialchars( $record['title'] ) . "</title>\n" .
						"<id>{$record['id']}</id>\n";
					if ( isset( $record['redirect'] ) ) {
						$s .= '<redirect>' . htmlspecialchars( $record['redirect'] ) . "</redirect>\n";
					}
					if ( isset( $record['disambig'] ) ) {
						$s .= "<disambig/>\n";
					}
					if ( !empty( $record['langlinks'] ) ) {
						$s .= "<langlinks>\n";
						foreach ( $record['langlinks'] as $lang => $title ) {
							$s .= '<link lang="' . htmlspecialchars( $lang ) . '">' . htmlspecialchars( $title ) . "</link>\n";
						}
					}
					$s .= "</page>\n";
				}
				$s .= '</yurik>';
				return array( $this->mimeTypes['xml'], $s );
			default:
				return array( $this->mimeTypes['php'], serialize( $result ) );
		}
	}

	function history() {
		global $wgRequest, $wgUser;
		$fname = 'BotQueryProcessor::history';

		// Validate parameters
		$title = Title::newFromText( $wgRequest->getText('title') );
		if ( is_null( $title ) ) {
			die( 'Error, must specify a valid title in the "title" parameter' );
		}
		$format = $this->getFormat();

		$conds = array(
			'page_namespace' => $title->getNamespace(),
			'page_title' => $title->getDBkey(),
			'page_id=rev_page',
			'rev_deleted' => 0,
		);
		
		$start = $wgRequest->getText( 'start' );
		if ( $start != '' ) {
			if ( preg_match( '/^[0-9]{14}$/', $start ) ) {
				$conds[] = 'rev_timestamp >= ' . $this->db->addQuotes( $start );
			} else {
				die( 'Error, start must be a 14-character numeric timestamp, e.g. 20060409000000' );
			}
		}
		
		$end = $wgRequest->getText( 'end' );
		if ( $end != '' ) {
			if ( preg_match( '/^[0-9]{14}$/', $end ) ) {
				$conds[] = 'rev_timestamp <= ' . $this->db->addQuotes( $end );
			} else {
				die( 'Error, end must be a 14-character numeric timestamp, e.g. 20060409000000' );
			}
		}

		$limit = $wgRequest->getInt( 'limit', 50 );
		$options = array(
			'LIMIT' => $limit,
			'ORDER BY' => 'rev_timestamp DESC'
		);

		// Do the query
		$data = array();
		$res = $this->db->select( array( 'page', 'revision' ), 'revision.*', $conds, $fname, $options );
		
		// Format the result
		switch ( $format ) {
			case 'tsv':
				$s = '';
				$first = true;
				while ( $row = $this->db->fetchObject( $res ) ) {
					$fields = get_object_vars( $row );
					if ( $first ) {
						// Header row
						$s .= implode( "\t", array_keys( $fields ) ) . "\n";
						$first = false;
					}
					$s .= implode( "\t", array_map( array( 'BotQueryProcessor', 'cleanTsv' ), 
						$fields ) ) . "\n";
				}
				break;
			case 'json':
				$data = array();
				while ( $row = $this->db->fetchObject( $res ) ) {
					$data[] = $row;
				}
				$s = json_encode( $data );
				break;
			case 'xml':
				$s = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n<history>\n";
				while ( $row = $this->db->fetchObject( $res ) ) {
					$s .= "<revision>\n";
					$fields = get_object_vars( $row );
					foreach ( $fields as $name => $value ) {
						$s .= "<$name>" . htmlspecialchars( $value ) . "</$name>\n";
					}
					$s .= "</revision>\n";
				}
				break;
			default:
				$format = 'php';
				$data = array();
				while ( $row = $this->db->fetchObject( $res ) ) {
					$data[] = $row;
				}
				$s = serialize( $data );
		}
		return array( $this->mimeTypes[$format], $s );
	}
}

?>

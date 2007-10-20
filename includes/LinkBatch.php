<?php

/**
 * Class representing a list of titles
 * The execute() method checks them all for existence and adds them to a LinkCache object
 *
 * @addtogroup Cache
 */
class LinkBatch {
	/**
	 * 2-d array, first index namespace, second index dbkey, value arbitrary
	 * with $wgLanguageTag enabled, 3-d array, third index language
	 */	
	var $data = array();

	function __construct( $arr = array() ) {
		foreach( $arr as $item ) {
			$this->addObj( $item );
		}
	}

	function addObj( $title ) {
		if ( is_object( $title ) ) {
			$this->add( $title->getNamespace(), $title->getDBkey(), $title->getLanguage() );
		} else {
			wfDebug( "Warning: LinkBatch::addObj got invalid title object\n" );
		}
	}

	function add( $ns, $dbkey, $language=false ) {
		if ( $ns < 0 ) {
			return;
		}
		if ( !array_key_exists( $ns, $this->data ) ) {
			$this->data[$ns] = array();
		}

		if(false!==$language) { 
			array_key_exists($dbkey, $this->data[$ns]) ? ( is_array($this->data[$ns][$dbkey])?1:
  				$this->data[$ns][$dbkey]=array(''=>$this->data[$ns][$dbkey])):$this->data[$ns][$dbkey]=array();
			$this->data[$ns][$dbkey][$language]=1;
		} else
		$this->data[$ns][$dbkey] = 1;
	}

	/**
	 * Set the link list to a given 2-d array
	 * First key is the namespace, second is the DB key, value arbitrary
	 */
	function setArray( $array ) {
		$this->data = $array;
	}

	/**
	 * Returns true if no pages have been added, false otherwise.
	 */
	function isEmpty() {
		return ($this->getSize() == 0);
	}

	/**
	 * Returns the size of the batch.
	 */
	function getSize() {
		return count( $this->data );
	}

	/**
	 * Do the query and add the results to the LinkCache object
	 * Return an array mapping PDBK to ID
	 */
	 function execute() {
	 	$linkCache =& LinkCache::singleton();
	 	return $this->executeInto( $linkCache );
	 }

	/**
	 * Do the query and add the results to a given LinkCache object
	 * Return an array mapping PDBK to ID
	 */
	function executeInto( &$cache ) {
		$fname = 'LinkBatch::executeInto';
		wfProfileIn( $fname );
		// Do query
		$res = $this->doQuery();
		if ( !$res ) {
			wfProfileOut( $fname );
			return array();
		}

		// For each returned entry, add it to the list of good links, and remove it from $remaining

		$ids = array();
		$remaining = $this->data;
		global $wgLanguageTag;
		while ( $row = $res->fetchObject() ) {
			if($wgLanguageTag) {
				$title = Title::makeTitle( $row->page_namespace, $row->page_title, $row->page_language );
				unset( $remaining[$row->page_namespace][$row->page_title] ); // [$row->page_language] );
			} else {
				$title = Title::makeTitle( $row->page_namespace, $row->page_title );
				unset( $remaining[$row->page_namespace][$row->page_title] );
			}
			$cache->addGoodLinkObj( $row->page_id, $title );
			$ids[$title->getPrefixedDBkey()] = $row->page_id;
		}
		$res->free();

		// The remaining links in $data are bad links, register them as such
		foreach ( $remaining as $ns => $dbkeys ) {
			foreach ( $dbkeys as $dbkey => $nothing ) {
				if($wgLanguageTag && is_array($nothing)) { 
					foreach($nothing as $lang=>$nothing2) 
						$title = Title::makeTitle( $ns, $dbkey, $lang);
				}
				else {
					$title = Title::makeTitle( $ns, $dbkey );
				}
				$cache->addBadLinkObj( $title );
				$ids[$title->getPrefixedDBkey()] = 0;
			}
		}
		wfProfileOut( $fname );
		return $ids;
	}

	/**
	 * Perform the existence test query, return a ResultWrapper with page_id fields
	 */
	function doQuery() {
		$fname = 'LinkBatch::doQuery';

		if ( $this->isEmpty() ) {
			return false;
		}
		wfProfileIn( $fname );

		// Construct query
		// This is very similar to Parser::replaceLinkHolders
		$dbr = wfGetDB( DB_SLAVE );
		$page = $dbr->tableName( 'page' );
		$set = $this->constructSet( 'page', $dbr );
		if ( $set === false ) {
			wfProfileOut( $fname );
			return false;
		}
		GLOBAL $wgLanguageTag; $lang=$wgLanguageTag?",page_language ":'';
		$sql = "SELECT page_id, page_namespace, page_title{$lang} FROM $page WHERE $set";

		// Do query
		$res = new ResultWrapper( $dbr,  $dbr->query( $sql, $fname ) );
		wfProfileOut( $fname );
		return $res;
	}

	/**
	 * Construct a WHERE clause which will match all the given titles.
	 * Give the appropriate table's field name prefix ('page', 'pl', etc).
	 *
	 * @param $prefix String: ??
	 * @return string
	 * @public
	 */
	function constructSet( $prefix, &$db ) {
		$first = true;
		$firstTitle = true;
		$sql = '';
		foreach ( $this->data as $ns => $dbkeys ) {
			if ( !count( $dbkeys ) ) {
				continue;
			}

			if ( $first ) {
				$first = false;
			} else {
				$sql .= ' OR ';
			}
			

			### MLMW Patch

			$sql .= "({$prefix}_namespace=$ns AND ";

			$firstTitle = true;

                        global $wgLanguageTag; if($wgLanguageTag) {

				foreach($dbkeys as $dbkey => $nothing) {

					if(!is_array($nothing)) continue;
					$isnull=false;
					$nothing=array_keys($nothing);
					if(false!==$tmp=array_search('',$nothing,1)) {
						$isnull=true;
						unset($nothing[$tmp]);
					}
					if($firstTitle) {
						$firstTitle=false;
						$sql.='(';
					}
					else {
						$sql.=' OR ';
					}
                                        $sql.="({$prefix}_title = ".$db->addQuotes($dbkey);
					if($nothing) {
						if(count($nothing)==1) {
							$sql.=" AND {$prefix}_language = '".current(array_keys($nothing))."'";
						}
						else {
							$sql.=" AND {$prefix}_language IN (".join(',',array_keys($nothing)).')';
						}
					}
					if($isnull) {
						if($nothing) $sql.=" OR ";
						else $sql.=" AND ";
						$sql.="{$prefix}_language is null";
					}
					$sql.= ")";
					unset($dbkeys[$dbkey]);
				}
				if(!$firstTitle) {
					$sql.=')';
					if(count($dbkeys)) $sql.=" OR ";
				}
			}

			### Undefined Language

			if (count($dbkeys)==1) { // avoid multiple-reference syntax if simple equality can be used
				$singleKey = array_keys($dbkeys);
				$sql .= "({$prefix}_namespace=$ns AND {$prefix}_title=".
					$db->addQuotes($singleKey[0]).
					")";
		#		if($wgLanguageTag && is_array(current($dbkeys))) {
		#			$sql.=" AND {$prefix}_language=".$db->addQuotes(current(array_keys(current($dbkeys))));
		#		}

			} elseif(count($dbkeys)) {
				$sql .= "({$prefix}_namespace=$ns AND {$prefix}_title IN (";
				
				$firstTitle = true;
				foreach( $dbkeys as $dbkey => $unused ) {
					if ( $firstTitle ) {
						$firstTitle = false;
					} else {
						$sql .= ',';
					}
					$sql .= $db->addQuotes( $dbkey );
				}
				$sql .= '))';
			}
			$sql .= ')';
		}

		if ( $first && $firstTitle ) {
			# No titles added
			return false;
		} else {
			return $sql;
		}
	}
}



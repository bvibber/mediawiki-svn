<?php
/**
 * DataTransclusion Source base class
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Daniel Kinzler for Wikimedia Deutschland
 * @copyright © 2010 Wikimedia Deutschland (Author: Daniel Kinzler)
 * @licence GNU General Public Licence 2.0 or later
 */

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

class DBDataTransclusionSource extends DataTransclusionSource {

  function __construct( $spec ) {
    if ( !isset( $spec[ 'keyFields' ] ) && isset( $spec[ 'keyTypes' ] ) ) $spec[ 'keyFields' ] = array_keys( $spec[ 'keyTypes' ] );

    DataTransclusionSource::__construct( $spec );

    $this->query = $spec[ 'query' ];

    if ( isset( $spec[ 'keyTypes' ] ) ) $this->keyTypes = $spec[ 'keyTypes' ];
    else $this->keyTypes = null;
  }

  public function convertKey( $key, $value ) {
    if ( !isset( $this->keyTypes[ $key ] ) ) return (string)$value;

    $t = strtolower( trim( $this->keyTypes[ $key ] ) );
    
    if ( $t == 'int' ) return (int)$value;
    else if ( $t == 'decimal' || $t == 'float' ) return (float)$value; 
    else return (string)$value;
  }

  public function fetchRecord( $field, $value ) {
    $db = wfGetDB( DB_SLAVE );

    if ( !preg_match( '/\w+[\w\d]+/', $field ) ) return false; // redundant, but make extra sure we don't get anythign evil here

    $value = $this->convertKey( $field, $value );

    if ( is_string( $value ) ) $v = $db->addQuotes( $value );
    else $v = $value;

    $where = "( " . $field . " = " . $v . " )";

    if ( preg_match('/[)\s]WHERE[\s(]/is', $this->query ) ) $sql = $this->query . " AND " . $where;
    else $sql = $this->query . " WHERE " . $where;

    $rs = $db->query( $sql, "DBDataTransclusionSource(" . $this->getName() . ")::fetchRecord" );
    if ( !$rs ) return false;

    $rec = $db->fetchRow( $rs );

    $db->freeResult( $rs );
    return $rec;
  }
}

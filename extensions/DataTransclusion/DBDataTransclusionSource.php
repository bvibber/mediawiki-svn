<?php
/**
 * DataTransclusion Source base class
 *
 * @file
 * @ingroup Extensions
 * @author Daniel Kinzler for Wikimedia Deutschland
 * @copyright © 2010 Wikimedia Deutschland (Author: Daniel Kinzler)
 * @licence GNU General Public Licence 2.0 or later
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

/**
  Implementations of DataTransclusionSource, fetching data records from an SQL database.
 *
 * In addition to the options supported by the DataTransclusionSource base class,
 * DBDataTransclusionSource accepts some additional options
 *
 *	 * $spec['query']: the SQL query for fetching records. May not contain a
 *		GROUP or LIMIT clause (use $spec['querySuffix'] for that). The
 *		WHERE clause is automatically generated from the requested key/value pair.
 *		If $spec['query'] already contains a WHERE clause, the condition for
 *		the desired key/value pair is appended using AND. Note that subqueries are
 *		not supported reliably. REQUIRED.
 *	 * $spec['querySuffix']: additional clauses to be added after the WHERE clause.
 *		Useful mostly to specify GROUP BY (or ORDER BY or LIMIT).
 *	 * $spec['keyTypes']: associative arrays specifying the data types for the key fields.
 *		Array keys are the field names, the associated values specify the type
 *		as 'int' for integers, 'float' or 'decimal' for decimals, or 'string'
 *		for string fields.
 *	 * $spec['keyFields']: like for DataTransclusionSource, this is list of fields
 *		that can be used as the key for fetching a record. However, it's not required
 *		for DBDataTransclusionSource: if not provided, array_keys( $spec['keyTypes'] )
 *		will be used. REQUIRED.
 *
 * For more information on options supported by DataTransclusionSource, see the class-level
 * documentation there.
 */
class DBDataTransclusionSource extends DataTransclusionSource {

	/**
	 * Initializes the DBDataTransclusionSource from the given parameter array.
	 * @param $spec associative array of options. See class-level documentation for details.
	 */
	function __construct( $spec ) {
		if ( !isset( $spec[ 'keyFields' ] ) && isset( $spec[ 'keyTypes' ] ) ) {
			$spec[ 'keyFields' ] = array_keys( $spec[ 'keyTypes' ] );
		}

		DataTransclusionSource::__construct( $spec );

		$this->query = $spec[ 'query' ];
		$this->querySuffix = @$spec[ 'querySuffix' ];

		if ( isset( $spec[ 'keyTypes' ] ) ) {
			$this->keyTypes = $spec[ 'keyTypes' ];
		} else {
			$this->keyTypes = null;
		}
	}

	public function convertKey( $key, $value ) {
		if ( !isset( $this->keyTypes[ $key ] ) ) {
			return (string)$value;
		}

		$t = strtolower( trim( $this->keyTypes[ $key ] ) );
		
		if ( $t == 'int' ) {
			return (int)$value;
		} else if ( $t == 'decimal' || $t == 'float' ) {
			return (float)$value;
		} else {
			return (string)$value;
		}
	}

	public function getQuery( $field, $value, $db = null ) {
		if ( !$db ) {
			$db = wfGetDB( DB_SLAVE );
		}

		if ( !preg_match( '/\w+[\w\d]+/', $field ) ) {
			return false; // redundant, but make extra sure we don't get anythign evil here //TESTME
		}

		$value = $this->convertKey( $field, $value ); 

		if ( is_string( $value ) ) {
			$v = $db->addQuotes( $value ); 
		} else {
			$v = $value;
		}

		$where = "( " . $field . " = " . $v . " )";

		if ( preg_match( '/[)\s]WHERE[\s(]/is', $this->query ) ) {
			$sql = $this->query . " AND " . $where;
		} else {
			$sql = $this->query . " WHERE " . $where;
		}


		if ( $this->querySuffix ) {
			$sql = $sql . ' ' . $this->querySuffix;
		}

		return $sql;
	}

	public function fetchRecord( $field, $value ) {
		$db = wfGetDB( DB_SLAVE );

		$sql = $this->getQuery( $field, $value, $db );
		wfDebugLog( 'DataTransclusion', "sql query for $field=$value: $sql\n" );

		$rs = $db->query( $sql, "DBDataTransclusionSource(" . $this->getName() . ")::fetchRecord" );

		if ( !$rs ) {
			wfDebugLog( 'DataTransclusion', "sql query failed for $field=$value\n" );
			return false;
		}

		$rec = $db->fetchRow( $rs );
		if ( !$rec ) {
			wfDebugLog( 'DataTransclusion', "no record found matching $field=$value\n" );
			return false;
		}

		$db->freeResult( $rs );

		wfDebugLog( 'DataTransclusion', "loaded record for $field=$value from database\n" );
		return $rec;
	}
}

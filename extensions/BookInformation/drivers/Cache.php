<?php

/**
 * Cache for book information requests
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */
class BookInformationCache {

	public static function get( $isbn ) {
		global $wgBookInformationCache;
		if( $wgBookInformationCache ) {
			$dbr = wfGetDB( DB_SLAVE );
			$res = $dbr->selectRow( 'bookinfo', '*', array( 'bi_isbn' => $isbn ), __METHOD__ );
			if( $res ) {
				$driver = unserialize( $res->bi_result );
				if( is_object( $driver ) && $driver instanceof BookInformationDriver ) {
					wfDebugLog( 'bookinfo', "Cache hit for {$isbn}\n" );
					return $driver;
				} else {
					wfDebugLog( 'bookinfo', "Cache received unexpected class from database\n" );
					return false;
				}
			} else {
				wfDebugLog( 'bookinfo', "Cache miss for {$isbn}\n" );
				return false;
			}			
		} else {
			wfDebugLog( 'bookinfo', "Cache disabled; implicit miss for {$isbn}\n" );
			return false;
		}
	}

	public static function set( $isbn, $driver ) {
		global $wgBookInformationCache;
		if( $wgBookInformationCache ) {
			$dbw = wfGetDB( DB_MASTER );
			$dbw->insert( 'bookinfo', self::prepareValues( $isbn, $driver ), __METHOD__, 'IGNORE' );
		}
	}
	
	private static function prepareValues( $isbn, $driver ) {
		return array(
			'bi_isbn' => $isbn,
			'bi_result' => serialize( $driver ),
		);
	}

}

?>
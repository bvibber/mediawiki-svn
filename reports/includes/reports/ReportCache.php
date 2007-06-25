<?php

/**
 * Report cache management functions
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
class ReportCache {

	/**
	 * Cache a report
	 *
	 * @param Report $report Report to cache
	 * @param int $limit Maximum cache set size (per namespace)
	 * @param callback $namespaceCallback Callback after each namespace
	 */
	public static function recache( $report, $limit = 1000, $namespaceCallback = null ) {
		foreach( self::getNamespaces( $report ) as $namespace ) {
			# Clear existing cached entries for this report and namespace
			$dbw = wfGetDB( DB_MASTER );
			$dbw->delete(
				'reportcache',
				array(
					'rp_report' => $report->getName(),
					'rp_namespace' => $namespace,
				),
				__METHOD__
			);
			# Obtain fresh entries for the report			
			$dbr  = wfGetDB( DB_SLAVE );
			$sql  = $report->getBaseSql( $dbr );
			$conds = $report->getExtraConditions();
			$conds[] = $report->getNamespaceClause( $namespace );
			$sql .= ' WHERE ' . implode( ' AND ', $conds );
			$sql .= " LIMIT {$limit}";
			$res = $dbr->query( $sql, __METHOD__ );
			$rows = $dbr->numRows( $res );
			# Insert the new entries into the cache
			while( $row = $dbr->fetchObject( $res ) ) {
				$dbw->insert(
					'reportcache',
					array(
						'rp_report' => $report->getName(),
						'rp_namespace' => $row->rp_namespace,
						'rp_title' => $row->rp_title,
						'rp_redirect' => $row->rp_redirect,
						'rp_params' => $dbw->encodeBlob( self::encodeParams( $report->extractParameters( $row ) ) ),
					),
					__METHOD__
				);
			}
			$dbr->freeResult( $res );
			# Update the cache state table
			$dbw->replace(
				'reportcache_info',
				array( 'rci_report' ),
				array(
					'rci_report' => $report->getName(),
					'rci_updated' => $dbw->timestamp(),
				),
				__METHOD__
			);
			# Callback?
			if( is_callable( $namespaceCallback ) )
				call_user_func( $namespaceCallback, $report, $namespace, $rows );
		}
	}
	
	/**
	 * Get a list of individual namespaces to cache for
	 * a given report
	 *
	 * @param Report $report Report to cache
	 * @return array
	 */
	private static function getNamespaces( $report ) {
		global $wgContLang;
		$namespaces = $report->getApplicableNamespaces();
		if( $namespaces === false ) {
			$namespaces = array();
			foreach( $wgContLang->getNamespaces() as $index => $name ) {
				if( $index >= 0 && $index != NS_MEDIAWIKI && $index != NS_MEDIAWIKI_TALK )
					$namespaces[] = $index;
			}
		}
		return $namespaces;
	}
	
	/**
	 * Get the timestamp of the last update to a cached
	 * result set, or false if not available
	 *
	 * @param Report $report Report to check
	 * @return mixed
	 */
	public static function getUpdateTime( $report ) {
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
			'reportcache_info',
			'*',
			array( 'rci_report' => $report->getName() ),
			__METHOD__
		);
		if( $dbr->numRows( $res ) > 0 ) {
			$row = $dbr->fetchObject( $res );
			return wfTimestamp( TS_MW, $row->rci_updated );
		} else {
			return false;
		}
	}

	/**
	 * Encode a set of parameters
	 *
	 * @param array $params
	 * @return string
	 */
	public static function encodeParams( $params ) {
		return serialize( $params );
	}

	/**
	 * Decode a set of parameters
	 *
	 * @param string $params
	 * @return array
	 */
	public static function decodeParams( $params ) {
		return unserialize( $params );
	}

}

?>
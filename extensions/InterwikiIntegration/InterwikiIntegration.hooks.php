<?php
 
class InterwikiIntegrationHooks {
	
	/*
	 * Creates necessary tables
	 */
	public static function InterwikiIntegrationCreateTable() {
		global $wgExtNewTables;
		$wgExtNewTables[] = array(
			'integration_db',
			dirname( __FILE__ ) . '/integration-db.sql'
		);
		$wgExtNewTables[] = array(
			'integration_namespace',
			dirname( __FILE__ ) . '/integration-namespace.sql'
		);
		/*$wgExtNewTables[] = array(
			'integration_namespace',
			dirname( __FILE__ ) . '/integration-disregard.sql'
		);*/
		return true;
	}
	
	/*
	 * Determines whether an interwiki link to a wiki on the same wiki
	 * farm is broken or not; if so, it will be colored red and link to the
	 * edit page on the target wiki
	 */
	public static function InterwikiIntegrationLink( $skin, $target, &$text,
		&$customAttribs, &$query, &$options, &$ret ) {
		global $wgInterwikiIntegrationBrokenLinkStyle, $wgPureWikiDeletionInEffect;
		
		if ( $target->isExternal() ) {
			$dbr = wfGetDB( DB_SLAVE );
			
			$interwikiPrefix = $target->getInterwiki ();
			$interwikiPrefix{0} = strtolower($interwikiPrefix{0});
			$result = $dbr->selectRow(
				'integration_db',
				'integration_dbname',
				array( "integration_prefix" => $interwikiPrefix )
			);
			
			if ( !$result ) {
				return true;
			}
			
			$targetDb = $result->integration_dbname;
			
			$dbrLocal = wfGetDB( DB_SLAVE, array(), $targetDb );
			
			$title = $target->getDBkey ();
			$colonPos = strpos ( $title, ':' );
			$namespaceIndex = '';
			if ( $colonPos ) {
				$namespace = ucfirst ( substr ( $title, 0, $colonPos ) );
				$newTitle = substr ( $title, $colonPos + 1,
					strlen ( $title ) - $colonPos - 1 );
				$namespaceResult = $dbrLocal->selectRow (
					'integration_namespace',
					'integration_namespace_index',
					array ( "integration_namespace_title" => $namespace )
				);
				if ( $namespaceResult ) {
					$namespaceIndex = $namespaceResult->integration_namespace_index;
					$title = $newTitle;
				}
			}
			
			$pageResult = $dbrLocal->selectRow(
				'page',
				'page_id',
				array ( "page_title" => ucfirst ( $title ),
					"page_namespace" => $namespaceIndex )
			);
			
			$disregardSpecial = $dbrLocal->selectRow (
				'integration_namespace',
				'integration_namespace_title',
				array ( "integration_namespace_index" => $namespaceIndex,
					"integration_namespace_title" => 'DisregardSpecial'
				)
			);
			if ( $disregardSpecial ) {
				return true;
			}
			
			$pureWikiDeletionInEffect = $dbrLocal->selectRow (
				'integration_db',
				'integration_pwd',
				array ( "integration_prefix" => ( $interwikiPrefix ) )
			);
			
			if ( $pageResult && $pureWikiDeletionInEffect->integration_pwd == 1 ) {
				$disregardForPWD = $dbrLocal->selectRow (
					'integration_namespace',
					'integration_namespace_title',
					array ( "integration_namespace_index" => $namespaceIndex,
						"integration_namespace_title" => 'DisregardForPWD'
					)
				);
				if ( $disregardForPWD ) {
					return true;
				}
				$blankResult = $dbrLocal->selectRow( 'blanked_page', 'blank_page_id'
					, array( "blank_page_id" => $pageResult->page_id ) );
	
			}
			
			if ( !$pageResult || isset ( $blankResult ) && $blankResult ) {
				$query['action'] = "edit";
				$customAttribs['style'] = $wgInterwikiIntegrationBrokenLinkStyle;
			
			}
		}
		return true;
	}
}


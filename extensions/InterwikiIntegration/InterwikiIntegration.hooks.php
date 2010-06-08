<?php
 
class InterwikiIntegrationHooks {
	
	/*
	 * Creates necessary tables
	 */
	public static function InterwikiIntegrationCreateTable() {
		global $wgExtNewTables;
		$wgExtNewTables[] = array(
			'integration_prefix',
			dirname( __FILE__ ) . '/interwikiintegration-prefix.sql'
		);
		$wgExtNewTables[] = array(
			'integration_namespace',
			dirname( __FILE__ ) . '/interwikiintegration-namespace.sql'
		);
		$wgExtNewTables[] = array(
			'integration_iwlinks',
			dirname( __FILE__ ) . '/interwikiintegration-iwlinks.sql'
		);
		return true;
	}
	
	/*
	 * Update the integration_iwlinks table when interwiki links are added or
	 * removed from articles.
	 */
	public static function InterwikiIntegrationArticleEditUpdates( &$article, &$editInfo, $changed ) {
		$mDb = wfGetDB( DB_MASTER );
		global $wgDBname;
		$parserOutput = $editInfo->output;
		$mId = $article->getID();
		$mTitle = $article->getTitle();
		$mURL = $mTitle->getFullURL();
		$mInterwikis = $parserOutput->getInterwikiLinks();
		$res = $mDb->select(
			'integration_iwlinks',
			array(
				'integration_iwl_prefix',
				'integration_iwl_title',
			),
			array(
				'integration_iwl_from' => $mId,
				'integration_iwl_from_db' => $wgDBname
			)
		);
		
		$existing = array();
		while ( $row = $mDb->fetchObject( $res ) ) {
			if ( !isset( $existing[$row->integration_iwl_prefix] ) ) {
				$existing[$row->integration_iwl_prefix] = array();
			}
			$existing[$row->integration_iwl_prefix][$row->integration_iwl_title] = 1;
		}
		
		$del = array();
		foreach ( $existing as $prefix => $dbkeys ) {
			if ( isset( $mInterwikis[$prefix] ) ) {
				$del[$prefix] = array_diff_key( $existing[$prefix], $mInterwikis[$prefix] );
			} else {
				$del[$prefix] = $existing[$prefix];
			}
		}
		
		$ins = array();
		foreach( $mInterwikis as $prefix => $dbkeys ) {
			# array_diff_key() was introduced in PHP 5.1, there is a compatibility function
			# in GlobalFunctions.php
			$diffs = isset( $existing[$prefix] ) ? array_diff_key( $dbkeys, $existing[$prefix] ) : $dbkeys;
			foreach ( $diffs as $dbk => $id ) {
				$dbk = ucfirst ( $dbk );
				$colonPos = strpos ( $dbk, ':' );
				$namespaceIndex = '';
				if ( $colonPos && $colonPos < strlen ( $dbk ) - 1 ) {
					$namespace = substr ( $dbk, 0, $colonPos );
					$namespaceResult = $mDb->selectRow (
						'integration_namespace',
						'integration_namespace_index',
						array ( "integration_namespace_title" => $namespace )
					);
					if ( $namespaceResult ) {
						$dbk = substr_replace ( $dbk, ucfirst ( substr ( $dbk, $colonPos + 1, 1) ) , $colonPos + 1 , 1 );
					}
				}
				$ins[] = array(
					'integration_iwl_from_db'  => $wgDBname,
					'integration_iwl_from'     => $mId,
					'integration_iwl_from_url' => $mURL,
					'integration_iwl_prefix'   => ucfirst ( $prefix ),
					'integration_iwl_title'    => $dbk
				);
			}
		}
		$table = 'integration_iwlinks';
		$fromField = "integration_iwl_from";
		$where = array( $fromField => $mId );
		$baseKey = 'integration_iwl_prefix';
		$clause = $mDb->makeWhereFrom2d( $del, $baseKey, "integration_iwl_title" );
		if ( $clause ) {
			$where[] = $clause;
		} else {
			$where = false;
		}
		if ( $where ) {
			$mDb->delete( $table, $where );
		}
		if ( count( $ins ) ) {
			$mDb->insert( $table, $ins );
		}
		return true;
	}
	
	/*
	 * When a page is created, purge caches of pages that link to it interwiki
	 */
	public static function InterwikiIntegrationArticleInsertComplete ( &$article, &$user, $text, $summary, $minoredit, 
		&$watchthis, $sectionanchor, &$flags, $revision ) {
		InterwikiIntegrationHooks::PurgeReferringPages ( $article->getTitle() );
		return true;
	}
	
	/*
	 * When a page is deleted, purge caches of pages that link to it interwiki
	 */
	public static function InterwikiIntegrationArticleDeleteComplete( &$article, &$user, $reason, $id ) {
		InterwikiIntegrationHooks::PurgeReferringPages ( $article->getTitle() );
		$mDb = wfGetDB( DB_MASTER );
		$mDb->delete(
			'integration_iwlinks',
			array ('integration_iwl_from'  => $id )
		);
		return true;
	}
	
	/*
	 * When a page is undeleted, purge caches of pages that link to it interwiki
	 */
	public static function InterwikiIntegrationArticleUndelete ( $title, $create ) {
		if ( $create ) {
			InterwikiIntegrationHooks::PurgeReferringPages ( $title );
		}
		return true;
	}

	/*
	 * When a page is moved, purge caches of pages that link to the new page interwiki
	 */
	public static function InterwikiIntegrationTitleMoveComplete ( &$title, &$newtitle, &$user, $oldid, $newid ) {
		InterwikiIntegrationHooks::PurgeReferringPages ( $title );
		return true;
	}
	
	/*
	 * When a page is blanked, purge caches of pages that link to the new page interwiki.
	 * This is called by a PureWikiDeletion hook.
	 */
	public static function InterwikiIntegrationArticleBlankComplete ( $title ) {
		InterwikiIntegrationHooks::PurgeReferringPages ( $title );
		return true;
	}
	
	/*
	 * When a page is unblanked, purge caches of pages that link to the new page interwiki.
	 * This is called by a PureWikiDeletion hook.
	 */
	public static function InterwikiIntegrationArticleUnblankComplete ( $title ) {
		InterwikiIntegrationHooks::PurgeReferringPages ( $title );
		return true;
	}
	
	public static function PurgeReferringPages ( $title ) {
		global $wgDBname, $wgInterwikiIntegrationPrefix;
		
		$mDb = wfGetDB( DB_MASTER );
		$titleName = str_replace ( ' ', '_', $title->getFullText() );
		$prefix = array();
		$prefix = array_keys ( $wgInterwikiIntegrationPrefix , $wgDBname );
		$purgeArray = array();
		
		foreach ( $prefix as $thisPrefix ) {
			$thisPrefix = ucfirst ( $thisPrefix );
			$result = $mDb->selectRow(
				array ('integration_iwlinks'),
				array (
				       'integration_iwl_from_url',
				       'integration_iwl_from_db',
				       'integration_iwl_from'
				),
				array (
					'integration_iwl_prefix'   => $thisPrefix,
					'integration_iwl_title'    => $titleName
				),
				__METHOD__
			);
				if ( $result ) {
					$referringPage = $result->integration_iwl_from;
					$purgeArray[] = $result->integration_iwl_from_url;
					$referringDb = $result->integration_iwl_from_db;
					$dbwReferring = wfGetDB( DB_MASTER , array(), $referringDb );
					$dbwReferring->update(
						'page',
						array( 'page_touched' => $dbwReferring->timestamp() ),
						array( 'page_id' => $referringPage
						)
					);
				}
		}
		
		if ( $result ) {
			SquidUpdate::purge ( $purgeArray );
		}
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
				'integration_prefix',
				'integration_dbname',
				array( "integration_prefix" => $interwikiPrefix )
			);
			
			if ( !$result ) {
				return true;
			}
			
			$targetDb = $result->integration_dbname;
			
			$dbrTarget = wfGetDB( DB_SLAVE, array(), $targetDb );
			
			$title = $target->getDBkey ();
			$colonPos = strpos ( $title, ':' );
			$namespaceIndex = '';
			if ( $colonPos ) {
				$namespace = ucfirst ( substr ( $title, 0, $colonPos ) );
				$newTitle = substr ( $title, $colonPos + 1,
					strlen ( $title ) - $colonPos - 1 );
				$namespaceResult = $dbr->selectRow (
					'integration_namespace',
					'integration_namespace_index',
					array (
					       'integration_namespace_title' => $namespace,
					       'integration_dbname' => $targetDb
					)
				);
				if ( $namespaceResult ) {
					$namespaceIndex = $namespaceResult->integration_namespace_index;
					$title = $newTitle;
				}
			}
			
			$pageResult = $dbrTarget->selectRow(
				'page',
				'page_id',
				array ( "page_title" => ucfirst ( $title ),
					"page_namespace" => $namespaceIndex )
			);
			
			$disregardSpecial = $dbrTarget->selectRow (
				'integration_namespace',
				'integration_namespace_title',
				array ( "integration_namespace_index" => $namespaceIndex,
					"integration_namespace_title" => 'DisregardSpecial'
				)
			);
			if ( $disregardSpecial ) {
				return true;
			}
			
			$pureWikiDeletionInEffect = $dbrTarget->selectRow (
				'integration_prefix',
				'integration_pwd',
				array ( "integration_prefix" => ( $interwikiPrefix ) )
			);
			
			if ( $pageResult && $pureWikiDeletionInEffect->integration_pwd == 1 ) {
				$disregardForPWD = $dbrTarget->selectRow (
					'integration_namespace',
					'integration_namespace_title',
					array ( "integration_namespace_index" => $namespaceIndex,
						"integration_namespace_title" => 'DisregardForPWD'
					)
				);
				if ( $disregardForPWD ) {
					return true;
				}
				$blankResult = $dbrTarget->selectRow( 'blanked_page', 'blank_page_id'
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


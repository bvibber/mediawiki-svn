<?php
class PopulateIntegrationTable extends SpecialPage {
	function __construct() {
		parent::__construct( 'PopulateIntegrationTable','integration' );
		wfLoadExtensionMessages( 'Integration' );
	}
 
	function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser, $wgLanguageCode
			,$wgLocalisationCacheConf, $wgExtraNamespaces
			, $wgLocalDatabases, $wgIntegrationPrefix
			, $wgMetaNamespace, $wgMetaNamespaceTalk
			, $wgSitename, $wgIntegrationPWD;
		if ( !$this->userCanExecute($wgUser) ) {
			$this->displayRestrictionError();
			return;
		}
		$dbr = wfGetDB( DB_SLAVE );
		$dbw = wfGetDB( DB_MASTER );
		
		$localDBname = $dbr -> getProperty ( 'mDBname' );
		
		$dbw->delete ( 'integration_db', '*' );
		if ( isset ( $wgIntegrationPrefix ) ) {
			
			foreach ( $wgIntegrationPrefix as $thisPrefix => $thisDatabase ) {
				$thisPWD = 0;
				if ( isset ( $wgIntegrationPWD[$thisDatabase])
				    && $wgIntegrationPWD[$thisDatabase] == true) {
					$thisPWD = '1';
				}
				$newDatabaseRow = array (
					'integration_dbname' => $thisDatabase,
					'integration_prefix' => $thisPrefix,
					'integration_pwd'    => $thisPWD
				);
				$dbw->insert ( 'integration_db', $newDatabaseRow );
				
				foreach ( $wgLocalDatabases as $thisDB ) {
					$foreignDbr = wfGetDB ( DB_SLAVE, array(), $thisDB );
					$foreignDbw = wfGetDB ( DB_MASTER, array(), $thisDB );
				
					if ( $thisDB != $localDBname && $thisDatabase == $localDBname ) {
						$foreignResult = $foreignDbr->selectRow(
							'interwiki',
							'iw_prefix',
							array( "iw_prefix" => $thisPrefix )
						);
						if ( !$foreignResult ) {
							$localTitle = Title::newFromText ( 'Foobarfoobar' );
							$localURL = $localTitle->getFullURL();
							$localURL = str_replace ( 'Foobarfoobar','$1',$localURL );
							$newInterwikiRow = array (
								'iw_prefix' => $thisPrefix,
								'iw_url' => $localURL,
								'iw_local' => '1',
								'iw_trans' => '0'
							);
							$foreignDbw->insert ( 'interwiki', $newInterwikiRow );
						}
					}
				}
			}
		}
		
		$myCache = new LocalisationCache ( $wgLocalisationCacheConf );
		
		$namespaceNames = $myCache->getItem ( $wgLanguageCode,'namespaceNames' );
		$namespaceNames[NS_PROJECT] = $wgMetaNamespace;
		$namespaceNames[NS_PROJECT_TALK] = $wgMetaNamespace."_talk";
		$dbw->delete ( 'integration_namespace', array( 'integration_dbname' => $localDBname ) );
		
		foreach ( $namespaceNames as $key => $thisName ) {
			$newNamespaceRow = array ( 'integration_dbname' => $localDBname,
				'integration_namespace_index' => $key,
				'integration_namespace_title' => $thisName
			);
			$dbw->insert ( 'integration_namespace', $newNamespaceRow);
		}
		foreach ( $wgExtraNamespaces as $key => $thisName ) {
			$newNamespaceRow = array ( 'integration_dbname' => $localDBname,
				'integration_namespace_index' => $key,
				'integration_namespace_title' => $thisName
			);
			$dbw->insert ( 'integration_namespace', $newNamespaceRow);
		}
		$newNamespaceRow = array ( 'integration_dbname' => $localDBname,
			'integration_namespace_index' => NS_SPECIAL,
			'integration_namespace_title' => 'DisregardSpecial'
		);
		$dbw->insert ( 'integration_namespace', $newNamespaceRow);
		$newNamespaceRow = array ( 'integration_dbname' => $localDBname,
			'integration_namespace_index' => NS_MEDIA,
			'integration_namespace_title' => 'DisregardForPWD'
		);
		$dbw->insert ( 'integration_namespace', $newNamespaceRow);
		$newNamespaceRow = array ( 'integration_dbname' => $localDBname,
			'integration_namespace_index' => NS_FILE,
			'integration_namespace_title' => 'DisregardForPWD'
		);
		$dbw->insert ( 'integration_namespace', $newNamespaceRow);
		$wgOut->setPagetitle( wfMsg( 'actioncomplete' ) );
		$wgOut->addWikiMsg( 'integration-setuptext', $wgSitename );
		return;
	}
}

<?php

/**
 * File holding the SpecialUpdate class.
 *
 * @file SpecialUpdate.php
 * @ingroup Deployment
 * @ingroup SpecialPage
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * A special page that allows checking for updates for both MediaWiki itself and extensions. 
 * 
 * @author Jeroen De Dauw
 */
class SpecialUpdate extends SpecialPage {

	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 */	
	public function __construct() {
		parent::__construct( 'Update', 'siteadmin' );
	}

	/**
	 * Main method.
	 * 
	 * @since 0.1 
	 * 
	 * @param $arg String
	 */	
	public function execute( $arg ) {
		global $wgOut, $wgUser, $wgVersion, $wgExtensionCredits;
		
		$wgOut->setPageTitle( wfMsg( 'update-title' ) );
		
		// If the user is authorized, display the page, if not, show an error.
		if ( $this->userCanExecute( $wgUser ) ) {
			$allExtensions = array();
			
			foreach ( $wgExtensionCredits as $type => $extensions ) {
				foreach ( $extensions as $extension ) {
					if ( array_key_exists( 'name', $extension ) && array_key_exists( 'version', $extension ) ) {
						$allExtensions[$extension['name']] = $extension['version']; 
					}
				}
			}
			
			$repository = wfGetRepository();
			$updates = $repository->installationHasUpdates( $wgVersion, $allExtensions );
			
			if ( $updates === false ) {
				$this->showCoreStatus( false );
				$this->showExtensionStatuses( false );				
			}
			else {
				// Check if there is a MediaWiki update.
				if ( array_key_exists( 'MediaWiki', $updates ) ) {
					$this->showCoreStatus( $updates['MediaWiki'] );
					unset( $updates['MediaWiki'] );
				}
				else {
					$this->showCoreStatus( false );
				}
				
				$this->showExtensionStatuses( count( $updates ) > 0 ? $updates : false );	
			}

		} else {
			$this->displayRestrictionError();
		}			
	}
	
	/**
	 * Displays messages indicating if the MediaWiki install is up
	 * to date or not, and if not, which updates are available.
	 * 
	 * @since 0.1 
	 * 
	 * @param $status
	 */
	protected function showCoreStatus( $status ) {
		global $wgVersion;
		
		
	}
	
	/**
	 * Shows a list of extensions that have updates avialable,
	 * or a message indicating they are all up to date.
	 * 
	 * @since 0.1 
	 * 
	 * @param $status
	 */	
	protected function showExtensionStatuses() {
		global $wgExtensionCredits;
		
		
		// TODO
	}
	
}
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
				$this->showExtensionStatuses();				
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

				$this->showExtensionStatuses( $updates );	
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
	 * @param $status Mixed: false when there are no updates or a version number (string) when there is. 
	 */
	protected function showCoreStatus( $status ) {
		global $wgOut, $wgVersion;
		
		if ( $status === false ) {
			$wgOut->addHTML( '<h3>' . wfMsg( 'mediawiki-up-to-date' ) . '</h3>' );
			$wgOut->addWikiMsg( 'mediawiki-up-to-date-long' );
		}
		else {
			$wgOut->addHTML( '<h3>' . wfMsg( 'mediawiki-update-available' ) . '</h3>' );
			// TODO: link
			$wgOut->addWikiMsgArray( 'mediawiki-update-available-long', array( $status, '' ) );
		}
	}
	
	/**
	 * Shows a list of extensions that have updates avialable,
	 * or a message indicating they are all up to date.
	 * 
	 * @since 0.1 
	 * 
	 * @param $extensions Array: the extensions that have updates and their version numbers.
	 */	
	protected function showExtensionStatuses( array $extensions = array() ) {
		global $wgOut;
		
		$wgOut->addHTML( '<h3>' . wfMsg( 'special-update-extensions' ) . '</h3>' );
		
		if ( count( $extensions ) == 0 ) {// TODO: >
			// TODO: Remove debug
			$extensions = array(
				'foobar' => (object)array(
					'version_id' => '42',
					'version_nr' => '4.2',
					'version_status' => 'stable' 
				)
			);
		
			$wgOut->addWikiMsg( 'extensions-updates-available' );
			
			$wgOut->addHTML( Html::element( 'button', array(), wfMsg( 'update-extensions-button' ) ) );
			
			$wgOut->addHTML( Html::openElement(
				'table',
				array( 'class' => 'wikitable', 'style' => 'width:100%' )
			) );
			
			// TODO: select all magic
			
			$wgOut->addHTML(
				'<tr><th>' .
				Html::element( 'input', array( 'type' => 'checkbox', 'id' => 'select-all-extensions' ) ) .
				'</th><th>' .
				Html::element( 'label', array( 'for' => 'select-all-extensions' ), wfMsg( 'select-all-extensions' ) ) .
				'</th></tr>'
			);
			
			foreach ( $extensions as $extensionName => $extensionData ) {
				$this->displayExtensionStatus( $extensionName, $extensionData );
			}
			
			$wgOut->addHTML(
				'<tr><th>' .
				Html::element( 'input', array( 'type' => 'checkbox', 'id' => 'select-all-extensions-2' ) ) .
				'</th><th>' .
				Html::element( 'label', array( 'for' => 'select-all-extensions-2' ), wfMsg( 'select-all-extensions' ) ) .
				'</th></tr>'
			);			
			
			$wgOut->addHTML( '</table>' );
			
			$wgOut->addHTML( Html::element( 'button', array(), wfMsg( 'update-extensions-button' ) ) );
		}
		else {
			$wgOut->addWikiMsg( 'extensions-up-to-date' );
		}
	}
	
	/**
	 * Displays a single row in the update list.
	 * 
	 * @since 0.1 
	 * 
	 * @param $extensionName String
	 * @param $extension Array
	 */		
	protected function displayExtensionStatus( $extensionName, $extension ) {
		global $wgOut, $wgExtensionCredits;
		
		$wgOut->addHTML( '' );
		
		$wgOut->addHTML(
			'<tr><th class="check-column">' .
				Html::element( 'input', array( 'type' => 'checkbox', 'id' => 'select-all-extensions' ) ) .
			'</th><td>' .
				Html::element( 'b', array(), $extensionName ) . '<br />' .
				wfMsg( 'extension-update-text', '', $extension->version_nr ) . //'<br />' . // TODO
				//wfMsg( 'extension-update-compatibility', '' ) .
			'</td></tr>'
		);
	}
	
}
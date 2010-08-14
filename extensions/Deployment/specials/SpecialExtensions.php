<?php

/**
 * File holding the SpecialExtensions class.
 *
 * @file SpecialExtensions.php
 * @ingroup Deployment
 * @ingroup SpecialPage
 *
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

/**
 * A special page that allows browing and searching through installed extensions.
 * Based on Special:Version.
 * 
 * @since 0.1
 * 
 * @author Jeroen De Dauw
 */
class SpecialExtensions extends SpecialPage {

	/**
	 * @var boolean
	 */
	protected $openedFirstExtension = false;
	
	protected static $viewvcUrls = array(
		'svn+ssh://svn.wikimedia.org/svnroot/mediawiki' => 'http://svn.wikimedia.org/viewvc/mediawiki',
		'http://svn.wikimedia.org/svnroot/mediawiki' => 'http://svn.wikimedia.org/viewvc/mediawiki',
		# Doesn't work at the time of writing but maybe some day: 
		'https://svn.wikimedia.org/viewvc/mediawiki' => 'http://svn.wikimedia.org/viewvc/mediawiki',
	);
	
	protected $typeFilter;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'Extensions', 'siteadmin' );	
	}

	/**
	 * Main method.
	 * 
	 * @since 0.1
	 * 
	 * @param $arg String
	 */
	public function execute( $arg ) {
		global $wgOut, $wgUser;

		$this->typeFilter = is_null( $arg ) ? 'all' : $arg;
		
		$wgOut->setPageTitle( wfMsg( 'extensions-title' ) );		
		
		// If the user is authorized, display the page, if not, show an error.
		if ( $this->userCanExecute( $wgUser ) ) {
			$this->displayPage();
		} else {
			$this->displayRestrictionError();
		}	
	}
	
	/**
	 * Creates and outputs the page contents.
	 * 
	 * @since 0.1
	 */
	protected function displayPage() {
		global $wgOut;
		
		// Shows an "add new" button linking to the Special:Install page. 
		$wgOut->addHTML( 
			Html::element(
				'button',
				array(
					'type' => 'button',
					'onclick' => 'window.location="' . Xml::escapeJsString( SpecialPage::getTitleFor( 'install' )->getFullURL() ) . '"'
				),
				wfMsg( 'add-new-extensions' )
			)
		);
		
		$wgOut->addWikiText(
			Xml::element( 'h2', array( 'id' => 'mw-version-ext' ), wfMsg( 'version-extensions' ) )
		);
		
		$this->displayFilterControl();
		
		$this->displayBulkActions();
		
		$this->displayExtensionList();		
	}

	/**
	 * Creates and outputs the filter control.
	 * 
	 * @since 0.1
	 */	
	protected function displayFilterControl() {
		global $wgOut, $wgExtensionCredits;
		
		$extensionAmount = 0;
		$filterSegments = array();
		
		$extensionTypes = SpecialVersion::getExtensionTypes();
		
		foreach ( $extensionTypes as $type => $message ) {
			if ( !array_key_exists( $type, $wgExtensionCredits ) ) {
				continue;
			}
			
			$amount = count( $wgExtensionCredits[$type] );
			
			if ( $amount > 0 ) {
				$filterSegments[] = $this->getTypeLink( $type, $message, $amount ); 
				$extensionAmount += $amount;					
			}
		}
		
		$all = array( $this->getTypeLink( 'all', wfMsg( 'extension-type-all' ), $extensionAmount ) );
		
		$wgOut->addHTML( implode( ' | ', array_merge( $all, $filterSegments ) ) );
	}
	
	/**
	 * Builds and returns the HTML for a single item in the filter control.
	 * 
	 * @since 0.1
	 * 
	 * @param $type String
	 * @param $message String
	 * @param $amount Integer
	 * 
	 * @return string
	 */
	protected function getTypeLink( $type, $message, $amount ) {
		if ( $this->typeFilter == $type ) {
			$name = Html::element( 'b', array(), $message );
		}
		else {
			$name = Html::element(
				'a',
				array(
					'href' => self::getTitle( $type )->getFullURL()
				),
				$message
			);			
		}
			
		return "$name ($amount)";
	}
	
	/**
	 * Creates and outputs the bilk actions control.
	 * 
	 * @since 0.1
	 */		
	protected function displayBulkActions() {
		// TODO
	}
	
	/**
	 * Displays the installed extensions (of the selected type).
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected function displayExtensionList() {
		global $wgOut, $wgExtensionCredits;
		
		if ( !array_key_exists( $this->typeFilter, $wgExtensionCredits ) && $this->typeFilter != 'all' ) {
			// TODO
		}
		else {
			$extensions = array();
			
			if ( $this->typeFilter == 'all' ) {
				foreach ( $wgExtensionCredits as $type => $exts ) {
					$extensions = array_merge( $extensions, $exts );
				}
			}
			else {
				$extensions = $wgExtensionCredits[$this->typeFilter];
			}
			
			if ( count( $extensions ) == 0 ) {
				// TODO
			}
			else {
				$listHtml = Html::openElement(
					'table',
					array( 'class' => 'wikitable', 'style' => 'width:100%' )
				);
				
				$listHtml .= '<tr>' . 
					Html::element( 'th', array(), wfMsg( 'extensionlist-name' ) ) .
					Html::element( 'th', array(), wfMsg( 'extensionlist-description' ) )
					.  '</tr>';
				
				foreach ( $extensions as $extension ) {
					$listHtml .= $this->getExtensionForList( $extension );
				}			
				
				$listHtml .= Html::closeElement( 'table' );
				
				$wgOut->addHTML( $listHtml );				
			}
		}
	}
	
	/**
	 * Creates and returns the html for a single extension in the list.
	 * 
	 * @since 0.1
	 * 
	 * @param $extensions Array
	 * 
	 * @return string
	 */	
	protected function getExtensionForList( array $extension ) {
		$html = '<tr>';
		
		$html .= Html::rawElement(
			'td',
			array(),
			Html::element( 'b', array(), $extension['name'] ) .
			'<br />' .
			Html::element(
				'a',
				array(
					'href' => $extension['url'],
					'class' => 'external text'
				),
				wfMsg( 'extensionlist-details' )		
			) .
			' | ' .
			Html::element(
				'a',
				array(
					'href' => '',
					'class' => 'external text'
				),
				wfMsg( 'extensionlist-download' )		
			)			
		);
		
		$description = self::getExtensionDescription( $extension );
		$authors = self::getExtensionAuthors( $extension );
		$version = self::getExtensionVersion( $extension );
		
		$html .= Html::rawElement(
			'td',
			array(),
			$description . '<br />' . $version . ' | ' . $authors
		);
		
		return $html . '</tr>';
	}
	
	/**
	 * Returns the decription for an extension.
	 * 
	 * @since 0.1
	 * 
	 * @param $extension Array
	 * 
	 * @return string
	 */
	public static function getExtensionDescription( array $extension ) {
		$description = array_key_exists( 'description', $extension ) ? $extension['description'] : '';
		
		if ( array_key_exists( 'descriptionmsg', $extension ) ) {
			if( is_array( $extension['descriptionmsg'] ) ) {
				$descriptionMsgKey = $extension['descriptionmsg'][0];
				
				array_shift( $extension['descriptionmsg'] );
				array_map( 'htmlspecialchars', $extension['descriptionmsg'] );
				
				$msg = wfMsg( $descriptionMsgKey, $extension['descriptionmsg'] );
			} else {
				$msg = wfMsg( $extension['descriptionmsg'] );
			}
			
 			if ( !wfEmptyMsg( $extension['descriptionmsg'], $msg ) && $msg != '' ) {
 				$description = $msg;
 			}
		}

		return $description;
	}
	
	/**
	 * Returns "created by [authors]" or an empty string when there are none.
	 * 
	 * @since 0.1
	 * 
	 * @param $extension Array
	 * 
	 * @return string
	 */	
	public static function getExtensionAuthors( array $extension ) {
		global $wgLang; 
		
		if ( !array_key_exists( 'author', $extension ) ) {
			return '';
		}
		
		// TODO: resolve wikitext
		return wfMsgExt( 'extensionlist-createdby', 'parsemag', $wgLang->listToText( (array)$extension['author'] ) );
	}
	
	/**
	 * Returns version of an extension or an empty string when not available.
	 * 
	 * @since 0.1
	 * 
	 * @param $extension Array
	 * 
	 * @return string
	 */		
	public static function getExtensionVersion( array $extension ) {
		// TODO: add "version " i18n stuff and escape
		return array_key_exists( 'version', $extension ) ? $extension['version'] : '';
	}
	
}
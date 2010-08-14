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
		
		$wgOut->addWikiText( $this->getExtensionList() );		
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
	 * Creates and returns the HTML for the extension list.
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected function getExtensionList() {
		global $wgExtensionCredits;
		
		$out = Xml::openElement( 'table', array( 'class' => 'wikitable', 'id' => 'sv-ext' ) );
		
		$extensionTypes = SpecialVersion::getExtensionTypes();
		
		// Make sure the 'other' type is set to an array. 
		if ( !array_key_exists( 'other', $wgExtensionCredits ) ) {
			$wgExtensionCredits['other'] = array();
		}
		
		// Find all extensions that do not have a valid type and give them the type 'other'.
		foreach ( $wgExtensionCredits as $type => $extensions ) {
			if ( !array_key_exists( $type, $extensionTypes ) ) {
				$wgExtensionCredits['other'] = array_merge( $wgExtensionCredits['other'], $extensions );
			}
		}
		
		// Loop through the extension categories to display their extensions in the list.
		foreach ( $extensionTypes as $type => $message ) {
			if ( $type != 'other' ) {
				$out .= $this->getExtensionCategory( $type, $message );
			}
		}
		
		// We want the 'other' type to be last in the list.
		$out .= $this->getExtensionCategory( 'other', $extensionTypes['other'] );
		
		return $out;
	}
	
	/**
	 * Creates and returns the HTML for a single extension category.
	 * 
	 * @since 0.1
	 * 
	 * @param $type String
	 * @param $message String
	 * 
	 * @return string
	 */
	protected function getExtensionCategory( $type, $message ) {
		global $wgExtensionCredits; 
		
		$out = '';
		
		if ( array_key_exists( $type, $wgExtensionCredits ) && count( $wgExtensionCredits[$type] ) > 0 ) {
			$out .= $this->getExtensionTypeHeader( $message, 'credits-' . $type );

			usort( $wgExtensionCredits[$type], array( $this, 'compare' ) );

			foreach ( $wgExtensionCredits[$type] as $extension ) {
				$out .= $this->getExtensionForList( $extension );
			}
		}

		return $out;
	}
	
	/**
	 * Gets the HTML for an extension type header.
	 * 
	 * @since 0.1
	 * 
	 * @param $text String
	 * @param $name String
	 * 
	 * @return string
	 */
	protected function getExtensionTypeHeader( $text, $name ) {
		$opt = array( 'colspan' => 4 );
		$out = '';

		if( $this->openedFirstExtension ) {
			// Insert a spacing line
			$out .= '<tr class="sv-space">' . Html::element( 'td', $opt ) . "</tr>\n";
		}
		$this->openedFirstExtension = true;

		if( $name ) {
			$opt['id'] = "sv-$name";
		}

		$out .= "<tr>" . Xml::element( 'th', $opt, $text ) . "</tr>\n";
		
		return $out;
	}	
	
	/**
	 * Gets the HTML for a single extension for the extension list.
	 * 
	 * @since 0.1
	 * 
	 * @param $extension Array
	 * 
	 * @return string
	 */
	protected function getExtensionForList( array $extension ) {
		global $wgLang;
		
		$name = isset( $extension['name'] ) ? $extension['name'] : '[no name]';
		
		if ( isset( $extension['path'] ) ) {
			$svnInfo = self::getSvnInfo( dirname($extension['path']) );
			$directoryRev = isset( $svnInfo['directory-rev'] ) ? $svnInfo['directory-rev'] : null;
			$checkoutRev = isset( $svnInfo['checkout-rev'] ) ? $svnInfo['checkout-rev'] : null;
			$viewvcUrl = isset( $svnInfo['viewvc-url'] ) ? $svnInfo['viewvc-url'] : null;
		} else {
			$directoryRev = null;
			$checkoutRev = null;
			$viewvcUrl = null;
		}

		# Make main link (or just the name if there is no URL).
		if ( isset( $extension['url'] ) ) {
			$mainLink = "[{$extension['url']} $name]";
		} else {
			$mainLink = $name;
		}
		
		if ( isset( $extension['version'] ) ) {
			$versionText = '<span class="mw-version-ext-version">' . 
				wfMsg( 'version-version', $extension['version'] ) . 
				'</span>';
		} else {
			$versionText = '';
		}

		# Make subversion text/link.
		if ( $checkoutRev ) {
			$svnText = wfMsg( 'version-svn-revision', $directoryRev, $checkoutRev );
			$svnText = isset( $viewvcUrl ) ? "[$viewvcUrl $svnText]" : $svnText;
		} else {
			$svnText = false;
		}

		# Make description text.
		$description = isset ( $extension['description'] ) ? $extension['description'] : '';
		
		if( isset ( $extension['descriptionmsg'] ) ) {
			# Look for a localized description.
			$descriptionMsg = $extension['descriptionmsg'];
			
			if( is_array( $descriptionMsg ) ) {
				$descriptionMsgKey = $descriptionMsg[0]; // Get the message key
				array_shift( $descriptionMsg ); // Shift out the message key to get the parameters only
				array_map( "htmlspecialchars", $descriptionMsg ); // For sanity
				$msg = wfMsg( $descriptionMsgKey, $descriptionMsg );
			} else {
				$msg = wfMsg( $descriptionMsg );
			}
 			if ( !wfEmptyMsg( $descriptionMsg, $msg ) && $msg != '' ) {
 				$description = $msg;
 			}
		}

		if ( $svnText !== false ) {
			$extNameVer = "<tr>
				<td><em>$mainLink $versionText</em></td>
				<td><em>$svnText</em></td>";
		} else {
			$extNameVer = "<tr>
				<td colspan=\"2\"><em>$mainLink $versionText</em></td>";
		}
		
		$author = isset ( $extension['author'] ) ? $extension['author'] : array();
		$extDescAuthor = "<td>$description</td>
			<td>" . $wgLang->listToText( (array)$author ) . "</td>
			</tr>\n";
		
		return $extNameVer . $extDescAuthor;		
	}
	
	/**
	 * Callback to sort extensions by type.
	 * 
	 * @since 0.1
	 */
	public function compare( $a, $b ) {
		global $wgLang;
		if( $a['name'] === $b['name'] ) {
			return 0;
		} else {
			return $wgLang->lc( $a['name'] ) > $wgLang->lc( $b['name'] )
				? 1
				: -1;
		}
	}
	
	/**
	 * Get an associative array of information about a given path, from its .svn 
	 * subdirectory. Returns false on error, such as if the directory was not 
	 * checked out with subversion.
	 *
	 * Returned keys are:
	 *    Required:
	 *        checkout-rev          The revision which was checked out
	 *    Optional:
	 *        directory-rev         The revision when the directory was last modified
	 *        url                   The subversion URL of the directory
	 *        repo-url              The base URL of the repository
	 *        viewvc-url            A ViewVC URL pointing to the checked-out revision
	 *        
	 * @since 0.1
	 * 
	 * @return array
	 */
	public static function getSvnInfo( $dir ) {
		// http://svnbook.red-bean.com/nightly/en/svn.developer.insidewc.html
		$entries = $dir . '/.svn/entries';

		if( !file_exists( $entries ) ) {
			return false;
		}

		$lines = file( $entries );
		if ( !count( $lines ) ) {
			return false;
		}

		// check if file is xml (subversion release <= 1.3) or not (subversion release = 1.4)
		if( preg_match( '/^<\?xml/', $lines[0] ) ) {
			// subversion is release <= 1.3
			if( !function_exists( 'simplexml_load_file' ) ) {
				// We could fall back to expat... YUCK
				return false;
			}

			// SimpleXml whines about the xmlns...
			wfSuppressWarnings();
			$xml = simplexml_load_file( $entries );
			wfRestoreWarnings();

			if( $xml ) {
				foreach( $xml->entry as $entry ) {
					if( $xml->entry[0]['name'] == '' ) {
						// The directory entry should always have a revision marker.
						if( $entry['revision'] ) {
							return array( 'checkout-rev' => intval( $entry['revision'] ) );
						}
					}
				}
			}
			
			return false;
		}

		// Subversion is release 1.4 or above.
		if ( count( $lines ) < 11 ) {
			return false;
		}
		
		$info = array(
			'checkout-rev' => intval( trim( $lines[3] ) ),
			'url' => trim( $lines[4] ),
			'repo-url' => trim( $lines[5] ),
			'directory-rev' => intval( trim( $lines[10] ) )
		);
		
		if ( isset( self::$viewvcUrls[$info['repo-url']] ) ) {
			$viewvc = str_replace( 
				$info['repo-url'], 
				self::$viewvcUrls[$info['repo-url']],
				$info['url']
			);
			
			$pathRelativeToRepo = substr( $info['url'], strlen( $info['repo-url'] ) );
			$viewvc .= '/?pathrev=';
			$viewvc .= urlencode( $info['checkout-rev'] );
			$info['viewvc-url'] = $viewvc;
		}
		
		return $info;
	}	
	
}
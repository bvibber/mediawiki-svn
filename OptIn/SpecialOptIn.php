<?php
/**
 * Special:OptIn
 *
 * @file
 * @ingroup Extensions
 */

class SpecialOptIn extends SpecialPage {
	function __construct() {
		parent::__construct( 'OptIn' );
		wfLoadExtensionMessages( 'OptIn' );
	}
	
	function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser;
		$this->setHeaders();
		$wgOut->setPageTitle( wfMsg( 'optin-title' ) );
		
		if ( $wgUser->isAnon() ) {
			$url = SpecialPage::getTitleFor( 'Userlogin' )->getFullURL(
				array( 'returnto' => $this->getTitle()->getPrefixedUrl() ) );
			$wgOut->wrapWikiMsg( "<div class='plainlinks'>\n$1\n</div>", array( 'optin-needlogin', $url ) );
			return;
		}
		
		if ( $wgRequest->wasPosted() ) {
			if ( $wgRequest->getVal( 'opt' ) == 'in' ) {
				$this->optIn( $wgUser );
				$wgOut->addWikiMsg( 'optin-success-in' );
			} else {
				$this->optOut( $wgUser );
				$wgOut->addWikfMsg( 'optin-success-out' );
			}
		} else {
			$wgOut->addWikiMsg( 'optin-intro' );
		}
		$this->showForm();
	}
	
	function showForm() {
		global $wgUser, $wgOut;
		$retval = Xml::openElement( 'form', array(
			'method' => 'post',
			'action' => $this->getTitle()->getLinkURL()
		) );
		$opt = ( $this->isOptedIn( $wgUser ) ? 'out' : 'in' );
		$retval .= Xml::hidden( 'opt', $opt );
		// Uses the optin-submit-in or optin-submit-out message
		$retval .= Xml::submitButton( wfMsg( "optin-submit-$opt" ) );
		$retval .= Xml::closeElement( 'form' );
		$wgOut->addHTML( $retval );
	}
	
	function isOptedIn( $user ) {
		global $wgOptInPrefs;
		foreach ( $wgOptInPrefs as $pref => $value ) {
			if ( $user->getOption( $pref ) != $value ) {
				return false;
			}
		}
		return true;
	}
	
	function optIn( $user ) {
		global $wgOptInPrefs;
		foreach( $wgOptInPrefs as $pref => $value ) {
			$user->setOption( $pref, $value );
		}
		$user->saveSettings();
	}
	
	function optOut( $user ) {
		global $wgOptInPrefs;
		foreach( $wgOptInPrefs as $pref => $value ) {
			$user->setOption( $pref, null );
		}
		$user->saveSettings();
	}
}

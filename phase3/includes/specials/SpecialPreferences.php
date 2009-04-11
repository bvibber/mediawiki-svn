<?php

class SpecialPreferences extends SpecialPage {
	function __construct() {
		parent::__construct( 'Preferences' );
	}
	
	function execute( $par ) {
		global $wgOut, $wgUser, $wgRequest, $wgTitle;
		
		$wgOut->setPageTitle( wfMsg( 'preferences' ) );
		$wgOut->setArticleRelated( false );
		$wgOut->setRobotPolicy( 'noindex,nofollow' );
		$wgOut->addScriptFile( 'prefs.js' );

		$wgOut->disallowUserJs();  # Prevent hijacked user scripts from sniffing passwords etc.
		
		if ( $wgUser->isAnon() ) {
			$wgOut->showErrorPage( 'prefsnologin', 'prefsnologintext', array($wgTitle->getPrefixedDBkey()) );
			return;
		}
		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}

		if ( $wgRequest->getCheck( 'success' ) ) {
			$wgOut->wrapWikiMsg(
				'<div class="successbox"><strong>$1</strong></div>',
				'savedprefs'
			);
		}
		
		$formDescriptor = Preferences::getPreferences( $wgUser );
		
		$htmlForm = new PreferencesForm( $formDescriptor, 'prefs' );
		
		$htmlForm->setSubmitText( wfMsg('saveprefs') );
		$htmlForm->setTitle( $this->getTitle() );
		$htmlForm->setSubmitID( 'prefsubmit' );
		$htmlForm->setSubmitCallback( array( 'SpecialPreferences', 'trySubmit' ) );

		$htmlForm->show();
	}
	
	static function trySubmit( $formData ) {
		global $wgUser, $wgEmailAuthentication, $wgEnableEmail;
		
		// Stuff that shouldn't be saved as a preference.
		$saveBlacklist = array(
				'realname',
				'emailaddress',
			);
		
		if( $wgEnableEmail ) {
			$newadr = $formData['emailaddress'];
			$oldadr = $wgUser->getEmail();
			if( ($newadr != '') && ($newadr != $oldadr) ) {
				# the user has supplied a new email address on the login page
				# new behaviour: set this new emailaddr from login-page into user database record
				$wgUser->setEmail( $newadr );
				# but flag as "dirty" = unauthenticated
				$wgUser->invalidateEmail();
				if ($wgEmailAuthentication) {
					# Mail a temporary password to the dirty address.
					# User can come back through the confirmation URL to re-enable email.
					$result = $wgUser->sendConfirmationMail();
					if( WikiError::isError( $result ) ) {
						return wfMsg( 'mailerror', htmlspecialchars( $result->getMessage() ) );
					} else {
						// TODO return this somehow
#						wfMsg( 'eauthentsent', $wgUser->getName() );
					}
				}
			} else {
				$wgUser->setEmail( $newadr );
			}
			if( $oldadr != $newadr ) {
				wfRunHooks( 'PrefsEmailAudit', array( $wgUser, $oldadr, $newadr ) );
			}
		}
		
		// Fortunately, the realname field is MUCH simpler
		global $wgAllowRealName;
		if ($wgAllowRealName) {
			$realName = $formData['realname'];
			$wgUser->setRealName( $realName );
		}
		
		foreach( $saveBlacklist as $b )
			unset( $formData[$b] );
		
		// Reset options to default state before saving.
		//  Keeps old preferences from interfering due to back-compat
		//  code, etc.
		$wgUser->resetOptions();
		
		foreach( $formData as $key => $value ) {
			$wgUser->setOption( $key, $value );
		}
		
		$wgUser->saveSettings();
		
		// Done
		global $wgOut;
		$wgOut->redirect( SpecialPage::getTitleFor( 'Preferences' )->getFullURL( 'success' ) );
		
		return true;
	}
}

/** Some tweaks to allow js prefs to work */
class PreferencesForm extends HTMLForm {

	function displayForm( $submitResult ) {
		global $wgUser, $wgOut;
		
		if ( $submitResult !== false ) {
			$this->displayErrors( $submitResult );
		}
		
		$html = $this->displaySection( $this->mFieldTree );
		
		// Hidden fields
		$html .= Xml::hidden( 'wpEditToken', $wgUser->editToken() );
		$html .= Xml::hidden( 'title', $this->getTitle() );
		
		$attribs = array();
		
		if ( isset($this->mSubmitID) )
			$attribs['id'] = $this->mSubmitID;
		
		$html .= Xml::submitButton( $this->getSubmitText(), $attribs );
		
		$html = Xml::tags( 'div', array( 'id' => 'preferences' ), $html );
		
		$html = Xml::tags( 'form',
							array(
								'action' => $this->getTitle()->getFullURL(),
								'method' => 'post',
							),
							$html );
							
		$wgOut->addHTML( $html );
	}
	
}

<?php
/* Shamelessly copied and modified from /includes/specials/SpecialPreferences.php v1.15 */
class EditUser extends SpecialPage {
	var $mQuickbar, $mStubs;
	var $mRows, $mCols, $mSkin, $mMath, $mDate, $mUserEmail, $mEmailFlag, $mNick;
	var $mUserLanguage, $mUserVariant;
	var $mSearch, $mRecent, $mRecentDays, $mTimeZone, $mHourDiff, $mSearchLines, $mSearchChars, $mAction;
	var $mReset, $mPosted, $mToggles, $mSearchNs, $mRealName, $mImageSize;
	var $mUnderline, $mWatchlistEdits;
	# password changing was removed from prefs in 1.14, so we just add it back in now :)
	var $mNewpass, $mRetypePass;
	
	function __construct() {
		SpecialPage::SpecialPage('EditUser', 'edituser');
	}
	function execute( $par ) {
		global $wgOut, $wgUser, $wgRequest;
	
		if( !$wgUser->isAllowed( 'edituser' ) ) {
			$wgOut->permissionRequired( 'edituser' );
			return false;
		}

		wfLoadExtensionMessages( 'EditUser' );

		$this->setHeaders();
		$this->target = ( isset( $par ) ) ? $par : $wgRequest->getText( 'username', '' );
		if( $this->target === '' ) {
			$wgOut->addHtml( $this->makeSearchForm() );
			return;
		}
		$targetuser = User::NewFromName( $this->target );
		if( $targetuser->getID() == 0 ) {
			$wgOut->addWikiMsg( 'edituser-nouser', htmlspecialchars( $this->target ) );
			return;
		}
		#Allow editing self via this interface
		if( $targetuser->isAllowed( 'edituser-exempt' ) && $targetuser->getName() != $wgUser->getName() ) {
			$wgOut->addWikiMsg( 'edituser-exempt', $targetuser->getName() );
			return;
		}

		$this->setHeaders();
		$this->outputHeader();
		$wgOut->disallowUserJs();  # Prevent hijacked user scripts from sniffing passwords etc.

		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		
		if ( $wgRequest->getCheck( 'reset' ) ) {
			$this->showResetForm();
			return;
		}
		
		$wgOut->addScriptFile( 'prefs.js' );
		
		//$this->loadGlobals( $this->target );
		$wgOut->addHtml( $this->makeSearchForm() . '<br />' );
		#End EditUser additions

		if ( $wgRequest->getCheck( 'success' ) ) {
			$wgOut->wrapWikiMsg(
				"<div class=\"successbox\"><strong>\n$1\n</strong></div><div id=\"mw-pref-clear\"></div>",
				'savedprefs'
			);
		}
		
		if ( $wgRequest->getCheck( 'eauth' ) ) {
			$wgOut->wrapWikiMsg( "<div class='error' style='clear: both;'>\n$1\n</div>",
									'eauthentsent', $this->target );
		}

		$htmlForm = Preferences::getFormObject( $targetuser, 'EditUserPreferencesForm' );
		$htmlForm->setSubmitCallback( array( $this, 'tryUISubmit' ) );
		$htmlForm->setTitle( $this->getTitle() );
		$htmlForm->addHiddenField( 'username', $this->target );
		$htmlForm->mEditUserUsername = $this->target;
		
		$htmlForm->show();
	}

	function showResetForm() {
		global $wgOut;

		$wgOut->addWikiMsg( 'prefs-reset-intro' );

		$htmlForm = new HTMLForm( array(), 'prefs-restore' );

		$htmlForm->setSubmitText( wfMsg( 'restoreprefs' ) );
		$htmlForm->setTitle( $this->getTitle() );
		$htmlForm->addHiddenField( 'username', $this->target );
		$htmlForm->addHiddenField( 'reset' , '1' );
		$htmlForm->setSubmitCallback( array( $this, 'submitReset' ) );
		$htmlForm->suppressReset();

		$htmlForm->show();
	}

	function submitReset( $formData ) {
		global $wgUser, $wgOut;
		$wgUser->resetOptions();
		$wgUser->saveSettings();

		$url = $this->getTitle()->getFullURL( array( 'success' => 1, 'username'=>$this->target ) );

		$wgOut->redirect( $url );

		return true;
	}
	
	function tryUISubmit( $formData ) {
		global $wgUser;
		
		$targetuser = User::NewFromName( $this->target );
		if( $targetuser->getID() == 0 ) {
			return  wfMsg( 'edituser-nouser' ) ;
		}
		
		$realUser = $wgUser;
		$wgUser = $targetuser;
		$res = Preferences::tryFormSubmit( $formData, 'uiEditUser' );
		$wgUser = $realUser;
		
		if ( $res ) {
			$urlOptions = array( 'success' => 1);

			if ( $res === 'eauth' ) {
				$urlOptions['eauth'] = 1;
			}

			//$queryString = implode( '&', $urlOptions );
			$urlOptions['username'] = $this->target;

			$url = $this->getTitle()->getFullURL( $urlOptions );

			global $wgOut;
			$wgOut->redirect( $url );
		}

		return true;
	}

	
	function makeSearchForm() {
		$fields = array();
		$fields['edituser-username'] = Html::input( 'username', $this->target );

		$thisTitle = Title::makeTitle( NS_SPECIAL, $this->getName() );
		$form = Html::openElement( 'form', array( 'method' => 'post', 'action' => $thisTitle->getLocalUrl() ) ) .
			Xml::buildForm( $fields, 'edituser-dosearch' ) .
			Html::hidden( 'issearch', '1' ) .
			Html::closeElement( 'form' );
		return $form;
	}
}

class EditUserPreferencesForm extends PreferencesForm {
	var $mEditUserUsername;
	
	function getButtons() {
		$html = HTMLForm::getButtons();

		global $wgUser;

		$sk = $wgUser->getSkin();
		$url = SpecialPage::getTitleFor( 'EditUser' )->getFullURL( array( 'reset' => 1, 'username' => $this->mEditUserUsername ) );

		$html .= "\n" . Xml::element('a', array( 'href'=> $url ), wfMsgHtml( 'restoreprefs' ) );

		$html = Xml::tags( 'div', array( 'class' => 'mw-prefs-buttons' ), $html );

		return $html;
	}
}

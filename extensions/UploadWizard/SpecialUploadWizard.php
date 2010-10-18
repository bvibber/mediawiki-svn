<?php
/**
 * Special:UploadWizard
 *
 * Easy to use multi-file upload page.
 *
 * @file
 * @ingroup SpecialPage
 * @ingroup Upload
 */

class SpecialUploadWizard extends SpecialPage {


	// $request is the request (usually wgRequest)
	// $par is everything in the URL after Special:UploadWizard. Not sure what we can use it for
	public function __construct( $request=null, $par=null ) {
		global $wgEnableAPI, $wgRequest;

		if (! $wgEnableAPI) {
			// XXX complain
		}

		// here we would configure ourselves based on stuff in $request and $wgRequest, but so far, we
		// don't have such things

		parent::__construct( 'UploadWizard', 'upload' );

		$this->simpleForm = new UploadWizardSimpleForm();
		$this->simpleForm->setTitle( $this->getTitle() );
	}

	/**
	 * Replaces default execute method
	 * Checks whether uploading enabled, user permissions okay, 
	 * @param subpage, e.g. the "foo" in Special:UploadWizard/foo. 
	 */
	public function execute( $subPage ) {
		global $wgScriptPath, $wgLang, $wgUser, $wgOut;

		// side effects: if we can't upload, will print error page to wgOut 
		// and return false
		if (! ( $this->isUploadAllowed() && $this->isUserUploadAllowed( $wgUser ) ) ) {
			return;
		}

		$langCode = $wgLang->getCode();

		$this->setHeaders();
		$this->outputHeader();

		$this->addJsVars( $subPage );
		
		$wgOut->addModules( 'ext.uploadWizard' );
		
		// where the uploadwizard will go
		// TODO import more from UploadWizard itself.
		// "createInterface" call?
		$wgOut->addHTML(
			'<div id="upload-licensing" class="upload-section" style="display: none;">Licensing tutorial</div>'
			. '<div id="upload-wizard" class="upload-section"><div class="loadingSpinner"></div></div>'
		);
		

		// fallback for non-JS
		$wgOut->addHTML('<noscript>');
		$this->simpleForm->show();
		$wgOut->addHTML('</noscript>');
	
	}

	/**
	 * Adds some global variables for our use, as well as initializes the UploadWizard
	 * @param subpage, e.g. the "foo" in Special:UploadWizard/foo
	 */
	public function addJsVars( $subPage ) {
		global $wgUser, $wgOut;
		global $wgUseAjax, $wgAjaxLicensePreview, $wgEnableAPI;
		global $wgEnableFirefogg, $wgFileExtensions;
		global $wgUploadWizardDebug;

		$wgOut->addScript( Skin::makeVariablesScript( array(
			'wgUploadWizardDebug' => !!$wgUploadWizardDebug,

			// uncertain if this is relevant. Can we do license preview with API?
			'wgAjaxLicensePreview' => $wgUseAjax && $wgAjaxLicensePreview,

			'wgEnableFirefogg' => (bool)$wgEnableFirefogg,

			// what is acceptable in this wiki
			'wgFileExtensions' => $wgFileExtensions,

			'wgSubPage' => $subPage

			// XXX need to have a better function for testing viability of a filename
			// 'wgFilenamePrefixBlacklist' => UploadBase::getFilenamePrefixBlacklist()

		) ) );

	}

	/**
	 * Check if anyone can upload (or if other sitewide config prevents this)
	 * Side effect: will print error page to wgOut if cannot upload.
	 * @return boolean -- true if can upload
	 */
	private function isUploadAllowed() {
		global $wgOut;

		// Check uploading enabled
		if( !UploadBase::isEnabled() ) {
			$wgOut->showErrorPage( 'uploaddisabled', 'uploaddisabledtext' );
			return false;
		}

		// Check whether we actually want to allow changing stuff
		if( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return false;
		}	

		// we got all the way here, so it must be okay to upload
		return true;
	}

	/**
	 * Check if the user can upload 
	 * Side effect: will print error page to wgOut if cannot upload.
	 * @param User
	 * @return boolean -- true if can upload
	 */
	private function isUserUploadAllowed( $user ) {
		global $wgOut, $wgGroupPermissions;

		if( !$user->isAllowed( 'upload' ) ) {
			if( !$user->isLoggedIn() && ( $wgGroupPermissions['user']['upload']
				|| $wgGroupPermissions['autoconfirmed']['upload'] ) ) {
				// Custom message if logged-in users without any special rights can upload
				$wgOut->showErrorPage( 'uploadnologin', 'uploadnologintext' );
			} else {
				$wgOut->permissionRequired( 'upload' );
			}
			return false;
		}

		// Check blocks
		if( $user->isBlocked() ) {
			$wgOut->blockedPage();
			return false;
		}

		// we got all the way here, so it must be okay to upload
		return true;
	}

}


/**
 * This is a hack on UploadForm.
 * Normally, UploadForm adds its own Javascript.
 * We wish to prevent this, because we want to control the case where we have Javascript.
 * So, we subclass UploadForm, and make the addUploadJS a no-op.
 */
class UploadWizardSimpleForm extends UploadForm {
	protected function addUploadJS( ) { }

}



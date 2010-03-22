<?php
/**
 * Special:UploadWizard
 *
 * Usability Initiative multi-file upload page.
 * This page is more of a hack right now, just for the usability testing we will perform in March-April.
 *
 * Deliberately not doing this as an extension because we need to stay within the JS2 branch for now,
 * and there aren't any extensions there. This Special page should probably not be permanent.
 *
 * @file
 * @ingroup SpecialPage
 * @ingroup Upload
 */

class SpecialUploadWizard extends SpecialPage {

	// $request is the request (usually wgRequest)
	// $par is everything in the URL after Special:UploadWizard. Not sure what we can use it for
        public function __construct( $request=null ) {
		global $wgEnableJS2, $wgEnableAPI, $wgRequest;

		if (! $wgEnableJS2) {
			// XXX complain
		}

		if (! $wgEnableAPI) {
			// XXX complain
		}

		// here we would configure ourselves based on stuff in $request and $wgRequest, but so far, we
		// don't have such things

		parent::__construct( 'UploadWizard', 'upload' );

		$this->simpleForm = new UploadWizardSimpleForm();
		$this->simpleForm->setTitle( $this->getTitle() );
        }

	public function execute() {
		global $wgUser, $wgOut, $wgMessageCache;

		# Check uploading enabled
		if( !UploadBase::isEnabled() ) {
			$wgOut->showErrorPage( 'uploaddisabled', 'uploaddisabledtext' );
			return;
		}

		# Check permissions
		global $wgGroupPermissions;
		if( !$wgUser->isAllowed( 'upload' ) ) {
			if( !$wgUser->isLoggedIn() && ( $wgGroupPermissions['user']['upload']
				|| $wgGroupPermissions['autoconfirmed']['upload'] ) ) {
				// Custom message if logged-in users without any special rights can upload
				$wgOut->showErrorPage( 'uploadnologin', 'uploadnologintext' );
			} else {
				$wgOut->permissionRequired( 'upload' );
			}
			return;
		}

		# Check blocks
		if( $wgUser->isBlocked() ) {
			$wgOut->blockedPage();
			return;
		}

		# Check whether we actually want to allow changing stuff
		if( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}


		$wgMessageCache->loadAllMessages();

		$this->setHeaders();
		$this->outputHeader();

		$wgOut->addHTML(
			'<div id="upload-licensing" class="upload-section">Licensing tutorial</div>'
			. '<div id="upload-wizard" class="upload-section"></div>'
		);

		$wgOut->addHTML('<noscript>');
		$this->simpleForm->show();
		$wgOut->addHTML('</noscript>');


		//$j('#firstHeading').html("Upload wizard");

		$this->addJS();
	}

	/**
	 * Adds some global variables for our use, as well as initializes the UploadWizard
	 */
	public function addJS() {
		global $wgUser, $wgOut;
		global $wgUseAjax, $wgAjaxLicensePreview, $wgEnableAPI;
		global $wgEnableFirefogg, $wgFileExtensions;

		$wgOut->addScript( Skin::makeVariablesScript( array(
			// uncertain if this is relevant. Can we do license preview with API?
			'wgAjaxLicensePreview' => $wgUseAjax && $wgAjaxLicensePreview,

			'wgEnableFirefogg' => (bool)$wgEnableFirefogg,

			// what is acceptable in this wiki
			'wgFileExtensions' => $wgFileExtensions,

			// our edit token
			'wgEditToken' => $wgUser->editToken(),

			// in the future, we ought to be telling JS land other things,
			// like: requirements for publication, acceptable licenses, etc.

			) )
		);

		$wgOut->addScript( $scriptVars );

		$initScript = <<<EOD
/*
 * This script is run on [[Special:UploadWizard]].
 * Creates an interface for uploading files in multiple steps, hence "wizard"
 */

mw.ready( function() {
	mw.load( 'UploadWizard.UploadWizardTest', function () {
		
		mw.setConfig( 'debug', true ); 

		mw.setDefaultConfig( 'uploadHandlerClass', null );
		mw.setConfig( 'userName', wgUserName ); 
		mw.setConfig( 'userLanguage', wgUserLanguage );
		mw.setConfig( 'fileExtensions', wgFileExtensions );
		mw.setConfig( 'token', wgEditToken );
		mw.setConfig( 'thumbnailWidth', 220 ); // new standard size

		// not for use with all wikis. 
		// The ISO 639 code for the language tagalog is "tl".
		// Normally we name templates for languages by the ISO 639 code.
		// Commons already had a template called 'tl', though.
		// so, this workaround will cause tagalog descriptions to be saved with this template instead.
		mw.setConfig( 'languageTemplateFixups', { tl: 'tgl' } );

		var uploadWizard = new mw.UploadWizard();
		uploadWizard.createInterface( '#upload-wizard' );
	
	} );
} );

EOD;
		$wgOut->addScript( Html::inlineScript( $initScript ) );
		

		// XXX unlike other vars this is specific to the file being uploaded -- re-upload context, for instance
		// Recorded here because we may probably need to
		// bring it back in some form later. Reupload forms may be special, only one file allowed
		/*
		$scriptVars = array(
			'wgUploadAutoFill' => !$this->mForReUpload,
			'wgUploadSourceIds' => $this->mSourceIds,
		);
		*/


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

/*
// XXX UploadWizard extension, do this in the normal SpecialPage way once JS2 issue resolved
function wfSpecialUploadWizard( $par ) {
	global $wgRequest;
	// can we obtain $request from here?
	// $this->loadRequest( is_null( $request ) ? $wgRequest : $request );
	$o = new SpecialUploadWizard( $wgRequest, $par );
	$o->execute();
}
*/

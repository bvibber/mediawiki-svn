<?php

class SpecialPreferences extends SpecialPage {
	function __construct() {
		parent::__construct( 'Preferences' );
	}
	
	function execute( $par ) {
		global $wgOut, $wgUser;
		
		$wgOut->setPageTitle( wfMsg( 'preferences' ) );
		
		$formDescriptor = Preferences::getPreferences( $wgUser );
		
		$htmlForm = new HTMLForm( $formDescriptor, 'prefs' );
		
		$htmlForm->setSubmitText( wfMsg('saveprefs') );
		$htmlForm->setTitle( $this->getTitle() );
		$htmlForm->setSubmitCallback( array( 'SpecialPreferences', 'trySubmit' ) );
		
		$htmlForm->show();
	}
	
	static function trySubmit( $formData ) {
		global $wgUser;
		
		// Stuff that shouldn't be saved as a preference.
		$saveBlacklist = array(
				'realname',
				'emailaddress',
			);
		
		foreach( $saveBlacklist as $b )
			unset( $formData[$b] );
		
		foreach( $formData as $key => $value ) {
			$wgUser->setOption( $key, $value );
		}
		
		$wgUser->saveSettings();
		
		return true;
	}
}

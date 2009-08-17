<?php

class DonateButton extends UnlistedSpecialPage {
	/* Members */

	private $mSharedMaxAge = 600;
	private $mMaxAge = 600;
	
	var $templates = array( 'Ruby', 'RubyText', 'Tourmaline', 'Sapphire' );

	/* Functions */ 
	
	function __construct() {
		parent::__construct( 'DonateButton' );
	}

	function execute () {
		global $wgOut;
		$wgOUt->disable();
		$this->sendHeaders();
		$js = $this->getJsOutput();
	}

	public function sharedMaxAge() {
		return $this->mSharedMaxAge();
	}   

	public function maxAge() {
		return $this->mMaxAge();
	}   

	// Set the caches 
	private function sendHeaders() {
		$smaxage = $this->sharedMaxAge();
		$maxage = $this->maxAge();
		$public = ( session_id() == '' );

		header( "Content-type: text/javascript; charset=utf-8" );
		if ( $public ) { 
			header( "Cache-Control: public, s-maxage=$smaxage, max-age=$maxage" );
		} else {
			header( "Cache-Control: private, s-maxage=0, max-age=$maxage" );
		}
	}

	public function getJsOutput() {
		global $wgFundraiserPortalTemplates;
	
		foreach( $wgFundraiserPortalTemplates as $template => $weight ) {
			$buttons[$template] = $this->getButtonText( $template );
		}

		$encButtons = json_encode( $buttons );

		return $this->getScriptFunctions() .
			'wgFundraiserPortalButtons=' . $encButtons . ";\n" .
			"wgFundraiserPortal=wgFundraiserPortalButtons[wgDonateButton];\n";
	}

	public function getScriptFunctions() {
		global $wgFundraiserPortalTemplates;
		$text = $this->fetchTemplate( 'donateScripts.js' );
		return strtr( $text,
			array( '{{{templateWeights}}}' =>
				json_encode( $wgFundraiserPortalTemplates ) ) );
	}

	public function getButtonText( $template ) {
		global $wgFundraiserImageUrl, $wgFundraiserPortalURL;
		global $wgFundraiserPortalTemplates;

		wfLoadExtensionMessages( 'FundraiserPortal' );

		// Add our tracking identifiet
		$buttonUrl = $wgFundraiserPortalURL . "&utm_source=$template";

		// Switch statement of horror
		if( isset( $wgFundraiserPortalTemplates[$template] ) ) {
			$text = $this->fetchTemplate( "$template.tmpl" );
			
			$text = strtr( $text, array(
				'{{{imageUrl}}}' => $wgFundraiserImageUrl,
				'{{{buttonUrl}}}' => $buttonUrl ));
			
			// Note these are raw; no HTML translation or anything...
			$text = preg_replace_callback( '/\{\{msg:(.*?)\}\}/',
				array( $this, 'templateMessageCallback' ),
				$text );
			
			return $text;
		}
		return false;
	}
	
	function templateMessageCallback( $matches ) {
		return wfMsg( $matches[1] );
	}
	
	/**
	 * Read one of this extension's resource files...
	 */
	protected function fetchTemplate( $filename ) {
		$basedir = dirname( __FILE__ );
		return file_get_contents( "$basedir/Templates/$filename" );
	}
}

<?php

/**
* Freenode Chat extension special page class.
*/

class FreenodeChat extends SpecialPage {

	function __construct() {
		SpecialPage::SpecialPage( 'FreenodeChat', 'freenodechat' );
	}

	function execute( $par ) {
		global $wgOut, $wgUser;
		global $wgFreenodeChannel;

		// Preperation.
		wfLoadExtensionMessages( 'FreenodeChat' );
		$this->setHeaders();

		// Introduction message, explaining to users what this is etc.
		$wgOut->addWikiMsg( 'freenodechat-header' );

		// Prepare query string to pass to widget.
		$queryAssoc = array(
			'channels' => $wgFreenodeChannel
		);

        if ( $wgUser->IsLoggedIn() ) $queryAssoc[ 'nick' ] = str_replace( ' ', '_', $wgUser->getName() );

        foreach ( $queryAssoc as $parameter => $value ) {
			$query[] = $parameter . '=' . urlencode( $value );
		}
		$queryString = implode( '&', $query );

		// Output widget.
		$wgOut->addHTML( Xml::openElement( 'iframe', array(
			'width'     => '1000',
			'height'    => '500',
			'src'       => 'http://webchat.freenode.net/?' . $queryString
		) ) . Xml::closeElement( 'iframe' ) );
	}
}

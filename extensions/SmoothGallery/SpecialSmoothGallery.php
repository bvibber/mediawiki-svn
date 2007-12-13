<?php

if( !defined( 'MEDIAWIKI' ) ) {
        echo( "not a valid entry point.\n" );
        die( 1 );
}

class SpecialSmoothGallery extends SpecialPage {
        /**#@+
         * @access private
         */
        var $mOptionArray, $mInput;

        /**
         * Constructor
         */
        function __construct() {
                global $wgOut;
                SpecialPage::SpecialPage( 'SmoothGallery', '', true );

                #inject messages
                loadSmoothGalleryI18n();
        }

        function execute( $par ) {
                global $wgImageLimits, $wgUser;
                global $wgSmoothGalleryDelimiter;
                global $wgOut;
                global $wgParser;
		global $wgRequest;

                $wopt = $wgUser->getOption( 'imagesize' );

                if( !isset( $wgImageLimits[$wopt] ) ) {
                        $wopt = User::getDefaultOption( 'imagesize' );
                }

                list($width, $height) = $wgImageLimits[$wopt];

                $this->mInput = $wgRequest->getVal( 'input' );

                $this->mOptionArray['height'] = $wgRequest->getVal( 'height', $height );
                $this->mOptionArray['width'] = $wgRequest->getVal( 'width', $width );
                $this->mOptionArray['showcarousel'] = $wgRequest->getVal( 'showcarousel', '1' );
                $this->mOptionArray['timed'] = $wgRequest->getVal( 'timed' );
                $this->mOptionArray['delay'] = $wgRequest->getVal( 'delay' );
                $this->mOptionArray['showarrows'] = $wgRequest->getVal( 'showarrows', '1' );
                $this->mOptionArray['showinfopane'] = $wgRequest->getVal( 'showinfopane', '1' );
                $this->mOptionArray['fallback'] = $wgRequest->getVal( 'fallback', '1' );

                //The extension expects true/false and not 1/0
                $boollist = array("showcarousel", "timed", "delay", "showarrows", "showinfopane");
                foreach ( $boollist as $bool ) {
                        if ( $this->mOptionArray[$bool] == "1" ) {
                                $this->mOptionArray[$bool] = "true";
                        } else {
                                $this->mOptionArray[$bool] = "false";
                        }
                }

                if (!$this->mInput) $this->mInput = $par;

                $this->mInput = str_replace( array('|', ':::'), $wgSmoothGalleryDelimiter, $this->mInput );

		SmoothGallery::setGalleryHeaders( $wgOut );

                $wgOut->addHTML( renderSmoothGallery( $this->mInput, $this->mOptionArray, $wgParser, true ) );
        }

}


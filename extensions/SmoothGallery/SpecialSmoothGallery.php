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
	}

	function execute( $par ) {
		global $wgSmoothGalleryUseDatabase;
		global $wgOut;
		global $wgRequest;

		wfLoadExtensionMessages( 'SmoothGallery' );

		SmoothGallery::setGalleryHeaders( $wgOut );
		SmoothGallery::setGallerySetHeaders( $wgOut );

		if ( ! $wgSmoothGalleryUseDatabase ) {
			global $wgImageLimits, $wgUser;
			global $wgSmoothGalleryDelimiter;
			global $wgParser;

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


			$wgOut->addHTML( initSmoothGallery( $this->mInput, $this->mOptionArray, $wgParser, true ) );
		} else {
			$gId = $wgRequest->getInt( 'gallery' );
			if ( $gId == NULL ) {
				$output = wfMsg("smoothgallery-error");
				$output .= wfMsg("smoothgallery-gallery-not-found");
				$wgOut->addHTML( $output );
			} else {
				$dbr = wfGetDB( DB_SLAVE );
				$row = $dbr->selectField( 'text_sg', 'sg_cache', 'sg_id=' . $dbr->addQuotes( $gId ));
				if ( $row == false ) {
					$output = wfMsg("smoothgallery-error");
					$output .= wfMsg("smoothgallery-gallery-not-found");
					$wgOut->addHTML( $output );
				} else {
					$wgOut->addHTML( $row );
				}
			}
		}
	}
}

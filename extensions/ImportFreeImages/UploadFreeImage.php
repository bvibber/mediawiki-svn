<?php

class UploadFreeImage extends UploadFromUrl {
	/**
	 * Hook to UploadCreateOnRequest.
	 * 
	 * This class processes wpSourceType=IFI
	 */
	public static function onUploadCreateFromRequest( $type, &$className ) {
		if ( $type == 'IFI' ) {
			$className = 'UploadFreeImage';
			// Stop processing
			return false;
		}
		return true;
	}
	
	public static function isEnabled() { return true; }
	
	/**
	 * A valid request requires wpFlickrId to be set
	 */
	public static function isValidRequest( $request ) {
		return (bool)$request->getVal( 'wpFlickrId' );
	}
	
	public function initializeFromRequest( &$request ) {
		return $this->initialize(
			$request->getText( 'wpDestFile' ),
	 		self::getUrl( $request->getText( 'wpFlickrId' ), $request->getText( 'wpSize' ) ),
			false
		);
	}
	public static function getUrl( $flickrId, $requestedSize ) {
		if ( !$requestedSize )
			return false;
		
		$ifi = new ImportFreeImages();
		$sizes = $ifi->getSizes( $flickrId );
		
		foreach ( $sizes as $size ) {
			if ( $size['label'] === $requestedSize ) {
				return $size['source'];
			}
		}
		
		return false;
	}
	
	/**
	 * UI hook to add an extra text box to the upload form
	 */
	public static function onUploadFormSourceDescriptors( &$descriptor, &$radio, $selectedSourceType ) {
		global $wgRequest;
		if ( $wgRequest->getVal( 'wpSourceType' ) != 'IFI' || !$wgRequest->getCheck( 'wpFlickrId' ) )
			return true;
			
		// We entered here from Special:ImportFreeImages, so kill all other source selections
		foreach ( $descriptor as $name => $value ) {
			if ( isset( $value['section'] ) && $value['section'] == 'source' )
				unset( $descriptor[$name] );
		}
		
		$ifi = new ImportFreeImages();
		$sizes = $ifi->getSizes( $wgRequest->getText( 'wpFlickrId' ) );
				
		$options = array();	
		foreach ( $sizes as $size ) {
			$label = wfMsgExt( 'importfreeimages_size_' . strtolower( $size['label'] ), 'parseinline' );
			$options[$label] = $size['label'];
		}
		
		$descriptor['Size'] = array(
			'type' => 'radio',
			'section' => 'source',
			'name' => 'Size',
			'options' => $options
		);
		$descriptor['wpFlickrId'] = array(
			'type' => 'hidden',
			'name' => 'wpFlickrId',
			'default' => $wgRequest->getText( 'wpFlickrId' ),
		);
		$descriptor['wpSourceType'] = array(
			'type' => 'hidden',
			'name' => 'wpSourceType',
			'default' => 'IFI',
		);
		
		// Stop running further hooks
		return false;
	}
	
	public static function onUploadFormInitDescriptor( &$descriptor ) {
		return true;
	}
}
<?php

/**
 * Parser function callbacks for the MediaFunctions extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */
class MediaFunctions {

	/**
	 * Error message constants
	 */
	const ERR_INVALID_TITLE = 'mediafunctions-invalid-title';
	const ERR_NOT_EXIST = 'mediafunctions-not-exist';

	/**
	 * Get the MIME type of a file
	 *
	 * @param Parser $parser Calling parser
	 * @param string $name File name
	 * @return string
	 */
	public static function mediamime( $parser, $name = '' ) {
		$title = self::resolve( $name );
		if( $title instanceof Title ) {
			$parser->mOutput->addImage( $title->getDBkey() );
			$file = wfFindFile( $title );
			return $file instanceof File
				? $file->getMimeType()
				: self::error( self::ERR_NOT_EXIST, $name );
		} else {
			return self::error( self::ERR_INVALID_TITLE, $name );
		}
	}

	/**
	 * Get the size of a file
	 *
	 * @param Parser $parser Calling parser
	 * @param string $name File name
	 * @return string
	 */
	public static function mediasize( $parser, $name = '' ) {
		$title = self::resolve( $name );
		if( $title instanceof Title ) {
			$parser->mOutput->addImage( $title->getDBkey() );
			$file = wfFindFile( $title );
			return $file instanceof File
				? $parser->mOptions->getSkin()->formatSize( $file->getSize() )
				: self::error( self::ERR_NOT_EXIST, $name );
		} else {
			return self::error( self::ERR_INVALID_TITLE, $name );
		}
	}

	/**
	 * Convert a string title into a Title
	 *
	 * The string can be with or without namespace, and might
	 * include an interwiki prefix, etc.
	 *
	 * @param string $text Title string
	 * @return mixed Title or null
	 */
	private static function resolve( $text ) {
		if( $text ) {
			$title = Title::newFromText( $text );
			if( $title instanceof Title ) {
				if( $title->getNamespace() == NS_IMAGE ) {
					return $title;
				} else {
					return Title::makeTitleSafe( NS_IMAGE, $title->getText() );
				}
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
	/**
	 * Generate an error
	 *
	 * @param string $error Error code
	 * @param string $name File name
	 * @return string
	 */
	private static function error( $error, $name ) {
		return htmlspecialchars( wfMsgForContent( $error, $name ) );
	}

}

?>
<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "PicturePopup extension\n";
	die( 1 );
}

global $wgMessageCache;
$wgMessageCache->addMessages( array(
	'picturepopup_invalid_title' => 'Invalid image title',
	'picturepopup_no_license' => 'Image has no license tag',
	'picturepopup_no_image' => 'Image does not exist',
	'picturepopup_no_license_list' => 'License list is invalid or missing',
	'picturepopup_license_list' => 'Project:Image copyright tags',
	'picturepopup_no_license_text' => 'License template has no element with id=imageLicenseText',
	'picturepopup_invalid_icon' => 'License template has missing or invalid imageLicenseIcon element',
));

class PicturePopup {
	var $mTitle;
	
	static function ajax( $image, $recache = false ) {
		global $wgMemc, $wgDBname, $wgLang;

		$title = Title::newFromText( $image );
		if ( $title->getNamespace() != NS_IMAGE ) {
			return self::jsonError( 'picturepopup_invalid_title' );
		}

		$sizeSel = self::getSizeSel();
		$lang = $wgLang->getCode();
		$hash = md5( $title->getPrefixedDBkey() );
		$memcKey = "$wgDBname:picturepopup:ajax:$sizeSel:$lang:$hash";
		if ( $recache ) {
			$wgMemc->delete( $memcKey );
		}
		$pp = new PicturePopup( $title );
		$value = $title->getRelatedCache( $wgMemc, $memcKey, 86400, 
			array( $pp, 'ajaxNoCache' ), array( $sizeSel ) );
		$response = new AjaxResponse( $value );
		$response->setContentType( 'application/json' );
		return $response;
	}

	/**
	 * Gets an index into $wgImageLimits to use for a maximum size
	 * Copied from ImagePage.php
	 */
	static function getSizeSel() {
		global $wgUser, $wgImageLimits;
		if( $wgUser->getOption( 'imagesize' ) == '' ) {
			$sizeSel = User::getDefaultOption( 'imagesize' );
		} else {
			$sizeSel = intval( $wgUser->getOption( 'imagesize' ) );
		}
		if( !isset( $wgImageLimits[$sizeSel] ) ) {
			$sizeSel = User::getDefaultOption( 'imagesize' );
		}
		return $sizeSel;
	}
	
	function __construct( $title ) {
		$this->mTitle = $title;
	}

	function ajaxNoCache( $sizeSel ) {
		$image = new Image( $this->mTitle );
		if ( !$image->exists() ) {
			return self::jsonError( 'picturepopup_no_image' );
		}
		$licenseData = $this->getImageLicenseMetadata( $this->mTitle );
		$imageData = $this->getImageMetadata( $image, $sizeSel );
		return json_encode( array_merge( $licenseData, $imageData ) );
	}

	static function error( $errorName /*, ... */ ) {
		$args = func_get_args();
		$text = call_user_func_array( 'wfMsgHtml', $args );
		return array( 
			'errorName' => $errorName,
			'errorText' => $text
		);
	}

	static function warning( $errorName /*, ... */ ) {
		$args = func_get_args();
		$text = call_user_func_array( 'wfMsgHtml', $args );
		return array( 
			'warningName' => $errorName,
			'warningText' => $text
		);
	}

	static function jsonError( $errorName /*, ... */ ) {
		$args = func_get_args();
		return json_encode( call_user_func_array( array( __CLASS__, 'error' ), $args ) );
	}

	static function jsonWarning( $errorName /*, ... */ ) {
		$args = func_get_args();
		return json_encode( call_user_func_array( array( __CLASS__, 'warning' ), $args ) );
	}	

	/**
	 * Get an array of dbkeys for license templates
	 */
	function getLicenseTemplateList() {
		global $wgMemc, $wgDBname;
		
		$listTitle = Title::newFromText( wfMsgForContent( 'picturepopup_license_list' ) );
		if ( !$listTitle ) {
			return false;
		}
		$memcKey = "$wgDBname:picturepopup:licenselist";
		return $listTitle->getRelatedCache( $wgMemc, $memcKey, 86400, 
			array( $this, 'getLicenseTemplateListNoCache' ), array( $listTitle ) );
	}

	/**
	 * Get an array of license templates, with no caching
	 */
	function getLicenseTemplateListNoCache( $listTitle ) {
		global $wgContLang;
		# Select all links to the template namespace
		$dbr =& wfGetDB( DB_SLAVE );
		$res = $dbr->select( 
			array( 'page', 'pagelinks' ), 
			array( 'pl_title' ),
			array(
				'page_namespace' => $listTitle->getNamespace(),
				'page_title' => $listTitle->getDBkey(),
				'pl_from=page_id',
				'pl_namespace' => NS_TEMPLATE,
			), __METHOD__
		);
		if ( !$dbr->numRows( $res ) ) {
			return false;
		}

		$templateList = array();
		while ( $row = $dbr->fetchObject( $res ) ) {
			$templateList[] = $row->pl_title;
		}
		return $templateList;
	}

	/**
	 * Get the license metadata for a given image
	 */
	function getImageLicenseMetadata( $imageTitle ) {
		# Get the license template list
		$licenseList = $this->getLicenseTemplateList();
		if ( !$licenseList ) {
			return self::warning( 'picturepopup_no_license_list' );
		}

		# Find a license template in templatelinks for the page of interest
		$dbr =& wfGetDB( DB_SLAVE );
		$row = $dbr->selectRow( 'templatelinks', 
			array( 'tl_title' ),
			array( 
				'tl_from' => $this->mTitle->getArticleID(),
				'tl_namespace' => NS_TEMPLATE,
				'tl_title IN(' . $dbr->makeList( $licenseList ) . ')'
		   	),
			__METHOD__
		);
		if ( !$row ) {
			return self::warning( 'picturepopup_no_license' );
		}
		$licenseTitle = Title::makeTitle( NS_TEMPLATE, $row->tl_title );
		if ( !$licenseTitle ) {
			return self::warning( 'picturepopup_no_license' );
		}
		return $this->getLicenseMetadata( $licenseTitle );
	}

	/**
	 * Get the license metadata for a given license
	 */
	function getLicenseMetadata( $title ) {
		global $wgMemc, $wgDBname, $wgParser, $wgLang;
		$lang = $wgLang->getCode();
		$memcKey = "$wgDBname:picturepopup:license:$lang:" . $title->getPrefixedDBkey();
		return $title->getRelatedCache( $wgMemc, $memcKey, 86400, 
			array( $this, 'getLicenseMetadataNoCache' ), array( $title ) );
	}

	/**
	 * Get the license metadata for a given license, with no caching
	 */
	function getLicenseMetadataNoCache( $title ) {
		global $wgParser;

		# Fun with shell-style lazy evaluation
		( $revision = Revision::newFromTitle( $title ) ) && 
		( $text = $revision->getText() ) ||
		( $text = false );

		$out = $wgParser->parse( $text, $title, new ParserOptions );
		$text = $out->getText();
		$wrappedText = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"'.
			' "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html>'.
			'<head><title>test</title></head><body>'.$text.'</body></html>';
		$xml = new SimpleXMLElement( $wrappedText );

		# Get description
		$m = $xml->xpath( '//*[@id="imageLicenseText"]' );
		if ( !isset( $m[0] ) ) {
			return self::warning( 'picturepopup_no_license_text' );
		}
		$description = strval( $m[0] );

		# Get name and icon
		$m = $xml->xpath( '//*[@id="imageLicenseIcon"]//img' );
		if ( !isset( $m[0] ) || !isset( $m[0]['alt'] ) || !isset( $m[0]['src'] ) ) {
			return self::warning( 'picturepopup_invalid_icon' );
		}
		$name = strval( $m[0]['alt'] );
		$icon = strval( $m[0]['src'] );

		return array(
			'licenseName' => $name,
			'licenseIcon' => $icon,
			'licenseDescription' => $description
		);
	}

	/**
	 * Get image metadata as an associative array
	 *
	 * Array elements are:
	 *   imageName              Canonical image name
	 *   
	 *   sourceWidth            The width of the source image, or zero for non-images
	 *   
	 *   sourceHeight           The height of the source image, or zero for non-images
	 *   
	 *   sourceURL              The URL of the file itself
	 *   
	 *   mustRender             True for SVGs and similar images with little browser support
	 *   
	 *   thumbURL               URL of a large thumbnail to display (or an icon for non-images)
	 *   
	 *   thumbWidth             The recommended client-side width of thumbURL
	 *   
	 *   thumbHeight            The recommended client-side height of thumbURL
	 *   
	 *   sourceLinkText         If there is a higher-resolution bitmap of the image, contains text 
	 *                          saying "download high resolution version". Otherwise contains 
	 *                          the image name, escaped for HTML.
	 *                          
	 *   multiPage              Set to true for multi-page images
	 *   
	 *   safeFile               Set to false if even linking to the image is dangerous, let alone 
	 *                          displaying inline.
	 *                          
	 *   safeFileWarning        If safeFile is set, this will contain recommended warning text
	 *                          that should be displayed near any link to the image
	 *
	 *  The two text elements (sourceLinkText and safeFileWarning) are both in HTML format. 
	 */
	function getImageMetadata( $image, $sizeSel ) {
		# TODO: refactor ImagePage::openShowImage to provide this information
		# This duplicates most of the logic from there at present
		
		global $wgImageLimits;
		list( $maxWidth, $maxHeight ) = $wgImageLimits[$sizeSel];

		$imageName = $image->getName();
		$sourceWidth = $image->getWidth();
		$sourceHeight = $image->getHeight();
		$mustRender = $image->mustRender();
		$sourceLinkText = htmlspecialchars( $image->getName() );
		$sourceURL = $image->getURL();
		$multiPage = $image->isMultipage();
		$safeFile = $image->isSafeFile();
		
		if ( $image->allowInlineDisplay() and $sourceWidth and $sourceHeight) {
			# We'll show a thumbnail of this image
			if ( $sourceWidth > $maxWidth || $sourceHeight > $maxHeight ) {
				# Calculate the thumbnail size.
				# First case, the limiting factor is the width, not the height.
				if ( $sourceWidth / $sourceHeight >= $maxWidth / $maxHeight ) {
					$thumbHeight = round( $sourceHeight * $maxWidth / $sourceWidth);
					$thumbWidth = $maxWidth;
					# Note that $thumbHeight <= $maxHeight now.
				} else {
					$thumbWidth = floor( $sourceWidth * $maxHeight / $sourceHeight);
					$thumbHeight = round( $sourceHeight * $thumbWidth / $sourceWidth );
					# Note that $thumbHeight <= $maxHeight now, but might not be identical
					# because of rounding.
				}

				if( $wgUseImageResize ) {
					$thumbnail = $image->getThumbnail( $thumbWidth, -1, $wgGenerateThumbnailOnParse );
					if ( $thumbnail == null ) {
						$thumbURL = $image->getViewURL();
					} else {
						$thumbURL = $thumbnail->getURL();
					}
				} else {
					# No resize ability? Show the full image, but scale
					# it down in the browser so it fits on the page.
					$thumbURL = $image->getViewURL();
				}
				if( !$mustRender ) {
					# "Download high res version" link below the image
					$sourceLinkText = wfMsgHtml('showbigimage', $sourceWidth, $sourceHeight, intval( $image->getSize()/1024 ) );
				}
			} else {
				$thumbURL = $image->getViewURL();
			}
		} else {
			# if direct link is allowed but it's not a renderable image, show an icon.
			if ( $safeFile ) {
				$icon = $image->iconThumb();
				$thumbURL = $icon->url;
				$thumbWidth = $icon->width;
				$thumbHeight = $icon->height;
			} else {
				$thumbURL = false;
				$thumbWidth = 0;
				$thumbHeight = 0;
			}
		}
		if ( !$safeFile ) {
			global $wgParser;
			$safeFileWarning = $wgParser->parse( wfMsg( 'mediawarning' ), $this->mTitle, new ParserOptions );
		} else {
			$safeFileWarning = false;
		}

		return compact( 'imageName', 'sourceWidth', 'sourceHeight', 'mustRender', 
			'sourceLinkText', 'sourceURL', 'multiPage', 'safeFile', 'safeFileWarning', 
			'thumbWidth', 'thumbHeight', 'thumbURL' );
	}
}
?>

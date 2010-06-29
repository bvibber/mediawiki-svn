<?php
/**
 * Copyright © Wikimedia Deutschland, 2009
 * Authors Hallo Welt! Medienwerkstatt GmbH
 * Authors Sebastian Ulbricht, Daniel Lynge, Marc Reymann, Markus Glaser
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

/**
 * inspired by djvuimage from Brion Vibber
 * modified and written by xarax
 * adapted to tiff by Hallo Welt! - Medienwerkstatt GmbH
 */

class PagedTiffImage {
	protected $_meta = null;
	protected $mFilename;

	function __construct( $filename ) {
		$this->mFilename = $filename;
	}

	/**
	 * Called by MimeMagick functions.
	 */
	public function isValid() {
		return count( $this->retrieveMetaData() );
	}

	/**
	 * Returns an array that corresponds to the native PHP function getimagesize().
	 */
	public function getImageSize() {
		$data = $this->retrieveMetaData();
		$size = $this->getPageSize( $data, 1 );

		if ( $size ) {
			$width = $size['width'];
			$height = $size['height'];
			return array( $width, $height, 'Tiff',
			"width=\"$width\" height=\"$height\"" );
		}
		return false;
	}

	/**
	 * Returns an array with width and height of the tiff page.
	 */
	public static function getPageSize( $data, $page ) {
		if ( isset( $data['page_data'][$page] ) ) {
			return array(
				'width'  => $data['page_data'][$page]['width'],
				'height' => $data['page_data'][$page]['height']
			);
		}
		return false;
	}

	/**
	 * Reads metadata of the tiff file via shell command and returns an associative array.
	 * layout:
	 * meta['page_amount'] = amount of pages
	 * meta['page_data'] = metadata per page
	 * meta['exif']  = Exif, XMP and IPTC
	 * meta['errors'] = identify-errors
	 * meta['warnings'] = identify-warnings
	 */
	public function retrieveMetaData() {
		global $wgImageMagickIdentifyCommand, $wgTiffExivCommand, $wgTiffUseExiv;

		if ( $this->_meta === null ) {
			if ( $wgImageMagickIdentifyCommand ) {

				wfProfileIn( 'PagedTiffImage::retrieveMetaData' );
				
				// ImageMagick is used to get the basic metadata of individual pages
				$cmd = wfEscapeShellArg( $wgImageMagickIdentifyCommand ) .
					' -format "[BEGIN]page=%p\nalpha=%A\nalpha2=%r\nheight=%h\nwidth=%w\ndepth=%z[END]" ' .
					wfEscapeShellArg( $this->mFilename ) . ' 2>&1';

				wfProfileIn( 'identify' );
				wfDebug( __METHOD__ . ": $cmd\n" );
				$dump = wfShellExec( $cmd, $retval );
				wfProfileOut( 'identify' );
				if ( $retval ) {
					$data['errors'][] = "identify command failed: $cmd";
					wfDebug( __METHOD__ . ": identify command failed: $cmd\n" );
					return $data; // fail. we *need* that info
				}
				$this->_meta = $this->convertDumpToArray( $dump );
				$this->_meta['exif'] = array();

				if ( $wgTiffUseExiv ) {
					// read EXIF, XMP, IPTC as name-tag => interpreted data 
					// -ignore unknown fields
					// see exiv2-doc @link http://www.exiv2.org/sample.html
					// NOTE: the linux version of exiv2 has a bug: it can only 
					// read one type of meta-data at a time, not all at once.
					$cmd = wfEscapeShellArg( $wgTiffExivCommand ) .
						' -u -psix -Pnt ' . wfEscapeShellArg( $this->mFilename );

					wfRunHooks( 'PagedTiffHandlerExivCommand', array( &$cmd, $this->mFilename ) );

					wfProfileIn( 'exiv2' );
					wfDebug( __METHOD__ . ": $cmd\n" );
					$dump = wfShellExec( $cmd, $retval );
					wfProfileOut( 'exiv2' );

					if ( $retval ) {
						$data['errors'][] = "exiv command failed: $cmd";
						wfDebug( __METHOD__ . ": exiv command failed: $cmd\n" );
						// don't fail - we are missing info, just report
					}

					$result = array();
					preg_match_all( '/(\w+)\s+(.+)/', $dump, $result, PREG_SET_ORDER );

					foreach ( $result as $data ) {
						$this->_meta['exif'][$data[1]] = $data[2];
					}
				} else {
					wfDebug( __METHOD__ . ": using internal Exif( {$this->mFilename} )\n" );
					$exif = new Exif( $this->mFilename );
					$data = $exif->getFilteredData();
					if ( $data ) {
						$data['MEDIAWIKI_EXIF_VERSION'] = Exif::version();
						$this->_meta['exif'] = $data;
					} 
				}
				wfProfileOut( 'PagedTiffImage::retrieveMetaData' );
			}
		}
		unset( $this->_meta['exif']['Image'] );
		unset( $this->_meta['exif']['filename'] );
		unset( $this->_meta['exif']['Base filename'] );
		return $this->_meta;
	}

	/**
	 * helper function of retrieveMetaData().
	 * parses shell return from identify-command into an array.
	 */
	protected function convertDumpToArray( $dump ) {
		global $wgTiffIdentifyRejectMessages, $wgTiffIdentifyBypassMessages;

		$data = array();
		if ( strval( $dump ) == '' ) {
			$data['errors'][] = "no metadata";
			return $data;
		}

		$infos = null;
		preg_match_all( '/\[BEGIN\](.+?)\[END\]/si', $dump, $infos, PREG_SET_ORDER );
		$data['page_amount'] = count( $infos );
		$data['page_data'] = array();
		foreach ( $infos as $info ) {
			$entry = array();
			$lines = explode( "\n", $info[1] );
			foreach ( $lines as $line ) {
				if ( trim( $line ) == '' ) {
					continue;
				}
				$parts = explode( '=', $line );
				if ( trim( $parts[0] ) == 'alpha' && trim( $parts[1] ) == '%A' ) {
					continue;
				}
				if ( trim( $parts[0] ) == 'alpha2' && !isset( $entry['alpha'] ) ) {
					switch( trim( $parts[1] ) ) {
						case 'DirectClassRGBMatte':
						case 'DirectClassRGBA':
							$entry['alpha'] = 'true';
							break;
						default:
							$entry['alpha'] = 'false';
							break;
					}
					continue;
				}
				$entry[trim( $parts[0] )] = trim( $parts[1] );
			}
			$entry['pixels'] = $entry['height'] * $entry['width'];
			$data['page_data'][$entry['page']] = $entry;
		}

		
		$dump = preg_replace( '/\[BEGIN\](.+?)\[END\]/si', '', $dump );
		if ( strlen( $dump ) ) {
			$errors = explode( "\n", $dump );
			foreach ( $errors as $error ) {
				$error = trim( $error );
				if ( $error === '' )
					continue;

				$knownError = false;
				foreach ( $wgTiffIdentifyRejectMessages as $msg ) {
					if ( preg_match( $msg, trim( $error ) ) ) {
						$data['errors'][] = $error;
						$knownError = true;
						break;
					}
				}
				if ( !$knownError ) {
					// ignore messages that match $wgTiffIdentifyBypassMessages
					foreach ( $wgTiffIdentifyBypassMessages as $msg ) {
						if ( preg_match( $msg, trim( $error ) ) ) {
							// $data['warnings'][] = $error;
							$knownError = true;
							break;
						}
					}
				}
				if ( !$knownError ) {
					$data['warnings'][] = $error;
				}
			}
		}
		return $data;
	}
}

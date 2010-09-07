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
				'width'  => intval( $data['page_data'][$page]['width'] ),
				'height' => intval( $data['page_data'][$page]['height'] )
			);
		}
		return false;
	}

	public function resetMetaData() {
		$this->_meta = null;
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
		global $wgTiffUseTiffinfo, $wgTiffTiffinfoCommand;

		if ( $this->_meta === null ) {
			wfProfileIn( 'PagedTiffImage::retrieveMetaData' );

			//fetch base info: number of pages, size and alpha for each page.
			//run hooks first, then optionally tiffinfo or, per default, ImageMagic's identify command
			if ( !wfRunHooks( 'PagedTiffHandlerTiffData', array( $this->mFilename, &$this->_meta ) ) ) {
				wfDebug( __METHOD__ . ": hook PagedTiffHandlerTiffData overrides TIFF data extraction\n" );
			} else if ( $wgTiffUseTiffinfo ) {
				// read TIFF directories using libtiff's tiffinfo, see 
				// http://www.libtiff.org/man/tiffinfo.1.html
				$cmd = wfEscapeShellArg( $wgTiffTiffinfoCommand ) .
					' ' . wfEscapeShellArg( $this->mFilename ) . ' 2>&1';

				wfProfileIn( 'tiffinfo' );
				wfDebug( __METHOD__ . ": $cmd\n" );
				$dump = wfShellExec( $cmd, $retval );
				wfProfileOut( 'tiffinfo' );

				if ( $retval ) {
					$data['errors'][] = "tiffinfo command failed: $cmd";
					wfDebug( __METHOD__ . ": tiffinfo command failed: $cmd\n" );
					return $data; // fail. we *need* that info
				}

				$this->_meta = $this->parseTiffinfoOutput( $dump );
			} else {
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

				$this->_meta = $this->parseIdentifyOutput( $dump );
			} 

			$this->_meta['exif'] = array();

			//fetch extended info: EXIF/IPTC/XMP
			//run hooks first, then optionally Exiv2 or, per default, the internal EXIF class
			if ( !empty( $this->_meta['errors'] ) ) {
				wfDebug( __METHOD__ . ": found errors, skipping EXIF extraction\n" );
			} else if ( !wfRunHooks( 'PagedTiffHandlerExifData', array( $this->mFilename, &$this->_meta['exif'] ) ) ) {
				wfDebug( __METHOD__ . ": hook PagedTiffHandlerExifData overrides EXIF extraction\n" );
			} else if ( $wgTiffUseExiv ) {
				// read EXIF, XMP, IPTC as name-tag => interpreted data 
				// -ignore unknown fields
				// see exiv2-doc @link http://www.exiv2.org/sample.html
				// NOTE: the linux version of exiv2 has a bug: it can only 
				// read one type of meta-data at a time, not all at once.
				$cmd = wfEscapeShellArg( $wgTiffExivCommand ) .
					' -u -psix -Pnt ' . wfEscapeShellArg( $this->mFilename ) . ' 2>&1';

				wfProfileIn( 'exiv2' );
				wfDebug( __METHOD__ . ": $cmd\n" );
				$dump = wfShellExec( $cmd, $retval );
				wfProfileOut( 'exiv2' );

				if ( $retval ) {
					$data['errors'][] = "exiv command failed: $cmd";
					wfDebug( __METHOD__ . ": exiv command failed: $cmd\n" );
					// don't fail - we are missing info, just report
				}

				$data = $this->parseExiv2Output( $dump );

				$this->_meta['exif'] = $data;
			} else {
				wfDebug( __METHOD__ . ": using internal Exif( {$this->mFilename} )\n" );
				$exif = new Exif( $this->mFilename );
				$data = $exif->getFilteredData();
				if ( $data ) {
					$data['MEDIAWIKI_EXIF_VERSION'] = Exif::version();
					$this->_meta['exif'] = $data;
				} 
			}

			unset( $this->_meta['exif']['Image'] );
			unset( $this->_meta['exif']['filename'] );
			unset( $this->_meta['exif']['Base filename'] );
			unset( $this->_meta['exif']['XMLPacket'] );
			unset( $this->_meta['exif']['ImageResources'] );
			
			$this->_meta['TIFF_METADATA_VERSION'] = TIFF_METADATA_VERSION;

			wfProfileOut( 'PagedTiffImage::retrieveMetaData' );
		}
		
		return $this->_meta;
	}

	private function addPageEntry( &$entry, &$metadata, &$prevPage ) {
		if ( !isset( $entry['page'] ) ) {
			$entry['page'] = $prevPage +1;
		} else {
			if ( $prevPage >= $entry['page'] ) {
				$metadata['errors'][] = "inconsistent page numbering in TIFF directory";
				return false;
			} 
		}

		if ( isset( $entry['width'] ) && isset( $entry['height'] ) ) {
			$prevPage = max($prevPage, $entry['page']);

			if ( !isset( $entry['alpha'] ) ) {
				$entry['alpha'] = 'false';
			}

			$entry['pixels'] = $entry['height'] * $entry['width'];
			$metadata['page_data'][$entry['page']] = $entry;
		}

		$entry = array();
		return true;
	}

	/**
	 * helper function of retrieveMetaData().
	 * parses shell return from tiffinfo-command into an array.
	 */
	protected function parseTiffinfoOutput( $dump ) {
		global $wgTiffTiffinfoRejectMessages, $wgTiffTiffinfoBypassMessages;

		$dump = preg_replace( '/ Image Length:/', "\n  Image Length:", $dump ); #HACK: width and length are given on a single line...
		$rows = preg_split('/[\r\n]+\s*/', $dump);

		$data = array();
		$data['page_data'] = array();

		$ignoreIFDs = array();
		$entry = array();

		$ignore = false;
		$prevPage = 0;

		foreach ( $rows as $row ) {
			$row = trim( $row );

			if ( preg_match('/^<|^$/', $row) ) {
				continue;
			}

			$error = false;

			foreach ( $wgTiffTiffinfoRejectMessages as $pattern ) {
				if ( preg_match( $pattern, trim( $row ) ) ) {
					$data['errors'][] = $row;
					$error = true;
					break;
				}
			}

			if ( $error ) continue;

			if ( preg_match('/^TIFF Directory at offset 0x[a-f0-9]+ \((\d+)\)/', $row, $m) ) {
				if ( $ignore ) {
					$entry = array();
				} else if ( $entry ) {
					$ok = $this->addPageEntry($entry, $data, $prevPage);
					if ( !$ok ) {
						$error = true;
						continue;
					}
				}

				$offset = (int)$m[1];
				$ignore = !empty( $ignoreIFDs[ $offset ] );
			} else if ( preg_match('#^(TIFF.*?Directory): (.*?/.*?): (.*)#i', $row, $m) ) {
				$bypass = false; 
				$msg = $m[3];

				foreach ( $wgTiffTiffinfoBypassMessages as $pattern ) {
					if ( preg_match( $pattern, trim( $row ) ) ) {
						$bypass = true;
						break;
					}
				}

				if ( !$bypass ) {
					$data['warnings'][] = $msg;
				}
			} else if ( preg_match('/^\s*(.*?)\s*:\s*(.*?)\s*$/', $row, $m) ) {
				$key = $m[1];
				$value = $m[2];

				if ( $key == 'Page Number' && preg_match('/(\d+)-(\d+)/', $value, $m) ) {
					$data['page_amount'] = (int)$m[2];
					$entry['page'] = (int)$m[1] +1;
				} else if ( $key == 'Samples/Pixel' ) {
					if ($value == '4') $entry['alpha'] = 'true';
				} else if ( $key == 'Extra samples' ) {
					if (preg_match('.*alpha.*', $value)) $entry['alpha'] = 'true';
				} else if ( $key == 'Image Width' || $key == 'PixelXDimension' ) {
					$entry['width'] = (int)$value;
				} else if ( $key == 'Image Length' || $key == 'PixelYDimension' ) {
					$entry['height'] = (int)$value;
				} else if ( preg_match('/.*IFDOffset/', $key) ) {
					# ignore extra IFDs, see <http://www.awaresystems.be/imaging/tiff/tifftags/exififd.html>
					# Note: we assume that we will always see the reference before the actual IFD, so we know which IFDs to ignore
					$offset = (int)$value;
					$ignoreIFDs[$offset] = true;
				}
			} else {
				// strange line
			}

		}

		if ( $entry && !$ignore ) {
			$ok = $this->addPageEntry($entry, $data, $prevPage);
		}

		if ( !isset( $data['page_amount'] ) ) {
			$data['page_amount'] = count( $data['page_data'] );
		}

		if ( ! $data['page_data'] ) {
			$data['errors'][] = 'no page data found in tiff directory!';
		}

		return $data;
	}

	/**
	 * helper function of retrieveMetaData().
	 * parses shell return from exiv2-command into an array.
	 */
	protected function parseExiv2Output( $dump ) {
		$result = array();
		preg_match_all( '/^(\w+)\s+(.+)$/m', $dump, $result, PREG_SET_ORDER );

		$data = array();

		foreach ( $result as $row ) {
			$data[$row[1]] = $row[2];
		}

		return $data;
	}

	/**
	 * helper function of retrieveMetaData().
	 * parses shell return from identify-command into an array.
	 */
	protected function parseIdentifyOutput( $dump ) {
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

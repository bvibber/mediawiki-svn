<?php
/**
Class to deal with reconciling and extracting metadata from bitmap images.
This is meant to comply with http://www.metadataworkinggroup.org/pdf/mwg_guidance.pdf

@todo finish IPTC
@todo xmp
@todo other image formats.
*/
class BitmapMetadataHandler {
	private $filetype;
	private $filename;
	private $metadata = Array();
	private $metaPriority = Array(
		20 => Array( 'other' ),
		40 => Array( 'file-comment' ),
		60 => Array( 'iptc-bad-hash' ),
		80 => Array( 'xmp' ),
		100 => Array( 'iptc-good-hash', 'iptc-no-hash' ),
		120 => Array( 'exif' ),
	);
	private $iptcType = 'iptc-no-hash';

	/** Function to extract metadata segmants of interest from jpeg files
	* based on GIFMetadataExtractor.
	*
	* we can almost use getimagesize to do this
	* but gis doesn't support having multiple app1 segments
	* and those can't extract xmp on files containing both exif and xmp data
	*
	* I'm not sure if this should be in a class of its own, like GIFMetadataExtractor
	*
	* @param String $filename name of jpeg file
	* @return Array of interesting segments.
	* Can throw an exception if given invalid file.
	*/
	function jpegSegmentSplitter () {
		$filename = $this->filename;
		if ( $this->filetype !== 'image/jpeg' ) throw new MWException( "jpegSegmentSplitter called on non-jpeg" );

		$segments = Array( 'XMP_ext' => Array() );

		if ( !$filename ) throw new MWException( "No filename specified for BitmapMetadataHandler" );
		if ( !file_exists( $filename ) || is_dir( $filename ) ) throw new MWException( "Invalid file $filename passed to BitmapMetadataHandler" );

		$fh = fopen( $filename, "rb" );

		if ( !$fh ) throw new MWException( "Could not open file $filename" );

		$buffer = fread( $fh, 2 );
		if ( $buffer !== "\xFF\xD8" ) throw new MWException( "Not a jpeg, no SOI" );
		while ( !feof( $fh ) ) {
			$buffer = fread( $fh, 1 );
			if ( $buffer !== "\xFF" ) {
				throw new MWException( "Error reading jpeg file marker" );
			}

			$buffer = fread( $fh, 1 );
			if ( $buffer === "\xFE" ) {
				// COM section -- file comment
				$segments["COM"] = self::jpegExtractMarker( $fh );
			} elseif ( $buffer === "\xE1" ) {
				// APP1 section (Exif, XMP, and XMP extended)
				$temp = self::jpegExtractMarker( $fh );

				// check what type of app segment this is.
				if ( substr( $temp, 0, 29 ) === "http://ns.adobe.com/xap/1.0/\x00" ) {
					$segments["XMP"] = $temp;
				} elseif ( substr( $temp, 0, 35 ) === "http://ns.adobe.com/xmp/extension/\x00" ) {
					// fixme - put some limit on this? what if someone
					// uploaded a file with 100mb worth of metadata.
					$segments["XMP_ext"][] = $temp;
				}
			} elseif ( $buffer === "\xED" ) {
				// APP13 - PSIR. IPTC and some photoshop stuff
				$temp = self::jpegExtractMarker( $fh );
				if ( substr( $temp, 0, 14 ) === "Photoshop 3.0\x00" ) {
					$segments["PSIR"] = $temp;
				}
			} elseif ( $buffer === "\xD9" || $buffer === "\xDA" ) {
				// EOI - end of image or SOS - start of scan. either way we're past any interesting segments
				return $segments;
			} else {
				// segment we don't care about, so skip
				$size = unpack( "nint", fread( $fh, 2 ) );
				if ( $size['int'] <= 2 ) throw new MWException( "invalid marker size in jpeg" );
				fseek( $fh, $size['int'] - 2, SEEK_CUR );
			}

		}
		// shouldn't get here.
		throw new MWException( "Reached end of jpeg file unexpectedly" );

	}
	/**
	* Helper function for jpegSegmentSplitter
	* @param &$fh FileHandle for jpeg file
	* @return data content of segment.
	*/
	private function jpegExtractMarker( &$fh ) {
		$size = unpack( "nint", fread( $fh, 2 ) );
		if ( $size['int'] <= 2 ) throw new MWException( "invalid marker size in jpeg" );
		return fread( $fh, $size['int'] - 2 );
	}

	/**
	* This does the photoshop image resource app13 block
	* of interest, IPTC-IIM metadata is stored here.
	*
	* Mostly just calls doPSIR and doIPTC
	*
	* @param String $app13 String containing app13 block from jpeg file
	*/
	private function doApp13 ( $app13 ) {
		$this->doPSIR( $app13 );

		$iptc = IPTC::parse( $app13 );
		$this->addMetadata( $iptc, $this->iptcType );
	}

	/**
	* This reads the photoshop image resource.
	* Currently it only compares the iptc/iim hash
	* with the stored hash, which is used to determine the precedence
	* of the iptc data. In future it may extract some other info, like
	* url of copyright license.
	*
	* This should generally be called by doApp13()
	*
	* @param String $app13 photoshop psir app13 block from jpg.
	*/
	private function doPSIR ( $app13 ) {
		if ( !$app13 ) return;
		// First compare hash with real thing
		// 0x404 contains IPTC, 0x425 has hash
		// This is used to determine if the iptc is newer than
		// the xmp data, as xmp programs update the hash,
		// where non-xmp programs don't.

		$offset = 14; // skip past PHOTOSHOP 3.0 identifier. should already be checked.
		$appLen = strlen( $app13 );
		$realHash = "";
		$recordedHash = "";

		// the +12 is the length of an empty item.
		while ( $offset + 12 <= $appLen ) {
			$valid = true;
			$id = false;
			$lenName = false;
			$lenData = false;

			if ( substr( $app13, $offset, 4 ) !== '8BIM' ) {
				// its supposed to be 8BIM
				// but apperently sometimes isn't esp. in
				// really old jpg's
				$valid = false;
			}
			$offset += 4;
			$id = substr( $app13, $offset, 2 );
			// id is a 2 byte id number which identifies
			// the piece of info this record contains.

			$offset += 2;
			
			// some record types can contain a name, which
			// is a pascal string 0-padded to be an even
			// number of bytes. Most times (and any time
			// we care) this is empty, making it two null bytes.

			$lenName = ord( substr( $app13, $offset, 1 ) ) + 1;
			// we never use the name so skip it. +1 for length byte
			if ( $lenName % 2 == 1 ) $lenName++; // pad to even.
			$offset += $lenName;

			// now length of data (unsigned long big endian)
			$lenData = unpack( 'Nlen', substr( $app13, $offset, 4 ) );
			$offset += 4; // 4bytes length field;

			// this should not happen, but check.
			if ( $lenData['len'] + $offset > $appLen ) {
				wfDebug( __METHOD__ . ' PSIR data too long.' );
				return false;
			}

			if ( $valid ) {
				switch ( $id ) {
					case "\x04\x04":
						// IPTC block
						$realHash = md5( substr( $app13, $offset, $lenData['len'] ), true );
						break;
					case "\x04\x25":
						$recordedHash = substr( $app13, $offset, $lenData['len'] );
						break;
				}
			}

			// if odd, add 1 to length to account for
			// null pad byte.
			if ( $lenData['len'] % 2 == 1 ) $lenData['len']++;
			$offset += $lenData['len'];
		
		}

		if ( !$realHash ) return false; // no iptc data

		if ( !$recordedHash ) {
			$this->iptcType = 'iptc-no-hash';
		} elseif ( $realHash === $recordedHash ) {
			$this->iptcType = 'iptc-good-hash';
		} else { /*$realHash !== $recordedHash */
			$this->iptcType = 'iptc-bad-hash';
		}

	}

	/** get exif info using exif class.
	* Basically what used to be in BitmapHandler::getMetadata().
	* Just calls stuff in the Exif class.
	*/
	function getExif () {
		if ( file_exists( $this->filename ) ) {
			$exif = new Exif( $this->filename );
			$data = $exif->getFilteredData();
			if ( $data ) {
				$this->addMetadata( $data, 'exif' );
			}
		}
	}
	/** Add misc metadata. Warning: atm if the metadata category
	* doesn't have a priority, it will be silently discarded.
	*
	* @param Array $metaArray array of metadata values
	* @param string $type type. defaults to other. if two things have the same type they're merged
	*/
	function addMetadata( $metaArray, $type = 'other' ) {
		if ( isset( $this->metadata[$type] ) ) {
			/* merge with old data */
			$metaArray = $metaArray + $this->metadata[$type];
		}

		$this->metadata[$type] = $metaArray;
	}

	/**
	* Merge together the various types of metadata
	* the different types have different priorites,
	* and are merged in order.
	*
	* This function is generally called by the media handlers' getMetadata()
	*
	* @return Array metadata array
	*/
	function getMetadataArray() {
		// this seems a bit ugly... This is all so its merged in right order
		// based on the MWG recomendation.
		$temp = Array();
		krsort( $this->metaPriority );
		foreach ( $this->metaPriority as $pri ) {
			foreach ( $pri as $type ) {
				if ( isset( $this->metadata[$type] ) ) {
					$temp = $temp + $this->metadata[$type];
				}
			}
		}
		return $temp;
	}

	/** constructor.
	* This generally shouldn't be called directly
	* instead BitmapMetadataHandler::newForJpeg should be used.
	*
	* @param string $file - full path to file
	* @param string $type - mime type of file
	*/
	function __construct ( $file, $type ) {
		$this->filename = $file;
		$this->filetype = $type;
	}
	/** factory function special for jpg's.
	* This is how new BitmapMetadataHandler's should be made.
	* at some point there should be a newForPNG, etc.
	*
	* @param string $file filename (with full path)
	* @return BitmapMetadataHandler
	*/
	static function newForJpeg ( $file ) {
		$meta = new self( $file, 'image/jpeg' );
		$meta->getExif();
		$seg = Array();
		$seg = $meta->JpegSegmentSplitter();
		if ( isset( $seg['COM'] ) ) {
			$meta->addMetadata( Array( 'JPEGFileComment' => $seg['COM'] ), 'file-comment' );
		}
		if ( isset( $seg['PSIR'] ) ) {
			$meta->doApp13( $seg['PSIR'] );
		}
		return $meta;
	}

}

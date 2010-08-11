<?php
/**
  * PNG frame counter.
  * Slightly derived from GIFMetadataExtractor.php
  * Deliberately not using MWExceptions to avoid external dependencies, encouraging
  * redistribution.
  */

class PNGMetadataExtractor {
	static $png_sig;
	static $CRC_size;

	static function getMetadata( $filename ) {
		self::$png_sig = pack( "C8", 137, 80, 78, 71, 13, 10, 26, 10 );
		self::$CRC_size = 4;
		
		$frameCount = 0;
		$loopCount = 1;
		$duration = 0.0;
		$xmp = '';
		$meta = array();

		if (!$filename)
			throw new Exception( __METHOD__ . ": No file name specified" );
		elseif ( !file_exists($filename) || is_dir($filename) )
			throw new Exception( __METHOD__ . ": File $filename does not exist" );
		
		$fh = fopen( $filename, 'r' );
		
		if (!$fh)
			throw new Exception( __METHOD__ . ": Unable to open file $filename" );
		
		// Check for the PNG header
		$buf = fread( $fh, 8 );
		if ( !($buf == self::$png_sig) ) {
			throw new Exception( __METHOD__ . ": Not a valid PNG file; header: $buf" );
		}

		// Read chunks
		while( !feof( $fh ) ) {
			$buf = fread( $fh, 4 );
			if( !$buf ) { throw new Exception( __METHOD__ . ": Read error" ); return; }
			$chunk_size = unpack( "N", $buf);
			$chunk_size = $chunk_size[1];

			$chunk_type = fread( $fh, 4 );
			if( !$chunk_type ) { throw new Exception( __METHOD__ . ": Read error" ); return; }

			if ( $chunk_type == "acTL" ) {
				$buf = fread( $fh, $chunk_size );
				if( !$buf ) { throw new Exception( __METHOD__ . ": Read error" ); return; }

				$actl = unpack( "Nframes/Nplays", $buf );
				$frameCount = $actl['frames'];
				$loopCount = $actl['plays'];
			} elseif ( $chunk_type == "fcTL" ) {
				$buf = fread( $fh, $chunk_size );
				if( !$buf ) { throw new Exception( __METHOD__ . ": Read error" ); return; }
				$buf = substr( $buf, 20 );	

				$fctldur = unpack( "ndelay_num/ndelay_den", $buf );
				if( $fctldur['delay_den'] == 0 ) $fctldur['delay_den'] = 100;
				if( $fctldur['delay_num'] ) {
					$duration += $fctldur['delay_num'] / $fctldur['delay_den'];
				}
			} elseif ( $chunk_type == "iTXt"  ) {
				// At the moment this only does XMP iText chunks,
				// but in the future might extract other metadata chunks.
				if( $chunk_size <= 22 ) {
					// something weird, so skip
					fseek( $fh, $chunk_size, SEEK_CUR );
					continue;
				}
				$itxtHeader = fread( $fh, 22 );
				if( !$itxtHeader ) { throw new Exception( __METHOD__ . ": Read error" ); return; }
				if( $itxtHeader !== "XML:com.adobe.xmp\x00\x00\x00\x00\x00" ) {
					// some other iTXt chunk.
					fseek( $fh, $chunk_size - 22, SEEK_CUR );
					continue;
				}
				$xmp = fread( $fh, $chunk_size - 22 );
				if( !$xmp ) { throw new Exception( __METHOD__ . ": Read error" ); return; }
			} elseif ( $chunk_type == "IEND" ) {
				break;
			} else {
				fseek( $fh, $chunk_size, SEEK_CUR );
			}
			fseek( $fh, self::$CRC_size, SEEK_CUR );
		}
		fclose( $fh );

		if( $loopCount > 1 ) {
			$duration *= $loopCount;
		}

		return array(
			'frameCount' => $frameCount,
			'loopCount' => $loopCount,
			'duration' => $duration,
			'xmp' => $xmp,
		);
		
	}
}

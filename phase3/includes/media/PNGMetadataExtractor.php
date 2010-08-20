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
	static $text_chunks;

	const VERSION = 1;

	static function getMetadata( $filename ) {
		self::$png_sig = pack( "C8", 137, 80, 78, 71, 13, 10, 26, 10 );
		self::$CRC_size = 4;
		/* based on list at http://owl.phy.queensu.ca/~phil/exiftool/TagNames/PNG.html#TextualData 
		 * and http://www.w3.org/TR/PNG/#11keywords
		 */
		self::$text_chunks = array(
			'xml:com.adobe.xmp' => 'xmp',
			# Artist is unofficial. Author is the recommended
			# keyword in the PNG spec. However some people output
			# Artist so support both.
			'artist'      => 'Artist',
			'model'       => 'Model',
			'make'        => 'Make',
			'author'      => 'Artist',
			'comment'     => 'PNGFileComment',
			'description' => 'ImageDescription',
			'title'       => 'ObjectName',
			'copyright'   => 'Copyright',
			# Source as in original device used to make image
			# not as in who gave you the image
			'source'      => 'Model',
			'software'    => 'Software',
			'disclaimer'  => 'Disclaimer',
			'warning'     => 'ContentWarning',
			'url'         => 'Identifier', # Not sure if this is best mapping. Maybe WebStatement.
			'label'       => 'Label',
			/* Other potentially useful things - Creation Time, Document */
		);

		$showXMP = function_exists( 'xml_parser_create_ns' );
		
		$frameCount = 0;
		$loopCount = 1;
		$duration = 0.0;
		$text = array();

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
			} elseif ( $chunk_type == "iTXt" ) {
				// Extracts iTXt chunks, uncompressing if neccesary.
				$buf = fread( $fh, $chunk_size );
				$items = array();
				if ( preg_match( 
					'/^([^\x00]{1,79})\x00(\x00|\x01)\x00([^\x00]*)(.)[^\x00]*\x00(.*)$/Ds',
					$buf, $items )
				) {
					/* $items[1] = text chunk name, $items[2] = compressed flag,
					 * $items[3] = lang code (or ""), $items[4]= compression type.
					 * $items[5] = content
					 */

					// Theoretically should be case-sensitive, but in practise...
					$items[1] = strtolower( $items[1] );
					if ( !isset( self::$text_chunks[$items[1]] ) ) {
						// Only extract textual chunks on our list.
						fseek( $fh, self::$CRC_size, SEEK_CUR );
						continue;
					}

					if ( $items[3] == '' ) {
						// if no lang specified use x-default like in xmp.
						$items[3] = 'x-default';
					}
					
					// if compressed
					if ( $items[2] == "\x01" ) {
						if ( function_exists( 'gzuncompress' ) && $items[4] === "\x00" ) {
							wfSuppressWarnings();
							$items[5] = gzuncompress( $items[5] );
							wfRestoreWarnings();

							if ( $items[5] === false ) {
								//decompression failed
								wfDebug( __METHOD__ . ' Error decompressing iTxt chunk - ' . $items[1] );
								fseek( $fh, self::$CRC_size, SEEK_CUR );
								continue;
							}

						} else {
							wfDebug( __METHOD__ . ' Skipping compressed png iTXt chunk due to lack of zlib,'
								. ' or potentially invalid compression method' );
							fseek( $fh, self::$CRC_size, SEEK_CUR );
							continue;
						}
					}
					$finalKeyword = self::$text_chunks[ $items[1] ];
					$text[ $finalKeyword ][ $items[3] ] = $items[5];
					$text[ $finalKeyword ]['_type'] = 'lang';

				} else {
					//Error reading iTXt chunk
					throw new Exception( __METHOD__ . ": Read error on iTXt chunk" );
					return;
				}

			} elseif ( $chunk_type == 'tEXt' ) {
				$buf = fread( $fh, $chunk_size );
				$keyword = '';
				$content = '';

				list( $keyword, $content ) = explode( "\x00", $buf, 2 );
				if ( $keyword === '' || $content === '' ) {
					throw new Exception( __METHOD__ . ": Read error on tEXt chunk" );
					return;
				}

				// Theoretically should be case-sensitive, but in practise...
				$keyword = strtolower( $keyword );
				if ( !isset( self::$text_chunks[ $keyword ] ) ) {
					// Don't recognize chunk, so skip.
					fseek( $fh, self::$CRC_size, SEEK_CUR );
					continue;
				}
				wfSuppressWarnings();
				$content = iconv( 'ISO-8859-1', 'UTF-8', $content);
				wfRestoreWarnings();

				if ( $content === false ) {
					throw new Exception( __METHOD__ . ": Read error (error with iconv)" );
					return;
				}

				$finalKeyword = self::$text_chunks[ $keyword ];
				$text[ $finalKeyword ][ 'x-default' ] = $content;
				$text[ $finalKeyword ]['_type'] = 'lang';

			} elseif ( $chunk_type == 'zTXt' ) {
				if ( function_exists( 'gzuncompress' ) ) {
					$buf = fread( $fh, $chunk_size );
					$keyword = '';
					$postKeyword = '';

					list( $keyword, $postKeyword ) = explode( "\x00", $buf, 2 );
					if ( $keyword === '' || $postKeyword === '' ) {
						throw new Exception( __METHOD__ . ": Read error on zTXt chunk" );
						return;
					}
					// Theoretically should be case-sensitive, but in practise...
					$keyword = strtolower( $keyword );

					if ( !isset( self::$text_chunks[ $keyword ] ) ) {
						// Don't recognize chunk, so skip.
						fseek( $fh, self::$CRC_size, SEEK_CUR );
						continue;
					}
					$compression = substr( $postKeyword, 0, 1 );
					$content = substr( $postKeyword, 1 );
					if ( $compression !== "\x00" ) {
						wfDebug( __METHOD__ . " Unrecognized compression method in zTXt ($keyword). Skipping." );
						fseek( $fh, self::$CRC_size, SEEK_CUR );
						continue;
					}

					wfSuppressWarnings();
					$content = gzuncompress( $content );
					wfRestoreWarnings();

					if ( $content === false ) {
						//decompression failed
						wfDebug( __METHOD__ . ' Error decompressing zTXt chunk - ' . $keyword );
						fseek( $fh, self::$CRC_size, SEEK_CUR );
						continue;
					}

					wfSuppressWarnings();
					$content = iconv( 'ISO-8859-1', 'UTF-8', $content);
					wfRestoreWarnings();

					if ( $content === false ) {
						throw new Exception( __METHOD__ . ": Read error (error with iconv)" );
						return;
					}

					$finalKeyword = self::$text_chunks[ $keyword ];
					$text[ $finalKeyword ][ 'x-default' ] = $content;
					$text[ $finalKeyword ]['_type'] = 'lang';

				} else {
					wfDebug( __METHOD__ . " Cannot decompress zTXt chunk due to lack of zlib. Skipping." );
					fseek( $fh, $chunk_size, SEEK_CUR );
				}

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
			'text' => $text,
		);
		
	}
}

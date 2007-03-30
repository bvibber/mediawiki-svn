<?php

require( dirname( __FILE__ ) . '/WebStoreCommon.php' );
$IP = dirname( realpath( __FILE__ ) ) . '/../..';
chdir( $IP );
define( 'MW_NO_OUTPUT_COMPRESSION', 1 );
require( './includes/WebStart.php' );

class InplaceScaler extends WebStoreCommon {
	function execute() {
		global $wgRequest, $wgContLanguageCode;

		if ( !$this->scalerAccessRanges ) {
			$this->error( 403, 'inplace_access_disabled' );
			return false;
		}

		/**
		 * Run access checks against REMOTE_ADDR rather than wfGetIP(), since we're not
		 * giving access even to trusted proxies, only direct clients.
		 */
		$allowed = false;
		foreach ( $this->scalerAccessRanges as $range ) {
			if ( IP::isInRange( $_SERVER['REMOTE_ADDR'], $range ) ) {
				$allowed = true;
				break;
			}
		}
		
		if ( !$allowed ) {
			$this->error( 403, 'inplace_access_denied' );
			return false;
		}

		if ( !$wgRequest->wasPosted() ) {
			echo $this->dtd();
?>
<html>
<head><title>inplace-scaler.php Test Interface</title></head>
<body>
<form method="post" action="inplace-scaler.php" enctype="multipart/form-data" >
<p>File: <input type="file" name="data" /></p>
<p>Width: <input type="text" name="width" /></p>
<p>Page: <input type="page" name="page" /></p>
<p><input type="submit" value="OK" /></p>
</form>
</body>
</html>
<?php
			return true;
		}

		$tempDir = $this->tmpDir . '/' . gmdate( self::$tempDirFormat );
		if ( !is_dir( $tempDir ) ) {
			if ( !wfMkdirParents( $tempDir ) ) {
				$this->error( 500, 'inplace_scaler_no_temp' );
				return false;
			}
		}

		$name = $wgRequest->getFileName( 'data' );
		$srcTemp = $wgRequest->getFileTempname( 'data' );
		$page = $wgRequest->getInt( 'page', 1 );
		$dstWidth = $wgRequest->getInt( 'width', 0 );

		# Check that the parameters are present
		if ( is_null( $name ) || !$dstWidth ) {
			$this->error( 400, 'inplace_scaler_not_enough_params' );
			return false;
		}

		$i = strrpos( $name, '.' );
		$ext = Image::normalizeExtension( $i ? substr( $name, $i + 1 ) : '' );

		$magic = MimeMagic::singleton();
		$mime = $magic->guessTypesForExtension( $ext );
		$deja = false;
		$size = Image::getImageSize( $srcTemp, $mime, $deja );
		if ( !$size ) {
			$this->error( 400, 'inplace_scaler_invalid_image' );
			return false;
		}

		$dstHeight = Image::scaleHeight( $size[0], $size[1], $dstWidth );

		list( $dstExt, $dstMime ) = Image::getThumbType( $ext, $mime );
		if ( preg_match( '/[ \\n;=]/', $name ) ) {
			$dstName = "thumb.$ext";
		} else {
			$dstName = $name;
		}
		if ( $dstExt != $ext ) {
			$dstName = "$dstName.$dstExt";
		}

		$dstTemp = tempnam( $tempDir, 'mwimg' );

		$error = Image::reallyRenderThumb( $srcTemp, $dstTemp, $mime, $dstWidth, $dstHeight, $page );
		if ( $error !== true ) {
			$this->error( 500, 'inplace_scaler_failed', $error );
			@unlink( $dstTemp );
			return false;
		}

		header( "Content-Type: $dstMime" );
		header( "Content-Disposition: inline;filename*=utf-8'$wgContLanguageCode'" . urlencode( $dstName ) );
		readfile( $dstTemp );
		unlink( $dstTemp );
	}
}


$s = new InplaceScaler;
$s->execute();

?>

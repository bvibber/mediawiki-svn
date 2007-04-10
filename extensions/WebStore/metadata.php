<?php

/*
 * Retrieve metadata for a file
 */

require( dirname( __FILE__ ) . '/WebStoreStart.php' );

class WebStoreImage extends Image {
	function __construct( $path ) {
		$title = Title::makeTitle( NS_IMAGE, 'Metadata.php dummy image');
		$this->imagePath = $path;
		parent::__construct( $title );
	}

	function getFullPath() {
		return $this->imagePath;
	}
}

class WebStoreMetadata extends WebStoreCommon {
	function execute() {
		global $wgRequest;

		if ( !$wgRequest->wasPosted() ) {
			echo $this->dtd();
?>
<html>
<head><title>metadata.php Test interface</title>
<body>
<form method="post" action="metadata.php">
<p>Repository: <select name="repository" value="public">
<option>public</option>
<option>temp</option>
<option>deleted</option>
</select>
</p>
<p>Relative path: <input type="text" name="path"></p>
<p><input type="submit" value="OK" /></p>
</form>
</body></html>
<?php
			return true;
		}

		$repository = $wgRequest->getVal( 'repository' );
		$root = $this->getRepositoryRoot( $repository );
		if ( strval( $root ) == '' ) {
				$this->error( 400, 'webstore_invalid_repository', $repository );
				return false;
		}

		$rel = $wgRequest->getVal( 'path' );
		if ( !$this->validateFilename( $rel ) ) {
			$this->error( 400, 'webstore_path_invalid' );
			return false;
		}

		$fullPath = $root . '/' . $rel;

		if ( !file_exists( $fullPath ) ) {
			$this->error( 400, 'webstore_metadata_not_found', $fullPath );
			return false;
		}

		$image = new WebStoreImage( $fullPath );
		$image->loadFromFile();

		$fields = array( 'width', 'height', 'bits', 'type', 'mime', 'metadata', 'size' );

		header( 'Content-Type: text/xml' );
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<response><status>success</status><metadata>\n";
		foreach ( $fields as $field ) {
			$value = $image->$field;
			if ( is_bool( $image->$field ) ) {
				$value = $value ? 1 : 0;
			}
			echo "<$field>" . htmlspecialchars( $value ) . "</$field>\n";
		}
		echo "</metadata></response>\n";
	}
}

$obj = new WebStoreMetadata;
$obj->executeCommon();

?>
